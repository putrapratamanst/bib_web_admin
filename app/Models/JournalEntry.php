<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JournalEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'number',
        'entry_date',
        'reference',
        'description',
        'status',
        'created_by',
        'updated_by',
    ];

    // append amount attribute
    protected $appends = [
        'amount',
        'amount_formatted',
        'date_formatted',
        'total_debit',
        'total_debit_formatted',
        'total_credit',
        'total_credit_formatted',
    ];

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
        return $this->hasMany(JournalEntryDetail::class, 'journal_entry_id', 'id');
    }

    // get amount of debit
    public function getAmountAttribute(): float
    {
        return $this->details->sum('debit');
    }

    // amount format
    public function getAmountFormattedAttribute(): string
    {
        return number_format($this->amount, 2, '.', ',');
    }

    // get total debit
    public function getTotalDebitAttribute(): float
    {
        return $this->details->sum('debit');
    }

    // total debit formatted
    public function getTotalDebitFormattedAttribute(): string
    {
        return number_format($this->total_debit, 2, ',', '.');
    }

    // get total credit
    public function getTotalCreditAttribute(): float
    {
        return $this->details->sum('credit');
    }

    // total credit formatted
    public function getTotalCreditFormattedAttribute(): string
    {
        return number_format($this->total_credit, 2, ',', '.');
    }

    // date format
    public function getDateFormattedAttribute(): string
    {
        return date('d-m-Y', strtotime($this->entry_date));
    }
}
