<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChartOfAccount extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = [
        'number',
        'name',
        'account_category_id',
        'description',
        'is_active',
    ];

    // display name
    public function getDisplayNameAttribute()
    {
        return $this->number . ' - ' . $this->name;
    }

    public function accountCategory(): BelongsTo
    {
        return $this->belongsTo(AccountCategory::class, 'account_category_id', 'id');
    }

    // scope to get account category name
    public function scopeAccountCategoryName($query, $accountCategoryName)
    {
        return $query->whereHas('accountCategory', function ($query) use ($accountCategoryName) {
            $query->where('name', $accountCategoryName);
        });
    }
}
