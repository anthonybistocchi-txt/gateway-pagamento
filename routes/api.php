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

Route::post('/login', [AuthController::class, 'login'])->name('login'); // ok
Route::post('/purchases', [TransactionController::class, 'store'])->name('purchases.store'); // ok

Route::middleware('auth:sanctum')->group(function () {

    Route::prefix('gateways')->middleware(CheckRole::class)->group(function () {
        Route::patch('/{id}/activate', [GatewayController::class, 'activate'])->name('gateways.activate');
        Route::patch('/{id}/deactivate', [GatewayController::class, 'deactivate'])->name('gateways.deactivate');
        Route::patch('/{id}/priority', [GatewayController::class, 'updatePriority'])->name('gateways.updatePriority');
    });

    Route::prefix('users')->middleware(CheckRole::class.':MANAGER')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('users.index'); // ok
        Route::post('/', [UserController::class, 'store'])->name('users.store'); // ok
        Route::patch('/{id}', [UserController::class, 'update'])->name('users.update'); // ok
        Route::delete('/{id}', [UserController::class, 'destroy'])->name('users.destroy'); // ok
    });

    Route::prefix('products')->middleware(CheckRole::class.':MANAGER,FINANCE')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('products.index'); // ok
        Route::post('/', [ProductController::class, 'store'])->name('products.store'); //ok
        Route::patch('/{id}', [ProductController::class, 'update'])->name('products.update'); //ok
        Route::delete('/{id}', [ProductController::class, 'destroy'])->name('products.destroy'); // ok
    });

    Route::prefix('clients')->middleware(CheckRole::class.':MANAGER,FINANCE')->group(function () {
        Route::get('/', [ClientController::class, 'index'])->name('clients.index'); // ok 
        Route::get('/{id}', [ClientController::class, 'details'])->name('clients.details'); // ok porem testar estorno para ve se atualiza na tabela
    });

    Route::prefix('purchases')->middleware(CheckRole::class.':FINANCE')->group(function () {
        Route::get('/', [PurchaseController::class, 'index'])->name('purchases.index'); // ok   
        Route::get('/{id}', [PurchaseController::class, 'details'])->name('purchases.details');  // ok
        Route::post('/{id}/refund', [TransactionController::class, 'refund'])->name('purchases.refund'); // ok porem testar dnv
    });
});