<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contact extends Model
{
    use HasFactory;

    use HasUuids;

    protected $fillable = [
        'contact_group_id',
        'display_name',
        'name',
        'email',
        'phone',
        'created_by',
        'updated_by',
    ];

    // append additional attribute for types first array
    protected $appends = ['type'];

    public function getTypeAttribute(): array
    {
        return $this->contactTypes->pluck('type')->toArray();
    }

    public function contactGroup(): BelongsTo
    {
        return $this->belongsTo(ContactGroup::class, 'contact_group_id', 'id');
    }
    
    public function contactTypes(): HasMany
    {
        return $this->hasMany(ContactType::class, 'contact_id', 'id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }
}
