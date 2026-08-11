<?php

namespace App\Filament\Resources\PengajuanSkpis\Tables;

use App\Enums\PengajuanStatus;
use App\Filament\Resources\PengajuanSkpis\PengajuanSkpiResource;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PengajuanSkpisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('mahasiswa.nama_lengkap')->label('Nama')->searchable()->sortable(),
                TextColumn::make('mahasiswa.nim')->label('NIM')->searchable()->sortable(),
                TextColumn::make('mahasiswa.programStudi.nama_prodi')->label('Program Studi')->searchable(),
                TextColumn::make('status')->label('Status')->badge(),
                TextColumn::make('diajukan_at')->label('Diajukan')->dateTime('d M Y H:i')->placeholder('—')->sortable(),
                TextColumn::make('updated_at')->label('Diperbarui')->dateTime('d M Y H:i')->sortable(),
            ])
            ->defaultSort('updated_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options(collect(PengajuanStatus::cases())->mapWithKeys(fn (PengajuanStatus $s) => [$s->value => $s->getLabel()])),
            ])
            ->recordActions([
                ViewAction::make(),
                PengajuanSkpiResource::verifikasiAction(),
                PengajuanSkpiResource::mintaRevisiAction(),
                PengajuanSkpiResource::setujuiAction(),
                PengajuanSkpiResource::terbitkanNomorAction(),
                PengajuanSkpiResource::terbitkanPdfAction(),
            ]);
    }
}
