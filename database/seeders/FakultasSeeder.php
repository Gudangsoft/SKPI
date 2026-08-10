<?php

namespace Database\Seeders;

use App\Models\Fakultas;
use Illuminate\Database\Seeder;

class FakultasSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['kode_fakultas' => 'FT', 'nama_fakultas' => 'Fakultas Teknik'],
            ['kode_fakultas' => 'FEB', 'nama_fakultas' => 'Fakultas Ekonomi dan Bisnis'],
            ['kode_fakultas' => 'FIKOM', 'nama_fakultas' => 'Fakultas Ilmu Komputer'],
        ];

        foreach ($items as $item) {
            Fakultas::firstOrCreate(['kode_fakultas' => $item['kode_fakultas']], $item);
        }
    }
}
