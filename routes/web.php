<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\HRController;
use App\Http\Controllers\PimpinanController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\CutiController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// ===========================================
// ✅ 1. Halaman Home (bisa diakses semua)
// ===========================================
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/home', [HomeController::class, 'index'])->name('home');

// ===========================================
// ✅ 2. Halaman Cuti (harus login)
// ===========================================
Route::middleware(['auth'])->group(function () {
    Route::get('/cuti', [CutiController::class, 'index'])->name('cuti.index');
    Route::get('/cuti/create', [CutiController::class, 'create'])->name('cuti.create');
    Route::post('/cuti', [CutiController::class, 'store'])->name('cuti.store');
});

// ===========================================
// ✅ 3. ADMIN AREA
// ===========================================
Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/permintaan-cuti', [AdminController::class, 'permintaanCuti'])->name('admin.permintaan');
    Route::get('/aturan-cuti', [AdminController::class, 'aturanCuti'])->name('admin.aturan');
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

// ===========================================
// ✅ 4. HR AREA
// ===========================================
Route::prefix('hr')->middleware(['auth', 'role:hr'])->group(function () {
    Route::get('/dashboard', [HRController::class, 'dashboard'])->name('hr.dashboard');
    Route::get('/permintaan-cuti', [HRController::class, 'permintaanCuti'])->name('hr.permintaan');
    Route::get('/aturan-cuti', [HRController::class, 'aturanCuti'])->name('hr.aturan');
    Route::get('/user-karyawan', [HRController::class, 'userKaryawan'])->name('hr.user');
});

// ===========================================
// ✅ 5. PIMPINAN AREA
// ===========================================
Route::prefix('pimpinan')->middleware(['auth', 'role:pimpinan'])->group(function () {
    Route::get('/dashboard', [PimpinanController::class, 'dashboard'])->name('pimpinan.dashboard');
    Route::get('/permintaan-cuti', [PimpinanController::class, 'permintaanCuti'])->name('pimpinan.permintaan');
    Route::get('/aturan-cuti', [PimpinanController::class, 'aturanCuti'])->name('pimpinan.aturan');
    Route::get('/user-karyawan', [PimpinanController::class, 'userKaryawan'])->name('pimpinan.user');
});

// ===========================================
// ✅ 6. PEGAWAI AREA
// ===========================================
Route::prefix('pegawai')->middleware(['auth', 'role:pegawai'])->group(function () {
    Route::get('/dashboard', [PegawaiController::class, 'dashboard'])->name('pegawai.dashboard');
    Route::get('/permintaan-cuti', [PegawaiController::class, 'permintaanCuti'])->name('pegawai.permintaan');
    Route::get('/aturan-cuti', [PegawaiController::class, 'aturanCuti'])->name('pegawai.aturan');
    Route::get('/user-karyawan', [PegawaiController::class, 'userKaryawan'])->name('pegawai.user');
});

// ===========================================
// ✅ 7. AUTHENTICATION ROUTES
// ===========================================
Auth::routes();
