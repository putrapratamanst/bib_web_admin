<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentAllocation extends Model
{
    use HasFactory;

    public $timestamps = true;


    protected $fillable = [
        'type',
        'cash_bank_id',
        'debit_note_id',
        'allocation',
        'status',
        'debit_note_billing_id',
        'cashout_id',
        'description',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'allocation' => 'decimal:2',
    ];

    protected $appends = [
        'allocation_formatted',
    ];

    public function getAllocationFormattedAttribute(): string
    {
        return number_format($this->allocation, 2, ',', '.');
    }

    // Scope for advance payments
    public function scopeAdvance($query)
    {
        return $query->where('type', 'advance')->whereNull('debit_note_id');
    }

    // Scope for allocated payments
    public function scopeAllocated($query)
    {
        return $query->where('type', 'allocation')->whereNotNull('debit_note_id');
    }

    // Check if this is an advance payment
    public function isAdvance(): bool
    {
        return $this->type === 'advance' && is_null($this->debit_note_id);
    }

    public function cashBank(): BelongsTo
    {
        return $this->belongsTo(CashBank::class, 'cash_bank_id', 'id');
    }

    public function debitNote(): BelongsTo
    {
        return $this->belongsTo(DebitNote::class, 'debit_note_id', 'id');
    }

    public function cashout(): BelongsTo
    {
        return $this->belongsTo(Cashout::class, 'cashout_id', 'id');
    }
}
