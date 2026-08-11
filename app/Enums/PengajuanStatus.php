<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum PengajuanStatus: string implements HasColor, HasIcon, HasLabel
{
    case Draft = 'draft';
    case Diajukan = 'diajukan';
    case Revisi = 'revisi';
    case DisetujuiProdi = 'disetujui_prodi';
    case DisetujuiFakultas = 'disetujui_fakultas';
    case NomorTerbit = 'nomor_terbit';
    case Published = 'published';

    public function getLabel(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Diajukan => 'Menunggu Verifikasi Prodi',
            self::Revisi => 'Perlu Revisi',
            self::DisetujuiProdi => 'Menunggu Validasi Kaprodi',
            self::DisetujuiFakultas => 'Disetujui Kaprodi',
            self::NomorTerbit => 'Nomor SKPI Terbit',
            self::Published => 'Terbit',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Draft => 'gray',
            self::Diajukan, self::DisetujuiProdi => 'warning',
            self::Revisi => 'danger',
            self::DisetujuiFakultas, self::NomorTerbit => 'info',
            self::Published => 'success',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Draft => 'heroicon-o-pencil',
            self::Diajukan, self::DisetujuiProdi => 'heroicon-o-clock',
            self::Revisi => 'heroicon-o-exclamation-triangle',
            self::DisetujuiFakultas, self::NomorTerbit => 'heroicon-o-check',
            self::Published => 'heroicon-o-check-badge',
        };
    }

    /**
     * Statuses in which the student may still freely edit the submission.
     */
    public static function editableByMahasiswa(): array
    {
        return [self::Draft, self::Revisi];
    }
}
