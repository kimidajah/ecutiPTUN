<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\CutiController;

Route::prefix('pegawai')->middleware(['auth', 'role:pegawai'])->group(function () {
    Route::get('/dashboard', [PegawaiController::class, 'dashboard'])->name('pegawai.dashboard');

    // ROUTE CUTI
    Route::get('/cuti', [CutiController::class, 'indexCuti'])->name('pegawai.cuti.index');
    Route::get('/cuti/create', [CutiController::class, 'createCuti'])->name('pegawai.cuti.create');
    Route::post('/cuti', [CutiController::class, 'storeCuti'])->name('pegawai.cuti.store');
    Route::get('/cuti/{id}', [CutiController::class, 'showCuti'])->name('pegawai.cuti.show');
    Route::get('/cuti/{id}/edit', [CutiController::class, 'editCuti'])->name('pegawai.cuti.edit');
    Route::put('/cuti/{id}', [CutiController::class, 'updateCuti'])->name('pegawai.cuti.update');
    Route::delete('/cuti/{id}', [CutiController::class, 'destroyCuti'])->name('pegawai.cuti.destroy');
});
