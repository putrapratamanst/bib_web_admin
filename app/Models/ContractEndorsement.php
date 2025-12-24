<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContractEndorsement extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'contract_id',
        'contract_reference_id',
        'endorsement_number',
    ];

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class, 'contract_id', 'id');
    }

    public function contractReference(): BelongsTo
    {
        return $this->belongsTo(Contract::class, 'contract_reference_id', 'id');
    }
}
