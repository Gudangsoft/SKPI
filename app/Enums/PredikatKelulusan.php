<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum PredikatKelulusan: string implements HasLabel
{
    case Memuaskan = 'memuaskan';
    case SangatMemuaskan = 'sangat_memuaskan';
    case Pujian = 'pujian';

    public function getLabel(): string
    {
        return match ($this) {
            self::Memuaskan => 'Memuaskan',
            self::SangatMemuaskan => 'Sangat Memuaskan',
            self::Pujian => 'Pujian (Cumlaude)',
        };
    }
}
