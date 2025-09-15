<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentAllocationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'cash_bank_id' => $this->cash_bank_id,
            'debit_note_id' => $this->debit_note_id,
            'allocation' => $this->allocation,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
