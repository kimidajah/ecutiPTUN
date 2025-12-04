<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HRController;

Route::prefix('hr')->middleware(['auth', 'role:sub_kepegawaian'])->name('hr.')->group(function () {

    Route::get('/dashboard', [HRController::class, 'index'])->name('dashboard');
    Route::get('/home', [HRController::class, 'index'])->name('home');
    Route::get('/aturan-cuti', [HRController::class, 'aturanCuti'])->name('aturan');
    Route::get('/user-karyawan', [HRController::class, 'userKaryawan'])->name('user');

    // Permintaan Cuti
    Route::get('/permintaan-cuti', [HRController::class, 'cutiIndex'])->name('cuti.index');
    Route::get('/permintaan-cuti/{id}', [HRController::class, 'cutiShow'])->name('cuti.show');
    Route::post('/permintaan-cuti/{id}/approve', [HRController::class, 'cutiApprove'])->name('cuti.approve');
    Route::post('/permintaan-cuti/{id}/reject', [HRController::class, 'cutiReject'])->name('cuti.reject');
});
