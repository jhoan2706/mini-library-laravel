<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookWebController;
use App\Http\Controllers\LoanHistoryController;
use Illuminate\Support\Facades\Route;

// RUTA PRINCIPAL - redirige a dashboard si está autenticado
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    return view('welcome-guest');
})->name('home');

// AUTENTICACIÓN
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// DASHBOARD y acciones de biblioteca
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [BookWebController::class, 'index'])->name('dashboard');
    Route::get('/books/{book}', [BookWebController::class, 'show'])->name('books.show')->middleware('auth');
    Route::post('/books', [BookWebController::class, 'store'])->name('books.store');
    Route::get('/books/{book}/edit', [BookWebController::class, 'edit'])->name('books.edit');
    Route::put('/books/{book}', [BookWebController::class, 'update'])->name('books.update');
    Route::delete('/books/{book}', [BookWebController::class, 'destroy'])->name('books.destroy');
    Route::post('/books/{book}/checkout', [BookWebController::class, 'checkout'])->name('books.checkout');

    // DEVOLUCIONES - RUTAS DIFERENTES PARA CADA TIPO
    Route::get('/loans', [LoanHistoryController::class, 'index'])->name('loans.index');
    Route::post('/loans/{loan}/checkin-quick', [BookWebController::class, 'checkin'])->name('loans.checkin.quick');
    Route::get('/loans/{loan}/checkin-form', [BookWebController::class, 'showCheckinForm'])->name('loans.checkin.form');
    Route::post('/loans/{loan}/checkin-store', [BookWebController::class, 'processCheckin'])->name('loans.checkin.store');

    Route::middleware('can:admin-only')->group(function () {
        Route::get('/admin/roles', [AdminController::class, 'roles'])->name('admin.roles');
        Route::post('/admin/roles', [AdminController::class, 'assignRole'])->name('admin.assign-role');
    });
});
