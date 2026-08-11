<?php

namespace App\Http\Controllers;

use App\Services\ImpersonationService;
use Illuminate\Http\RedirectResponse;

class ImpersonationController extends Controller
{
    public function stop(ImpersonationService $impersonation): RedirectResponse
    {
        $impersonation->stop();

        return redirect('/admin/users');
    }
}
