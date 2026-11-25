<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['name', 'code'];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public static function idFor(string $code): ?string
    {
        return static::query()->where('code', $code)->value('id');
    }
}
