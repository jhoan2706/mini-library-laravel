<?php

use App\Http\Controllers\BookWebController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// ✅ RUTA PRINCIPAL - redirige a dashboard si está autenticado
Route::get('/', function () {
    if (auth()->check()) {
        return redirect('/dashboard');
    }
    return view('welcome-guest');
})->name('home');

// ✅ DASHBOARD - solo para autenticados
Route::get('/dashboard', [BookWebController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard');

// ✅ AUTENTICACIÓN
Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ✅ RUTAS PROTEGIDAS
Route::middleware(['auth'])->group(function () {
    Route::post('/books', [BookWebController::class, 'store']);
});