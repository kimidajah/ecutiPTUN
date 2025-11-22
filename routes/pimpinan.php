<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PimpinanController;

Route::prefix('pimpinan')->middleware(['auth', 'role:pimpinan'])->name('pimpinan.')->group(function () {

    Route::get('/dashboard', [PimpinanController::class, 'dashboard'])->name('dashboard');
    Route::get('/home', [PimpinanController::class, 'dashboard'])->name('home');
    Route::get('/aturan-cuti', [PimpinanController::class, 'aturanCuti'])->name('aturan');
    Route::get('/user-karyawan', [PimpinanController::class, 'userKaryawan'])->name('user');

    // Permintaan Cuti
    Route::get('/cuti', [PimpinanController::class, 'cutiIndex'])->name('cuti.index');
    Route::get('/cuti/{id}', [PimpinanController::class, 'cutiShow'])->name('cuti.show');
    Route::post('/cuti/{id}/approve', [PimpinanController::class, 'cutiApprove'])->name('cuti.approve');
    Route::post('/cuti/{id}/reject', [PimpinanController::class, 'cutiReject'])->name('cuti.reject');
});
