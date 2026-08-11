<?php

use App\Enums\PengajuanStatus;
use App\Livewire\Pengajuan\RepeatableCategoryManager;
use App\Models\Mahasiswa;
use App\Models\Prestasi;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

it('lets a student add, save, and remove a prestasi item with a document upload', function () {
    Storage::fake('local');

    $mahasiswa = Mahasiswa::firstOrFail();
    $pengajuan = $mahasiswa->pengajuanSkpis()->create(['status' => PengajuanStatus::Draft]);

    $this->actingAs($mahasiswa->user);

    $component = Livewire::test(RepeatableCategoryManager::class, [
        'pengajuan' => $pengajuan,
        'kategori' => 'prestasi',
    ]);

    $component->call('addItem');

    $component->set('items.0.nama_prestasi', 'Juara 1 Lomba Coding Nasional')
        ->set('items.0.tingkat', 'nasional')
        ->set('items.0.penyelenggara', 'Kemendikbud')
        ->set('items.0.tahun', 2024)
        ->set('items.0.dokumen_bukti', UploadedFile::fake()->create('sertifikat.pdf', 100, 'application/pdf'))
        ->call('save')
        ->assertHasNoErrors();

    expect(Prestasi::where('pengajuan_skpi_id', $pengajuan->id)->count())->toBe(1);

    $prestasi = Prestasi::where('pengajuan_skpi_id', $pengajuan->id)->firstOrFail();
    expect($prestasi->nama_prestasi)->toBe('Juara 1 Lomba Coding Nasional');
    expect($prestasi->dokumen_bukti_path)->not->toBeNull();
    Storage::disk('local')->assertExists($prestasi->dokumen_bukti_path);

    $component->call('removeItem', 0)->call('save');

    expect(Prestasi::where('pengajuan_skpi_id', $pengajuan->id)->count())->toBe(0);
});

it('validates required fields before saving a prestasi item', function () {
    $mahasiswa = Mahasiswa::firstOrFail();
    $pengajuan = $mahasiswa->pengajuanSkpis()->create(['status' => PengajuanStatus::Draft]);

    $this->actingAs($mahasiswa->user);

    Livewire::test(RepeatableCategoryManager::class, [
        'pengajuan' => $pengajuan,
        'kategori' => 'prestasi',
    ])
        ->call('addItem')
        ->call('save')
        ->assertHasErrors(['items.0.nama_prestasi', 'items.0.tingkat', 'items.0.tahun']);
});

it('blocks a non owner from reaching the kategori page that mounts the component', function () {
    $mahasiswaA = Mahasiswa::first();
    $mahasiswaB = Mahasiswa::skip(1)->first();

    $pengajuan = $mahasiswaA->pengajuanSkpis()->create(['status' => PengajuanStatus::Draft]);

    $this->actingAs($mahasiswaB->user);

    $this->get("/pengajuan/{$pengajuan->id}/kategori/prestasi")->assertForbidden();
});
