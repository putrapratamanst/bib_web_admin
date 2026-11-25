<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use RuntimeException;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected static function booted(): void
    {
        static::creating(function (self $user): void {
            if ($user->company_id) {
                return;
            }

            $currentUser = Auth::user();
            $companyId = $currentUser ? $currentUser->company_id : Company::idFor('BIB');

            if (! $companyId) {
                throw new RuntimeException('Cannot create a user before the BIB company exists.');
            }

            $user->company_id = $companyId;
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'role',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Check if user has approver role
     */
    public function isApprover(): bool
    {
        return $this->role === 'approver';
    }

    /**
     * Check if user can approve credit notes
     */
    public function canApproveCreditNotes(): bool
    {
        return $this->isApprover();
    }
}
