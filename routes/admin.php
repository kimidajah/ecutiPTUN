<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;

Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/permintaan-cuti', [AdminController::class, 'permintaanCuti'])->name('admin.permintaan.cuti');
    Route::get('/user-karyawan', [AdminController::class, 'userKaryawan'])->name('admin.user');

    // ✅ Resource untuk manajemen user
    Route::resource('user', AdminController::class)->names([
        'index' => 'admin.user.index',
        'create' => 'admin.user.create',
        'store' => 'admin.user.store',
        'edit' => 'admin.user.edit',
        'update' => 'admin.user.update',
        'destroy' => 'admin.user.destroy',
    ]);
});
