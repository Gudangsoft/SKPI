<?php

namespace App\Filament\Resources\PengajuanSkpis\Pages;

use App\Filament\Resources\PengajuanSkpis\PengajuanSkpiResource;
use Filament\Resources\Pages\ListRecords;

class ListPengajuanSkpis extends ListRecords
{
    protected static string $resource = PengajuanSkpiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
