<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LuminousPayment extends Model
{
    use HasUuids, BelongsToCompany;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['luminous_invoice_id', 'date', 'amount', 'created_by'];

    protected $casts = ['date' => 'date', 'amount' => 'decimal:2'];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(LuminousInvoice::class, 'luminous_invoice_id');
    }
}
