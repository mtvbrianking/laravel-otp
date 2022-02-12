<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Auth::routes();

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::group(['prefix' => '/2fa', 'as' => '2fa.', 'middleware' => 'password.confirm'], function () {
    // Toogle 2FA ON | OFF
    Route::post('/', [HomeController::class, 'toogle2fa'])->name('toogle');
    // Show QR Code
    Route::get('/qrcode', [HomeController::class, 'show2faQrcode'])->name('qrcode');
    // Show enter OTP
    Route::get('/otp', [HomeController::class, 'show2faOtp'])->name('otp');
    // Validate OTP
    Route::post('/otp', [HomeController::class, 'verify2faOtp'])->name('otp.verify');
});