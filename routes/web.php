<?php

use App\Http\Controllers\BalanceController;
use App\Http\Controllers\CashFlowController;
use App\Http\Controllers\ProfitAndLossController;
use Illuminate\Support\Facades\Route;

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

    // Contract
    Route::get('/contracts', [\App\Http\Controllers\Transaction\ContractController::class, 'index'])->name('transaction.contracts.index');
    Route::get('/contracts/create', [\App\Http\Controllers\Transaction\ContractController::class, 'create'])->name('transaction.contracts.create');
    Route::get('/contracts/{id}', [\App\Http\Controllers\Transaction\ContractController::class, 'show'])->name('transaction.contracts.show');
    Route::get('/contracts/add-unit/automobile/{id} ', [\App\Http\Controllers\Transaction\ContractController::class, 'showAddUnit'])->name('transaction.contracts.show-add-unit');

    // Journal Entry
    Route::get('/journal-entries', [\App\Http\Controllers\Transaction\JournalEntryController::class, 'index'])->name('transaction.journal-entries.index');
    Route::get('/journal-entries/create', [\App\Http\Controllers\Transaction\JournalEntryController::class, 'create'])->name('transaction.journal-entries.create');

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
    // Route::get('/debit-notes/create', 
    // [\App\Http\Controllers\Transaction\DebitNoteController::class, 'create'])->name('transaction.debit-notes.create');
    Route::get(
        '/debit-notes/{id}',
        [\App\Http\Controllers\Transaction\DebitNoteController::class, 'show']
    )->name('transaction.debit-notes.show');
});



Route::prefix('report')->group(function () {
    Route::get('/balance', App\Livewire\Report\Balance\Index::class)->name('report.balance.index');
    Route::get('/profit-and-loss', App\Livewire\Report\ProfitAndLoss\Index::class)->name('report.profitandloss.index');
    Route::get('/cash-flow', App\Livewire\Report\CashFlow\Index::class)->name('report.cashflow.index');
    Route::get('/download-balance', [BalanceController::class, 'downloadBalance'])->name('report.balance.download');
    Route::get('/download-profit-and-loss', [ProfitAndLossController::class, 'downloadProfitAndLoss'])->name('report.profitandloss.download');
    Route::get('/download-cash-flow', [CashFlowController::class, 'downloadCashFlow'])->name('report.cashflow.download');
    Route::get('/console', [\App\Http\Controllers\Report\ConsoleReportController::class, 'index'])->name('report.console.index');

    // Balance Sheet
    Route::get('/balance-sheet', [\App\Http\Controllers\Report\BalanceSheetController::class, 'index'])->name('report.balance-sheet.index');
});
