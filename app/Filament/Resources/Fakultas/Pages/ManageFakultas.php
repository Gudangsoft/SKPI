<?php

namespace App\Filament\Resources\Fakultas\Pages;

use App\Filament\Resources\Fakultas\FakultasResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageFakultas extends ManageRecords
{
    protected static string $resource = FakultasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
