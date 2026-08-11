<?php

namespace App\Filament\Resources\Mahasiswas\Schemas;

use App\Enums\JenisKelamin;
use App\Models\ProgramStudi;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class MahasiswaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Akun Login')
                    ->relationship('user')
                    ->columns(2)
                    ->components([
                        TextInput::make('name')
                            ->label('Nama Akun')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->unique(
                                table: 'users',
                                column: 'email',
                                modifyRuleUsing: fn ($rule, $record) => $record?->user_id ? $rule->ignore($record->user_id) : $rule,
                            )
                            ->maxLength(255),
                        TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->revealable()
                            ->formatStateUsing(fn () => null)
                            ->dehydrateStateUsing(fn (?string $state) => filled($state) ? Hash::make($state) : null)
                            ->dehydrated(fn (?string $state) => filled($state))
                            ->required(fn (string $operation) => $operation === 'create')
                            ->helperText('Kosongkan jika tidak ingin mengubah password.'),
                    ]),

                Section::make('Program Studi')
                    ->components([
                        Select::make('program_studi_id')
                            ->label('Program Studi')
                            ->options(fn () => ProgramStudi::query()->orderBy('nama_prodi')->pluck('nama_prodi', 'id'))
                            ->searchable()
                            ->required(),
                    ]),

                Section::make('Biodata')
                    ->columns(2)
                    ->components([
                        TextInput::make('nim')
                            ->label('NIM')
                            ->required()
                            ->maxLength(30)
                            ->unique(ignoreRecord: true),
                        TextInput::make('nama_lengkap')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('tempat_lahir')
                            ->label('Tempat Lahir')
                            ->maxLength(255),
                        DatePicker::make('tanggal_lahir')
                            ->label('Tanggal Lahir'),
                        Select::make('jenis_kelamin')
                            ->label('Jenis Kelamin')
                            ->options(collect(JenisKelamin::cases())->mapWithKeys(fn (JenisKelamin $j) => [$j->value => $j->label()])),
                        TextInput::make('angkatan')
                            ->label('Angkatan')
                            ->numeric()
                            ->required()
                            ->minValue(2000)
                            ->maxValue(2100),
                        TextInput::make('tahun_lulus')
                            ->label('Tahun Lulus')
                            ->numeric()
                            ->minValue(2000)
                            ->maxValue(2100),
                        TextInput::make('no_hp')
                            ->label('No. HP')
                            ->tel()
                            ->maxLength(20),
                        Textarea::make('alamat')
                            ->label('Alamat')
                            ->columnSpanFull(),
                        FileUpload::make('foto_path')
                            ->label('Foto')
                            ->image()
                            ->disk('local')
                            ->directory('mahasiswa/foto')
                            ->visibility('private')
                            ->maxSize(1024)
                            ->helperText('Foto formal (pas foto), rasio 3:4, disarankan minimal 300×400px. Maksimal 1 MB.')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
