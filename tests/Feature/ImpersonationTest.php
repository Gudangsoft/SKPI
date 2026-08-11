<?php

use App\Filament\Resources\Users\Pages\ListUsers;
use App\Models\User;
use App\Support\Roles;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

it('lets super_admin login as an admin_prodi user', function () {
    $superAdmin = User::where('email', 'superadmin@skpi.test')->firstOrFail();
    $adminProdi = User::where('email', 'adminprodi.ti@skpi.test')->firstOrFail();

    Livewire::actingAs($superAdmin)
        ->test(ListUsers::class)
        ->callTableAction('loginAs', $adminProdi)
        ->assertHasNoTableActionErrors();

    expect(Auth::id())->toBe($adminProdi->id);
    expect(session('impersonator_id'))->toBe($superAdmin->id);
});

it('hides login as for the currently logged in user themself', function () {
    $superAdmin = User::where('email', 'superadmin@skpi.test')->firstOrFail();

    Livewire::actingAs($superAdmin)
        ->test(ListUsers::class)
        ->assertTableActionHidden('loginAs', $superAdmin);
});

it('hides login as for other super_admin users', function () {
    $superAdmin = User::where('email', 'superadmin@skpi.test')->firstOrFail();
    $otherSuperAdmin = User::factory()->create();
    $otherSuperAdmin->assignRole(Roles::SUPER_ADMIN);

    Livewire::actingAs($superAdmin)
        ->test(ListUsers::class)
        ->assertTableActionHidden('loginAs', $otherSuperAdmin);
});

it('restores the original admin session when stopping impersonation', function () {
    $superAdmin = User::where('email', 'superadmin@skpi.test')->firstOrFail();
    $kaprodi = User::where('email', 'kaprodi.ti@skpi.test')->firstOrFail();

    Livewire::actingAs($superAdmin)
        ->test(ListUsers::class)
        ->callTableAction('loginAs', $kaprodi)
        ->assertHasNoTableActionErrors();

    expect(Auth::id())->toBe($kaprodi->id);

    $this->get(route('impersonate.stop'))
        ->assertRedirect('/admin/users');

    expect(Auth::id())->toBe($superAdmin->id);
    expect(session()->has('impersonator_id'))->toBeFalse();
});

it('rejects stopping impersonation when no impersonation session exists', function () {
    $superAdmin = User::where('email', 'superadmin@skpi.test')->firstOrFail();

    $this->actingAs($superAdmin)
        ->get(route('impersonate.stop'))
        ->assertForbidden();
});
