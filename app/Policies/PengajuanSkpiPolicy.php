<?php

namespace App\Policies;

use App\Models\PengajuanSkpi;
use App\Models\User;
use App\Support\Roles;

class PengajuanSkpiPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole([Roles::MAHASISWA, Roles::SUPER_ADMIN, Roles::ADMIN_PRODI, Roles::KAPRODI]);
    }

    public function view(User $user, PengajuanSkpi $pengajuan): bool
    {
        if ($this->isOwner($user, $pengajuan)) {
            return true;
        }

        if ($user->hasRole(Roles::SUPER_ADMIN)) {
            return true;
        }

        if ($user->hasAnyRole([Roles::ADMIN_PRODI, Roles::KAPRODI])) {
            return $user->program_studi_id === $pengajuan->mahasiswa->program_studi_id;
        }

        return false;
    }

    public function update(User $user, PengajuanSkpi $pengajuan): bool
    {
        return $this->isOwner($user, $pengajuan) && $pengajuan->isEditableByMahasiswa();
    }

    protected function isOwner(User $user, PengajuanSkpi $pengajuan): bool
    {
        return $user->mahasiswa && $user->mahasiswa->id === $pengajuan->mahasiswa_id;
    }
}
