<?php

use App\Models\Mahasiswa;
use App\Support\Roles;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

it('allows a mahasiswa to log in and view the dashboard and profil', function () {
    $mahasiswa = Mahasiswa::firstOrFail();

    $response = $this->post('/login', [
        'email' => $mahasiswa->user->email,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('dashboard'));
    $this->assertAuthenticatedAs($mahasiswa->user);

    $this->get('/dashboard')->assertSuccessful()->assertSee($mahasiswa->nim);
    $this->get('/mahasiswa/profil')->assertSuccessful()->assertSee($mahasiswa->nim);
});

it('lets a mahasiswa update their own biodata', function () {
    Storage::fake('local');

    $mahasiswa = Mahasiswa::firstOrFail();

    $this->actingAs($mahasiswa->user);

    $this->put('/mahasiswa/profil', [
        'tempat_lahir' => 'Bandung',
        'tanggal_lahir' => '2000-05-10',
        'jenis_kelamin' => 'L',
        'no_hp' => '081200000000',
        'alamat' => 'Jl. Merdeka No. 10',
        'foto' => UploadedFile::fake()->image('foto.jpg'),
    ])->assertRedirect(route('mahasiswa.profil.edit'));

    $mahasiswa->refresh();

    expect($mahasiswa->tempat_lahir)->toBe('Bandung');
    expect($mahasiswa->foto_path)->not->toBeNull();
});

it('rejects staff credentials on the student login form', function () {
    $adminProdi = \App\Models\User::where('email', 'adminprodi.ti@skpi.test')->firstOrFail();

    $response = $this->post('/login', [
        'email' => $adminProdi->email,
        'password' => 'password',
    ]);

    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});

it('blocks staff roles from student routes', function () {
    $adminProdi = \App\Models\User::where('email', 'adminprodi.ti@skpi.test')->firstOrFail();

    $this->actingAs($adminProdi);

    $this->get('/dashboard')->assertForbidden();
});
