<?php

namespace App\Policies;

use App\Models\Mahasiswa;
use App\Models\User;
use App\Support\Roles;

class MahasiswaPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole([Roles::SUPER_ADMIN, Roles::ADMIN_PRODI, Roles::KAPRODI]);
    }

    public function view(User $user, Mahasiswa $mahasiswa): bool
    {
        if ($user->hasRole(Roles::SUPER_ADMIN)) {
            return true;
        }

        return $user->hasAnyRole([Roles::ADMIN_PRODI, Roles::KAPRODI])
            && $user->program_studi_id === $mahasiswa->program_studi_id;
    }

    public function create(User $user): bool
    {
        return $user->hasRole(Roles::SUPER_ADMIN);
    }

    public function update(User $user, Mahasiswa $mahasiswa): bool
    {
        return $user->hasRole(Roles::SUPER_ADMIN);
    }

    public function delete(User $user, Mahasiswa $mahasiswa): bool
    {
        return $user->hasRole(Roles::SUPER_ADMIN);
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasRole(Roles::SUPER_ADMIN);
    }
}
