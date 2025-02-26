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
Route::post('/cash-bank', [\App\Http\Controllers\Api\CashBankController::class, 'store'])->name('api.cash-banks.store');

// Contract
Route::get('/contract', [\App\Http\Controllers\Api\ContractController::class, 'index'])->name('api.contracts.index');
Route::get('/contract/datatables', [\App\Http\Controllers\Api\ContractController::class, 'datatables'])->name('api.contracts.datatables');
Route::post('/contract', [\App\Http\Controllers\Api\ContractController::class, 'store'])->name('api.contracts.store');

// Journal Entry
Route::get('/journal-entry', [\App\Http\Controllers\Api\JournalEntryController::class, 'index'])->name('api.journal-entries.index');
Route::get('/journal-entry/datatables', [\App\Http\Controllers\Api\JournalEntryController::class, 'datatables'])->name('api.journal-entries.datatables');
Route::post('/journal-entry', [\App\Http\Controllers\Api\JournalEntryController::class, 'store'])->name('api.journal-entries.store');

// Credit Note
Route::get('/credit-note', [\App\Http\Controllers\Api\CreditNoteController::class, 'index'])->name('api.credit-notes.index');
Route::get('/credit-note/datatables', [\App\Http\Controllers\Api\CreditNoteController::class, 'datatables'])->name('api.credit-notes.datatables');
Route::post('/credit-note', [\App\Http\Controllers\Api\CreditNoteController::class, 'store'])->name('api.credit-notes.store');

// Debit Note
Route::get('/debit-note', [\App\Http\Controllers\Api\DebitNoteController::class, 'index'])->name('api.debit-notes.index');
Route::get('/debit-note/datatables', [\App\Http\Controllers\Api\DebitNoteController::class, 'datatables'])->name('api.debit-notes.datatables');

// report prefix
Route::prefix('report')->group(function () {
    Route::get('/console', [\App\Http\Controllers\Api\Report\ConsoleReportController::class, 'index'])->name('api.report.console.index');

    Route::get('/balance-sheet', [\App\Http\Controllers\Api\Report\BalanceSheetController::class, 'index'])->name('api.report.balance-sheet.index');
});