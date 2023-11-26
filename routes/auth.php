<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('register', [AuthController::class, 'register'])
    ->name('auth.register');

Route::post('login', [AuthController::class, 'login'])
    ->middleware('guest')
    ->name('auth.login');

Route::post('logout', [AuthController::class, 'logout'])
    ->middleware('auth:api')
    ->name('auth.logout');
