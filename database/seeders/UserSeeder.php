<?php

namespace Database\Seeders;

use App\Models\ProgramStudi;
use App\Models\User;
use App\Support\Roles;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@skpi.test'],
            ['name' => 'Super Admin', 'password' => 'password']
        );
        $superAdmin->syncRoles([Roles::SUPER_ADMIN]);

        $ti = ProgramStudi::where('kode_prodi', 'TI')->first();

        if ($ti) {
            $adminProdi = User::firstOrCreate(
                ['email' => 'adminprodi.ti@skpi.test'],
                ['name' => 'Admin Prodi TI', 'password' => 'password', 'program_studi_id' => $ti->id]
            );
            $adminProdi->update(['program_studi_id' => $ti->id]);
            $adminProdi->syncRoles([Roles::ADMIN_PRODI]);

            $kaprodi = User::firstOrCreate(
                ['email' => 'kaprodi.ti@skpi.test'],
                ['name' => 'Kaprodi TI', 'password' => 'password', 'program_studi_id' => $ti->id]
            );
            $kaprodi->update(['program_studi_id' => $ti->id]);
            $kaprodi->syncRoles([Roles::KAPRODI]);
        }
    }
}
