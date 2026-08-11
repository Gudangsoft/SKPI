<?php

use App\Filament\Pages\Settings as SettingsPage;
use App\Filament\Resources\HeroSlides\Pages\ManageHeroSlides;
use App\Models\HeroSlide;
use App\Models\Setting;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

it('lets super_admin view and create a hero slide', function () {
    Storage::fake('public');

    $superAdmin = User::where('email', 'superadmin@skpi.test')->firstOrFail();
    $this->actingAs($superAdmin);

    $this->get('/admin/hero-slides')->assertSuccessful();

    Livewire::test(ManageHeroSlides::class)
        ->callAction('create', data: [
            'image_path' => UploadedFile::fake()->image('slide.jpg'),
            'title' => 'Selamat Datang di SKPI',
            'subtitle' => 'Satu portal untuk semua pengajuan',
            'active' => true,
        ])
        ->assertHasNoActionErrors();

    expect(HeroSlide::where('title', 'Selamat Datang di SKPI')->exists())->toBeTrue();
});

it('blocks admin_prodi from the hero slide resource', function () {
    $adminProdi = User::where('email', 'adminprodi.ti@skpi.test')->firstOrFail();
    $this->actingAs($adminProdi);

    $this->get('/admin/hero-slides')->assertForbidden();
});

it('renders the active hero slide on the public homepage', function () {
    HeroSlide::create([
        'title' => 'Judul Slide Uji Coba',
        'subtitle' => 'Subjudul slide uji coba',
        'active' => true,
        'sort_order' => 0,
    ]);

    $this->get('/')
        ->assertSuccessful()
        ->assertSee('Judul Slide Uji Coba');
});

it('excludes inactive slides and falls back to the default hero when none are active', function () {
    HeroSlide::create([
        'title' => 'Slide Nonaktif',
        'active' => false,
        'sort_order' => 0,
    ]);

    $this->get('/')
        ->assertSuccessful()
        ->assertDontSee('Slide Nonaktif')
        ->assertSee('Ajukan SKPI Anda');
});

it('shows the footer informasi block only when contact info is set', function () {
    $this->get('/')->assertSuccessful()->assertDontSee('JL. Dr. Kasih No. 1, Jakarta Barat');

    Setting::current()->update([
        'contact_address' => 'JL. Dr. Kasih No. 1, Jakarta Barat',
        'contact_phone' => '+62 817 9980 610',
        'contact_email' => 'info@skpi.test',
    ]);

    $this->get('/')
        ->assertSuccessful()
        ->assertSee('JL. Dr. Kasih No. 1, Jakarta Barat')
        ->assertSee('+62 817 9980 610')
        ->assertSee('info@skpi.test');
});

it('lets super_admin save footer and contact settings', function () {
    $superAdmin = User::where('email', 'superadmin@skpi.test')->firstOrFail();
    $this->actingAs($superAdmin);

    Livewire::test(SettingsPage::class)
        ->fillForm([
            'contact_phone' => '+62 817 9980 610',
            'contact_email' => 'info@skpi.test',
            'footer_bg_type' => 'color',
            'footer_bg_color' => '#0f172a',
            'footer_text_color' => '#e2e8f0',
        ])
        ->call('save');

    $setting = Setting::current();
    expect($setting->contact_phone)->toBe('+62 817 9980 610');
    expect($setting->footer_bg_color)->toBe('#0f172a');
});
