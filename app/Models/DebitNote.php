<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DebitNote extends Model
{
    use HasFactory;

    use HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'contract_id',
        'number',
        'date',
        'due_date',
        'installment',
        'currency_code',
        'exchange_rate',
        'amount',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'date' => 'date',
        'due_date' => 'date',
        'exchange_rate' => 'decimal:2',
        'amount' => 'decimal:2',
    ];

    protected $appends = [
        'date_formatted',
        'due_date_formatted',
        'exchange_rate_formatted',
        'amount_formatted',
    ];

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class, 'contract_id', 'id');
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_code', 'code');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    public function debitNoteDetails(): HasMany
    {
        return $this->hasMany(DebitNoteDetail::class, 'debit_note_id', 'id');
    }

    public function getDateFormattedAttribute(): string
    {
        return $this->date->format('d-m-Y');
    }

    public function getDueDateFormattedAttribute(): string
    {
        return $this->due_date ? $this->due_date->format('d-m-Y') : '';
    }

    public function getExchangeRateFormattedAttribute(): string
    {
        return number_format($this->exchange_rate, 2, ",", ".");
    }

    public function getAmountFormattedAttribute(): string
    {
        return number_format($this->amount, 2, ",", ".");
    }

    public function creditNotes(): HasMany
    {
        return $this->hasMany(CreditNote::class, 'debit_note_id', 'id');
    }

    public function cashBankDetails(): HasMany
    {
        return $this->hasMany(CashBankDetail::class, 'debit_note_id', 'id');
    }

    // summary amount of credit notes by debit note
    public function getCreditNoteAmountAttribute(): float
    {
        return $this->creditNotes->sum('amount');
    }

    // summary amount of cash bank details by debit note where cash bank type is receive
    public function getReceiveAmountAttribute(): float
    {
        return $this->cashBankDetails()
            ->join('cash_banks', 'cash_bank_details.cash_bank_id', '=', 'cash_banks.id')
            ->where('cash_banks.type', 'receive')
            ->sum('cash_bank_details.amount');
    }

    public function getReceiveDateAttribute()
    {
        return $this->cashBankDetails()
            ->join('cash_banks', 'cash_bank_details.cash_bank_id', '=', 'cash_banks.id')
            ->where('cash_banks.type', 'receive')
            ->max('cash_banks.date');
    }

    // summary amount of cash bank details by debit note where cash bank type is pay
    public function getPayAmountAttribute(): float
    {
        return $this->cashBankDetails()
            ->join('cash_banks', 'cash_bank_details.cash_bank_id', '=', 'cash_banks.id')
            ->where('cash_banks.type', 'pay')
            ->sum('cash_bank_details.amount');
    }

    public function getPayDateAttribute()
    {
        return $this->cashBankDetails()
            ->join('cash_banks', 'cash_bank_details.cash_bank_id', '=', 'cash_banks.id')
            ->where('cash_banks.type', 'pay')
            ->max('cash_banks.date');
    }

    public function debitNoteBillings(): HasMany
    {
        return $this->hasMany(DebitNoteBilling::class, 'debit_note_id', 'id');
    }

    public function paymentAllocations(): HasMany
    {
        return $this->hasMany(PaymentAllocation::class, 'debit_note_id', 'id');
    }
}
