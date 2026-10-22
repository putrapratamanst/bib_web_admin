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

            $this->ensurePending($contract->approval_status, 'Contract');

            $this->updateApproval($contract, $decision, $userId, $reason, 'rejection_reason');

            return $contract->fresh();
        });
    }

    public function submitDebitNote(string $id, string $decision, int $userId, ?string $reason = null): object
    {
        return DB::transaction(function () use ($id, $decision, $userId, $reason) {
            $debitNote = \App\Models\DebitNote::with('contract')->lockForUpdate()->findOrFail($id);
            $this->ensureContractApproved($debitNote->contract?->approval_status);
            $this->ensurePending($debitNote->approval_status, 'DebitNote');
            $this->updateApproval($debitNote, $decision, $userId, $reason, 'approval_notes');

            if ($decision === 'approve') {
                $debitNote->debitNoteBillings()
                    ->where('status', 'pending')
                    ->lockForUpdate()
                    ->get()
                    ->each(function ($billing) use ($userId) {
                        $billing->update([
                            'status' => 'approved',
                        ]);
                    });
            }

            return $debitNote->fresh();
        });
    }

    public function submitBilling(string $id, string $decision, int $userId, ?string $reason = null): object
    {
        return DB::transaction(function () use ($id, $decision) {
            $billing = \App\Models\DebitNoteBilling::query()->lockForUpdate()->findOrFail($id);
            $this->ensurePending($billing->status, 'DebitNoteBilling');
            $billing->update(['status' => $decision === 'approve' ? 'approved' : 'rejected']);

            return $billing->fresh();
        });
    }

    public function submitCreditNote(string $id, string $decision, int $userId, ?string $reason = null): object
    {
        return DB::transaction(function () use ($id, $decision, $userId, $reason) {
            $creditNote = \App\Models\CreditNote::with('contract')->lockForUpdate()->findOrFail($id);
            $this->ensureContractApproved($creditNote->contract?->approval_status);
            $this->ensurePending($creditNote->approval_status, 'CreditNote');
            $this->updateApproval($creditNote, $decision, $userId, $reason, 'approval_notes');

            return $creditNote->fresh();
        });
    }

    private function submitModel(string $modelClass, string $id, string $decision, int $userId, ?string $reason): object
    {
        return DB::transaction(function () use ($modelClass, $id, $decision, $userId, $reason) {
            $model = $modelClass::query()->lockForUpdate()->findOrFail($id);
            $this->ensurePending($model->approval_status, class_basename($modelClass));
            $this->updateApproval($model, $decision, $userId, $reason, 'approval_notes');

            return $model->fresh();
        });
    }

    private function ensurePending(?string $status, string $label): void
    {
        if ($status !== 'pending') {
            throw ValidationException::withMessages([
                'approval' => "{$label} ini sudah diproses.",
            ]);
        }
    }

    private function ensureContractApproved(?string $status): void
    {
        if ($status !== 'approved') {
            throw ValidationException::withMessages([
                'contract' => 'Contract harus Approved terlebih dahulu.',
            ]);
        }
    }

    private function updateApproval(object $model, string $decision, int $userId, ?string $reason, string $notesColumn): void
    {
        $model->update([
            'approval_status' => $decision === 'approve' ? 'approved' : 'rejected',
            'approved_by' => $userId,
            'approved_at' => now(),
            $notesColumn => $decision === 'reject' ? $reason : null,
        ]);
    }
}
