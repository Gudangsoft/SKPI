<?php

namespace App\Http\Controllers;

use App\Enums\PengajuanStatus;
use App\Models\PengajuanSkpi;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VerificationController extends Controller
{
    public function index(Request $request): View
    {
        $query = $request->query('q');
        $result = null;

        if (filled($query)) {
            $result = $this->findPublished($query);
        }

        return view('verification.index', [
            'result' => $result,
            'query' => $query,
            'searched' => filled($query),
        ]);
    }

    public function show(string $token): View
    {
        return view('verification.show', [
            'result' => $this->findPublished($token),
        ]);
    }

    protected function findPublished(string $query): ?PengajuanSkpi
    {
        return PengajuanSkpi::query()
            ->where('status', PengajuanStatus::Published)
            ->where(fn ($q) => $q->where('nomor_skpi', $query)->orWhere('verification_token', $query))
            ->with(['mahasiswa.programStudi.fakultas'])
            ->first();
    }
}
