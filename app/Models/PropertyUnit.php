<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PropertyUnit extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'db_propertyitem';

    protected $fillable = [
        'location',
        'risk_type',
        'reinstallment_value_clause',
        'nominated_loss_adjuster',
        'discount',
        'contract_id',
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }
}
