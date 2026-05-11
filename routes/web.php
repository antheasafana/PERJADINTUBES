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
    })->name('dashboard'); // ← TAMBAHAN NAMA ROUTE

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

});