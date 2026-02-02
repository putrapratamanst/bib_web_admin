<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DebitNoteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'contract' => new ContractResource($this->contract),
            'billing_address' => $this->whenLoaded('billingAddress', function() {
                return [
                    'id' => $this->billingAddress->id,
                    'address' => $this->billingAddress->address,
                    'is_primary' => $this->billingAddress->is_primary,
                ];
            }),
            'number' => $this->number,
            'date' => $this->date,
            'due_date' => $this->due_date ?? null,
            'installment' => $this->installment,
            'currency_code' => $this->currency_code,
            'exchange_rate' => $this->exchange_rate,
            'amount' => $this->amount,
            'status' => $this->status,
            'approval_status' => $this->approval_status,
            'approval_status_badge' => $this->approval_status_badge,
            'approved_by' => $this->approvedBy ? $this->approvedBy->name : null,
            'approved_at' => $this->approved_at,
            'approved_at_formatted' => $this->approved_at_formatted,
            'approval_notes' => $this->approval_notes,
            'can_be_approved' => $this->canBeApproved(),
            'can_be_printed' => $this->canBePrinted(),
            'created_by' => $this->createdBy ? $this->createdBy->name : null,
            'updated_by' => $this->updatedBy ? $this->updatedBy->name : null,
        ];
    }
}
