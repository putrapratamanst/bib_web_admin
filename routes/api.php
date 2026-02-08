<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Protected API Routes
Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/account-category', [\App\Http\Controllers\Api\AccountCategoryController::class, 'index'])->name('api.account-categories.index');
    Route::get('/account-category/select2', [\App\Http\Controllers\Api\AccountCategoryController::class, 'select2'])->name('api.account-categories.select2');


Route::get('/chart-of-account', [\App\Http\Controllers\Api\ChartOfAccountController::class, 'index'])->name('api.chart-of-accounts.index');
//datatables
Route::get('/chart-of-account/datatables', [\App\Http\Controllers\Api\ChartOfAccountController::class, 'datatables'])->name('api.chart-of-accounts.datatables');
Route::get('/chart-of-account/select2', [\App\Http\Controllers\Api\ChartOfAccountController::class, 'select2'])->name('api.chart-of-accounts.select2');
Route::post('/chart-of-account', [\App\Http\Controllers\Api\ChartOfAccountController::class, 'store'])->name('api.chart-of-accounts.store');
Route::get('/chart-of-account/{id}', [\App\Http\Controllers\Api\ChartOfAccountController::class, 'show'])->name('api.chart-of-accounts.show');
Route::put('/chart-of-account/{id}', [\App\Http\Controllers\Api\ChartOfAccountController::class, 'update'])->name('api.chart-of-accounts.update');

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

// Currency
Route::get('/currency', [\App\Http\Controllers\Api\CurrencyController::class, 'index'])->name('api.currencies.index');
Route::get('/currency/datatables', [\App\Http\Controllers\Api\CurrencyController::class, 'datatables'])->name('api.currencies.datatables');
Route::get('/currency/select2', [\App\Http\Controllers\Api\CurrencyController::class, 'select2'])->name('api.currencies.select2');
Route::post('/currency', [\App\Http\Controllers\Api\CurrencyController::class, 'store'])->name('api.currencies.store');
Route::put('/currency/{currency}', [\App\Http\Controllers\Api\CurrencyController::class, 'update'])->name('api.currencies.update');
Route::delete('/currency/{currency}', [\App\Http\Controllers\Api\CurrencyController::class, 'destroy'])->name('api.currencies.destroy');

// Contract Type
Route::get('/contract-types', [\App\Http\Controllers\Api\ContractTypeController::class, 'index'])->name('api.contract-types.index');
Route::get('/contract-types/select2', [\App\Http\Controllers\Api\ContractTypeController::class, 'select2'])->name('api.contract-types.select2');

// Billing Address
Route::get('/contact/{contactId}/billing-address', [\App\Http\Controllers\Api\BillingAddressController::class, 'index'])->name('api.billing-addresses.index');
Route::get('/billing-address/select2', [\App\Http\Controllers\Api\BillingAddressController::class, 'select2'])->name('api.billing-addresses.select2');
Route::get('/billing-address/{id}', [\App\Http\Controllers\Api\BillingAddressController::class, 'show'])->name('api.billing-addresses.show');
Route::post('/billing-address', [\App\Http\Controllers\Api\BillingAddressController::class, 'store'])->name('api.billing-addresses.store');
Route::put('/billing-address/{id}', [\App\Http\Controllers\Api\BillingAddressController::class, 'update'])->name('api.billing-addresses.update');
Route::delete('/billing-address/{id}', [\App\Http\Controllers\Api\BillingAddressController::class, 'destroy'])->name('api.billing-addresses.destroy');
Route::post('/billing-address/{id}/set-primary', [\App\Http\Controllers\Api\BillingAddressController::class, 'setPrimary'])->name('api.billing-addresses.set-primary');

// Cash Bank
Route::get('/cash-bank', [\App\Http\Controllers\Api\CashBankController::class, 'index'])->name('api.cash-banks.index');
Route::get('/cash-bank/datatables', [\App\Http\Controllers\Api\CashBankController::class, 'datatables'])->name('api.cash-banks.datatables');
Route::get('/cash-bank/{id}', [\App\Http\Controllers\Api\CashBankController::class, 'show'])->name('api.cash-banks.show');
Route::post('/cash-bank', [\App\Http\Controllers\Api\CashBankController::class, 'store'])->name('api.cash-banks.store');
Route::put('/cash-bank/{id}', [\App\Http\Controllers\Api\CashBankController::class, 'update'])->name('api.cash-banks.update');


