<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Auth::routes();

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/2fa', [HomeController::class, 'show2faQrcode'])
	->middleware('password.confirm')
	->name('2fa.qrcode');

Route::post('/2fa', [HomeController::class, 'toogle2fa'])
	->middleware('password.confirm')
	->name('2fa.toogle');