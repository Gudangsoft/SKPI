<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\ProgramStudi;
use App\Models\User;
use App\Support\Roles;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->revealable()
                    ->dehydrateStateUsing(fn (?string $state) => filled($state) ? Hash::make($state) : null)
                    ->dehydrated(fn (?string $state) => filled($state))
                    ->required(fn (string $operation) => $operation === 'create')
                    ->helperText('Kosongkan jika tidak ingin mengubah password.'),
                Select::make('role')
                    ->label('Peran')
                    ->options([
                        Roles::SUPER_ADMIN => 'Super Admin',
                        Roles::ADMIN_PRODI => 'Admin Prodi',
                        Roles::KAPRODI => 'Kaprodi',
                    ])
                    ->required()
                    ->live()
                    ->afterStateHydrated(function ($component, ?User $record) {
                        $component->state($record?->getRoleNames()->first());
                    }),
                Select::make('program_studi_id')
                    ->label('Program Studi')
                    ->options(fn () => ProgramStudi::query()->orderBy('nama_prodi')->pluck('nama_prodi', 'id'))
                    ->searchable()
                    ->visible(fn (Get $get) => in_array($get('role'), [Roles::ADMIN_PRODI, Roles::KAPRODI]))
                    ->required(fn (Get $get) => in_array($get('role'), [Roles::ADMIN_PRODI, Roles::KAPRODI]))
                    ->dehydrateStateUsing(fn ($state, Get $get) => in_array($get('role'), [Roles::ADMIN_PRODI, Roles::KAPRODI]) ? $state : null),
            ]);
    }
}
