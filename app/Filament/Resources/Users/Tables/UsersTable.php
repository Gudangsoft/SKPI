<?php

namespace App\Filament\Resources\Users\Tables;

use App\Support\Roles;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nama')->searchable()->sortable(),
                TextColumn::make('email')->label('Email')->searchable()->sortable(),
                TextColumn::make('roles.name')->label('Peran')->badge(),
                TextColumn::make('programStudi.nama_prodi')->label('Program Studi')->placeholder('—'),
            ])
            ->filters([
                SelectFilter::make('roles')
                    ->label('Peran')
                    ->relationship('roles', 'name')
                    ->options([
                        Roles::SUPER_ADMIN => 'Super Admin',
                        Roles::ADMIN_PRODI => 'Admin Prodi',
                        Roles::KAPRODI => 'Kaprodi',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
