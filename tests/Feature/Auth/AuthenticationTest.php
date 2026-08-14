<?php

use App\Models\User;
use App\Support\MathCaptcha;
use App\Support\Roles;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::firstOrCreate(['name' => Roles::MAHASISWA, 'guard_name' => 'web']);
});

test('login screen can be rendered', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
    $response->assertSee('Verifikasi');
});

test('users can authenticate using the login screen', function () {
    $user = User::factory()->create();
    $user->assignRole(Roles::MAHASISWA);

    [$a, $b] = MathCaptcha::generate('student_login');

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
        'captcha' => $a + $b,
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();
    $user->assignRole(Roles::MAHASISWA);

    [$a, $b] = MathCaptcha::generate('student_login');

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
        'captcha' => $a + $b,
    ]);

    $this->assertGuest();
});

test('users can not authenticate with a wrong captcha answer', function () {
    $user = User::factory()->create();
    $user->assignRole(Roles::MAHASISWA);

    [$a, $b] = MathCaptcha::generate('student_login');

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
        'captcha' => $a + $b + 1,
    ]);

    $this->assertGuest();
    $response->assertSessionHasErrors('captcha');
});

test('users can logout', function () {
    $user = User::factory()->create();
    $user->assignRole(Roles::MAHASISWA);

    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect('/');
});
