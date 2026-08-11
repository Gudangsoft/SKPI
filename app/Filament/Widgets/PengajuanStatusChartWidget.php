<?php

namespace App\Filament\Widgets;

use App\Enums\PengajuanStatus;
use App\Models\PengajuanSkpi;
use App\Support\Roles;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class PengajuanStatusChartWidget extends ChartWidget
{
    protected ?string $heading = 'Distribusi Status Pengajuan';

    protected function getType(): string
    {
        return 'pie';
    }

    protected function getData(): array
    {
        $user = Auth::user();

        $query = PengajuanSkpi::query()->where('status', '!=', PengajuanStatus::Draft);

        if (! ($user?->hasRole(Roles::SUPER_ADMIN) ?? false)) {
            $query->whereRelation('mahasiswa', 'program_studi_id', $user->program_studi_id);
        }

        $counts = $query->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $statuses = collect(PengajuanStatus::cases())->reject(fn (PengajuanStatus $status) => $status === PengajuanStatus::Draft);

        return [
            'datasets' => [
                [
                    'data' => $statuses->map(fn (PengajuanStatus $status) => $counts[$status->value] ?? 0)->values()->all(),
                    'backgroundColor' => $statuses->map(fn (PengajuanStatus $status) => $this->colorFor($status))->values()->all(),
                ],
            ],
            'labels' => $statuses->map(fn (PengajuanStatus $status) => $status->getLabel())->values()->all(),
        ];
    }

    protected function colorFor(PengajuanStatus $status): string
    {
        return match ($status->getColor()) {
            'gray' => '#9ca3af',
            'warning' => '#f59e0b',
            'danger' => '#ef4444',
            'info' => '#0ea5e9',
            'success' => '#10b981',
            default => '#9ca3af',
        };
    }
}
