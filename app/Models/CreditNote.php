<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CreditNote extends Model
{
    use HasFactory;

    use HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'contract_id',
        'debit_note_id',
        'number',
        'date',
        'description',
        'currency_code',
        'exchange_rate',
        'amount',
        'status',
        'approval_status',
        'approved_by',
        'approved_at',
        'approval_notes',
        'created_by',
        'updated_by',
        'billing_id',
    ];

    protected $appends = [
        'exchange_rate_formatted',
        'amount_formatted',
        'date_formatted',
        'approved_at_formatted',
        'approval_status_badge',
    ];

    protected $casts = [
        'date' => 'date',
        'approved_at' => 'datetime',
    ];

    public function getExchangeRateFormattedAttribute(): string
    {
        return number_format($this->exchange_rate, 2, ".", ",");
    }

    public function getAmountFormattedAttribute(): string
    {
        return number_format($this->amount, 2, ".", ",");
    }

    public function getDateFormattedAttribute(): string
    {
        return $this->date->format('d M Y');
    }

    public function getApprovedAtFormattedAttribute(): ?string
    {
        return $this->approved_at ? $this->approved_at->format('d M Y H:i') : null;
    }

    public function getApprovalStatusBadgeAttribute(): string
    {
        return match($this->approval_status) {
            'pending' => '<span class="badge bg-warning">Pending</span>',
            'approved' => '<span class="badge bg-success">Approved</span>',
            'rejected' => '<span class="badge bg-danger">Rejected</span>',
            default => '<span class="badge bg-secondary">Unknown</span>',
        };
    }

    // Check if credit note can be printed/exported
    public function canBePrinted(): bool
    {
        return $this->approval_status === 'approved';
    }

    // Check if credit note can be approved
    public function canBeApproved(): bool
    {
        return $this->approval_status === 'pending';
    }

    public function canBeEdited(): bool
    {
        return $this->approval_status === 'pending';
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class, 'contract_id', 'id');
    }

    public function debitNote(): BelongsTo
    {
        return $this->belongsTo(DebitNote::class, 'debit_note_id', 'id');
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_code', 'code');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by', 'id');
    }

    public function billing(): BelongsTo
    {
        return $this->belongsTo(DebitNoteBilling::class, 'billing_id', 'id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }
}
