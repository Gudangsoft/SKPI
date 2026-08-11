<?php

use App\Filament\Pages\MenuBuilder;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(DatabaseSeeder::class);
});

it('lets super_admin view the menu builder page', function () {
    $superAdmin = User::where('email', 'superadmin@skpi.test')->firstOrFail();
    $this->actingAs($superAdmin);

    $this->get('/admin/menu-builder')->assertSuccessful();
});

it('blocks admin_prodi from the menu builder page', function () {
    $adminProdi = User::where('email', 'adminprodi.ti@skpi.test')->firstOrFail();
    $this->actingAs($adminProdi);

    $this->get('/admin/menu-builder')->assertForbidden();
});

it('lets super_admin add a custom url item to a menu', function () {
    $superAdmin = User::where('email', 'superadmin@skpi.test')->firstOrFail();
    $this->actingAs($superAdmin);

    $menu = Menu::where('slug', 'landing-navbar')->firstOrFail();

    Livewire::test(MenuBuilder::class)
        ->set('menuId', $menu->id)
        ->fillForm([
            'type' => 'url',
            'url' => 'https://example.test',
            'label' => 'Tautan Eksternal',
        ])
        ->call('addItem');

    expect(MenuItem::where('menu_id', $menu->id)->where('label', 'Tautan Eksternal')->exists())->toBeTrue();
});

it('persists a re-parented tree via updateTree', function () {
    $superAdmin = User::where('email', 'superadmin@skpi.test')->firstOrFail();
    $this->actingAs($superAdmin);

    $menu = Menu::where('slug', 'landing-navbar')->firstOrFail();
    $items = $menu->items()->orderBy('sort_order')->get();
    expect($items)->toHaveCount(3);

    [$first, $second, $third] = $items;

    // Re-parent $second under $first (this is the "drag to nest" the JS layer
    // would produce), leave $third as a sibling root.
    $tree = [
        [
            'id' => $first->id,
            'children' => [
                ['id' => $second->id, 'children' => []],
            ],
        ],
        ['id' => $third->id, 'children' => []],
    ];

    Livewire::test(MenuBuilder::class)
        ->set('menuId', $menu->id)
        ->call('updateTree', $tree);

    expect($second->refresh()->parent_id)->toBe($first->id);
    expect($third->refresh()->parent_id)->toBeNull();
});

it('rejects a tree deeper than 3 levels and leaves existing data untouched', function () {
    $superAdmin = User::where('email', 'superadmin@skpi.test')->firstOrFail();
    $this->actingAs($superAdmin);

    $menu = Menu::where('slug', 'landing-navbar')->firstOrFail();
    $items = $menu->items()->orderBy('sort_order')->get();
    [$first, $second, $third] = $items;

    // 4 levels deep: first (1) > second (2) > third (3) > synthetic id (4).
    // The synthetic id is never persisted to since validation short-circuits
    // before persistTree() runs — only the rejection itself is under test.
    $tree = [
        [
            'id' => $first->id,
            'children' => [
                [
                    'id' => $second->id,
                    'children' => [
                        [
                            'id' => $third->id,
                            'children' => [
                                ['id' => 999999, 'children' => []],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ];

    $component = Livewire::test(MenuBuilder::class)->set('menuId', $menu->id);
    $result = $component->instance()->updateTree($tree);

    expect($result)->toBeFalse();
    expect($third->refresh()->parent_id)->not->toBe($second->id);
});
