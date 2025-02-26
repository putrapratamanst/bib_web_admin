<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContractDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_id',
        'insurance_id',
        'description',
        'percentage',
        'brokerage_fee',
        'eng_fee',
    ];

    protected $appends = [
        'percentage_formatted',
        'brokerage_fee_formatted',
        'eng_fee_formatted',
    ];

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class, 'contract_id', 'id');
    }

    public function insurance(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'insurance_id', 'id');
    }

    public function getPercentageFormattedAttribute(): string
    {
        return number_format($this->percentage, 2) . '%';
    }

    public function getBrokerageFeeFormattedAttribute(): string
    {
        return number_format($this->brokerage_fee, 2) . '%';
    }

    public function getEngFeeFormattedAttribute($value): string
    {
        return number_format($this->eng_fee, 2) . '%';
    }
}
