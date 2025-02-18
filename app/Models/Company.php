<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'companies';
    protected $primaryKey = 'id';
    protected $keyType = 'int';

    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'code',
        'name',
        'email',
        'description',
        'status',
    ];
}
