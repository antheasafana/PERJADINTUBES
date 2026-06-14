<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;

/*
|--------------------------------------------------------------------------
| CONTROLLER
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PengajuanController;
use App\Http\Controllers\TransaksiPengeluaranController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\VerifikasiController;
use App\Http\Controllers\RealisasiDanaController;
use App\Http\Controllers\PengirimanEmailController;
use App\Http\Controllers\RekomendasiPerjalananController; // <-- TAMBAHAN: Impor Controller Rekomendasi Perjalanan AI

/*
|--------------------------------------------------------------------------
| MAIL
|--------------------------------------------------------------------------
*/

use App\Mail\TesMail;
use App\Models\Pengajuan;

/*
|--------------------------------------------------------------------------
| LOGIN
|--------------------------------------------------------------------------
*/

Route::get('/login', [
    AuthController::class,
    'showLoginForm'
])->name('login');

Route::post('/login', [
    AuthController::class,
    'login'
])->name('login.process');

/*
|--------------------------------------------------------------------------
| LOGOUT
|--------------------------------------------------------------------------
*/

Route::post('/logout', [
    AuthController::class,
    'logout'
])->name('logout');

Route::get('/admin/verifikasi/pdf', [
    VerifikasiController::class,
    'exportPdf'
])->name('verifikasi.pdf');

/*
|--------------------------------------------------------------------------
| PEGAWAI
|--------------------------------------------------------------------------
*/

Route::middleware('pegawai')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD
    |--------------------------------------------------------------------------
    */

    Route::get('/dashboard', [
        PengajuanController::class,
        'dashboard'
    ])->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | PENGAJUAN
    |--------------------------------------------------------------------------
    */

    Route::get('/pengajuan', [
        PengajuanController::class,
        'index'
    ])->name('pengajuan.index');

    Route::get('/pengajuan/create', [
        PengajuanController::class,
        'create'
    ])->name('pengajuan.create');

    Route::post('/pengajuan/store', [
        PengajuanController::class,
        'store'
    ])->name('pengajuan.store');

    Route::get('/pengajuan/{id}/view', [
        PengajuanController::class,
        'show'
    ])->name('pengajuan.view');

    Route::get('/pengajuan/{id}/pdf', [
        PengajuanController::class,
        'exportPdf',
    ])->name('pengajuan.pdf');

    Route::get('/pengajuan/{id}/pdf-ringkas', [
        PengajuanController::class,
        'exportPengajuanRingkasPdf',
    ])->name('pengajuan.pdf.ringkas');

    Route::get('/pengajuan/{id}/realisasi-pdf', [
        PengajuanController::class,
        'exportRealisasiPdf',
    ])->name('pengajuan.realisasi.pdf');

    Route::get('/pengajuan/{id}/realisasi', [
        PengajuanController::class,
        'realisasiForm'
    ])->name('pengajuan.realisasi');

    Route::post('/pengajuan/{id}/realisasi', [
        PengajuanController::class,
        'realisasiStore'
    ])->name('pengajuan.realisasi.store');

    Route::get('/pengajuan/{id}/edit', [
        PengajuanController::class,
        'edit'
    ])->name('pengajuan.edit');

    Route::put('/pengajuan/{id}', [
        PengajuanController::class,
        'update'
    ])->name('pengajuan.update');

    Route::delete('/pengajuan/{id}', [
        PengajuanController::class,
        'destroy'
    ])->name('pengajuan.destroy');

    Route::get('/realisasi-dana', [
        PengajuanController::class,
        'realisasiIndex',
    ])->name('realisasi.index');

    Route::get('/pengeluaran', [
        TransaksiPengeluaranController::class,
        'index'
    ])->name('pengeluaran.index');

    Route::get('/pengeluaran/{id}', [
        TransaksiPengeluaranController::class,
        'show'
    ])->name('pengeluaran.show');

    Route::get('/pengeluaran/{id_pengajuan}/create', [
        TransaksiPengeluaranController::class,
        'create'
    ])->name('pengeluaran.create');

    Route::post('/pengeluaran/{id_pengajuan}/store', [
        TransaksiPengeluaranController::class,
        'store'
    ])->name('pengeluaran.store');

    Route::get('/realisasi/{id}/pdf', [
        RealisasiDanaController::class,
        'exportPdf'
    ]);
    
    // Proses pengiriman email (Sintaks dibersihkan)
    Route::get('/kirim_email_realisasi', [PengirimanEmailController::class, 'kirim_email_realisasi'])
    ->name('email.pembayaran');
    
    Route::post('/notifications/read-all', function () {
        auth()->user()->unreadNotifications->markAsRead();
        return back();
    })->name('notifications.readAll');

    Route::patch('/realisasi/{id}/batalkan', [PengajuanController::class, 'batalkan'])
    ->name('realisasi.batalkan');

    // Rute Pembaruan Rekomendasi AI
    Route::match(['get', 'post'], '/dashboard/refresh-rekomendasi-ai', [RekomendasiPerjalananController::class, 'generateRekomendasi'])
    ->name('dashboard.refreshRekomendasiAi');
});