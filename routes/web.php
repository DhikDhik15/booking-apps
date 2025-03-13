<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;

Route::get('/', [OrderController::class, 'lists'])->name('orders.lists');
Route::resource('orders', OrderController::class);
