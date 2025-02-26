<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CashBankResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'number' => $this->number,
            'contact' => new ContactResource($this->contact),
            'date' => $this->date,
            'chart_of_account' => new ChartOfAccountResource($this->chartOfAccount),
            'amount' => $this->amount,
            'description' => $this->description,
            'reference' => $this->reference,
            'status' => $this->status,
            'created_by' => $this->createdBy ? $this->createdBy->name : null,
            'updated_by' => $this->updatedBy ? $this->updatedBy->name : null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
