<?php

use App\Http\Controllers\Api\V1\BookController;
use App\Http\Controllers\Api\V1\LoanController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('v1')->group(function () {
    // ✅ ASIGNAR NOMBRES ÚNICOS A LAS RUTAS DE LA API
    Route::apiResource('books', BookController::class)->names([
        'index' => 'api.books.index',
        'store' => 'api.books.store',
        'show' => 'api.books.show',
        'update' => 'api.books.update',
        'destroy' => 'api.books.destroy',
    ]);

    Route::post('books/{book}/generate-metadata', [BookController::class, 'generateMetadata'])->name('api.books.generate-metadata');
    Route::post('copies/{copy}/check-out', [LoanController::class, 'checkOut'])->name('api.copies.checkout');
    Route::post('loans/{loan}/check-in', [LoanController::class, 'checkIn'])->name('api.loans.checkin');
});