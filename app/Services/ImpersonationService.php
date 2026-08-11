<?php

namespace App\Services;

use App\Models\User;
use App\Support\Roles;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ImpersonationService
{
    public function start(User $target): void
    {
        $admin = Auth::user();

        abort_unless($admin?->hasRole(Roles::SUPER_ADMIN), 403);
        abort_if($admin->is($target), 403, 'Tidak dapat login sebagai akun sendiri.');
        abort_if($target->hasRole(Roles::SUPER_ADMIN), 403, 'Tidak dapat login sebagai sesama Super Admin.');

        session(['impersonator_id' => $admin->id]);

        Auth::login($target);

        Log::info('Impersonation started', [
            'impersonator_id' => $admin->id,
            'impersonator_email' => $admin->email,
            'target_id' => $target->id,
            'target_email' => $target->email,
        ]);
    }

    public function stop(): void
    {
        $adminId = session('impersonator_id');

        abort_unless($adminId, 403);

        $admin = User::findOrFail($adminId);
        $target = Auth::user();

        session()->forget('impersonator_id');

        Auth::login($admin);

        Log::info('Impersonation stopped', [
            'impersonator_id' => $admin->id,
            'target_id' => $target?->id,
            'target_email' => $target?->email,
        ]);
    }
}
