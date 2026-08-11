<?php

namespace App\Http\Controllers\Student;

use App\Enums\PengajuanStatus;
use App\Enums\PredikatKelulusan;
use App\Http\Controllers\Controller;
use App\Models\PengajuanSkpi;
use App\Support\PengajuanKategori;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class PengajuanController extends Controller
{
    public function index(Request $request): View
    {
        $mahasiswa = $request->user()->mahasiswa;

        $pengajuans = $mahasiswa
            ? $mahasiswa->pengajuanSkpis()->latest()->get()
            : collect();

        return view('student.pengajuan.index', [
            'pengajuans' => $pengajuans,
            'canCreate' => $mahasiswa && ! $pengajuans->contains(fn (PengajuanSkpi $p) => $p->status !== PengajuanStatus::Published),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $mahasiswa = $request->user()->mahasiswa()->firstOrFail();

        $existing = $mahasiswa->pengajuanSkpis()
            ->where('status', '!=', PengajuanStatus::Published->value)
            ->latest()
            ->first();

        if ($existing) {
            return Redirect::route('pengajuan.akademik', $existing);
        }

        $pengajuan = $mahasiswa->pengajuanSkpis()->create([
            'status' => PengajuanStatus::Draft,
        ]);

        return Redirect::route('pengajuan.akademik', $pengajuan);
    }

    public function akademik(Request $request, PengajuanSkpi $pengajuan): View
    {
        $this->authorize('view', $pengajuan);

        return view('student.pengajuan.akademik', [
            'pengajuan' => $pengajuan,
            'mahasiswa' => $pengajuan->mahasiswa()->with('programStudi.fakultas')->first(),
            'predikatOptions' => PredikatKelulusan::cases(),
            'editable' => $pengajuan->isEditableByMahasiswa(),
        ]);
    }

    public function updateAkademik(Request $request, PengajuanSkpi $pengajuan): RedirectResponse
    {
        $this->authorize('update', $pengajuan);

        $validated = $request->validate([
            'ipk' => ['required', 'numeric', 'between:0,4'],
            'judul_skripsi' => ['required', 'string', 'max:255'],
            'tanggal_lulus' => ['required', 'date'],
            'no_ijazah' => ['nullable', 'string', 'max:255'],
            'predikat_kelulusan' => ['required', 'in:'.implode(',', array_column(PredikatKelulusan::cases(), 'value'))],
        ]);

        $pengajuan->update($validated);

        return Redirect::route('pengajuan.kategori', [$pengajuan, 'prestasi'])
            ->with('status', 'akademik-updated');
    }

    public function kategori(Request $request, PengajuanSkpi $pengajuan, string $kategori): View
    {
        $this->authorize('view', $pengajuan);

        abort_unless(PengajuanKategori::forSlug($kategori), 404);

        return view('student.pengajuan.kategori', [
            'pengajuan' => $pengajuan,
            'kategori' => $kategori,
            'editable' => $pengajuan->isEditableByMahasiswa(),
        ]);
    }

    public function review(Request $request, PengajuanSkpi $pengajuan): View
    {
        $this->authorize('view', $pengajuan);

        $pengajuan->load([
            'mahasiswa.programStudi.fakultas',
            'prestasis',
            'organisasis',
            'sertifikasis',
            'pelatihanSeminars',
            'magangPkls',
            'kompetensiAktivitas',
            'statusHistories.changedBy',
        ]);

        return view('student.pengajuan.review', [
            'pengajuan' => $pengajuan,
            'editable' => $pengajuan->isEditableByMahasiswa(),
        ]);
    }

    public function submit(Request $request, PengajuanSkpi $pengajuan): RedirectResponse
    {
        $this->authorize('update', $pengajuan);

        if (blank($pengajuan->judul_skripsi) || blank($pengajuan->tanggal_lulus) || blank($pengajuan->ipk)) {
            return Redirect::route('pengajuan.akademik', $pengajuan)
                ->with('error', 'Lengkapi data akademik terlebih dahulu sebelum mengajukan.');
        }

        $pengajuan->update([
            'status' => PengajuanStatus::Diajukan,
            'catatan_revisi' => null,
        ]);

        return Redirect::route('pengajuan.show', $pengajuan)->with('status', 'pengajuan-submitted');
    }

    /**
     * The "show" page reuses the review screen: it already covers status,
     * riwayat, and full data summary in one read-only-aware view.
     */
    public function show(Request $request, PengajuanSkpi $pengajuan): View
    {
        return $this->review($request, $pengajuan);
    }
}
