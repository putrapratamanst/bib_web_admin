<?php

use App\Http\Controllers\BalanceController;
use App\Http\Controllers\CashFlowController;
use App\Http\Controllers\ProfitAndLossController;
use Illuminate\Support\Facades\Route;

// Authentication Routes
Route::get('/login', [\App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [\App\Http\Controllers\Auth\LoginController::class, 'login']);
Route::post('/logout', [\App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/', function () {
        return view('home');
    })->name('home');

    Route::prefix('master')->group(function () {
    // Chart of Account
    Route::get('/chart-of-accounts', [\App\Http\Controllers\Master\ChartOfAccountController::class, 'index'])->name('master.chart-of-accounts.index');
    Route::get('/chart-of-accounts/create', [\App\Http\Controllers\Master\ChartOfAccountController::class, 'create'])->name('master.chart-of-accounts.create');

    // Contact Group
    Route::get('/contact-groups', [\App\Http\Controllers\Master\ContactGroupController::class, 'index'])->name('master.contact-groups.index');
    Route::get('/contact-groups/create', [\App\Http\Controllers\Master\ContactGroupController::class, 'create'])->name('master.contact-groups.create');

    // Contact
    Route::get('/contacts', [\App\Http\Controllers\Master\ContactController::class, 'index'])->name('master.contacts.index');
    Route::get('/contacts/create', [\App\Http\Controllers\Master\ContactController::class, 'create'])->name('master.contacts.create');
    Route::get('/contacts/{id}', [\App\Http\Controllers\Master\ContactController::class, 'show'])->name('master.contacts.show');
});

// route group transaction
Route::prefix('transaction')->group(function () {
    Route::get('/cash-banks', [\App\Http\Controllers\Transaction\CashBankController::class, 'index'])->name('transaction.cash-banks.index');
    Route::get('/cash-banks/create', [\App\Http\Controllers\Transaction\CashBankController::class, 'create'])->name('transaction.cash-banks.create');
    Route::get('/cash-banks/{id}', [\App\Http\Controllers\Transaction\CashBankController::class, 'show'])->name('transaction.cash-banks.show');
    Route::get('/cash-banks/{id}/print', [\App\Http\Controllers\Transaction\CashBankController::class, 'print'])->name('transaction.cash-banks.print');

    // Contract
    Route::get('/contracts', [\App\Http\Controllers\Transaction\ContractController::class, 'index'])->name('transaction.contracts.index');
    Route::get('/contracts/create', [\App\Http\Controllers\Transaction\ContractController::class, 'create'])->name('transaction.contracts.create');
    Route::get('/contracts/{id}', [\App\Http\Controllers\Transaction\ContractController::class, 'show'])->name('transaction.contracts.show');
    Route::get('/contracts/{id}/edit', [\App\Http\Controllers\Transaction\ContractController::class, 'edit'])->name('transaction.contracts.edit');
    Route::get('/contracts/add-unit/automobile/{id} ', [\App\Http\Controllers\Transaction\ContractController::class, 'showAddUnit'])->name('transaction.contracts.show-add-unit');
    Route::get('/contracts/add-unit/property/{id} ', [\App\Http\Controllers\Transaction\ContractController::class, 'showAddProperty'])->name('transaction.contracts.show-add-property');

    // Journal Entry
    Route::get('/journal-entries', [\App\Http\Controllers\Transaction\JournalEntryController::class, 'index'])->name('transaction.journal-entries.index');
    Route::get('/journal-entries/create', [\App\Http\Controllers\Transaction\JournalEntryController::class, 'create'])->name('transaction.journal-entries.create');
    Route::get('/journal-entries/{id}', [\App\Http\Controllers\Transaction\JournalEntryController::class, 'show'])->name('transaction.journal-entries.show');
    Route::get('/journal-entries/{id}/print', [\App\Http\Controllers\Transaction\JournalEntryController::class, 'print'])->name('transaction.journal-entries.print');

    // Credit Note
    Route::get(
        '/credit-notes',
        [\App\Http\Controllers\Transaction\CreditNoteController::class, 'index']
    )->name('transaction.credit-notes.index');
    Route::get(
        '/credit-notes/create',
        [\App\Http\Controllers\Transaction\CreditNoteController::class, 'create']
    )->name('transaction.credit-notes.create');
    Route::get(
        '/credit-notes/{id}',
        [\App\Http\Controllers\Transaction\CreditNoteController::class, 'show']
    )->name('transaction.credit-notes.show');

    // Debit Note
    Route::get(
        '/debit-notes',
        [\App\Http\Controllers\Transaction\DebitNoteController::class, 'index']
    )->name('transaction.debit-notes.index');
    Route::get('/debit-notes/create', 
    [\App\Http\Controllers\Transaction\DebitNoteController::class, 'create'])->name('transaction.debit-notes.create');
    Route::post('/debit-notes', 
    [\App\Http\Controllers\Transaction\DebitNoteController::class, 'store'])->name('transaction.debit-notes.store');
    Route::get(
        '/debit-notes/{id}',
        [\App\Http\Controllers\Transaction\DebitNoteController::class, 'show']
    )->name('transaction.debit-notes.show');

    Route::get('/debit-notes/{id}/billing', [\App\Http\Controllers\Transaction\DebitNoteBillingController::class, 'create'])->name('transaction.debit-notes-billing.create');
    Route::post('/debit-notes/{id}/billing', [\App\Http\Controllers\Transaction\DebitNoteBillingController::class, 'store'])->name('transaction.debitnotebillings.store');
    
    // Debit Note Billings List
    Route::get('/debit-note-billings', [\App\Http\Controllers\Transaction\DebitNoteBillingController::class, 'index'])->name('transaction.debit-note-billings.index');
    Route::post('/debit-note-billings/{id}/post-to-cashout', [\App\Http\Controllers\Transaction\DebitNoteBillingController::class, 'postToCashout'])->name('transaction.debit-note-billings.post-to-cashout');
    Route::get('/billings', [\App\Http\Controllers\Transaction\DebitNoteBillingController::class, 'index'])->name('transaction.billings.index');
    Route::get('/billings/print/{id}', [\App\Http\Controllers\Transaction\DebitNoteBillingController::class, 'printBilling'])->name('transaction.billings.print');
    Route::get('/billings/{id}', [\App\Http\Controllers\Transaction\DebitNoteBillingController::class, 'show'])->name('transaction.billings.show');
    Route::get('/billings/create', [\App\Http\Controllers\Transaction\DebitNoteBillingController::class, 'create'])->name('transaction.billings.create');

    Route::get('/payment-allocations', [\App\Http\Controllers\Transaction\PaymentAllocationController::class, 'index'])->name('transaction.payment-allocations.index');
    Route::get('/payment-allocations/create/{cashbankID}', [\App\Http\Controllers\Transaction\PaymentAllocationController::class, 'create'])->name('transaction.payment-allocations.create');
    Route::get('/payment-allocations/{id}', [\App\Http\Controllers\Transaction\PaymentAllocationController::class, 'show'])->name('transaction.payment-allocations.show');
    Route::post('/payment-allocations/post/{id}', [\App\Http\Controllers\Transaction\PaymentAllocationController::class, 'post'])->name('transaction.payment-allocations.post');

    // Advances
    Route::get('/advances', [\App\Http\Controllers\Transaction\AdvanceController::class, 'index'])->name('transaction.advances.index');
    Route::get('/advances/create', [\App\Http\Controllers\Transaction\AdvanceController::class, 'create'])->name('transaction.advances.create');
    Route::get('/advances/{id}', [\App\Http\Controllers\Transaction\AdvanceController::class, 'show'])->name('transaction.advances.show');

    // Cashouts
    Route::get('/cashouts', [\App\Http\Controllers\Transaction\CashoutController::class, 'index'])->name('transaction.cashouts.index');
    Route::get('/cashouts/{id}', [\App\Http\Controllers\Transaction\CashoutController::class, 'show'])->name('transaction.cashouts.show');
    Route::post('/cashouts/{id}/mark-paid', [\App\Http\Controllers\Transaction\CashoutController::class, 'markAsPaid'])->name('transaction.cashouts.mark-paid');
    Route::post('/cashouts/{id}/mark-cancelled', [\App\Http\Controllers\Transaction\CashoutController::class, 'markAsCancelled'])->name('transaction.cashouts.mark-cancelled');
});



Route::prefix('report')->group(function () {
    Route::get('/balance', App\Livewire\Report\Balance\Index::class)->name('report.balance.index');
    Route::get('/profit-and-loss', App\Livewire\Report\ProfitAndLoss\Index::class)->name('report.profitandloss.index');
    Route::get('/cash-flow', App\Livewire\Report\CashFlow\Index::class)->name('report.cashflow.index');
    Route::get('/download-balance', [BalanceController::class, 'downloadBalance'])->name('report.balance.download');
    Route::get('/download-profit-and-loss', [ProfitAndLossController::class, 'downloadProfitAndLoss'])->name('report.profitandloss.download');
    Route::get('/download-cash-flow', [CashFlowController::class, 'downloadCashFlow'])->name('report.cashflow.download');
    Route::get('/console', [\App\Http\Controllers\Report\ConsoleReportController::class, 'index'])->name('report.console.index');
    Route::get('/piutang', [\App\Http\Controllers\Report\PiutangReportController::class, 'index'])->name('report.piutang.index');
    Route::get('/cashout', function() { return view('report.cashout'); })->name('report.cashout.index');
    Route::get('/cashout-reconciliation', [\App\Http\Controllers\Report\CashoutReportController::class, 'reconciliation'])->name('report.cashout.reconciliation');

    // Balance Sheet
    Route::get('/balance-sheet', [\App\Http\Controllers\Report\BalanceSheetController::class, 'index'])->name('report.balance-sheet.index');
    
    // Debit Note Report
    Route::get('/debit-notes', [\App\Http\Controllers\Report\ReportController::class, 'debitNotes'])->name('report.debit-notes.index');
    
    // Cashout Report
    Route::get('/cashouts', [\App\Http\Controllers\Report\ReportController::class, 'cashouts'])->name('report.cashouts.index');
    
    // Account Statement Report
    Route::get('/account-statement', [\App\Http\Controllers\Report\AccountStatementController::class, 'index'])->name('report.account-statement.index');
});

}); // End of auth middleware group
