<?php

namespace App\Filament\Resources\PengajuanSkpis\Schemas;

use App\Enums\PengajuanStatus;
use App\Models\PengajuanSkpi;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\RepeatableEntry\TableColumn;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Storage;

class PengajuanSkpiInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Status')
                    ->columns(2)
                    ->components([
                        TextEntry::make('status')->label('Status')->badge(),
                        TextEntry::make('diajukan_at')->label('Diajukan')->dateTime('d M Y H:i')->placeholder('—'),
                        TextEntry::make('catatan_revisi')
                            ->label('Catatan Revisi')
                            ->columnSpanFull()
                            ->visible(fn (PengajuanSkpi $record) => $record->status === PengajuanStatus::Revisi && filled($record->catatan_revisi)),
                    ]),

                Section::make('Data Akademik')
                    ->columns(2)
                    ->components([
                        TextEntry::make('mahasiswa.nama_lengkap')->label('Nama'),
                        TextEntry::make('mahasiswa.nim')->label('NIM'),
                        TextEntry::make('mahasiswa.programStudi.nama_prodi')->label('Program Studi'),
                        TextEntry::make('ipk')->label('IPK')->placeholder('—'),
                        TextEntry::make('judul_skripsi')->label('Judul Skripsi')->placeholder('—')->columnSpanFull(),
                        TextEntry::make('tanggal_lulus')->label('Tanggal Lulus')->date('d M Y')->placeholder('—'),
                        TextEntry::make('predikat_kelulusan')->label('Predikat')->placeholder('—'),
                    ]),

                Section::make('Penerbitan SKPI')
                    ->columns(2)
                    ->visible(fn (PengajuanSkpi $record) => filled($record->nomor_skpi))
                    ->components([
                        TextEntry::make('nomor_skpi')->label('Nomor SKPI'),
                        TextEntry::make('nomor_skpi_generated_at')->label('Tanggal Terbit Nomor')->dateTime('d M Y H:i')->placeholder('—'),
                        TextEntry::make('pejabatPenandatangan.nama')->label('Pejabat Penandatangan')->placeholder('—'),
                        TextEntry::make('published_at')->label('Tanggal Publikasi')->dateTime('d M Y H:i')->placeholder('—'),
                        TextEntry::make('pdf_path')
                            ->label('Dokumen PDF')
                            ->state('Lihat / Unduh PDF')
                            ->url(fn (PengajuanSkpi $record) => Storage::disk('public')->url($record->pdf_path))
                            ->openUrlInNewTab()
                            ->visible(fn (PengajuanSkpi $record) => filled($record->pdf_path)),
                        TextEntry::make('verification_token')
                            ->label('Tautan Verifikasi')
                            ->state(fn (PengajuanSkpi $record) => route('verification.show', $record->verification_token))
                            ->url(fn (PengajuanSkpi $record) => route('verification.show', $record->verification_token))
                            ->openUrlInNewTab()
                            ->visible(fn (PengajuanSkpi $record) => $record->status === PengajuanStatus::Published)
                            ->columnSpanFull(),
                    ]),

                Section::make('Prestasi')
                    ->columnSpanFull()
                    ->visible(fn (PengajuanSkpi $record) => $record->prestasis->isNotEmpty())
                    ->components([
                        RepeatableEntry::make('prestasis')
                            ->hiddenLabel()
                            ->table([
                                TableColumn::make('Nama Prestasi'),
                                TableColumn::make('Tingkat'),
                                TableColumn::make('Peringkat'),
                                TableColumn::make('Penyelenggara'),
                                TableColumn::make('Tahun'),
                            ])
                            ->schema([
                                TextEntry::make('nama_prestasi')->hiddenLabel(),
                                TextEntry::make('tingkat')->hiddenLabel()->badge(),
                                TextEntry::make('peringkat')->hiddenLabel()->placeholder('—'),
                                TextEntry::make('penyelenggara')->hiddenLabel()->placeholder('—'),
                                TextEntry::make('tahun')->hiddenLabel(),
                            ]),
                    ]),

                Section::make('Organisasi')
                    ->columnSpanFull()
                    ->visible(fn (PengajuanSkpi $record) => $record->organisasis->isNotEmpty())
                    ->components([
                        RepeatableEntry::make('organisasis')
                            ->hiddenLabel()
                            ->table([
                                TableColumn::make('Nama Organisasi'),
                                TableColumn::make('Jabatan'),
                                TableColumn::make('Periode Mulai'),
                                TableColumn::make('Periode Selesai'),
                            ])
                            ->schema([
                                TextEntry::make('nama_organisasi')->hiddenLabel(),
                                TextEntry::make('jabatan')->hiddenLabel()->placeholder('—'),
                                TextEntry::make('periode_mulai')->hiddenLabel()->date('d M Y')->placeholder('—'),
                                TextEntry::make('periode_selesai')->hiddenLabel()->date('d M Y')->placeholder('—'),
                            ]),
                    ]),

                Section::make('Sertifikasi')
                    ->columnSpanFull()
                    ->visible(fn (PengajuanSkpi $record) => $record->sertifikasis->isNotEmpty())
                    ->components([
                        RepeatableEntry::make('sertifikasis')
                            ->hiddenLabel()
                            ->table([
                                TableColumn::make('Nama Sertifikat'),
                                TableColumn::make('Penerbit'),
                                TableColumn::make('No. Sertifikat'),
                                TableColumn::make('Tahun'),
                            ])
                            ->schema([
                                TextEntry::make('nama_sertifikat')->hiddenLabel(),
                                TextEntry::make('penerbit')->hiddenLabel()->placeholder('—'),
                                TextEntry::make('no_sertifikat')->hiddenLabel()->placeholder('—'),
                                TextEntry::make('tahun')->hiddenLabel(),
                            ]),
                    ]),

                Section::make('Pelatihan / Seminar')
                    ->columnSpanFull()
                    ->visible(fn (PengajuanSkpi $record) => $record->pelatihanSeminars->isNotEmpty())
                    ->components([
                        RepeatableEntry::make('pelatihanSeminars')
                            ->hiddenLabel()
                            ->table([
                                TableColumn::make('Nama Kegiatan'),
                                TableColumn::make('Penyelenggara'),
                                TableColumn::make('Peran'),
                                TableColumn::make('Tahun'),
                                TableColumn::make('Jumlah Jam'),
                            ])
                            ->schema([
                                TextEntry::make('nama_kegiatan')->hiddenLabel(),
                                TextEntry::make('penyelenggara')->hiddenLabel()->placeholder('—'),
                                TextEntry::make('peran')->hiddenLabel()->badge(),
                                TextEntry::make('tahun')->hiddenLabel(),
                                TextEntry::make('jumlah_jam')->hiddenLabel()->placeholder('—'),
                            ]),
                    ]),

                Section::make('Magang / PKL')
                    ->columnSpanFull()
                    ->visible(fn (PengajuanSkpi $record) => $record->magangPkls->isNotEmpty())
                    ->components([
                        RepeatableEntry::make('magangPkls')
                            ->hiddenLabel()
                            ->table([
                                TableColumn::make('Nama Instansi'),
                                TableColumn::make('Posisi'),
                                TableColumn::make('Periode Mulai'),
                                TableColumn::make('Periode Selesai'),
                            ])
                            ->schema([
                                TextEntry::make('nama_instansi')->hiddenLabel(),
                                TextEntry::make('posisi')->hiddenLabel()->placeholder('—'),
                                TextEntry::make('periode_mulai')->hiddenLabel()->date('d M Y')->placeholder('—'),
                                TextEntry::make('periode_selesai')->hiddenLabel()->date('d M Y')->placeholder('—'),
                            ]),
                    ]),

                Section::make('Kompetensi / Aktivitas')
                    ->columnSpanFull()
                    ->visible(fn (PengajuanSkpi $record) => $record->kompetensiAktivitas->isNotEmpty())
                    ->components([
                        RepeatableEntry::make('kompetensiAktivitas')
                            ->hiddenLabel()
                            ->table([
                                TableColumn::make('Nama Kegiatan'),
                                TableColumn::make('Keterangan'),
                                TableColumn::make('Tahun'),
                            ])
                            ->schema([
                                TextEntry::make('nama_kegiatan')->hiddenLabel(),
                                TextEntry::make('keterangan')->hiddenLabel()->placeholder('—'),
                                TextEntry::make('tahun')->hiddenLabel(),
                            ]),
                    ]),

                Section::make('Riwayat Status')
                    ->columnSpanFull()
                    ->components([
                        RepeatableEntry::make('statusHistories')
                            ->hiddenLabel()
                            ->table([
                                TableColumn::make('Status'),
                                TableColumn::make('Oleh'),
                                TableColumn::make('Catatan'),
                                TableColumn::make('Waktu'),
                            ])
                            ->schema([
                                TextEntry::make('status_to')->hiddenLabel()->badge(),
                                TextEntry::make('changedBy.name')->hiddenLabel()->placeholder('Sistem'),
                                TextEntry::make('catatan')->hiddenLabel()->placeholder('—'),
                                TextEntry::make('created_at')->hiddenLabel()->dateTime('d M Y H:i'),
                            ])
                            ->placeholder('Belum ada riwayat.'),
                    ]),
            ]);
    }
}
