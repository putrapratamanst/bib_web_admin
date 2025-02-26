<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChartOfAccountResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'display_name' => $this->display_name,
            'code' => $this->code,
            'name' => $this->name,
            'account_category' => new AccountCategoryResource($this->accountCategory),
            'description' => $this->description,
            'balance_type' => $this->balance_type,
            'is_active' => $this->is_active,
            'is_editable' => $this->is_editable,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
