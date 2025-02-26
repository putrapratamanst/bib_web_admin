<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DebitNoteDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'debit_note_id',
        'insurance_id',
        'amount'
    ];

    protected $appends = [
        'amount_formatted'
    ];

    public function getAmountFormattedAttribute()
    {
        return number_format($this->amount, 2, ',', '.');
    }

    public function debitNote(): BelongsTo
    {
        return $this->belongsTo(DebitNote::class, 'debit_note_id', 'id');
    }

    public function insurance(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'insurance_id', 'id');
    }
}
