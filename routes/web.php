<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Backstage\ConcertsController;
use App\Http\Controllers\ConcertController;
use App\Http\Controllers\ConcertOrderController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return 'Laravel';
});

Route::get('/concerts/{id}', [ConcertController::class, 'show'])->name('concerts.show');

Route::post('/concerts/{id}/orders', [ConcertOrderController::class, 'store']);

Route::get('/orders/{confirmationNumber}', [OrderController::class, 'show'])->name('orders.show');

Route::post('/login', [LoginController::class, 'login'])->name('auth.login');
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
// Route::post('/logout', [LoginController::class, 'logout'])->name('auth.logout');

Route::middleware(['auth'])->prefix('backstage')->namespace('Backstage')->group(function () {
    Route::get('/concerts/', [ConcertsController::class, 'index'])->name('backstage.concerts.index');
    Route::get('/concerts/new', [ConcertsController::class, 'create']);
    Route::post('/concerts/', [ConcertsController::class, 'store']);
    Route::get('/concerts/{id}/edit', [ConcertsController::class, 'edit'])->name('backstage.concerts.edit');
    Route::patch('/concerts/{id}', [ConcertsController::class, 'update'])->name('backstage.concerts.update');
});
