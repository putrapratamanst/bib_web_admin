<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DebitNote extends Model
{
    use HasFactory;

    use HasUuids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'contract_id',
        'contact_id',
        'billing_address_id',
        'number',
        'date',
        'due_date',
        'installment',
        'currency_code',
        'exchange_rate',
        'amount',
        'status',
        'approval_status',
        'approved_by',
        'approved_at',
        'approval_notes',
        'is_posted',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'date' => 'date',
        'due_date' => 'date',
        'exchange_rate' => 'decimal:2',
        'amount' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    protected $appends = [
        'date_formatted',
        'due_date_formatted',
        'exchange_rate_formatted',
        'amount_formatted',
        'approved_at_formatted',
        'approval_status_badge',
        'is_posted',
    ];

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class, 'contract_id', 'id');
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'contact_id', 'id');
    }

    public function billingAddress(): BelongsTo
    {
        return $this->belongsTo(BillingAddress::class, 'billing_address_id', 'id');
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_code', 'code');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by', 'id');
    }

    public function debitNoteDetails(): HasMany
    {
        return $this->hasMany(DebitNoteDetail::class, 'debit_note_id', 'id');
    }

    public function getDateFormattedAttribute(): string
    {
        return $this->date->format('d-m-Y');
    }

    public function getDueDateFormattedAttribute(): string
    {
        return $this->due_date ? $this->due_date->format('d-m-Y') : '';
    }

    public function getExchangeRateFormattedAttribute(): string
    {
        return number_format($this->exchange_rate, 2, ".", ",");
    }

    public function getAmountFormattedAttribute(): string
    {
        return number_format($this->amount, 2, ".", ",");
    }

    public function getApprovedAtFormattedAttribute(): ?string
    {
        return $this->approved_at ? $this->approved_at->format('d M Y H:i') : null;
    }

    public function getApprovalStatusBadgeAttribute(): string
    {
        return match($this->approval_status) {
            'pending' => '<span class="badge bg-warning">Pending Approval</span>',
            'approved' => '<span class="badge bg-success">Approved</span>',
            'rejected' => '<span class="badge bg-danger">Rejected</span>',
            default => '<span class="badge bg-light text-dark">Unknown</span>',
        };
    }

    // Check if debit note can be printed/exported
    public function canBePrinted(): bool
    {
        return $this->approval_status === 'approved';
    }

    // Check if debit note can be approved
    public function canBeApproved(): bool
    {
        return $this->approval_status === 'pending';
    }

    // Check if debit note can be edited
    public function canBeEdited(): bool
    {
        // Allow editing only when status is pending AND not yet reviewed by approver
        // (approved_by and approved_at are null means not yet reviewed)
        return $this->approval_status === 'pending' && 
               is_null($this->approved_by) && 
               is_null($this->approved_at);
    }

    // Check if debit note can be submitted for approval  
    public function canBeSubmittedForApproval(): bool
    {
        // Always false since default is already pending
        return false;
    }

    public function creditNotes(): HasMany
    {
        return $this->hasMany(CreditNote::class, 'debit_note_id', 'id');
    }

    public function cashBankDetails(): HasMany
    {
        return $this->hasMany(CashBankDetail::class, 'debit_note_id', 'id');
    }

    // summary amount of credit notes by debit note
    public function getCreditNoteAmountAttribute(): float
    {
        return $this->creditNotes->sum('amount');
    }

    // summary amount of cash bank details by debit note where cash bank type is receive
    public function getReceiveAmountAttribute(): float
    {
        return $this->cashBankDetails()
            ->join('cash_banks', 'cash_bank_details.cash_bank_id', '=', 'cash_banks.id')
            ->where('cash_banks.type', 'receive')
            ->sum('cash_bank_details.amount');
    }

    public function getReceiveDateAttribute()
    {
        return $this->cashBankDetails()
            ->join('cash_banks', 'cash_bank_details.cash_bank_id', '=', 'cash_banks.id')
            ->where('cash_banks.type', 'receive')
            ->max('cash_banks.date');
    }

    // summary amount of cash bank details by debit note where cash bank type is pay
    public function getPayAmountAttribute(): float
    {
        return $this->cashBankDetails()
            ->join('cash_banks', 'cash_bank_details.cash_bank_id', '=', 'cash_banks.id')
            ->where('cash_banks.type', 'pay')
            ->sum('cash_bank_details.amount');
    }

    public function getPayDateAttribute()
    {
        return $this->cashBankDetails()
            ->join('cash_banks', 'cash_bank_details.cash_bank_id', '=', 'cash_banks.id')
            ->where('cash_banks.type', 'pay')
            ->max('cash_banks.date');
    }

    public function debitNoteBillings(): HasMany
    {
        return $this->hasMany(DebitNoteBilling::class, 'debit_note_id', 'id');
    }

    public function paymentAllocations(): HasMany
    {
        return $this->hasMany(PaymentAllocation::class, 'debit_note_id', 'id');
    }

    public function cashouts(): HasMany
    {
        return $this->hasMany(Cashout::class, 'debit_note_id', 'id');
    }

    // Method untuk posting debit note dan auto create cashouts
    public function postDebitNote(): bool
    {
        // Pastikan status masih active dan belum di-post
        if ($this->status !== 'active') {
            return false;
        }

        // Cek apakah installment = 0 atau tidak ada installment
        if ($this->installment <= 1) {
            // Buat satu cashout dengan total amount debit note
            return $this->createSingleCashout();
        } else {
            // Buat cashout berdasarkan billing yang ada
            return $this->createCashoutsFromBillings();
        }
    }

    // Method untuk buat single cashout (tanpa installment)
    private function createSingleCashout(): bool
    {
        $this->cashouts()->create([
            'debit_note_billing_id' => null, // Tidak terkait billing spesifik
            'insurance_id' => $this->contract->contact_id ?? null, // Ambil dari contract
            'number' => $this->generateCashoutNumber(),
            'date' => now()->toDateString(),
            'due_date' => $this->due_date,
            'currency_code' => $this->currency_code,
            'exchange_rate' => $this->exchange_rate,
            'amount' => $this->amount, // Ambil dari amount debit note
            'installment_number' => 1,
            'description' => "Cashout untuk Debit Note: {$this->number}",
            'status' => 'pending',
            'created_by' => auth()->id() ?? 1,
        ]);

        return true;
    }

    // Method untuk buat cashouts dari billings
    private function createCashoutsFromBillings(): bool
    {
        // Loop semua billing yang ada
        foreach ($this->debitNoteBillings as $index => $billing) {
            $this->cashouts()->create([
                'debit_note_billing_id' => $billing->id,
                'insurance_id' => $this->contract->contact_id ?? null,
                'number' => $this->generateCashoutNumber(),
                'date' => now()->toDateString(),
                'due_date' => $billing->due_date,
                'currency_code' => $this->currency_code,
                'exchange_rate' => $this->exchange_rate,
                'amount' => $billing->amount,
                'installment_number' => $index + 1,
                'description' => "Cashout untuk Debit Note: {$this->number} - Billing: {$billing->billing_number}",
                'status' => 'pending',
                'created_by' => auth()->id() ?? 1,
            ]);
        }

        return true;
    }

    // Helper method untuk generate nomor cashout
    private function generateCashoutNumber(): string
    {
        $prefix = 'CO';
        $date = now()->format('Ymd');
        $sequence = Cashout::whereDate('created_at', now()->toDateString())->count() + 1;
        
        return "{$prefix}/{$date}/" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    // Summary amount of cashouts by debit note
    public function getCashoutAmountAttribute(): float
    {
        return $this->cashouts->sum('amount');
    }

    // Check if debit note has been posted (has cashouts)
    public function getIsPostedAttribute(): bool
    {
        return $this->cashouts()->exists();
    }

    public function billings(): HasMany
    {
        return $this->hasMany(DebitNoteBilling::class, 'debit_note_id', 'id');
    }
}
