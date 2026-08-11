<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\User;
use App\Services\ImpersonationService;
use App\Support\Roles;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

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
                Action::make('loginAs')
                    ->label('Login Sebagai')
                    ->icon(Heroicon::OutlinedArrowRightEndOnRectangle)
                    ->color('gray')
                    ->visible(fn (User $record): bool => Auth::id() !== $record->id && ! $record->hasRole(Roles::SUPER_ADMIN))
                    ->requiresConfirmation()
                    ->modalHeading('Login Sebagai Pengguna Ini?')
                    ->modalDescription(fn (User $record): string => "Anda akan login sebagai {$record->name} ({$record->email}). Gunakan tombol \"Kembali ke akun saya\" di bagian atas halaman untuk kembali.")
                    ->modalSubmitActionLabel('Ya, Login Sebagai')
                    ->action(fn (User $record) => app(ImpersonationService::class)->start($record))
                    ->successRedirectUrl(fn (): string => '/admin'),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
