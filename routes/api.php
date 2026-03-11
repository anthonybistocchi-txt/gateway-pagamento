<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PurchaseController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/newPurchase', [PurchaseController::class, 'newPurchase'])->name('newPurchase');
