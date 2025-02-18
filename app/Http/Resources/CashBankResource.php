<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CashBankResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'number' => $this->number,
            'type' => $this->type,
            'chart_of_account' => $this->chartOfAccount->only(['id', 'name']),
            'contact' => $this->contact->only(['id', 'name']),
            'date' => $this->date,
            'reference' => $this->reference,
            'memo' => $this->memo,
            'currency' => $this->currency->only(['id', 'code', 'name']),
            'exchange_rate' => $this->exchange_rate,
            'amount' => $this->amount,
            'details' => $this->details->map(function ($detail) {
                return [
                    'id' => $detail->id,
                    'chart_of_account_id' => $detail->chart_of_account_id,
                    'description' => $detail->description,
                    'amount' => $detail->amount,
                ];
            }),
        ];
    }
}
