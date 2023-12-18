<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Config\ConfigController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Spending\AccountController;
use App\Http\Controllers\Spending\TransactionCategoryController;
use App\Http\Controllers\Spending\TransactionController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,2');

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'getUser']);

    Route::prefix('/spending')->group(function () {
        Route::prefix('/accounts')->group(function () {
            Route::get('/', [AccountController::class, 'index']);
            Route::post('/', [AccountController::class, 'store']);
            Route::get('/{account}', [AccountController::class, 'show']);
            Route::put('/{account}', [AccountController::class, 'update']);
            Route::delete('/{account}', [AccountController::class, 'destroy']);
        });

        Route::prefix('/transaction-categories')->group(function () {
            Route::get('/', [TransactionCategoryController::class, 'index']);
            Route::post('/', [TransactionCategoryController::class, 'store']);
            Route::get('/{transactionCategory}', [TransactionCategoryController::class, 'show']);
            Route::put('/{transactionCategory}', [TransactionCategoryController::class, 'update']);
            Route::delete('/{transactionCategory}', [TransactionCategoryController::class, 'destroy']);
        });

        Route::prefix('/transactions')->group(function () {
            Route::get('/', [TransactionController::class, 'index']);
            Route::post('/', [TransactionController::class, 'store']);
            Route::get('/{transaction}', [TransactionController::class, 'show']);
            Route::put('/{transaction}', [TransactionController::class, 'update']);
            Route::delete('/{transaction}', [TransactionController::class, 'destroy']);
        });

        Route::get('/actual-balances', [ConfigController::class, 'getSpendingActualBalances']);
        Route::get('/settings', [ConfigController::class, 'getSpendingSettings']);
        Route::post('/settings', [ConfigController::class, 'updateSpendingSettings']);
    });

    Route::prefix('/dashboard')->group(function () {
        Route::post('/spending-data', [DashboardController::class, 'getSpendingData']);
    });
});
