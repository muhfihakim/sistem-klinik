<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MidtransWebhookController;
use App\Http\Controllers\Api\AuthController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Rute ini nantinya bisa diakses via: domain-anda.com/api/midtrans-callback
Route::post('/midtrans-callback', [MidtransWebhookController::class, 'handle']);

// Jalur Login API (Tanpa Token)
Route::post('login', [AuthController::class, 'login']);

// Jalur yang Dilindungi JWT (Harus Pakai Token)
Route::middleware('auth:api')->group(function () {
    Route::get('me', [AuthController::class, 'me']);
    Route::post('logout', [AuthController::class, 'logout']);

    // Contoh Data Pasien untuk Demo Kuliah
    Route::get('data-pasien', function () {
        return response()->json([
            'status' => 'success',
            'data' => [
                ['id' => 1, 'nama' => 'Pasien A', 'diagnosa' => 'Flu Burung'],
                ['id' => 2, 'nama' => 'Pasien B', 'diagnosa' => 'Sehat'],
            ]
        ]);
    });
});
