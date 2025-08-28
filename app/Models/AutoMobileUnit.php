<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AutoMobileUnit extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'db_kendaraanitem';

    protected $fillable = [
        'id',
        'contract_id',
        'nopolisi',
        'merk',
        'tahun',
        'norangka',
        'nomesin',
        'penggunaan',
        'rate',
        'brokerage',
        'discount',
        'idmu',
        'total',
        'idcover',
        'recycle'
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->id = \Illuminate\Support\Str::uuid()->toString();
        });
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }
}
