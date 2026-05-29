<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuratKeluarController;
use App\Http\Controllers\SuratMasukController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Surat Keluar API Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('surat-keluar', SuratKeluarController::class);
});

// Surat Masuk API Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('surat-masuk/{id}', [SuratMasukController::class, 'show']);
    Route::get('surat-masuk', [SuratMasukController::class, 'index']);
    Route::get('surat-masuk/welcome', [SuratMasukController::class, 'welcome']);
});
