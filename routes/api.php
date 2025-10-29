<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/account-category', [\App\Http\Controllers\Api\AccountCategoryController::class, 'index'])->name('api.account-categories.index');
Route::get('/account-category/select2', [\App\Http\Controllers\Api\AccountCategoryController::class, 'select2'])->name('api.account-categories.select2');


Route::get('/chart-of-account', [\App\Http\Controllers\Api\ChartOfAccountController::class, 'index'])->name('api.chart-of-accounts.index');
//datatables
Route::get('/chart-of-account/datatables', [\App\Http\Controllers\Api\ChartOfAccountController::class, 'datatables'])->name('api.chart-of-accounts.datatables');
Route::get('/chart-of-account/select2', [\App\Http\Controllers\Api\ChartOfAccountController::class, 'select2'])->name('api.chart-of-accounts.select2');
Route::post('/chart-of-account', [\App\Http\Controllers\Api\ChartOfAccountController::class, 'store'])->name('api.chart-of-accounts.store');

// Contact Group
Route::get('/contact-group', [\App\Http\Controllers\Api\ContactGroupController::class, 'index'])->name('api.contact-groups.index');
Route::get('/contact-group/datatables', [\App\Http\Controllers\Api\ContactGroupController::class, 'datatables'])->name('api.contact-groups.datatables');
Route::get('/contact-group/select2', [\App\Http\Controllers\Api\ContactGroupController::class, 'select2'])->name('api.contact-groups.select2');
Route::post('/contact-group', [\App\Http\Controllers\Api\ContactGroupController::class, 'store'])->name('api.contact-groups.store');

// Contact
Route::get('/contact', [\App\Http\Controllers\Api\ContactController::class, 'index'])->name('api.contacts.index');
Route::get('/contact/datatables', [\App\Http\Controllers\Api\ContactController::class, 'datatables'])->name('api.contacts.datatables');
Route::get('/contact/select2', [\App\Http\Controllers\Api\ContactController::class, 'select2'])->name('api.contacts.select2');
Route::post('/contact', [\App\Http\Controllers\Api\ContactController::class, 'store'])->name('api.contacts.store');
Route::put('/contact/{id}', [\App\Http\Controllers\Api\ContactController::class, 'update'])->name('api.contacts.update');

// Cash Bank
Route::get('/cash-bank', [\App\Http\Controllers\Api\CashBankController::class, 'index'])->name('api.cash-banks.index');
Route::get('/cash-bank/datatables', [\App\Http\Controllers\Api\CashBankController::class, 'datatables'])->name('api.cash-banks.datatables');
Route::get('/cash-bank/{id}', [\App\Http\Controllers\Api\CashBankController::class, 'show'])->name('api.cash-banks.show');
Route::post('/cash-bank', [\App\Http\Controllers\Api\CashBankController::class, 'store'])->name('api.cash-banks.store');


// Payment Allocation
Route::get('/payment-allocation', [\App\Http\Controllers\Api\PaymentAllocationController::class, 'index'])->name('api.payment-allocations.index');
Route::get('/payment-allocation/datatables', [\App\Http\Controllers\Api\PaymentAllocationController::class, 'datatables'])->name('api.payment-allocations.datatables');
Route::post('/payment-allocation', [\App\Http\Controllers\Api\PaymentAllocationController::class, 'store'])->name('api.payment-allocations.store');

// Contract
Route::get('/contract', [\App\Http\Controllers\Api\ContractController::class, 'index'])->name('api.contracts.index');
Route::get('/contract/datatables', [\App\Http\Controllers\Api\ContractController::class, 'datatables'])->name('api.contracts.datatables');
Route::get('/contract/select2', [\App\Http\Controllers\Api\ContractController::class, 'select2'])->name('api.contracts.select2');
Route::get('/contract/{id}', [\App\Http\Controllers\Api\ContractController::class, 'show'])->name('api.contracts.show');
Route::post('/contract', [\App\Http\Controllers\Api\ContractController::class, 'store'])->name('api.contracts.store');
Route::post('/contracts/add-unit/automobile/{contract}', [\App\Http\Controllers\Api\ContractController::class, 'storeAutomobileUnit'])->name('transaction.contracts.store-automobile-units');
Route::post('/contracts/add-unit/property/{contract}', [\App\Http\Controllers\Api\ContractController::class, 'storePropertyUnit'])->name('transaction.contracts.store-property-units');


