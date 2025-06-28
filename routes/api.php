<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserRequestController;
use App\Http\Controllers\Api\InformationController;
use App\Http\Controllers\Api\LaporanTypeController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/laporan-types', [LaporanTypeController::class, 'index']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Auth routes
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::post('/change-password', [AuthController::class, 'changePassword']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // User Request routes
    Route::prefix('requests')->group(function () {
        Route::get('/', [UserRequestController::class, 'index']);
        Route::get('/statistics', [UserRequestController::class, 'statistics']);
        Route::post('/', [UserRequestController::class, 'store']);
        Route::get('/{id}', [UserRequestController::class, 'show'])->middleware('user.owns.request');
    });

    // Public Information routes
    Route::prefix('information')->group(function () {
        Route::get('/', [InformationController::class, 'index']);
        Route::post('/', [InformationController::class, 'store']);
        Route::get('/{information}', [InformationController::class, 'show']);
        Route::put('/{information}', [InformationController::class, 'update']);
        Route::delete('/{information}', [InformationController::class, 'destroy']);
        Route::get('/laporan-type/{laporanTypeId}', [InformationController::class, 'getByLaporanType']);
    });
});
