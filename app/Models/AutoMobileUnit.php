<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AutomobileUnit extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'contract_id',
        'no_polisi',
        'merk_tahun',
        'no_rangka_mesin',
        'penggunaan',
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
