<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Billing extends Model
{
    use HasFactory, HasUuids;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'contract_id',
        'number',
        'date',
        'due_date',
        'amount',
        'description',
        'status',
        'created_by',
        'updated_by',
    ];

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class, 'contract_id', 'id');
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

    public function getDueDateAttribute($value): string
    {
        if ($value == null) {
            return '';
        }
        return date('d-m-Y', strtotime($value));
    }

    public function getAmountAttribute($value): string
    {
        return number_format($value, 2, ',', '.');
    }
}
