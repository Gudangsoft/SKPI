<?php

use App\Models\Fakultas;
use App\Models\Mahasiswa;
use App\Models\ProgramStudi;
use App\Models\User;
use App\Support\Roles;
use Database\Seeders\DatabaseSeeder;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

it('allows super_admin to view master data resources', function () {
    $superAdmin = User::whereHas('roles', fn ($q) => $q->where('name', Roles::SUPER_ADMIN))->firstOrFail();

    $this->actingAs($superAdmin);

    $this->get('/admin')->assertSuccessful();
    $this->get('/admin/fakultas')->assertSuccessful();
    $this->get('/admin/program-studis')->assertSuccessful();
    $this->get('/admin/mahasiswas')->assertSuccessful();
    $this->get('/admin/mahasiswas/create')->assertSuccessful();
    $this->get('/admin/users')->assertSuccessful();
    $this->get('/admin/users/create')->assertSuccessful();
});

it('blocks mahasiswa role from accessing the admin panel', function () {
    $mahasiswaUser = Mahasiswa::firstOrFail()->user;

    $this->actingAs($mahasiswaUser);

    $this->get('/admin')->assertForbidden();
});

it('scopes admin_prodi to only their own program studi mahasiswa', function () {
    $adminProdi = User::where('email', 'adminprodi.ti@skpi.test')->firstOrFail();
    $otherProdiMahasiswa = Mahasiswa::whereHas('programStudi', fn ($q) => $q->where('kode_prodi', 'SI'))->firstOrFail();

    $this->actingAs($adminProdi);

    $this->get('/admin/mahasiswas')->assertSuccessful();
    // Out-of-scope records are excluded by the resource's query scoping entirely,
    // so they 404 rather than 403 — this avoids leaking that the record exists.
    $this->get("/admin/mahasiswas/{$otherProdiMahasiswa->id}/edit")->assertNotFound();
});

it('blocks non super_admin from the users resource', function () {
    $adminProdi = User::where('email', 'adminprodi.ti@skpi.test')->firstOrFail();

    $this->actingAs($adminProdi);

    $this->get('/admin/users')->assertForbidden();
});
