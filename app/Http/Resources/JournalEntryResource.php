<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JournalEntryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'entry_date' => $this->entry_date,
            'reference' => $this->reference,
            'description' => $this->description,
            'status' => $this->status,
            'created_by' => $this->created_by ? $this->createdBy->name : null,
            'updated_by' => $this->updated_by ? $this->updatedBy->name : null,
            'details' => JournalEntryDetailResource::collection($this->details),
        ];
    }
}
