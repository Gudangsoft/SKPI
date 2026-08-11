<?php

use App\Filament\Pages\Settings;
use App\Models\Setting;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

it('lets super_admin view and update brand settings', function () {
    $superAdmin = User::where('email', 'superadmin@skpi.test')->firstOrFail();
    $this->actingAs($superAdmin);

    $this->get('/admin/settings')->assertSuccessful();

    Livewire::test(Settings::class)
        ->fillForm([
            'app_name' => 'Kampus Baru',
            'tagline' => 'Tagline Baru',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $setting = Setting::current();
    expect($setting->app_name)->toBe('Kampus Baru');
    expect($setting->tagline)->toBe('Tagline Baru');
});

it('requires an app name when saving brand settings', function () {
    $superAdmin = User::where('email', 'superadmin@skpi.test')->firstOrFail();
    $this->actingAs($superAdmin);

    Livewire::test(Settings::class)
        ->fillForm(['app_name' => ''])
        ->call('save')
        ->assertHasFormErrors(['app_name' => 'required']);
});

it('blocks admin_prodi from the brand settings page', function () {
    $adminProdi = User::where('email', 'adminprodi.ti@skpi.test')->firstOrFail();
    $this->actingAs($adminProdi);

    $this->get('/admin/settings')->assertForbidden();
});
