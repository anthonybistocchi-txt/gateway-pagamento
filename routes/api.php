<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\GatewayController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\TransactionController;
use App\Http\Middleware\CheckRole; 
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/purchases', [TransactionController::class, 'store'])->name('purchases.store');

Route::middleware('auth:sanctum')->group(function () {

    Route::prefix('gateways')->middleware(CheckRole::class)->group(function () {
        Route::patch('/{id}/activate', [GatewayController::class, 'activate'])->name('gateways.activate');
        Route::patch('/{id}/deactivate', [GatewayController::class, 'deactivate'])->name('gateways.deactivate');
        Route::patch('/{id}/priority', [GatewayController::class, 'updatePriority'])->name('gateways.updatePriority');
    });

    Route::prefix('users')->middleware(CheckRole::class.':MANAGER')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('users.index');
        Route::post('/', [UserController::class, 'store'])->name('users.store');
        Route::patch('/{id}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/{id}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    Route::prefix('products')->middleware(CheckRole::class.':MANAGER,FINANCE')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('products.index');
        Route::post('/', [ProductController::class, 'store'])->name('products.store');
        Route::patch('/{id}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/{id}', [ProductController::class, 'destroy'])->name('products.destroy');
    });

    Route::prefix('clients')->group(function () {
        Route::get('/', [ClientController::class, 'index'])->name('clients.index');
        Route::get('/{id}', [ClientController::class, 'details'])->name('clients.details'); 
    });

    Route::prefix('purchases')->group(function () {
        Route::get('/', [PurchaseController::class, 'index'])->name('purchases.index');
        Route::get('/{id}', [PurchaseController::class, 'show'])->name('purchases.details'); 
        Route::post('/{id}/refund', [TransactionController::class, 'refund'])->name('purchases.refund')->middleware(CheckRole::class.':FINANCE'); 
    });
});