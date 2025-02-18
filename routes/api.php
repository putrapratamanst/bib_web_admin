<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::prefix('transaction')->group(function () {
    Route::get('contract', [App\Http\Controllers\Api\Transaction\ContractController::class, 'index'])->name('api.transaction.contract.index');

    Route::post('contract', [App\Http\Controllers\Api\Transaction\ContractController::class, 'store'])->name('api.transaction.contract.store');

    Route::post('billing', [App\Http\Controllers\Api\Transaction\BillingController::class, 'store'])->name('api.transaction.billing.store');

    Route::get('cash-transaction', [App\Http\Controllers\Api\Transaction\CashTransactionController::class, 'index'])->name('api.transaction.cash-transaction.index');

    Route::post('cash-transaction', [App\Http\Controllers\Api\Transaction\CashTransactionController::class, 'store'])->name('api.transaction.cash-transaction.store');
});

// resource api
Route::apiResource('cash-bank', App\Http\Controllers\Api\CashBankController::class);
