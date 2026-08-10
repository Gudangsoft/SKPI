<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfilController extends Controller
{
    public function edit(Request $request): View
    {
        $mahasiswa = $request->user()->mahasiswa()->with('programStudi.fakultas')->firstOrFail();

        return view('student.profil.edit', [
            'mahasiswa' => $mahasiswa,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $mahasiswa = $request->user()->mahasiswa()->firstOrFail();

        $validated = $request->validate([
            'tempat_lahir' => ['nullable', 'string', 'max:255'],
            'tanggal_lahir' => ['nullable', 'date'],
            'jenis_kelamin' => ['nullable', 'in:L,P'],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'alamat' => ['nullable', 'string'],
            'foto' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('foto')) {
            $validated['foto_path'] = $request->file('foto')->store('mahasiswa/foto', 'local');
        }
        unset($validated['foto']);

        $mahasiswa->update($validated);

        return Redirect::route('mahasiswa.profil.edit')->with('status', 'profil-updated');
    }
}
