<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum TingkatPrestasi: string implements HasLabel
{
    case Internasional = 'internasional';
    case Nasional = 'nasional';
    case Wilayah = 'wilayah';
    case Lokal = 'lokal';

    public function getLabel(): string
    {
        return match ($this) {
            self::Internasional => 'Internasional',
            self::Nasional => 'Nasional',
            self::Wilayah => 'Wilayah',
            self::Lokal => 'Lokal',
        };
    }
}
