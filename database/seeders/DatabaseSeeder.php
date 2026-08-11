<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            SettingSeeder::class,
            RoleSeeder::class,
            FakultasSeeder::class,
            ProgramStudiSeeder::class,
            PejabatPenandatanganSeeder::class,
            UserSeeder::class,
            MahasiswaSeeder::class,
            PengajuanSkpiSeeder::class,
            PageSeeder::class,
            MenuSeeder::class,
        ]);
    }
}
