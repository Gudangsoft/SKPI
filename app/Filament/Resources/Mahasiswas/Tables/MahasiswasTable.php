<?php

namespace App\Filament\Resources\Mahasiswas\Tables;

use App\Models\ProgramStudi;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MahasiswasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('foto_path')->label('')->circular()->disk('local'),
                TextColumn::make('nim')->label('NIM')->searchable()->sortable(),
                TextColumn::make('nama_lengkap')->label('Nama')->searchable()->sortable(),
                TextColumn::make('programStudi.nama_prodi')->label('Program Studi')->searchable()->sortable(),
                TextColumn::make('angkatan')->label('Angkatan')->sortable(),
                TextColumn::make('user.email')->label('Email')->searchable(),
            ])
            ->filters([
                SelectFilter::make('program_studi_id')
                    ->label('Program Studi')
                    ->options(fn () => ProgramStudi::query()->orderBy('nama_prodi')->pluck('nama_prodi', 'id')),
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
