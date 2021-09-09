<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\{
    LoginController
};

/*
  |--------------------------------------------------------------------------
  | API Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register API routes for your application. These
  | routes are loaded by the RouteServiceProvider within a group which
  | is assigned the "api" middleware group. Enjoy building your API!
  |
 */



Route::group(['prefix' => 'v1', 'namespace' => 'Api/v1'], function () {
    Route::post('/register', [LoginController::class, 'register']);
    Route::post('/verify-otp', [LoginController::class, 'verifyOtp']);
    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/forget-password', [LoginController::class, 'forgetPassword']);
    Route::post('/resend-otp', [LoginController::class, 'resendOtp']);
    Route::post('/reset-password', [LoginController::class, 'resetPassword']);
    Route::group(['middleware' => 'is_user_active'], function () {
        Route::group(['middleware' => 'auth:sanctum'], function () {
            Route::post('/logout', [LoginController::class, 'logout']);
        });
    });
});
