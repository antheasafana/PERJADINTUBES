<?php

use App\Http\Controllers\AuthController;

// =======================
// LOGIN
// =======================

Route::get('/',
    [AuthController::class,'showLoginForm']
);

Route::get('/login',
    [AuthController::class,'showLoginForm']
);

Route::post('/login',
    [AuthController::class,'login']
);

// =======================
// LOGOUT
// =======================

Route::post('/logout',
    [AuthController::class,'logout']
);

// =======================
// DASHBOARD PEGAWAI
// =======================

Route::middleware('pegawai')
->group(function(){

    Route::get('/dashboard',function(){

        return view('pegawai.dashboard');

    });

});