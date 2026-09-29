<?php

namespace App\Console\Commands;

use App\Models\Contract;
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
        $pending = [
            'contracts' => Contract::query()->where('approval_status', 'pending')->count(),
            'debit_notes' => DebitNote::query()->where('approval_status', 'pending')->count(),
            'credit_notes' => CreditNote::query()->where('approval_status', 'pending')->count(),
        ];
        $total = array_sum($pending);

        $this->info(sprintf(
            'Pending approval: %d contract, %d debit note, %d credit note (total %d).',
            $pending['contracts'],
            $pending['debit_notes'],
            $pending['credit_notes'],
            $total,
        ));

        if ($total === 0 || $this->option('dry-run')) {
            return self::SUCCESS;
        }

        $approvers = User::query()
            ->where('role', 'approver')
            ->whereNotNull('email')
            ->get();

        if ($approvers->isEmpty()) {
            $this->warn('Tidak ada approver dengan email yang dapat menerima reminder.');

            return self::SUCCESS;
        }

        $approvers->each(function (User $approver) use ($pending): void {
            $approver->notify(new PendingApprovalReminder($pending));
            $this->line("Reminder dikirim ke {$approver->email}.");
        });

        return self::SUCCESS;
    }
}