<?php

use App\Enums\PengajuanStatus;
use App\Filament\Resources\PengajuanSkpis\Pages\ListPengajuanSkpis;
use App\Models\Mahasiswa;
use App\Models\PejabatPenandatangan;
use App\Models\PengajuanSkpi;
use App\Models\User;
use App\Services\NomorSkpiGenerator;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

function penerbitanPengajuanFor(string $nim): PengajuanSkpi
{
    return Mahasiswa::where('nim', $nim)->firstOrFail()->pengajuanSkpis()->firstOrFail();
}

it('generates a sequential, formatted nomor skpi', function () {
    $andi = penerbitanPengajuanFor('2021010001');
    $dimas = penerbitanPengajuanFor('2020010003');

    $generator = app(NomorSkpiGenerator::class);
    $first = $generator->generate($andi);

    $andi->update(['nomor_skpi' => $first, 'nomor_skpi_generated_at' => now()]);

    $second = $generator->generate($dimas);

    expect($first)->toMatch('#^0001/SKPI/TI/[IVX]+/\d{4}$#');
    expect($second)->toMatch('#^0002/SKPI/TI/[IVX]+/\d{4}$#');
});

it('lets kaprodi terbitkan nomor for a disetujui_fakultas submission', function () {
    $kaprodi = User::where('email', 'kaprodi.ti@skpi.test')->firstOrFail();
    $pengajuan = penerbitanPengajuanFor('2020010003');
    $pengajuan->update(['status' => PengajuanStatus::DisetujuiFakultas]);

    $pejabat = PejabatPenandatangan::where('jabatan', 'Rektor')->firstOrFail();

    Livewire::actingAs($kaprodi)
        ->test(ListPengajuanSkpis::class)
        ->callTableAction('terbitkanNomor', $pengajuan, ['pejabat_penandatangan_id' => $pejabat->id])
        ->assertHasNoTableActionErrors();

    $pengajuan->refresh();
    expect($pengajuan->status)->toBe(PengajuanStatus::NomorTerbit);
    expect($pengajuan->nomor_skpi)->not->toBeNull();
    expect($pengajuan->pejabat_penandatangan_id)->toBe($pejabat->id);
    expect($pengajuan->nomor_skpi_generated_at)->not->toBeNull();
});

it('blocks admin_prodi from terbitkan nomor', function () {
    $adminProdi = User::where('email', 'adminprodi.ti@skpi.test')->firstOrFail();
    $pengajuan = penerbitanPengajuanFor('2020010003');
    $pengajuan->update(['status' => PengajuanStatus::DisetujuiFakultas]);

    Livewire::actingAs($adminProdi)
        ->test(ListPengajuanSkpis::class)
        ->assertTableActionHidden('terbitkanNomor', $pengajuan);
});

it('lets kaprodi terbitkan pdf for a nomor_terbit submission and publishes it', function () {
    Storage::fake('public');

    $kaprodi = User::where('email', 'kaprodi.ti@skpi.test')->firstOrFail();
    $pengajuan = penerbitanPengajuanFor('2021010001');
    $pejabat = PejabatPenandatangan::where('jabatan', 'Rektor')->firstOrFail();

    $pengajuan->update([
        'status' => PengajuanStatus::NomorTerbit,
        'nomor_skpi' => '0001/SKPI/TI/VIII/2026',
        'nomor_skpi_generated_at' => now(),
        'pejabat_penandatangan_id' => $pejabat->id,
    ]);

    Livewire::actingAs($kaprodi)
        ->test(ListPengajuanSkpis::class)
        ->callTableAction('terbitkanPdf', $pengajuan)
        ->assertHasNoTableActionErrors();

    $pengajuan->refresh();
    expect($pengajuan->status)->toBe(PengajuanStatus::Published);
    expect($pengajuan->published_at)->not->toBeNull();
    expect($pengajuan->pdf_path)->not->toBeNull();
    Storage::disk('public')->assertExists($pengajuan->pdf_path);
});

it('shows valid data for a published submission on the public verification page', function () {
    Storage::fake('public');

    $pengajuan = penerbitanPengajuanFor('2021010001');
    Storage::disk('public')->put('skpi-pdf/fake.pdf', 'fake-pdf-content');

    $pengajuan->update([
        'status' => PengajuanStatus::Published,
        'nomor_skpi' => '0001/SKPI/TI/VIII/2026',
        'nomor_skpi_generated_at' => now(),
        'published_at' => now(),
        'pdf_path' => 'skpi-pdf/fake.pdf',
    ]);

    $this->get(route('verification.show', $pengajuan->verification_token))
        ->assertSuccessful()
        ->assertSee('sah dan terverifikasi')
        ->assertSee('0001/SKPI/TI/VIII/2026')
        ->assertSee($pengajuan->mahasiswa->nama_lengkap);
});

it('shows a not-valid state for an unpublished or unknown token', function () {
    $draft = penerbitanPengajuanFor('2021010002');

    $this->get(route('verification.show', $draft->verification_token))
        ->assertSuccessful()
        ->assertSee('tidak ditemukan atau belum terbit');

    $this->get(route('verification.show', 'not-a-real-token'))
        ->assertSuccessful()
        ->assertSee('tidak ditemukan atau belum terbit');
});
