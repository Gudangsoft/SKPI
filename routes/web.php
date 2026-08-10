<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Student\DashboardController;
use App\Http\Controllers\Student\ProfilController;
use App\Support\Roles;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'role:'.Roles::MAHASISWA])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/mahasiswa/profil', [ProfilController::class, 'edit'])->name('mahasiswa.profil.edit');
    Route::put('/mahasiswa/profil', [ProfilController::class, 'update'])->name('mahasiswa.profil.update');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

require __DIR__.'/auth.php';