// Payment Allocation
Route::get('/payment-allocation', [\App\Http\Controllers\Api\PaymentAllocationController::class, 'index'])->name('api.payment-allocations.index');
Route::get('/payment-allocation/datatables', [\App\Http\Controllers\Api\PaymentAllocationController::class, 'datatables'])->name('api.payment-allocations.datatables');
Route::post('/payment-allocation', [\App\Http\Controllers\Api\PaymentAllocationController::class, 'store'])->name('api.payment-allocations.store');
Route::post('/payment-allocation/{cashbankID}', [\App\Http\Controllers\Api\PaymentAllocationController::class, 'storeByCashBankID'])->name('api.payment-allocations.storeByCashBankID');
Route::post('/payment-allocation/{cashbankID}/store-all', [\App\Http\Controllers\Api\PaymentAllocationController::class, 'storeAll'])->name('api.payment-allocations.storeAll');
Route::post('/payment-allocation/{cashbankID}/cashout', [\App\Http\Controllers\Api\PaymentAllocationController::class, 'storeByCashBankIDForCashout'])->name('api.payment-allocations.storeByCashBankIDForCashout');
Route::post('/payment-allocation/{cashbankID}/write-off', [\App\Http\Controllers\Api\PaymentAllocationController::class, 'writeOff'])->name('api.payment-allocations.writeOff');

// Advance
Route::get('/advances/datatables', [\App\Http\Controllers\Api\AdvanceController::class, 'datatables'])->name('api.advances.datatables');
Route::get('/advances/cash-bank/select2', [\App\Http\Controllers\Api\AdvanceController::class, 'cashBankSelect2'])->name('api.advances.cash-bank.select2');
Route::get('/advances/cash-bank/{id}', [\App\Http\Controllers\Api\AdvanceController::class, 'getCashBankDetail'])->name('api.advances.cash-bank.detail');
Route::post('/advances', [\App\Http\Controllers\Api\AdvanceController::class, 'store'])->name('api.advances.store');
Route::get('/advances/{id}', [\App\Http\Controllers\Api\AdvanceController::class, 'show'])->name('api.advances.show');
Route::delete('/advances/{id}', [\App\Http\Controllers\Api\AdvanceController::class, 'destroy'])->name('api.advances.destroy');

// Refund
Route::get('/refunds/datatables', [\App\Http\Controllers\Api\RefundController::class, 'datatables'])->name('api.refunds.datatables');
Route::get('/refunds/advance/select2', [\App\Http\Controllers\Api\RefundController::class, 'advanceSelect2'])->name('api.refunds.advance.select2');
Route::get('/refunds/advance/{id}', [\App\Http\Controllers\Api\RefundController::class, 'getAdvanceDetail'])->name('api.refunds.advance.detail');
Route::post('/refunds', [\App\Http\Controllers\Api\RefundController::class, 'store'])->name('api.refunds.store');
Route::get('/refunds/{id}', [\App\Http\Controllers\Api\RefundController::class, 'show'])->name('api.refunds.show');
Route::delete('/refunds/{id}', [\App\Http\Controllers\Api\RefundController::class, 'destroy'])->name('api.refunds.destroy');

// Contract
Route::get('/contract', [\App\Http\Controllers\Api\ContractController::class, 'index'])->name('api.contracts.index');
Route::get('/contract/datatables', [\App\Http\Controllers\Api\ContractController::class, 'datatables'])->name('api.contracts.datatables');
Route::get('/contract/select2', [\App\Http\Controllers\Api\ContractController::class, 'select2'])->name('api.contracts.select2');
Route::get('/contract/generate-number', [\App\Http\Controllers\Api\ContractController::class, 'generateNumber'])->name('api.contracts.generate-number');
Route::get('/contract/{id}', [\App\Http\Controllers\Api\ContractController::class, 'show'])->name('api.contracts.show');
Route::post('/contract', [\App\Http\Controllers\Api\ContractController::class, 'store'])->name('api.contracts.store');
Route::put('/contract/{id}', [\App\Http\Controllers\Api\ContractController::class, 'update'])->name('api.contracts.update');
Route::post('/contracts/{id}/approve', [\App\Http\Controllers\Api\ContractController::class, 'approve'])->name('api.contracts.approve');
Route::post('/contracts/{id}/reject', [\App\Http\Controllers\Api\ContractController::class, 'reject'])->name('api.contracts.reject');
Route::post('/contracts/add-unit/automobile/{contract}', [\App\Http\Controllers\Api\ContractController::class, 'storeAutomobileUnit'])->name('transaction.contracts.store-automobile-units');
Route::post('/contracts/add-unit/property/{contract}', [\App\Http\Controllers\Api\ContractController::class, 'storePropertyUnit'])->name('transaction.contracts.store-property-units');
Route::post('/contract/{id}/documents', [\App\Http\Controllers\Api\ContractController::class, 'uploadDocument'])->name('api.contracts.upload-document');
Route::get('/contract/{id}/documents', [\App\Http\Controllers\Api\ContractController::class, 'getDocuments'])->name('api.contracts.get-documents');
Route::delete('/contract/{contractId}/documents/{documentId}', [\App\Http\Controllers\Api\ContractController::class, 'deleteDocument'])->name('api.contracts.delete-document');
Route::get('/contract/{contractId}/documents/{documentId}/download', [\App\Http\Controllers\Api\ContractController::class, 'downloadDocument'])->name('api.contracts.download-document');


