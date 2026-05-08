<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PengajuanController;

Route::get('/pengajuan', [PengajuanController::class, 'index'])->name('pengajuan.index');

Route::get('/pengajuan/create', [PengajuanController::class, 'create'])->name('pengajuan.create');

Route::post('/pengajuan/store', [PengajuanController::class, 'store'])->name('pengajuan.store');