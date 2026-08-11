<?php

use App\Filament\Resources\Pages\Pages\CreatePage;
use App\Models\Page;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

it('lets super_admin view, create, and edit pages', function () {
    $superAdmin = User::where('email', 'superadmin@skpi.test')->firstOrFail();
    $this->actingAs($superAdmin);

    $this->get('/admin/pages')->assertSuccessful();
    $this->get('/admin/pages/create')->assertSuccessful();

    $page = Page::firstOrFail();
    $this->get("/admin/pages/{$page->id}/edit")->assertSuccessful();
});

it('blocks admin_prodi from the pages resource', function () {
    $adminProdi = User::where('email', 'adminprodi.ti@skpi.test')->firstOrFail();
    $this->actingAs($adminProdi);

    $this->get('/admin/pages')->assertForbidden();
});

it('renders a published page at its public url', function () {
    $page = Page::create([
        'title' => 'Halaman Uji Coba',
        'slug' => 'halaman-uji-coba',
        'content' => '<p>Konten uji coba.</p>',
        'published_at' => now()->subDay(),
    ]);

    $this->get("/halaman/{$page->slug}")
        ->assertSuccessful()
        ->assertSee('Halaman Uji Coba')
        ->assertSee('Konten uji coba.', false);
});

it('404s for a draft page', function () {
    $page = Page::create([
        'title' => 'Halaman Draf',
        'slug' => 'halaman-draf',
        'published_at' => null,
    ]);

    $this->get("/halaman/{$page->slug}")->assertNotFound();
});

it('404s for an unknown page slug', function () {
    $this->get('/halaman/tidak-ada')->assertNotFound();
});

it('lets super_admin create a page with a featured image', function () {
    Storage::fake('public');

    $superAdmin = User::where('email', 'superadmin@skpi.test')->firstOrFail();
    $this->actingAs($superAdmin);

    Livewire::test(CreatePage::class)
        ->fillForm([
            'title' => 'Halaman Dengan Gambar',
            'slug' => 'halaman-dengan-gambar',
            'featured_image_path' => UploadedFile::fake()->image('cover.jpg'),
            'content' => '<p>Konten dengan gambar.</p>',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $page = Page::where('slug', 'halaman-dengan-gambar')->firstOrFail();
    expect($page->featured_image_path)->not->toBeNull();
    Storage::disk('public')->assertExists($page->featured_image_path);
});

it('shows the featured image and og:image meta tag on the public page', function () {
    Storage::fake('public');
    Storage::disk('public')->put('pages/cover.jpg', 'fake-image-content');

    $page = Page::create([
        'title' => 'Halaman Bergambar',
        'slug' => 'halaman-bergambar',
        'content' => '<p>Konten.</p>',
        'featured_image_path' => 'pages/cover.jpg',
        'published_at' => now()->subDay(),
    ]);

    $this->get("/halaman/{$page->slug}")
        ->assertSuccessful()
        ->assertSee($page->featuredImageUrl(), false)
        ->assertSee('og:image', false);
});
