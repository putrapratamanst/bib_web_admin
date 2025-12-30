<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JournalEntryDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'journal_entry_id',
        'chart_of_account_id',
        'debit',
        'credit',
        'description',
    ];

    protected $appends = [
        'debit_formatted',
        'credit_formatted',
    ];

    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_id', 'id');
    }

    public function chartOfAccount(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'chart_of_account_id', 'id');
    }

    // debit formatted
    public function getDebitFormattedAttribute(): string
    {
        return number_format($this->debit, 2, ',', '.');
    }

    // credit formatted
    public function getCreditFormattedAttribute(): string
    {
        return number_format($this->credit, 2, ',', '.');
    }
}