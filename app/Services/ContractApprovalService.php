<?php

namespace App\Services;

use App\Models\Contract;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ContractApprovalService
{
    public function submit(Contract $contract, string $decision, int $userId, ?string $reason = null): Contract
    {
        return DB::transaction(function () use ($contract, $decision, $userId, $reason) {
            $contract = Contract::query()->lockForUpdate()->findOrFail($contract->id);

            if ($contract->approval_status !== 'pending') {
                throw ValidationException::withMessages([
                    'contract' => 'Contract ini sudah diproses.',
                ]);
            }

            $status = $decision === 'approve' ? 'approved' : 'rejected';
            $notes = $decision === 'reject' ? $reason : null;
            $contract->update([
                'approval_status' => $status,
                'approved_by' => $userId,
                'approved_at' => now(),
                'rejection_reason' => $notes,
            ]);

            foreach ($contract->debitNotes()->lockForUpdate()->get() as $debitNote) {
                $debitNote->update([
                    'approval_status' => $status,
                    'approved_by' => $userId,
                    'approved_at' => now(),
                    'approval_notes' => $notes,
                ]);

                if ($decision === 'approve') {
                    $debitNote->debitNoteBillings()->lockForUpdate()->get()->each(function ($billing) {
                        $billing->update(['status' => 'posted']);
                    });
                }
            }

            return $contract->fresh(['debitNotes.debitNoteBillings']);
        });
    }
}
