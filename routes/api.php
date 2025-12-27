<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/refresh', [AuthController::class, 'refresh'])->middleware('refreshJwt');



Route::middleware('jwt')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
});
