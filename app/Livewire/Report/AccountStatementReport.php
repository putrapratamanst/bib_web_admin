<?php

namespace App\Livewire\Report;

use App\Models\AccountStatement;
use App\Models\ChartOfAccount;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class AccountStatementReport extends Component
{
    use WithPagination;

    public $chart_of_account_id = '';
    public $date_from;
    public $date_to;
    public $page = 1;

    protected $paginationTheme = 'bootstrap';

    public function mount()
    {
        // Set default date range to current month
        $this->date_from = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->date_to = Carbon::now()->endOfMonth()->format('Y-m-d');
    }

    public function updatingChartOfAccountId()
    {
        $this->resetPage();
    }

    public function updatingDateFrom()
    {
        $this->resetPage();
    }

    public function updatingDateTo()
    {
        $this->resetPage();
    }

    public function render()
    {
        $transactions = collect();
        $openingBalance = 0;
        $closingBalance = 0;
        $totalDebit = 0;
        $totalCredit = 0;
        $chartOfAccount = null;

        if ($this->chart_of_account_id) {
            $chartOfAccount = ChartOfAccount::find($this->chart_of_account_id);

            // Build statement
            $statement = AccountStatement::buildStatement(
                $this->chart_of_account_id,
                $this->date_from,
                $this->date_to
            );

            $openingBalance = $statement['opening_balance'];
            $closingBalance = $statement['closing_balance'];
            $totalDebit = $statement['total_debit'];
            $totalCredit = $statement['total_credit'];
            $transactions = $statement['transactions'];
        }

        // Manual pagination for the transactions
        $perPage = 50;
        $page = $this->page ?: 1;
        $total = $transactions->count();
        $pagedData = $transactions->forPage($page, $perPage)->values();
        $transactionsPaginated = new LengthAwarePaginator($pagedData, $total, $perPage, $page, [
            'path' => request()->url(),
            'query' => request()->query(),
        ]);

        // Get cash/bank accounts for dropdown (accounts with type Cash and Bank)
        $cashBankAccounts = ChartOfAccount::whereIn('account_category_id', [3])
            ->orderBy('code')
            ->get();

        return view('livewire.report.account-statement-report', [
            'transactions' => $transactionsPaginated,
            'openingBalance' => $openingBalance,
            'closingBalance' => $closingBalance,
            'totalDebit' => $totalDebit,
            'totalCredit' => $totalCredit,
            'chartOfAccount' => $chartOfAccount,
            'cashBankAccounts' => $cashBankAccounts,
        ]);
    }

    public function exportExcel()
    {
        if (!$this->chart_of_account_id) {
            $this->dispatch('alert', [
                'type' => 'error',
                'message' => 'Please select an account first'
            ]);
            return;
        }

        $params = [
            'chart_of_account_id' => $this->chart_of_account_id,
            'date_from' => $this->date_from,
            'date_to' => $this->date_to,
            'format' => 'excel'
        ];

        $url = route('api.reports.account-statement') . '?' . http_build_query($params);

        $this->dispatch('downloadFile', ['url' => $url]);
    }
}
