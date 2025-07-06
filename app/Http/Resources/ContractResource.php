<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContractResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'contract_type' => $this->contractType->name,
            'number' => $this->number,
            'contact' => new ContactResource($this->contact),
            'period_start' => $this->period_start,
            'period_end' => $this->period_end,
            'currency' => $this->currency->name,
            'exchange_rate' => $this->exchange_rate,
            'coverage_amount' => $this->coverage_amount,
            'gross_premium' => $this->gross_premium,
            'discount' => $this->discount,
            'stamp_fee' => $this->stamp_fee,
            'amount' => $this->amount,
            'installment_count' => $this->installment_count,
            'covered_item' => $this->covered_item,
            'memo' => $this->memo,
            'status' => $this->status,
            'created_by' => $this->createdBy ? $this->createdBy->name : null,
            'updated_by' => $this->updatedBy ? $this->updatedBy->name : null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
