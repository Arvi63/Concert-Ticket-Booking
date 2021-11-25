<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ConcertController;
use App\Http\Controllers\ConcertOrderController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::get('/concerts/{id}', [ConcertController::class, 'show']);

Route::post('/concerts/{id}/orders', [ConcertOrderController::class, 'store']);

Route::get('/orders/{confirmationNumber}', [OrderController::class, 'show'])->name('orders.show');

Route::post('/login', [LoginController::class, 'login']);

// Route::get('/backstage/concerts',[])
