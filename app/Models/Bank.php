<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bank extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'banks';
    protected $primaryKey = 'id';
    protected $keyType = 'int';

    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'code',
        'name',
        'email',
        'status',
    ];
}
