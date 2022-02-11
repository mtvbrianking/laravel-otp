<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Auth::routes();

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::post('/toogle2fa', [HomeController::class, 'toogle2fa'])
	->middleware('password.confirm')
	->name('toogle.2fa');
