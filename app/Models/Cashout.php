<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cashout extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'debit_note_id',
        'debit_note_billing_id', // Link ke billing spesifik
        'insurance_id',
        'number',
        'date',
        'due_date',
        'currency_code',
        'exchange_rate',
        'amount',
        'installment_number', // Tracking installment ke berapa
        'description',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'date' => 'date',
        'due_date' => 'date',
        'exchange_rate' => 'decimal:2',
        'amount' => 'decimal:2',
    ];

    protected $appends = [
        'date_formatted',
        'due_date_formatted',
        'exchange_rate_formatted',
        'amount_formatted',
    ];

    // Formatted attributes
    public function getDateFormattedAttribute(): string
    {
        return $this->date?->format('d M Y') ?? 'Not set';
    }

    public function getDueDateFormattedAttribute(): string
    {
        return $this->due_date?->format('d M Y') ?? 'Not set';
    }

    public function getExchangeRateFormattedAttribute(): string
    {
        return number_format($this->exchange_rate, 2, ",", ".");
    }

    public function getAmountFormattedAttribute(): string
    {
        return number_format($this->amount, 2, ",", ".");
    }

    // Relationships
    public function debitNote(): BelongsTo
    {
        return $this->belongsTo(DebitNote::class, 'debit_note_id', 'id');
    }

    public function debitNoteBilling(): BelongsTo
    {
        return $this->belongsTo(DebitNoteBilling::class, 'debit_note_billing_id', 'id');
    }

    public function insurance(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'insurance_id', 'id');
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

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeByInsurance($query, $insuranceId)
    {
        return $query->where('insurance_id', $insuranceId);
    }

    public function scopeByDebitNote($query, $debitNoteId)
    {
        return $query->where('debit_note_id', $debitNoteId);
    }

    // Journal entries relationship
    public function journalEntries()
    {
        return $this->hasMany(JournalEntry::class, 'reference', 'number')
                    ->where('reference', 'LIKE', 'Cashout%');
    }

    // Get the latest journal entry
    public function journalEntry()
    {
        return $this->hasOne(JournalEntry::class, 'reference', 'number')
                    ->where('reference', 'LIKE', 'Cashout%')
                    ->latest();
    }

    // Create journal entry when cashout is created (pending state)
    public function createPendingJournalEntry()
    {
        // Ambil chart of accounts yang diperlukan
        $payableAccount = ChartOfAccount::where('code', 'LIKE', '2%')->where('name', 'LIKE', '%payable%')->first();
        $expenseAccount = ChartOfAccount::where('code', 'LIKE', '5%')->where('name', 'LIKE', '%insurance%')->first();
        
        if (!$payableAccount || !$expenseAccount) {
            return false; // Skip jika COA belum ada
        }

        $journalEntry = JournalEntry::create([
            'number' => $this->generateJournalNumber(),
            'entry_date' => $this->date,
            'reference' => "Cashout: {$this->number}",
            'description' => "Provision for cashout to {$this->insurance->display_name}",
            'status' => 'active',
            'created_by' => $this->created_by,
        ]);

        // Detail: Debit Insurance Expense, Credit Insurance Payable
        $journalEntry->details()->createMany([
            [
                'chart_of_account_id' => $expenseAccount->id,
                'debit' => $this->amount,
                'credit' => 0,
                'description' => "Insurance expense for {$this->insurance->display_name}",
            ],
            [
                'chart_of_account_id' => $payableAccount->id,
                'debit' => 0,
                'credit' => $this->amount,
                'description' => "Payable to {$this->insurance->display_name}",
            ]
        ]);

        return $journalEntry;
    }

    // Create journal entry when cashout is paid
    public function createPaidJournalEntry()
    {
        $payableAccount = ChartOfAccount::where('code', 'LIKE', '2%')->where('name', 'LIKE', '%payable%')->first();
        $cashAccount = ChartOfAccount::where('code', 'LIKE', '1%')->where('name', 'LIKE', '%cash%')->first();
        
        if (!$payableAccount || !$cashAccount) {
            return false;
        }

        $journalEntry = JournalEntry::create([
            'number' => $this->generateJournalNumber(),
            'entry_date' => now()->toDateString(),
            'reference' => "Cashout Payment: {$this->number}",
            'description' => "Payment to {$this->insurance->display_name}",
            'status' => 'active',
            'created_by' => $this->updated_by,
        ]);

        // Detail: Debit Insurance Payable, Credit Cash
        $journalEntry->details()->createMany([
            [
                'chart_of_account_id' => $payableAccount->id,
                'debit' => $this->amount,
                'credit' => 0,
                'description' => "Payment of payable to {$this->insurance->display_name}",
            ],
            [
                'chart_of_account_id' => $cashAccount->id,
                'debit' => 0,
                'credit' => $this->amount,
                'description' => "Cash payment to {$this->insurance->display_name}",
            ]
        ]);

        return $journalEntry;
    }

    // Generate journal entry number
    private function generateJournalNumber(): string
    {
        $prefix = 'JE';
        $date = now()->format('Ymd');
        $sequence = JournalEntry::whereDate('created_at', now()->toDateString())->count() + 1;
        
        return "{$prefix}/{$date}/" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    // Auto create journal entries based on status changes
    protected static function booted()
    {
        static::creating(function ($cashout) {
            // Auto-generate cashout number if not provided
            if (empty($cashout->number)) {
                $cashout->number = $cashout->generateCashoutNumber();
            }
        });

        static::created(function ($cashout) {
            // Create pending journal entry when cashout is created
            $cashout->createPendingJournalEntry();
        });

        static::updated(function ($cashout) {
            // Create paid journal entry when status changes to paid
            if ($cashout->isDirty('status') && $cashout->status === 'paid') {
                $cashout->createPaidJournalEntry();
            }
        });
    }

    // Generate cashout number
    private function generateCashoutNumber(): string
    {
        $prefix = 'CSH';
        $date = now()->format('Ym');
        $sequence = self::whereRaw('DATE_FORMAT(created_at, "%Y%m") = ?', [$date])->count() + 1;
        
        return "{$prefix}-{$date}-" . str_pad($sequence, 6, '0', STR_PAD_LEFT);
    }
}
