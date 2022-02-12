<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Auth::routes();

Route::get('/', [HomeController::class, 'index'])->name('home');

// 0. Profile -> Security ...
// 1. Enable 2FA 
// 2. Show / Scan QR Code 
// 3. Enter OTP

Route::get('/2fa', [HomeController::class, 'show2faQrcode'])
    ->middleware('password.confirm')
    ->name('2fa.qrcode');

Route::post('/2fa', [HomeController::class, 'toogle2fa'])
    ->middleware('password.confirm')
    ->name('2fa.toogle');

Route::get('/2fa/otp', [HomeController::class, 'show2faOtp'])
    ->middleware('password.confirm')
    ->name('2fa.otp');

Route::post('/2fa/otp', [HomeController::class, 'verify2faOtp'])
    ->middleware('password.confirm')
    ->name('2fa.otp.verify');
