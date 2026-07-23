<?php

use App\Http\Controllers\Api\V1\BookController;
use App\Http\Controllers\Api\V1\LoanController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    Route::apiResource('books', BookController::class);
    Route::post('copies/{copy}/check-out', [LoanController::class, 'checkOut']);
    Route::post('loans/{loan}/check-in', [LoanController::class, 'checkIn']);
});