// Journal Entry
Route::get('/journal-entry', [\App\Http\Controllers\Api\JournalEntryController::class, 'index'])->name('api.journal-entries.index');
Route::get('/journal-entry/datatables', [\App\Http\Controllers\Api\JournalEntryController::class, 'datatables'])->name('api.journal-entries.datatables');
Route::get('/journal-entry/generate-number', [\App\Http\Controllers\Api\JournalEntryController::class, 'generateNumber'])->name('api.journal-entries.generate-number');
Route::post('/journal-entry', [\App\Http\Controllers\Api\JournalEntryController::class, 'store'])->name('api.journal-entries.store');

// Credit Note
Route::get('/credit-note', [\App\Http\Controllers\Api\CreditNoteController::class, 'index'])->name('api.credit-notes.index');
Route::get('/credit-note/datatables', [\App\Http\Controllers\Api\CreditNoteController::class, 'datatables'])->name('api.credit-notes.datatables');
Route::get('/credit-note/generate-number', [\App\Http\Controllers\Api\CreditNoteController::class, 'generateNumber'])->name('api.credit-notes.generate-number');
Route::post('/credit-note', [\App\Http\Controllers\Api\CreditNoteController::class, 'store'])->name('api.credit-notes.store');
Route::post('/credit-note/{id}/approve', [\App\Http\Controllers\Api\CreditNoteController::class, 'approve'])->name('api.credit-notes.approve');
Route::post('/credit-note/{id}/reject', [\App\Http\Controllers\Api\CreditNoteController::class, 'reject'])->name('api.credit-notes.reject');

// Debit Note
Route::get('/debit-note', [\App\Http\Controllers\Api\DebitNoteController::class, 'index'])->name('api.debit-notes.index');
Route::get('/debit-note/datatables', [\App\Http\Controllers\Api\DebitNoteController::class, 'datatables'])->name('api.debit-notes.datatables');
Route::post('/debit-note', [\App\Http\Controllers\Api\DebitNoteController::class, 'store'])->name('api.debit-notes.store');
Route::get('/debit-note/{id}', [\App\Http\Controllers\Api\DebitNoteController::class, 'show'])->name('api.debit-notes.show');
Route::put('/debit-note/{id}', [\App\Http\Controllers\Api\DebitNoteController::class, 'update'])->name('api.debit-notes.update');
Route::post('/debit-note/{id}/post', [\App\Http\Controllers\Api\DebitNoteController::class, 'postDebitNote'])->name('api.debit-notes.post');
Route::post('/debit-note/{id}/approve', [\App\Http\Controllers\Api\DebitNoteController::class, 'approve'])->name('api.debit-notes.approve');
Route::post('/debit-note/{id}/reject', [\App\Http\Controllers\Api\DebitNoteController::class, 'reject'])->name('api.debit-notes.reject');

// Debit Note Billing
Route::get('/debit-note-billing/datatables', [\App\Http\Controllers\Api\DebitNoteBillingController::class, 'datatables'])->name('api.billings.datatables');
Route::get('/debit-note-billing/select2', [\App\Http\Controllers\Api\DebitNoteBillingController::class, 'select2'])->name('api.debit-note-billings.select2');
Route::get('/debit-note-billing/{id}', [\App\Http\Controllers\Api\DebitNoteBillingController::class, 'show'])->name('api.debit-note-billings.show');
Route::post('/debit-note-billing/{id}/post-to-cashout', [\App\Http\Controllers\Api\DebitNoteBillingController::class, 'postToCashout'])->name('api.debit-note-billings.post-to-cashout');
Route::post('/debit-note-billing/{id}/post', [\App\Http\Controllers\Api\DebitNoteBillingController::class, 'postBilling'])->name('api.billings.post');
Route::get('/debit-note-billing/{id}/print', [\App\Http\Controllers\Api\DebitNoteBillingController::class, 'printBilling'])->name('api.billings.print');

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
    
    // Cashout Report
    Route::get('/cashouts', [\App\Http\Controllers\Api\ReportController::class, 'cashouts'])->name('api.reports.cashouts');
    
    // Account Statement Report
    Route::get('/account-statement', [\App\Http\Controllers\Api\ReportController::class, 'accountStatement'])->name('api.reports.account-statement');
});

}); // End of auth middleware group