<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DebitNoteBilling extends Model
{
    use HasFactory;

    use HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'debit_note_id',
        'billing_number',
        'date',
        'due_date',
        'amount',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'date' => 'date',
        'due_date' => 'date',
        'amount' => 'decimal:2',
    ];

    protected $appends = [
        'date_formatted',
        'due_date_formatted',
        'amount_formatted',
        'amount_formatted_raw',
    ];

    public function getAmountFormattedRawAttribute(): string 
    {
        return $this->getNetAmount();
    }

    public function getDateFormattedAttribute(): string
    {
        return $this->date ? $this->date->format('d-m-Y') : '';
    }

    public function getDueDateFormattedAttribute(): string
    {
        return $this->due_date ? $this->due_date->format('d-m-Y') : '';
    }

    public function getNetAmount(): float
    {
        $creditNoteAmount = CreditNote::where('billing_id', $this->id)->sum('amount');
        return floatval($this->amount) - floatval($creditNoteAmount);
    }

    public function getAmountFormattedAttribute(): string
    {
        return number_format($this->getNetAmount(), 2, '.', ',');
    }

    /**
     * Get the debit note that owns the billing.
     */
    public function debitNote(): BelongsTo
    {
        return $this->belongsTo(DebitNote::class, 'debit_note_id');
    }

    public function paymentAllocation(): HasMany
    {
        return $this->hasMany(PaymentAllocation::class, 'debit_note_billing_id');
    }

    public function paymentAllocations(): HasMany
    {
        return $this->hasMany(PaymentAllocation::class, 'debit_note_billing_id');
    }
    
}
