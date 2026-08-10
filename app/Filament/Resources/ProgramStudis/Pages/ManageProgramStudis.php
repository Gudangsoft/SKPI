<?php

namespace App\Filament\Resources\ProgramStudis\Pages;

use App\Filament\Resources\ProgramStudis\ProgramStudiResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageProgramStudis extends ManageRecords
{
    protected static string $resource = ProgramStudiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
