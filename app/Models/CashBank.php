<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CashBank extends Model
{
    use HasFactory;

    use HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'type',
        'number',
        'contact_id',
        'date',
        'chart_of_account_id',
        'amount',
        'description',
        'reference',
        'status',
        'created_by',
        'updated_by',
    ];

    // atribut display_date
    protected $appends = [
        'display_type',
        'display_date',
        'display_amount',
    ];

    public function getDisplayTypeAttribute(): string
    {
        return $this->type === 'receive' ? 'Receive' : 'Payment';
    }

    public function getDisplayDateAttribute(): string
    {
        return date('d M Y', strtotime($this->date));
    }

    public function getDisplayAmountAttribute(): string
    {
        return number_format($this->amount, 2, '.', ',');
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'contact_id', 'id');
    }

    public function chartOfAccount(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'chart_of_account_id', 'id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    public function cashBankDetails(): HasMany
    {
        return $this->hasMany(CashBankDetail::class, 'cash_bank_id', 'id');
    }
}