// Journal Entry
Route::get('/journal-entry', [\App\Http\Controllers\Api\JournalEntryController::class, 'index'])->name('api.journal-entries.index');
Route::get('/journal-entry/datatables', [\App\Http\Controllers\Api\JournalEntryController::class, 'datatables'])->name('api.journal-entries.datatables');
Route::post('/journal-entry', [\App\Http\Controllers\Api\JournalEntryController::class, 'store'])->name('api.journal-entries.store');

// Credit Note
Route::get('/credit-note', [\App\Http\Controllers\Api\CreditNoteController::class, 'index'])->name('api.credit-notes.index');
Route::get('/credit-note/datatables', [\App\Http\Controllers\Api\CreditNoteController::class, 'datatables'])->name('api.credit-notes.datatables');
Route::get('/credit-note/generate-number', [\App\Http\Controllers\Api\CreditNoteController::class, 'generateNumber'])->name('api.credit-notes.generate-number');
Route::post('/credit-note', [\App\Http\Controllers\Api\CreditNoteController::class, 'store'])->name('api.credit-notes.store');

// Debit Note
Route::get('/debit-note', [\App\Http\Controllers\Api\DebitNoteController::class, 'index'])->name('api.debit-notes.index');
Route::get('/debit-note/datatables', [\App\Http\Controllers\Api\DebitNoteController::class, 'datatables'])->name('api.debit-notes.datatables');
Route::post('/debit-note', [\App\Http\Controllers\Api\DebitNoteController::class, 'store'])->name('api.debit-notes.store');
Route::get('/debit-note/{id}', [\App\Http\Controllers\Api\DebitNoteController::class, 'show'])->name('api.debit-notes.show');
Route::post('/debit-note/{id}/post', [\App\Http\Controllers\Api\DebitNoteController::class, 'postDebitNote'])->name('api.debit-notes.post');

// Debit Note Billing
Route::get('/debit-note-billing/datatables', [\App\Http\Controllers\Api\DebitNoteBillingController::class, 'datatables'])->name('api.debit-note-billings.datatables');
Route::get('/debit-note-billing/select2', [\App\Http\Controllers\Api\DebitNoteBillingController::class, 'select2'])->name('api.debit-note-billings.select2');
Route::get('/debit-note-billing/{id}', [\App\Http\Controllers\Api\DebitNoteBillingController::class, 'show'])->name('api.debit-note-billings.show');
Route::post('/debit-note-billing/{id}/post-to-cashout', [\App\Http\Controllers\Api\DebitNoteBillingController::class, 'postToCashout'])->name('api.debit-note-billings.post-to-cashout');

// Cashout
Route::get('/cashout', [\App\Http\Controllers\Api\CashoutController::class, 'index'])->name('api.cashouts.index');
Route::get('/cashout/datatables', [\App\Http\Controllers\Api\CashoutController::class, 'datatables'])->name('api.cashouts.datatables');
Route::get('/cashout/{id}', [\App\Http\Controllers\Api\CashoutController::class, 'show'])->name('api.cashouts.show');
Route::put('/cashout/{id}', [\App\Http\Controllers\Api\CashoutController::class, 'update'])->name('api.cashouts.update');
Route::post('/cashout/{id}/mark-paid', [\App\Http\Controllers\Api\CashoutController::class, 'markAsPaid'])->name('api.cashouts.mark-paid');
Route::post('/cashout/{id}/mark-cancelled', [\App\Http\Controllers\Api\CashoutController::class, 'mark-cancelled'])->name('api.cashouts.mark-cancelled');

// report prefix
Route::prefix('report')->group(function () {
    Route::get('/console', [\App\Http\Controllers\Api\Report\ConsoleReportController::class, 'index'])->name('api.report.console.index');
    Route::get('/piutang', [\App\Http\Controllers\Api\Report\PiutangReportController::class, 'index'])->name('api.report.piutang.index');
    Route::get('/cashout', [\App\Http\Controllers\Api\Report\CashoutReportController::class, 'index'])->name('api.report.cashout.index');
    Route::get('/cashout-reconciliation', [\App\Http\Controllers\Api\Report\CashoutReportController::class, 'reconciliation'])->name('api.report.cashout-reconciliation.index');

    Route::get('/balance-sheet', [\App\Http\Controllers\Api\Report\BalanceSheetController::class, 'index'])->name('api.report.balance-sheet.index');
    
    // Debit Note Report
    Route::get('/debit-notes', [\App\Http\Controllers\Api\ReportController::class, 'debitNotes'])->name('api.reports.debit-notes');
});