<?php

namespace App\Filament\Resources\PengajuanSkpis\Pages;

use App\Filament\Resources\PengajuanSkpis\PengajuanSkpiResource;
use Filament\Resources\Pages\ViewRecord;

class ViewPengajuanSkpi extends ViewRecord
{
    protected static string $resource = PengajuanSkpiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            PengajuanSkpiResource::verifikasiAction(),
            PengajuanSkpiResource::mintaRevisiAction(),
            PengajuanSkpiResource::setujuiAction(),
        ];
    }
}
