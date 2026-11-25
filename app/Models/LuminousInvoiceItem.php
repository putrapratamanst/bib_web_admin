<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LuminousInvoiceItem extends Model
{
    use HasUuids, BelongsToCompany;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'luminous_invoice_id',
        'cashout_id',
        'basos_amount',
        'cashout_number',
        'debit_note_number',
        'policy_number',
        'insured_name',
        'insurance_name',
        'cashout_date',
    ];

    protected $casts = [
        'basos_amount' => 'decimal:2',
        'cashout_date' => 'date',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(LuminousInvoice::class, 'luminous_invoice_id');
    }
}
