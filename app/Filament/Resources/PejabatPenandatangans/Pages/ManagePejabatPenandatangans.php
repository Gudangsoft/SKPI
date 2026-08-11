<?php

namespace App\Filament\Resources\PejabatPenandatangans\Pages;

use App\Filament\Resources\PejabatPenandatangans\PejabatPenandatanganResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManagePejabatPenandatangans extends ManageRecords
{
    protected static string $resource = PejabatPenandatanganResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
