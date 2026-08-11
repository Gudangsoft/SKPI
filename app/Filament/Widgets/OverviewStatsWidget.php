<?php

namespace App\Filament\Widgets;

use App\Enums\PengajuanStatus;
use App\Models\Mahasiswa;
use App\Models\PengajuanSkpi;
use App\Models\ProgramStudi;
use App\Support\Roles;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class OverviewStatsWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $user = Auth::user();
        $isSuperAdmin = $user?->hasRole(Roles::SUPER_ADMIN) ?? false;

        $mahasiswaQuery = Mahasiswa::query();
        $pengajuanQuery = PengajuanSkpi::query();

        if (! $isSuperAdmin) {
            $mahasiswaQuery->where('program_studi_id', $user->program_studi_id);
            $pengajuanQuery->whereRelation('mahasiswa', 'program_studi_id', $user->program_studi_id);
        }

        $totalMahasiswa = (clone $mahasiswaQuery)->count();
        $menungguVerifikasi = (clone $pengajuanQuery)->where('status', PengajuanStatus::Diajukan)->count();
        $disetujuiTerbit = (clone $pengajuanQuery)->whereIn('status', [
            PengajuanStatus::DisetujuiFakultas,
            PengajuanStatus::NomorTerbit,
            PengajuanStatus::Published,
        ])->count();

        return [
            Stat::make('Total Mahasiswa', $totalMahasiswa)
                ->icon('heroicon-o-user-group')
                ->color('gray'),

            $isSuperAdmin
                ? Stat::make('Total Program Studi', ProgramStudi::query()->count())
                    ->icon('heroicon-o-building-library')
                    ->color('gray')
                : Stat::make('Menunggu Validasi Kaprodi', (clone $pengajuanQuery)->where('status', PengajuanStatus::DisetujuiProdi)->count())
                    ->icon('heroicon-o-check-badge')
                    ->color('info'),

            Stat::make('Menunggu Verifikasi Prodi', $menungguVerifikasi)
                ->icon('heroicon-o-clock')
                ->color('warning'),

            Stat::make('Disetujui / Terbit', $disetujuiTerbit)
                ->icon('heroicon-o-check-circle')
                ->color('success'),
        ];
    }
}
