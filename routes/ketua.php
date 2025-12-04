<?php

use App\Http\Controllers\KetuaController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:ketua'])->prefix('ketua')->name('ketua.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [KetuaController::class, 'dashboard'])->name('dashboard');

    // Daftar Cuti yang perlu disetujui
    Route::get('/cuti', [KetuaController::class, 'cutiIndex'])->name('cuti.index');

    // Detail Cuti
    Route::get('/cuti/{id}', [KetuaController::class, 'cutiShow'])->name('cuti.show');

    // Approve Cuti
    Route::post('/cuti/{id}/approve', [KetuaController::class, 'cutiApprove'])->name('cuti.approve');

    // Reject Cuti
    Route::post('/cuti/{id}/reject', [KetuaController::class, 'cutiReject'])->name('cuti.reject');
});
