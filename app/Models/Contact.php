<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'code',
        'name',
        'email',
        'phone',
        'address',
        'description',
        'status',
        'account_mapping_receivable',
        'account_mapping_payable',
    ];

    // one contact have more type
    public function type(): HasMany
    {
        return $this->hasMany(ContactType::class, 'contact_id', 'id');
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class, 'client_id', 'id');
    }

    // public function accountReceivable(): BelongsTo
    // {
    //     return $this->belongsTo(ChartOfAccount::class, 'account_mapping_receivable', 'id');
    // }

    // public function accountPayable(): BelongsTo
    // {
    //     return $this->belongsTo(ChartOfAccount::class, 'account_mapping_payable', 'id');
    // }
}
