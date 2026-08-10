<?php

namespace App\Filament\Resources\Mahasiswas\Pages;

use App\Filament\Resources\Mahasiswas\MahasiswaResource;
use App\Support\Roles;
use Filament\Resources\Pages\CreateRecord;

class CreateMahasiswa extends CreateRecord
{
    protected static string $resource = MahasiswaResource::class;

    protected function afterCreate(): void
    {
        $this->record->user->syncRoles([Roles::MAHASISWA]);
    }
}
