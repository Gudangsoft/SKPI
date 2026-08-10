<?php

namespace App\Policies;

use App\Models\User;
use App\Support\Roles;

class FakultasPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(Roles::SUPER_ADMIN);
    }

    public function view(User $user): bool
    {
        return $user->hasRole(Roles::SUPER_ADMIN);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(Roles::SUPER_ADMIN);
    }

    public function update(User $user): bool
    {
        return $user->hasRole(Roles::SUPER_ADMIN);
    }

    public function delete(User $user): bool
    {
        return $user->hasRole(Roles::SUPER_ADMIN);
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasRole(Roles::SUPER_ADMIN);
    }
}
