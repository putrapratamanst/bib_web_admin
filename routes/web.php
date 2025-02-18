<?php

use Illuminate\Support\Facades\Route;

Route::get('/test', [App\Http\Controllers\TestController::class, 'index']);

Route::get("/", App\Livewire\Home::class)->name('home');

Route::get("/under-construction", App\Livewire\UnderConstruction::class)->name('under-construction');

Route::prefix('master')->group(function () {
    
    // company
    Route::get("/company", App\Livewire\Master\Company\Index::class)->name('company.index');
    Route::get("/company/create", App\Livewire\Master\Company\Create::class)->name('company.create');
    Route::get("/company/edit/{id}", App\Livewire\Master\Company\Edit::class)->name('company.edit');

    // bank
    Route::get("/bank", App\Livewire\Master\Bank\Index::class)->name('bank.index');
    Route::get("/bank/create", App\Livewire\Master\Bank\Create::class)->name('bank.create');
    Route::get("/bank/edit/{id}", App\Livewire\Master\Bank\Edit::class)->name('bank.edit');

    // contact
    Route::get("/contact", App\Livewire\Master\Contact\Index::class)->name('contact.index');
    Route::get("/contact/create", App\Livewire\Master\Contact\Create::class)->name('contact.create');
    Route::get("/contact/edit/{id}", App\Livewire\Master\Contact\Edit::class)->name('contact.edit');

    // Chart of Account
    Route::get("/coa", App\Livewire\Master\ChartOfAccount\Index::class)->name('coa.index');
    Route::get("/coa/create", App\Livewire\Master\ChartOfAccount\Create::class)->name('coa.create');
    Route::get("/coa/edit/{id}", App\Livewire\Master\ChartOfAccount\Edit::class)->name('coa.edit');
});

Route::prefix('transaction')->group(function () {
    // Route::get("cash-bank", App\Livewire\Transaction\CashBank\Index::class)->name('transaction.cash-bank.index');
    // Route::get("cash-bank/create_receive", App\Livewire\Transaction\CashBank\Create\Receive::class)->name('transaction.cash-bank.create_receive');

    Route::get("cash-bank", [App\Http\controllers\CashBankController::class, 'index'])->name('transaction.cash-bank.index');


    Route::get("contract", [App\Http\Controllers\Transaction\ContractController::class, 'index'])->name('transaction.contract.index');
    Route::get("contract/{id}", [App\Http\Controllers\Transaction\ContractController::class, 'show'])->name('transaction.contract.show');
    Route::get("contract/create", [App\Http\Controllers\Transaction\ContractController::class, 'create'])->name('transaction.contract.create');

    Route::get("billing", App\Livewire\Transaction\Billing\Index::class)->name('transaction.billing.index');

    // Route::get("billing", [App\Http\Controllers\Transaction\BillingController::class, 'index'])->name('transaction.billing.index');
    Route::get("billing/create/{contractId}", [App\Http\Controllers\Transaction\BillingController::class, 'create'])->name('transaction.billing.create');

    Route::get("credit-note", App\Livewire\Transaction\CreditNote\Index::class)->name('transaction.credit-note.index');

    Route::get("cash-transactions", [App\Http\Controllers\Transaction\CashTransactionController::class, 'index'])->name('transaction.cash-transaction.index');
    Route::get("cash-transactions/create", [App\Http\Controllers\Transaction\CashTransactionController::class, 'create'])->name('transaction.cash-transaction.create');

    Route::get("cash-transactions/{id}", [App\Http\Controllers\Transaction\CashTransactionController::class, 'show'])->name('transaction.cash-transaction.show');


    Route::get('payment-allocation', App\Livewire\Transaction\PaymentAllocation\Index::class)->name('transaction.payment-allocation.index');
    Route::get('payment-allocation/create', App\Livewire\Transaction\PaymentAllocation\Create::class)->name('transaction.payment-allocation.create');
});