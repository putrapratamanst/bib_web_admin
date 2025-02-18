<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashBankDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'cash_bank_id',
        'chart_of_account_id',
        'description',
        'amount',
    ];

    public function cashBank(): BelongsTo
    {
        return $this->belongsTo(CashBank::class, 'cash_bank_id', 'id');
    }

    public function chartOfAccount(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'chart_of_account_id', 'id');
    }
}
