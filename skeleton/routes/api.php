<?php

use Illuminate\Support\Facades\Route;
// use Vendor\Name\Http\Controllers\Api\V1\AuthController;

Route::prefix('api')->group(function () {
    Route::prefix('v1')->group(function () {

        // Route::post('login', [AuthController::class, 'login']);
        // Route::post('register', [AuthController::class, 'register']);
    });
});
