<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contract extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'contract_type_id',
        'number',
        'client_id',
        'address',
        'period_start',
        'period_end',
        'description',
        'count_of_item',
        'status',
        'currency_id',
        'currency_rate',
        'discount',
        'gross_amount',
    ];

    public function contractType(): BelongsTo
    {
        return $this->belongsTo(ContractType::class, 'contract_type_id', 'id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'client_id', 'id');
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_id', 'id');
    }

    public function getDiscountAmountAttribute(): float
    {
        return $this->gross_amount * $this->discount / 100;
    }

    public function getNettAmountAttribute(): float
    {
        return $this->gross_amount - ($this->gross_amount * $this->discount / 100);
    }

    public function getPeriodAttribute(): string
    {
        return Carbon::parse($this->period_start)->format('d-m-Y') . ' - ' . Carbon::parse($this->period_end)->format('d-m-Y');
    }

    public function billings(): HasMany
    {
        return $this->hasMany(Billing::class, 'contract_id', 'id');
    }
}
