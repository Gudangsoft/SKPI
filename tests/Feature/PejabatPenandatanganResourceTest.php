<?php

use App\Filament\Resources\PejabatPenandatangans\Pages\ManagePejabatPenandatangans;
use App\Models\PejabatPenandatangan;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

it('lets super_admin view and create a pejabat penandatangan', function () {
    $superAdmin = User::where('email', 'superadmin@skpi.test')->firstOrFail();
    $this->actingAs($superAdmin);

    $this->get('/admin/pejabat-penandatangans')->assertSuccessful();

    Livewire::test(ManagePejabatPenandatangans::class)
        ->callAction('create', data: [
            'nama' => 'Dr. Uji Coba, M.Kom.',
            'jabatan' => 'Wakil Rektor',
        ])
        ->assertHasNoActionErrors();

    expect(PejabatPenandatangan::where('nama', 'Dr. Uji Coba, M.Kom.')->exists())->toBeTrue();
});

it('blocks admin_prodi from the pejabat penandatangan resource', function () {
    $adminProdi = User::where('email', 'adminprodi.ti@skpi.test')->firstOrFail();
    $this->actingAs($adminProdi);

    $this->get('/admin/pejabat-penandatangans')->assertForbidden();
});
