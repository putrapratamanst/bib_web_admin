<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportFinance extends Model
{
    use HasFactory;

    use HasUlids;

    protected $fillable = [
        'account_category_id',
        'code',
        'name',
        'description',
        'balance_type',
        'is_active',
        'is_editable',
        'created_by',
        'updated_by',
        'prefix'
    ];

    // append the following to the model code and name
    protected $appends = ['display_name'];

    public function getDisplayNameAttribute(): string
    {
        return $this->code . ' - ' . $this->name;
    }

    public function accountCategory(): BelongsTo
    {
        return $this->belongsTo(AccountCategory::class, 'account_category_id', 'id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeEditable($query)
    {
        return $query->where('is_editable', true);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where('code', 'like', "%$search%")
            ->orWhere('name', 'like', "%$search%");
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('account_category_id', $category);
    }
}
