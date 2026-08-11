<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>SKPI {{ $pengajuan->nomor_skpi }}</title>
    @php
        $setting = \App\Models\Setting::current();
        $mahasiswa = $pengajuan->mahasiswa;
        $fakultas = $mahasiswa->programStudi->fakultas;

        $kategori = [
            'Prestasi' => [$pengajuan->prestasis, ['nama_prestasi' => 'Nama Prestasi', 'tingkat' => 'Tingkat', 'peringkat' => 'Peringkat', 'penyelenggara' => 'Penyelenggara', 'tahun' => 'Tahun']],
            'Organisasi' => [$pengajuan->organisasis, ['nama_organisasi' => 'Nama Organisasi', 'jabatan' => 'Jabatan', 'periode_mulai' => 'Mulai', 'periode_selesai' => 'Selesai']],
            'Sertifikasi' => [$pengajuan->sertifikasis, ['nama_sertifikat' => 'Nama Sertifikat', 'penerbit' => 'Penerbit', 'no_sertifikat' => 'No. Sertifikat', 'tahun' => 'Tahun']],
            'Pelatihan / Seminar' => [$pengajuan->pelatihanSeminars, ['nama_kegiatan' => 'Nama Kegiatan', 'penyelenggara' => 'Penyelenggara', 'peran' => 'Peran', 'tahun' => 'Tahun', 'jumlah_jam' => 'Jam']],
            'Magang / PKL' => [$pengajuan->magangPkls, ['nama_instansi' => 'Nama Instansi', 'posisi' => 'Posisi', 'periode_mulai' => 'Mulai', 'periode_selesai' => 'Selesai']],
            'Kompetensi / Aktivitas' => [$pengajuan->kompetensiAktivitas, ['nama_kegiatan' => 'Nama Kegiatan', 'keterangan' => 'Keterangan', 'tahun' => 'Tahun']],
        ];
    @endphp
    <style>
        @page { margin: 100px 60px 90px 60px; }
        body { font-family: "DejaVu Sans", sans-serif; font-size: 11px; color: #1e293b; }
        .kop { text-align: center; border-bottom: 2px solid #1e293b; padding-bottom: 10px; margin-bottom: 16px; }
        .kop img { height: 50px; vertical-align: middle; margin-right: 10px; }
        .kop .nama-instansi { font-size: 16px; font-weight: bold; text-transform: uppercase; }
        .kop .fakultas { font-size: 12px; }
        .judul { text-align: center; margin-bottom: 4px; }
        .judul h1 { font-size: 14px; text-decoration: underline; margin: 0; text-transform: uppercase; }
        .judul .nomor { font-size: 11px; margin-top: 2px; }
        .intro { margin: 14px 0; text-align: justify; }
        table.data { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        table.data td { padding: 2px 4px; vertical-align: top; }
        table.data td.label { width: 150px; color: #475569; }
        table.data td.sep { width: 10px; }
        .section-title { font-size: 12px; font-weight: bold; margin: 14px 0 4px; border-bottom: 1px solid #cbd5e1; padding-bottom: 2px; }
        table.list { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        table.list th, table.list td { border: 1px solid #cbd5e1; padding: 4px 6px; font-size: 10px; text-align: left; }
        table.list th { background: #f1f5f9; }
        .pengesahan { width: 100%; margin-top: 30px; }
        .pengesahan .ttd-col { width: 260px; text-align: center; float: right; }
        .pengesahan .ttd-img { height: 60px; margin: 6px 0; }
        .pengesahan .nama-pejabat { font-weight: bold; text-decoration: underline; }
        .verifikasi { margin-top: 90px; clear: both; text-align: center; font-size: 9px; color: #64748b; }
        .verifikasi img { height: 80px; }
        .clearfix { clear: both; }
    </style>
</head>
<body>
    <div class="kop">
        @if ($setting->logoUrl())
            <img src="{{ public_path('storage/'.$setting->logo_path) }}">
        @endif
        <span class="nama-instansi">{{ $setting->app_name }}</span><br>
        <span class="fakultas">{{ $fakultas->nama_fakultas }}</span>
    </div>

    <div class="judul">
        <h1>Surat Keterangan Pendamping Ijazah</h1>
        <div class="nomor">Nomor: {{ $pengajuan->nomor_skpi }}</div>
    </div>

    <div class="intro">
        Surat Keterangan Pendamping Ijazah ini diterbitkan untuk menerangkan capaian akademik dan non-akademik mahasiswa berikut selama masa studi:
    </div>

    <table class="data">
        <tr><td class="label">Nama</td><td class="sep">:</td><td>{{ $mahasiswa->nama_lengkap }}</td></tr>
        <tr><td class="label">NIM</td><td class="sep">:</td><td>{{ $mahasiswa->nim }}</td></tr>
        <tr><td class="label">Program Studi</td><td class="sep">:</td><td>{{ $mahasiswa->programStudi->nama_prodi }}</td></tr>
        <tr><td class="label">Fakultas</td><td class="sep">:</td><td>{{ $fakultas->nama_fakultas }}</td></tr>
        <tr><td class="label">IPK</td><td class="sep">:</td><td>{{ $pengajuan->ipk }}</td></tr>
        <tr><td class="label">Judul Skripsi</td><td class="sep">:</td><td>{{ $pengajuan->judul_skripsi }}</td></tr>
        <tr><td class="label">Tanggal Lulus</td><td class="sep">:</td><td>{{ optional($pengajuan->tanggal_lulus)->translatedFormat('d F Y') }}</td></tr>
        <tr><td class="label">Predikat Kelulusan</td><td class="sep">:</td><td>{{ $pengajuan->predikat_kelulusan?->getLabel() }}</td></tr>
    </table>

    @foreach ($kategori as $label => [$items, $fields])
        @if ($items->isNotEmpty())
            <div class="section-title">{{ $label }}</div>
            <table class="list">
                <thead>
                    <tr>
                        @foreach ($fields as $header)
                            <th>{{ $header }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $item)
                        <tr>
                            @foreach (array_keys($fields) as $field)
                                @php $value = $item->{$field}; @endphp
                                <td>
                                    @if ($value instanceof \Illuminate\Support\Carbon)
                                        {{ $value->format('d-m-Y') }}
                                    @elseif ($value instanceof \BackedEnum)
                                        {{ $value->getLabel() }}
                                    @else
                                        {{ $value ?? '—' }}
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    @endforeach

    <div class="pengesahan">
        <div class="ttd-col">
            {{ $fakultas->nama_fakultas }}, {{ optional($pengajuan->nomor_skpi_generated_at)->translatedFormat('d F Y') }}<br>

            @if ($pengajuan->pejabatPenandatangan?->tanda_tangan_path)
                <img class="ttd-img" src="{{ public_path('storage/'.$pengajuan->pejabatPenandatangan->tanda_tangan_path) }}">
            @else
                <div style="height: 60px;"></div>
            @endif

            <div class="nama-pejabat">{{ $pengajuan->pejabatPenandatangan?->nama }}</div>
            <div>{{ $pengajuan->pejabatPenandatangan?->jabatan }}</div>
        </div>
        <div class="clearfix"></div>
    </div>

    <div class="verifikasi">
        <img src="{{ $qrCode }}"><br>
        Pindai kode QR atau kunjungi {{ route('verification.show', $pengajuan->verification_token) }} untuk memverifikasi keaslian dokumen ini.
    </div>
</body>
</html>
