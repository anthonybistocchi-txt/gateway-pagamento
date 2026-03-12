<?php

use App\Http\Controllers\AuthController;

use App\Http\Controllers\PurchaseController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/purchases', [PurchaseController::class, 'store'])->name('store');
