<?php

use App\Filament\Pages\Auth\EditProfile;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

it('lets a staff member view the admin profile page', function () {
    $superAdmin = User::where('email', 'superadmin@skpi.test')->firstOrFail();
    $this->actingAs($superAdmin);

    $this->get('/admin/profile')->assertSuccessful();
});

it('lets a staff member update their name and upload a cropped avatar', function () {
    Storage::fake('public');

    $superAdmin = User::where('email', 'superadmin@skpi.test')->firstOrFail();
    $this->actingAs($superAdmin);

    Livewire::test(EditProfile::class)
        ->fillForm([
            'name' => 'Super Admin Baru',
            'avatar_path' => UploadedFile::fake()->image('avatar.jpg'),
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $superAdmin->refresh();

    expect($superAdmin->name)->toBe('Super Admin Baru');
    expect($superAdmin->avatar_path)->not->toBeNull();
    Storage::disk('public')->assertExists($superAdmin->avatar_path);
    expect($superAdmin->getFilamentAvatarUrl())->toContain($superAdmin->avatar_path);
});
