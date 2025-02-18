<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashTransaction extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'contact_id',
        'number',
        'date',
        'type',
        'bank_id',
        'bank_account_name',
        'bank_account_number',
        'amount',
        'currency_id',
        'currency_rate',
        'description',
        'status',
    ];

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'contact_id', 'id');
    }

    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class, 'bank_id', 'id');
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_id', 'id');
    }

    public function getTypeNewAttribute(): string
    {
        return $this->type == 'in' ? 'Cash In' : 'Cash Out';
    }

    public function getDateNewAttribute(): string
    {
        return date('d-m-Y', strtotime($this->date));
    }

    public function getAmountAttribute($value): string
    {
        return number_format($value, 2, ',', '.');
    }

    public function getCurrencyRateAttribute($value): string
    {
        return number_format($value, 0, ',', '.');
    }
}
