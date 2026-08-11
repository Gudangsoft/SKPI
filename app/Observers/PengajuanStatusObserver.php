<?php

namespace App\Observers;

use App\Enums\PengajuanStatus;
use App\Models\PengajuanSkpi;
use Illuminate\Support\Facades\Auth;

class PengajuanStatusObserver
{
    /**
     * Stamp the relevant *_by / *_at columns before the row is persisted,
     * so they land in the same UPDATE query as the status change.
     */
    public function saving(PengajuanSkpi $pengajuan): void
    {
        if (! $pengajuan->isDirty('status')) {
            return;
        }

        match ($pengajuan->status) {
            PengajuanStatus::Diajukan => $pengajuan->diajukan_at = now(),
            PengajuanStatus::DisetujuiProdi => [
                $pengajuan->diverifikasi_prodi_by = Auth::id(),
                $pengajuan->diverifikasi_prodi_at = now(),
            ],
            PengajuanStatus::DisetujuiFakultas => [
                $pengajuan->disetujui_fakultas_by = Auth::id(),
                $pengajuan->disetujui_fakultas_at = now(),
            ],
            PengajuanStatus::Published => $pengajuan->published_at = now(),
            default => null,
        };
    }

    /**
     * Log the transition once the row is safely persisted. `getOriginal()` still
     * reflects the pre-save value here — Eloquent only calls syncOriginal() after
     * the "saved" event fires.
     */
    public function saved(PengajuanSkpi $pengajuan): void
    {
        if (! $pengajuan->wasChanged('status')) {
            return;
        }

        $from = $pengajuan->getOriginal('status');
        $to = $pengajuan->status;

        $pengajuan->statusHistories()->create([
            'status_from' => $from?->value,
            'status_to' => $to->value,
            'changed_by' => Auth::id(),
            'catatan' => $to === PengajuanStatus::Revisi ? $pengajuan->catatan_revisi : null,
        ]);
    }
}
