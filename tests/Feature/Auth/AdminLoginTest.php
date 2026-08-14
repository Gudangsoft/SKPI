<?php

use App\Filament\Pages\Auth\Login;
use App\Models\User;
use App\Support\MathCaptcha;
use Database\Seeders\DatabaseSeeder;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

it('renders the admin login screen with a captcha question', function () {
    $this->get('/admin/login')
        ->assertSuccessful()
        ->assertSee('Verifikasi');
});

it('lets a staff member authenticate with the correct captcha answer', function () {
    $superAdmin = User::where('email', 'superadmin@skpi.test')->firstOrFail();

    $component = Livewire::test(Login::class);
    [$a, $b] = $component->get('captchaNumbers');

    $component
        ->fillForm([
            'email' => $superAdmin->email,
            'password' => 'password',
            'captcha' => $a + $b,
        ])
        ->call('authenticate')
        ->assertHasNoFormErrors();

    $this->assertAuthenticatedAs($superAdmin);
});

it('blocks a staff member with the wrong captcha answer', function () {
    $superAdmin = User::where('email', 'superadmin@skpi.test')->firstOrFail();

    $component = Livewire::test(Login::class);
    [$a, $b] = $component->get('captchaNumbers');

    $component
        ->fillForm([
            'email' => $superAdmin->email,
            'password' => 'password',
            'captcha' => $a + $b + 1,
        ])
        ->call('authenticate')
        ->assertHasFormErrors(['captcha']);

    $this->assertGuest();
});

it('regenerates the captcha numbers after a failed attempt', function () {
    $superAdmin = User::where('email', 'superadmin@skpi.test')->firstOrFail();

    $component = Livewire::test(Login::class);
    [$a, $b] = $component->get('captchaNumbers');

    $component
        ->fillForm([
            'email' => $superAdmin->email,
            'password' => 'password',
            'captcha' => $a + $b + 1,
        ])
        ->call('authenticate');

    $newNumbers = $component->get('captchaNumbers');

    // The stale session answer for the old numbers must no longer validate.
    expect(MathCaptcha::check('admin_login', $a + $b))->toBeFalse();
    expect($newNumbers)->not->toBeNull();
});
