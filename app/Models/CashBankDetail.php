<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashBankDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'cash_bank_id',
        'debit_note_id',
        'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function cashBank(): BelongsTo
    {
        return $this->belongsTo(CashBank::class, 'cash_bank_id', 'id');
    }

    public function debitNote(): BelongsTo
    {
        return $this->belongsTo(DebitNote::class, 'debit_note_id', 'id');
    }

    public function paymentAllocation(): BelongsTo
    {
        return $this->belongsTo(PaymentAllocation::class, 'id', 'cash_bank_id');
    }
}
