<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MidtransWebhookController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Rute ini nantinya bisa diakses via: domain-anda.com/api/midtrans-callback
Route::post('/midtrans-callback', [MidtransWebhookController::class, 'handle']);
