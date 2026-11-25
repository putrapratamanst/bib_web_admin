<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LuminousInvoice extends Model
{
    use HasFactory, HasUuids, BelongsToCompany;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'number',
        'insurance_id',
        'recipient',
        'description',
        'date',
        'city',
        'subject',
        'due_date',
        'amount',
        'paid_amount',
        'document_image',
        'bank_name',
        'bank_account',
        'account_name',
        'signatory_name',
        'signatory_title',
        'status',
        'created_by',
    ];

    protected $casts = [
        'date' => 'date',
        'due_date' => 'date',
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(LuminousInvoiceItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(LuminousPayment::class);
    }

    public function getOutstandingAmountAttribute(): float
    {
        return max(0, round((float) $this->amount - (float) $this->paid_amount, 2));
    }

    public function refreshStatus(): void
    {
        $paid = round((float) $this->paid_amount, 2);
        $amount = round((float) $this->amount, 2);
        $this->update(['status' => $paid >= $amount ? 'paid' : ($paid > 0 ? 'partial' : 'unpaid')]);
    }
}
