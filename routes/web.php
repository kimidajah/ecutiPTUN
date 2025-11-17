<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HRController;
use App\Http\Controllers\CutiController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\PimpinanController;

// ===========================================
// ✅ 1. Halaman Home (bisa diakses semua)
// ===========================================
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/home', [HomeController::class, 'index'])->name('home');

// ===========================================
// ✅ 3. ADMIN AREA
// ===========================================
Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/permintaan-cuti', [AdminController::class, 'permintaanCuti'])->name('admin.permintaan.cuti');
    Route::get('/permintaan-cuti/{id}', [AdminController::class, 'detailCuti'])->name('admin.permintaan.detail');
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

    // ROUTE CUTI
    Route::get('/cuti', [CutiController::class, 'indexCuti'])->name('pegawai.cuti.index');
    Route::get('/cuti/create', [CutiController::class, 'createCuti'])->name('pegawai.cuti.create');
    Route::post('/cuti', [CutiController::class, 'storeCuti'])->name('pegawai.cuti.store');
    Route::get('/cuti/{id}', [CutiController::class, 'showCuti'])->name('pegawai.cuti.show');
    Route::get('/cuti/{id}/edit', [CutiController::class, 'editCuti'])->name('pegawai.cuti.edit');
    Route::put('/cuti/{id}', [CutiController::class, 'updateCuti'])->name('pegawai.cuti.update');
    Route::delete('/cuti/{id}', [CutiController::class, 'destroyCuti'])->name('pegawai.cuti.destroy');
});

// ==============================
// ROUTE UNTUK HR
// ==============================
Route::middleware(['auth', 'role:hr'])->prefix('hr')->name('hr.')->group(function () {

    // Dashboard HR
    Route::get('/dashboard', [HRController::class, 'index'])
        ->name('dashboard');

    // Home HR (opsional)
    Route::get('/home', [HRController::class, 'index'])
        ->name('home');

    // ==========================
    // PERMINTAAN CUTI (HR)
    // ==========================

    // Page daftar cuti
    Route::get('/permintaan-cuti', 
[HRController::class, 'cutiIndex']    )->name('cuti.index');

    // Detail cuti
    Route::get('/permintaan-cuti/{id}', 
[HRController::class, 'cutiIndex']    )->name('cuti.show');

    // Approve cuti
    Route::post('/permintaan-cuti/{id}/approve', 
[HRController::class, 'cutiIndex']    )->name('cuti.approve');

    // Reject cuti
    Route::post('/permintaan-cuti/{id}/reject', 
[HRController::class, 'cutiIndex']    )->name('cuti.reject');

});

Route::middleware(['auth', 'role:pimpinan'])
    ->prefix('pimpinan')
    ->name('pimpinan.')
    ->group(function () {

    // Dashboard Pimpinan
    Route::get('/dashboard', [PimpinanController::class, 'dashboard'])
        ->name('dashboard');

    // ==========================
    // CUTI PIMPINAN
    // ==========================

    // Daftar pengajuan cuti
    Route::get('/cuti', [PimpinanController::class, 'cutiIndex'])
        ->name('cuti.index');

    // Detail pengajuan cuti
    Route::get('/cuti/{id}', [PimpinanController::class, 'cutiShow'])
        ->name('cuti.show');

    // Approve cuti
    Route::post('/cuti/{id}/approve', [PimpinanController::class, 'cutiApprove'])
        ->name('cuti.approve');

    // Reject cuti
    Route::post('/cuti/{id}/reject', [PimpinanController::class, 'cutiReject'])
        ->name('cuti.reject');
});




// ===========================================
// ✅ 7. AUTHENTICATION ROUTES
// ===========================================
Auth::routes();
