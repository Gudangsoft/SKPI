<?php

use App\Enums\PengajuanStatus;
use App\Models\Mahasiswa;
use Database\Seeders\DatabaseSeeder;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

it('lets a mahasiswa create a draft pengajuan and fill data akademik', function () {
    $mahasiswa = Mahasiswa::firstOrFail();
    // The seeded demo mahasiswa already has a pengajuan (see PengajuanSkpiSeeder);
    // this test covers the from-scratch creation flow, so start with a clean slate.
    $mahasiswa->pengajuanSkpis()->delete();

    $this->actingAs($mahasiswa->user);

    $this->post('/pengajuan')->assertRedirect();

    $pengajuan = $mahasiswa->pengajuanSkpis()->firstOrFail();
    expect($pengajuan->status)->toBe(PengajuanStatus::Draft);

    $this->put("/pengajuan/{$pengajuan->id}/akademik", [
        'ipk' => 3.75,
        'judul_skripsi' => 'Sistem Informasi Akademik Berbasis Web',
        'tanggal_lulus' => '2025-08-01',
        'no_ijazah' => null,
        'predikat_kelulusan' => 'pujian',
    ])->assertRedirect(route('pengajuan.kategori', [$pengajuan, 'prestasi']));

    $pengajuan->refresh();
    expect($pengajuan->judul_skripsi)->toBe('Sistem Informasi Akademik Berbasis Web');
});

it('reuses an existing non-published pengajuan instead of creating duplicates', function () {
    $mahasiswa = Mahasiswa::firstOrFail();
    $this->actingAs($mahasiswa->user);

    $this->post('/pengajuan');
    $first = $mahasiswa->pengajuanSkpis()->firstOrFail();

    $this->post('/pengajuan')->assertRedirect(route('pengajuan.akademik', $first));

    expect($mahasiswa->pengajuanSkpis()->count())->toBe(1);
});

it('blocks submit until required akademik fields are filled', function () {
    $mahasiswa = Mahasiswa::firstOrFail();
    $this->actingAs($mahasiswa->user);

    $pengajuan = $mahasiswa->pengajuanSkpis()->create(['status' => PengajuanStatus::Draft]);

    $this->post("/pengajuan/{$pengajuan->id}/submit")
        ->assertRedirect(route('pengajuan.akademik', $pengajuan));

    expect($pengajuan->refresh()->status)->toBe(PengajuanStatus::Draft);
});

it('submits a complete draft and transitions to diajukan', function () {
    $mahasiswa = Mahasiswa::firstOrFail();
    $this->actingAs($mahasiswa->user);

    $pengajuan = $mahasiswa->pengajuanSkpis()->create([
        'status' => PengajuanStatus::Draft,
        'ipk' => 3.5,
        'judul_skripsi' => 'Judul Skripsi',
        'tanggal_lulus' => '2025-01-01',
        'predikat_kelulusan' => 'memuaskan',
    ]);

    $this->post("/pengajuan/{$pengajuan->id}/submit")
        ->assertRedirect(route('pengajuan.show', $pengajuan));

    $pengajuan->refresh();
    expect($pengajuan->status)->toBe(PengajuanStatus::Diajukan);
    expect($pengajuan->diajukan_at)->not->toBeNull();
    // History logging starts at the first real transition (draft -> diajukan), not at
    // row creation: Eloquent's insert path never calls syncChanges(), so wasChanged()
    // in the observer's saved() hook is never true for a plain create().
    expect($pengajuan->statusHistories()->count())->toBe(1);
});

it('prevents a student from viewing or editing another students pengajuan', function () {
    $mahasiswaA = Mahasiswa::first();
    $mahasiswaB = Mahasiswa::skip(1)->first();

    $pengajuan = $mahasiswaA->pengajuanSkpis()->create(['status' => PengajuanStatus::Draft]);

    $this->actingAs($mahasiswaB->user);

    $this->get("/pengajuan/{$pengajuan->id}")->assertForbidden();
    $this->get("/pengajuan/{$pengajuan->id}/akademik")->assertForbidden();
});

it('prevents editing once a pengajuan is no longer in draft or revisi', function () {
    $mahasiswa = Mahasiswa::firstOrFail();
    $this->actingAs($mahasiswa->user);

    $pengajuan = $mahasiswa->pengajuanSkpis()->create(['status' => PengajuanStatus::Diajukan]);

    $this->put("/pengajuan/{$pengajuan->id}/akademik", [
        'ipk' => 3.9,
        'judul_skripsi' => 'Judul Baru',
        'tanggal_lulus' => '2025-01-01',
        'predikat_kelulusan' => 'memuaskan',
    ])->assertForbidden();
});
