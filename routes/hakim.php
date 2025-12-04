<?php

use App\Http\Controllers\HakimController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:hakim'])->prefix('hakim')->name('hakim.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [HakimController::class, 'dashboard'])->name('dashboard');

    // Daftar Cuti
    Route::get('/cuti', [HakimController::class, 'cutiIndex'])->name('cuti.index');

    // Buat Pengajuan Cuti
    Route::get('/cuti/create', [HakimController::class, 'cutiCreate'])->name('cuti.create');
    Route::post('/cuti', [HakimController::class, 'cutiStore'])->name('cuti.store');

    // Detail Cuti
    Route::get('/cuti/{id}', [HakimController::class, 'cutiShow'])->name('cuti.show');

    // Edit Cuti
    Route::get('/cuti/{id}/edit', [HakimController::class, 'cutiEdit'])->name('cuti.edit');
    Route::put('/cuti/{id}', [HakimController::class, 'cutiUpdate'])->name('cuti.update');

    // Batalkan Cuti
    Route::delete('/cuti/{id}', [HakimController::class, 'cutiCancel'])->name('cuti.cancel');

    // Cetak/unduh surat keterangan cuti (hanya untuk cuti yang disetujui pimpinan)
    Route::get('/cuti/{id}/surat', [HakimController::class, 'cetakSuratCuti'])->name('cuti.surat');
});
