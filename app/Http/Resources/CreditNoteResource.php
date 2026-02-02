<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CreditNoteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return array_merge(parent::toArray($request), [
            'approval_status_badge' => $this->approval_status_badge,
            'approved_at_formatted' => $this->approved_at_formatted,
            'approved_by_name' => $this->approvedBy->name ?? null,
            'can_be_approved' => $this->canBeApproved(),
            'can_be_printed' => $this->canBePrinted(),
        ]);
    }
}
