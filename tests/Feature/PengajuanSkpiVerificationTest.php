<?php

use App\Enums\PengajuanStatus;
use App\Filament\Resources\PengajuanSkpis\Pages\ListPengajuanSkpis;
use App\Filament\Resources\PengajuanSkpis\Pages\ViewPengajuanSkpi;
use App\Models\Mahasiswa;
use App\Models\PengajuanSkpi;
use App\Models\User;
use App\Support\Roles;
use Database\Seeders\DatabaseSeeder;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

function pengajuanFor(string $nim): PengajuanSkpi
{
    return Mahasiswa::where('nim', $nim)->firstOrFail()->pengajuanSkpis()->firstOrFail();
}

it('lets admin_prodi verifikasi a diajukan submission in their own prodi', function () {
    $adminProdi = User::where('email', 'adminprodi.ti@skpi.test')->firstOrFail();
    $andi = pengajuanFor('2021010001'); // seeded Diajukan, prodi TI

    Livewire::actingAs($adminProdi)
        ->test(ListPengajuanSkpis::class)
        ->callTableAction('verifikasi', $andi)
        ->assertHasNoTableActionErrors();

    $andi->refresh();
    expect($andi->status)->toBe(PengajuanStatus::DisetujuiProdi);
    expect($andi->diverifikasi_prodi_by)->toBe($adminProdi->id);
    expect($andi->diverifikasi_prodi_at)->not->toBeNull();

    $history = $andi->statusHistories->first();
    expect($history->status_from)->toBe(PengajuanStatus::Diajukan);
    expect($history->status_to)->toBe(PengajuanStatus::DisetujuiProdi);
    expect($history->changed_by)->toBe($adminProdi->id);
});

it('lets admin_prodi send a diajukan submission back for revisi with a note', function () {
    $adminProdi = User::where('email', 'adminprodi.ti@skpi.test')->firstOrFail();
    $andi = pengajuanFor('2021010001');

    Livewire::actingAs($adminProdi)
        ->test(ListPengajuanSkpis::class)
        ->callTableAction('mintaRevisi', $andi, ['catatan_revisi' => 'Lengkapi data pendukung.'])
        ->assertHasNoTableActionErrors();

    $andi->refresh();
    expect($andi->status)->toBe(PengajuanStatus::Revisi);
    expect($andi->catatan_revisi)->toBe('Lengkapi data pendukung.');
    expect($andi->statusHistories->first()->catatan)->toBe('Lengkapi data pendukung.');
});

it('requires a catatan when requesting revisi', function () {
    $adminProdi = User::where('email', 'adminprodi.ti@skpi.test')->firstOrFail();
    $andi = pengajuanFor('2021010001');

    Livewire::actingAs($adminProdi)
        ->test(ListPengajuanSkpis::class)
        ->callTableAction('mintaRevisi', $andi, ['catatan_revisi' => ''])
        ->assertHasTableActionErrors(['catatan_revisi']);

    expect($andi->refresh()->status)->toBe(PengajuanStatus::Diajukan);
});

it('lets kaprodi setujui a disetujui_prodi submission in their own prodi', function () {
    $kaprodi = User::where('email', 'kaprodi.ti@skpi.test')->firstOrFail();
    $dimas = pengajuanFor('2020010003'); // seeded DisetujuiProdi, prodi TI

    Livewire::actingAs($kaprodi)
        ->test(ListPengajuanSkpis::class)
        ->callTableAction('setujui', $dimas)
        ->assertHasNoTableActionErrors();

    $dimas->refresh();
    expect($dimas->status)->toBe(PengajuanStatus::DisetujuiFakultas);
    expect($dimas->disetujui_fakultas_by)->toBe($kaprodi->id);
    expect($dimas->disetujui_fakultas_at)->not->toBeNull();
});

it('lets kaprodi send a disetujui_prodi submission back for revisi', function () {
    $kaprodi = User::where('email', 'kaprodi.ti@skpi.test')->firstOrFail();
    $dimas = pengajuanFor('2020010003');

    Livewire::actingAs($kaprodi)
        ->test(ListPengajuanSkpis::class)
        ->callTableAction('mintaRevisi', $dimas, ['catatan_revisi' => 'Perbaiki data sertifikasi.'])
        ->assertHasNoTableActionErrors();

    expect($dimas->refresh()->status)->toBe(PengajuanStatus::Revisi);
});

it('hides actions that do not belong to the viewing role or current status', function () {
    $adminProdi = User::where('email', 'adminprodi.ti@skpi.test')->firstOrFail();
    $kaprodi = User::where('email', 'kaprodi.ti@skpi.test')->firstOrFail();
    $andi = pengajuanFor('2021010001'); // Diajukan
    $dimas = pengajuanFor('2020010003'); // DisetujuiProdi

    Livewire::actingAs($adminProdi)
        ->test(ListPengajuanSkpis::class)
        ->assertTableActionHidden('setujui', $dimas);

    Livewire::actingAs($kaprodi)
        ->test(ListPengajuanSkpis::class)
        ->assertTableActionHidden('verifikasi', $andi)
        ->assertTableActionHidden('setujui', $andi);
});

it('excludes draft submissions from the staff queue for same-prodi staff', function () {
    $adminProdi = User::where('email', 'adminprodi.ti@skpi.test')->firstOrFail();
    $bunga = pengajuanFor('2021010002'); // seeded Draft, prodi TI

    $this->actingAs($adminProdi);
    $this->get("/admin/pengajuan-skpis/{$bunga->id}")->assertNotFound();
});

it('excludes draft submissions from the staff queue for super_admin', function () {
    $superAdmin = User::whereHas('roles', fn ($q) => $q->where('name', Roles::SUPER_ADMIN))->firstOrFail();
    $bunga = pengajuanFor('2021010002'); // seeded Draft, prodi TI

    $this->actingAs($superAdmin);
    $this->get("/admin/pengajuan-skpis/{$bunga->id}")->assertNotFound();
});

it('scopes admin_prodi and kaprodi to their own program studi', function () {
    $adminProdi = User::where('email', 'adminprodi.ti@skpi.test')->firstOrFail();
    $citra = pengajuanFor('2022020001'); // seeded Revisi, prodi SI

    $this->actingAs($adminProdi);
    $this->get("/admin/pengajuan-skpis/{$citra->id}")->assertNotFound();

    Livewire::actingAs($adminProdi)
        ->test(ListPengajuanSkpis::class)
        ->assertCanNotSeeTableRecords([$citra]);
});

it('lets super_admin act on any prodi regardless of scope', function () {
    $superAdmin = User::whereHas('roles', fn ($q) => $q->where('name', Roles::SUPER_ADMIN))->firstOrFail();
    $citra = pengajuanFor('2022020001'); // prodi SI
    $citra->update(['status' => PengajuanStatus::Diajukan]);

    Livewire::actingAs($superAdmin)
        ->test(ListPengajuanSkpis::class)
        ->callTableAction('verifikasi', $citra)
        ->assertHasNoTableActionErrors();

    expect($citra->refresh()->status)->toBe(PengajuanStatus::DisetujuiProdi);
});

it('exposes the same workflow actions as header actions on the view page', function () {
    $adminProdi = User::where('email', 'adminprodi.ti@skpi.test')->firstOrFail();
    $andi = pengajuanFor('2021010001');

    Livewire::actingAs($adminProdi)
        ->test(ViewPengajuanSkpi::class, ['record' => $andi->getKey()])
        ->callAction('verifikasi')
        ->assertHasNoActionErrors();

    expect($andi->refresh()->status)->toBe(PengajuanStatus::DisetujuiProdi);
});
