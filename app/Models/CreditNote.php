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
        'created_by',
        'updated_by',
    ];

    protected $appends = [
        'exchange_rate_formatted',
        'amount_formatted',
        'date_formatted',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function getExchangeRateFormattedAttribute(): string
    {
        return number_format($this->exchange_rate, 2, ",", ".");
    }

    public function getAmountFormattedAttribute(): string
    {
        return number_format($this->amount, 2, ",", ".");
    }

    public function getDateFormattedAttribute(): string
    {
        return $this->date->format('d M Y');
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
}
