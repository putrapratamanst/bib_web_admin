<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactType extends Model
{
    use HasFactory;

    protected $fillable = [
        'contact_id',
        'type',
    ];

    public function getTypeDisplayAttribute()
    {
        return match ($this->type) {
            'client' => 'Client',
            'agent' => 'Agent',
            'insurance' => 'Insurance',
            default => 'Unknown',
        };
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'contact_id', 'id');
    }

    public function scopeClient($query)
    {
        return $query->where('type', 'client');
    }

    public function scopeAgent($query)
    {
        return $query->where('type', 'agent');
    }

    public function scopeInsurance($query)
    {
        return $query->where('type', 'insurance');
    }
}
