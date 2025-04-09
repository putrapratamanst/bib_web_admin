<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contract extends Model
{
    use HasFactory;

    use HasUuids;

    protected $fillable = [
        'contract_status',
        'contract_type_id',
        'number',
        'policy_number',
        'contact_id',
        'period_start',
        'period_end',
        'currency_code',
        'exchange_rate',
        'coverage_amount',
        'gross_premium',
        'discount',
        'stamp_fee',
        'amount',
        'installment_count',
        'memo',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
    ];

    protected $appends = [
        // merge period_start and period_end to period
        'period',
        'exchange_rate_formatted',
        'coverage_amount_formatted',
        'gross_premium_formatted',
        'discount_formatted',
        'discount_amount_formatted',
        'stamp_fee_formatted',
        'amount_formatted',
        'period_start_formatted',
        'period_end_formatted',
    ];

    public function getPeriodAttribute(): string
    {
        // return $this->period_start->format('d M Y') . ' - ' . $this->period_end->format('d M Y');
        return $this->period_start_formatted . ' - ' . $this->period_end_formatted;
    }

    public function getExchangeRateFormattedAttribute(): string
    {
        return number_format($this->exchange_rate, 2, ",", ".");
    }

    public function getCoverageAmountFormattedAttribute(): string
    {
        return number_format($this->coverage_amount, 2, ",", ".");
    }

    public function getGrossPremiumFormattedAttribute(): string
    {
        return number_format($this->gross_premium, 2, ",", ".");
    }

    public function getDiscountFormattedAttribute(): string
    {
        // to %
        return number_format($this->discount, 2, ",", ".") . '%';
    }

    public function getStampFeeFormattedAttribute(): string
    {
        return number_format($this->stamp_fee, 2, ",", ".");
    }

    public function getDiscountAmountAttribute(): float
    {
        return $this->gross_premium * ($this->discount / 100);
    }

    public function getDiscountAmountFormattedAttribute(): string
    {
        $discountAmount = $this->gross_premium * ($this->discount / 100);

        return number_format($discountAmount, 2, ",", ".");
    }

    public function getAmountFormattedAttribute(): string
    {
        return number_format($this->amount, 2, ",", ".");
    }

    public function getPeriodStartFormattedAttribute(): string
    {
        // check if null
        if ($this->period_start == null) {
            return 'not set';
        }

        return $this->period_start->format('d M Y');
    }

    public function getPeriodEndFormattedAttribute(): string
    {
        // check if null
        if ($this->period_end == null) {
            return 'not set';
        }

        return $this->period_end->format('d M Y');
    }

    public function contractType(): BelongsTo
    {
        return $this->belongsTo(ContractType::class, 'contract_type_id', 'id');
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'contact_id', 'id');
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

    public function details(): HasMany
    {
        return $this->hasMany(ContractDetail::class, 'contract_id', 'id');
    }

    public function debitNotes(): HasMany
    {
        return $this->hasMany(DebitNote::class, 'contract_id', 'id');
    }

    public function creditNotes(): HasMany
    {
        return $this->hasMany(CreditNote::class, 'contract_id', 'id');
    }
}
