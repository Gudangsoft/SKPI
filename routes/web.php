<?php

use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Student\DashboardController;
use App\Http\Controllers\Student\PengajuanController;
use App\Http\Controllers\Student\ProfilController;
use App\Support\Roles;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/halaman/{slug}', [PageController::class, 'show'])->name('pages.show');

Route::middleware(['auth', 'role:'.Roles::MAHASISWA])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/mahasiswa/profil', [ProfilController::class, 'edit'])->name('mahasiswa.profil.edit');
    Route::put('/mahasiswa/profil', [ProfilController::class, 'update'])->name('mahasiswa.profil.update');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('/pengajuan', [PengajuanController::class, 'index'])->name('pengajuan.index');
    Route::post('/pengajuan', [PengajuanController::class, 'store'])->name('pengajuan.store');
    Route::get('/pengajuan/{pengajuan}', [PengajuanController::class, 'show'])->name('pengajuan.show');
    Route::get('/pengajuan/{pengajuan}/akademik', [PengajuanController::class, 'akademik'])->name('pengajuan.akademik');
    Route::put('/pengajuan/{pengajuan}/akademik', [PengajuanController::class, 'updateAkademik'])->name('pengajuan.akademik.update');
    Route::get('/pengajuan/{pengajuan}/kategori/{kategori}', [PengajuanController::class, 'kategori'])->name('pengajuan.kategori');
    Route::get('/pengajuan/{pengajuan}/review', [PengajuanController::class, 'review'])->name('pengajuan.review');
    Route::post('/pengajuan/{pengajuan}/submit', [PengajuanController::class, 'submit'])->name('pengajuan.submit');
});

require __DIR__.'/auth.php';
