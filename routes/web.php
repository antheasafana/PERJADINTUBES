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

    Route::get('/pegawai', function () {
        return redirect()->route('dashboard');
    })->name('pegawai.dashboard');

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

    Route::get('/pengajuan/{id}/edit', [
        PengajuanController::class,
        'edit'
    ])->name('pengajuan.edit');

    Route::put('/pengajuan/{id}', [
        PengajuanController::class,
        'update'
    ])->name('pengajuan.update');

    /*
    |--------------------------------------------------------------------------
    | PENGELUARAN
    |--------------------------------------------------------------------------
    */

    Route::get('/pengeluaran', [
        TransaksiPengeluaranController::class,
        'index'
    ])->name('pengeluaran.index');

    Route::get('/pengeluaran/create/{id_pengajuan}', [
        TransaksiPengeluaranController::class,
        'create'
    ])->name('pengeluaran.create');

    Route::post('/pengeluaran/store/{id_pengajuan}', [
        TransaksiPengeluaranController::class,
        'store'
    ])->name('pengeluaran.store');

    Route::get('/pengeluaran/{id}', [
        TransaksiPengeluaranController::class,
        'show'
    ])->name('pengeluaran.show');

    /*
    |--------------------------------------------------------------------------
    | PEMBAYARAN PEGAWAI
    |--------------------------------------------------------------------------
    */

    Route::get('/pembayaran', [
        PembayaranController::class,
        'index'
    ])->name('pembayaran.index');

});

/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
*/

Route::middleware('admin')
    ->prefix('admin')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | VERIFIKASI PENGAJUAN
        |--------------------------------------------------------------------------
        */

        Route::get('/verifikasi', [
            VerifikasiController::class,
            'index'
        ])->name('verifikasi.index');

        Route::get('/verifikasi/{id}', [
            VerifikasiController::class,
            'show'
        ])->name('verifikasi.show');

        Route::post('/verifikasi/{id}/approve', [
            VerifikasiController::class,
            'approve'
        ])->name('verifikasi.approve');

        Route::post('/verifikasi/{id}/reject', [
            VerifikasiController::class,
            'reject'
        ])->name('verifikasi.reject');

        /*
        |--------------------------------------------------------------------------
        | DETAIL PENGELUARAN
        |--------------------------------------------------------------------------
        */

        Route::get('/pengeluaran/{id}', [
            TransaksiPengeluaranController::class,
            'showAdmin'
        ])->name('admin.pengeluaran.show');

        /*
        |--------------------------------------------------------------------------
        | APPROVE / REJECT PENGELUARAN
        |--------------------------------------------------------------------------
        */

        Route::post('/pengeluaran/{id}/verifikasi', [
            TransaksiPengeluaranController::class,
            'verifikasi'
        ])->name('pengeluaran.verifikasi');

        /*
        |--------------------------------------------------------------------------
        | PEMBAYARAN
        |--------------------------------------------------------------------------
        */

        Route::post('/pengeluaran/{id}/pembayaran', [
            TransaksiPengeluaranController::class,
            'pembayaran'
        ])->name('pengeluaran.pembayaran');
    });

/*
|--------------------------------------------------------------------------
| TEST EMAIL MAILTRAP
|--------------------------------------------------------------------------
*/

Route::get('/tesemail', function () {

    $pengajuan = Pengajuan::latest()->first();

    if (!$pengajuan) {
        return "Tidak ada data pengajuan untuk testing email.";
    }

    Mail::to('test@mailtrap.io')->send(
        new TesMail($pengajuan)
    );

    return "Email berhasil dikirim ke Mailtrap!";
});