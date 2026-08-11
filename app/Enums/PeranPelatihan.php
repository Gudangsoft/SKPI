<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum PeranPelatihan: string implements HasLabel
{
    case Peserta = 'peserta';
    case Panitia = 'panitia';
    case Pemateri = 'pemateri';

    public function getLabel(): string
    {
        return match ($this) {
            self::Peserta => 'Peserta',
            self::Panitia => 'Panitia',
            self::Pemateri => 'Pemateri',
        };
    }
}
