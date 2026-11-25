<?php

namespace App\Console\Commands;

use App\Models\Contract;
use App\Models\Company;
use App\Models\CreditNote;
use App\Models\DebitNote;
use App\Models\User;
use App\Notifications\PendingApprovalReminder;
use Illuminate\Console\Command;

class SendApprovalReminders extends Command
{
    protected $signature = 'approval:remind {--dry-run : Tampilkan jumlah pending tanpa mengirim email}';

    protected $description = 'Kirim reminder approval yang masih pending kepada semua approver';

    public function handle(): int
    {
        $reminders = Company::query()->get()->mapWithKeys(function (Company $company) {
            $pending = [
                'contracts' => Contract::where('company_id', $company->id)->where('approval_status', 'pending')->count(),
                'debit_notes' => DebitNote::where('company_id', $company->id)->where('approval_status', 'pending')->count(),
                'credit_notes' => CreditNote::where('company_id', $company->id)->where('approval_status', 'pending')->count(),
            ];

            return [$company->id => ['company' => $company, 'pending' => $pending]];
        })->filter(fn ($row) => array_sum($row['pending']) > 0);

        foreach ($reminders as $row) {
            $pending = $row['pending'];
            $this->info(sprintf(
                '%s: %d contract, %d debit note, %d credit note (total %d).',
                $row['company']->name,
                $pending['contracts'],
                $pending['debit_notes'],
                $pending['credit_notes'],
                array_sum($pending),
            ));
        }

        if ($reminders->isEmpty() || $this->option('dry-run')) {
            return self::SUCCESS;
        }

        foreach ($reminders as $row) {
            $approvers = User::where('company_id', $row['company']->id)
                ->where('role', 'approver')
                ->whereNotNull('email')
                ->get();

            if ($approvers->isEmpty()) {
                $this->warn("Tidak ada approver untuk {$row['company']->name}.");
                continue;
            }

            $approvers->each(function (User $approver) use ($row): void {
                $approver->notify(new PendingApprovalReminder($row['pending']));
                $this->line("Reminder dikirim ke {$approver->email}.");
            });
        }

        return self::SUCCESS;
    }
}