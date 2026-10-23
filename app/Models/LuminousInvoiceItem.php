<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LuminousInvoiceItem extends Model
{
    use HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['luminous_invoice_id', 'cashout_id', 'basos_amount'];

    protected $casts = ['basos_amount' => 'decimal:2'];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(LuminousInvoice::class, 'luminous_invoice_id');
    }

    public function cashout(): BelongsTo
    {
        return $this->belongsTo(Cashout::class);
    }
}
