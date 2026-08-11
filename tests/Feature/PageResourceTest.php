<?php

use App\Models\Page;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;

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
