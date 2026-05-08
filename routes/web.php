<?php

use Illuminate\Support\Facades\Route;

// CONTROLLER
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PengajuanController;

// =======================
// LOGIN
// =======================

Route::get('/login', [
    AuthController::class,
    'showLoginForm'
]);

Route::post('/login', [
    AuthController::class,
    'login'
]);

// =======================
// LOGOUT
// =======================

Route::post('/logout', [
    AuthController::class,
    'logout'
]);

// =======================
// DASHBOARD PEGAWAI
// =======================

Route::middleware('pegawai')
->group(function () {

    // DASHBOARD
    Route::get('/dashboard', function () {

        return view('pengajuan.dashboard');

    });

    // =======================
    // PENGAJUAN
    // =======================

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

});