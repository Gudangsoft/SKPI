<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\Page;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $landingNavbar = Menu::firstOrCreate(['slug' => 'landing-navbar'], ['name' => 'Navbar Beranda']);
        $landingFooter = Menu::firstOrCreate(['slug' => 'landing-footer'], ['name' => 'Footer Beranda']);
        $studentNavbar = Menu::firstOrCreate(['slug' => 'student-navbar'], ['name' => 'Navbar Portal Mahasiswa']);

        $pages = Page::whereIn('slug', ['tentang-skpi', 'faq', 'panduan-pengajuan'])->get()->keyBy('slug');

        foreach ([$landingNavbar, $landingFooter] as $menu) {
            $order = 0;

            foreach (['tentang-skpi', 'faq', 'panduan-pengajuan'] as $slug) {
                $page = $pages->get($slug);

                if (! $page) {
                    continue;
                }

                $menu->items()->firstOrCreate(
                    ['page_id' => $page->id, 'parent_id' => null],
                    [
                        'label' => $page->title,
                        'type' => 'page',
                        'sort_order' => $order++,
                    ],
                );
            }
        }

        $studentLinks = [
            ['label' => 'Dashboard', 'route_name' => 'dashboard'],
            ['label' => 'Data Mahasiswa', 'route_name' => 'mahasiswa.profil.edit'],
            ['label' => 'Pengajuan SKPI', 'route_name' => 'pengajuan.index'],
        ];

        foreach ($studentLinks as $order => $link) {
            $studentNavbar->items()->firstOrCreate(
                ['route_name' => $link['route_name'], 'parent_id' => null],
                [
                    'label' => $link['label'],
                    'type' => 'route',
                    'sort_order' => $order,
                ],
            );
        }
    }
}
