<?php

use App\Http\Controllers\Post\PostController;
use Illuminate\Support\Facades\Route;

Route::apiResource('posts', PostController::class)
    ->parameter('posts', 'post:slug')
    ->middleware('auth:api');
