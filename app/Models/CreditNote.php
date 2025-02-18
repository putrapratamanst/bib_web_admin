<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CreditNote extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'billing_id',
        'number',
        'date',
        'amount',
        'description',
        'status',
        'note',
    ];

    public function billing(): BelongsTo
    {
        return $this->belongsTo(Billing::class, 'billing_id', 'id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    public function getDateAttribute($value): string
    {
        return date('d-m-Y', strtotime($value));
    }

    public function getAmountAttribute($value): string
    {
        return number_format($value, 2, ',', '.');
    }
}
