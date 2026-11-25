<?php

namespace App\Services;

use App\Models\Cashout;
use App\Models\Company;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class LuminousReceivableService
{
    public function amountForCashout(Cashout $cashout): float
    {
        $cashout->loadMissing('debitNote.contract.details');

        $contract = $cashout->debitNote?->contract;
        $detail = $contract?->details->firstWhere('insurance_id', $cashout->insurance_id);

        if (! $detail) {
            return 0.0;
        }

        $installments = max(1, (int) $cashout->debitNote->installment);

        return round((float) $contract->gross_premium * $detail->percentage / 100 * $detail->eng_fee / 100 / $installments, 2);
    }

    public function createFromPaidCashout(Cashout $cashout): void
    {
        $bibCompanyId = Company::idFor('BIB');
        if (! $bibCompanyId || $cashout->company_id !== $bibCompanyId || $cashout->status !== 'paid') {
            return;
        }

        $cashout->loadMissing('debitNote.contract.details');
        $amount = $this->amountForCashout($cashout);
        if ($amount <= 0) {
            return;
        }

        $luminousCompanyId = Company::idFor('LUMINOUS');
        if (! $luminousCompanyId) {
            throw new RuntimeException('Company LUMINOUS belum disiapkan.');
        }

        $sourceContract = $cashout->debitNote?->contract;
        if (! $sourceContract) {
            throw new RuntimeException("Kontrak untuk cashout {$cashout->number} tidak ditemukan.");
        }

        $paidAt = $cashout->paid_at ? Carbon::parse($cashout->paid_at) : now();
        $dueDate = $paidAt->copy()->addDays(30);
        $userId = $cashout->updated_by ?: $cashout->created_by;

        DB::transaction(function () use ($cashout, $amount, $sourceContract, $paidAt, $dueDate, $userId, $bibCompanyId, $luminousCompanyId) {
            $luminousCompany = DB::table('companies')
                ->where('id', $luminousCompanyId)
                ->lockForUpdate()
                ->first();

            if (! $luminousCompany) {
                throw new RuntimeException('Company LUMINOUS tidak ditemukan.');
            }

            if (DB::table('debit_notes')->where('source_cashout_id', $cashout->id)->exists()) {
                return;
            }

            $contactName = DB::table('companies')->where('id', $bibCompanyId)->value('name');
            if (! $contactName) {
                throw new RuntimeException('Nama company BIB tidak ditemukan.');
            }

            $contactId = DB::table('contacts')
                ->where('display_name', $contactName)
                ->value('id');

            if (! $contactId) {
                $contactId = (string) Str::uuid();
                DB::table('contacts')->insert([
                    'id' => $contactId,
                    'display_name' => $contactName,
                    'name' => $contactName,
                    'created_by' => $userId,
                    'updated_by' => $userId,
                    'company_id' => $luminousCompanyId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            if (! DB::table('contact_types')
                ->where('contact_id', $contactId)
                ->where('type', 'client')
                ->exists()) {
                DB::table('contact_types')->insert([
                    'contact_id' => $contactId,
                    'type' => 'client',
                    'company_id' => $luminousCompanyId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $billingAddressId = $this->primaryBillingAddressId($contactId);

            $contractTypeId = DB::table('contract_types')
                ->where('company_id', $luminousCompanyId)
                ->where('code', 'LUMENG')
                ->value('id');

            if (! $contractTypeId) {
                $contractTypeId = DB::table('contract_types')->insertGetId([
                    'code' => 'LUMENG',
                    'name' => 'Engineering Fee',
                    'company_id' => $luminousCompanyId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $contractNumber = 'LUM-ENG-' . str_replace('-', '', $sourceContract->id);
            $luminousContract = DB::table('contracts')
                ->where('company_id', $luminousCompanyId)
                ->where('number', $contractNumber)
                ->lockForUpdate()
                ->first();

            if (! $luminousContract) {
                $contractId = (string) Str::uuid();
                $periodStart = $sourceContract->period_start
                    ? Carbon::parse($sourceContract->period_start)->toDateString()
                    : $paidAt->toDateString();
                $periodEnd = $sourceContract->period_end
                    ? Carbon::parse($sourceContract->period_end)->toDateString()
                    : $periodStart;

                DB::table('contracts')->insert([
                    'id' => $contractId,
                    'contract_type_id' => $contractTypeId,
                    'contract_status' => 'new',
                    'number' => $contractNumber,
                    'contact_id' => $contactId,
                    'period_start' => $periodStart,
                    'period_end' => $periodEnd,
                    'currency_code' => $cashout->debitNote->currency_code,
                    'exchange_rate' => $cashout->debitNote->exchange_rate,
                    'amount' => 0,
                    'coverage_amount' => $amount,
                    'gross_premium' => $amount,
                    'discount' => 0,
                    'stamp_fee' => 0,
                    'status' => 'active',
                    'created_by' => $userId,
                    'updated_by' => $userId,
                    'company_id' => $luminousCompanyId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $luminousContract = DB::table('contracts')->where('id', $contractId)->first();
            }

            $debitNoteNumber = $this->nextNumber(
                'debit_notes',
                'number',
                'LUM/D' . $paidAt->format('y/m') . '-',
                $luminousCompanyId
            );
            $billingNumber = $this->nextNumber(
                'debit_note_billings',
                'billing_number',
                'LUM-B-' . $paidAt->format('Ymd') . '-',
                $luminousCompanyId
            );
            $debitNoteId = (string) Str::uuid();
            $now = now();

            DB::table('debit_notes')->insert([
                'id' => $debitNoteId,
                'contract_id' => $luminousContract->id,
                'contact_id' => $contactId,
                'billing_address_id' => $billingAddressId,
                'number' => $debitNoteNumber,
                'source_cashout_id' => $cashout->id,
                'date' => $paidAt->toDateString(),
                'due_date' => $dueDate->toDateString(),
                'installment' => 1,
                'currency_code' => $cashout->debitNote->currency_code,
                'exchange_rate' => $cashout->debitNote->exchange_rate,
                'amount' => $amount,
                'gross_premium' => $amount,
                'discount_percent' => 0,
                'discount_amount' => 0,
                'net_premium_amount' => $amount,
                'status' => 'active',
                'approval_status' => 'approved',
                'approved_by' => $userId,
                'approved_at' => $now,
                'created_by' => $userId,
                'updated_by' => $userId,
                'company_id' => $luminousCompanyId,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            DB::table('debit_note_billings')->insert([
                'id' => (string) Str::uuid(),
                'debit_note_id' => $debitNoteId,
                'billing_number' => $billingNumber,
                'date' => $paidAt->toDateString(),
                'due_date' => $dueDate->toDateString(),
                'amount' => $amount,
                'gross_premium' => $amount,
                'discount_percent' => 0,
                'discount_amount' => 0,
                'net_premium_amount' => $amount,
                'status' => 'posted',
                'company_id' => $luminousCompanyId,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            DB::table('contracts')
                ->where('id', $luminousContract->id)
                ->increment('amount', $amount, ['updated_at' => $now]);
        });
    }

    public function createManual(array $data, int $userId): void
    {
        $luminousCompanyId = Company::idFor('LUMINOUS');
        if (! $luminousCompanyId || auth()->user()?->company_id !== $luminousCompanyId) {
            throw new RuntimeException('Tagihan manual hanya dapat dibuat oleh user Luminous.');
        }

        $date = Carbon::parse($data['date']);
        $dueDate = Carbon::parse($data['due_date']);
        $amount = round((float) $data['amount'], 2);

        DB::transaction(function () use ($data, $userId, $luminousCompanyId, $date, $dueDate, $amount) {
            $luminousCompany = DB::table('companies')
                ->where('id', $luminousCompanyId)
                ->lockForUpdate()
                ->first();

            if (! $luminousCompany) {
                throw new RuntimeException('Company LUMINOUS tidak ditemukan.');
            }

            $contractTypeId = DB::table('contract_types')
                ->where('company_id', $luminousCompanyId)
                ->where('code', 'LUMMAN')
                ->value('id');

            if (! $contractTypeId) {
                $contractTypeId = DB::table('contract_types')->insertGetId([
                    'code' => 'LUMMAN',
                    'name' => 'Manual Receivable',
                    'company_id' => $luminousCompanyId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $contractNumber = $this->nextNumber(
                'contracts',
                'number',
                'LUM-MAN-' . $date->format('ym') . '-',
                $luminousCompanyId
            );
            $debitNoteNumber = $this->nextNumber(
                'debit_notes',
                'number',
                'LUM/M' . $date->format('y/m') . '-',
                $luminousCompanyId
            );
            $billingNumber = $this->nextNumber(
                'debit_note_billings',
                'billing_number',
                'LUM-MB-' . $date->format('Ymd') . '-',
                $luminousCompanyId
            );
            $now = now();
            $contractId = (string) Str::uuid();
            $debitNoteId = (string) Str::uuid();
            $billingAddressId = $this->primaryBillingAddressId($data['contact_id']);

            DB::table('contracts')->insert([
                'id' => $contractId,
                'contract_type_id' => $contractTypeId,
                'contract_status' => 'new',
                'number' => $contractNumber,
                'policy_number' => $contractNumber,
                'contact_id' => $data['contact_id'],
                'period_start' => $date->toDateString(),
                'period_end' => $dueDate->toDateString(),
                'currency_code' => $data['currency_code'],
                'exchange_rate' => $data['exchange_rate'],
                'coverage_amount' => $amount,
                'gross_premium' => $amount,
                'discount' => 0,
                'stamp_fee' => 0,
                'amount' => $amount,
                'installment_count' => 1,
                'memo' => $data['description'],
                'status' => 'active',
                'created_by' => $userId,
                'updated_by' => $userId,
                'company_id' => $luminousCompanyId,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            DB::table('debit_notes')->insert([
                'id' => $debitNoteId,
                'contract_id' => $contractId,
                'contact_id' => $data['contact_id'],
                'billing_address_id' => $billingAddressId,
                'number' => $debitNoteNumber,
                'date' => $date->toDateString(),
                'due_date' => $dueDate->toDateString(),
                'installment' => 1,
                'currency_code' => $data['currency_code'],
                'exchange_rate' => $data['exchange_rate'],
                'amount' => $amount,
                'gross_premium' => $amount,
                'discount_percent' => 0,
                'discount_amount' => 0,
                'net_premium_amount' => $amount,
                'status' => 'active',
                'approval_status' => 'approved',
                'approved_by' => $userId,
                'approved_at' => $now,
                'created_by' => $userId,
                'updated_by' => $userId,
                'company_id' => $luminousCompanyId,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            DB::table('debit_note_details')->insert([
                'debit_note_id' => $debitNoteId,
                'insurance_id' => $data['contact_id'],
                'amount' => $amount,
                'company_id' => $luminousCompanyId,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            DB::table('debit_note_billings')->insert([
                'id' => (string) Str::uuid(),
                'debit_note_id' => $debitNoteId,
                'billing_number' => $billingNumber,
                'date' => $date->toDateString(),
                'due_date' => $dueDate->toDateString(),
                'amount' => $amount,
                'gross_premium' => $amount,
                'discount_percent' => 0,
                'discount_amount' => 0,
                'net_premium_amount' => $amount,
                'status' => 'posted',
                'company_id' => $luminousCompanyId,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        });
    }

    private function nextNumber(string $table, string $column, string $prefix, string $companyId): string
    {
        $lastNumber = DB::table($table)
            ->where('company_id', $companyId)
            ->where($column, 'like', $prefix . '%')
            ->orderByDesc($column)
            ->value($column);

        $sequence = $lastNumber
            ? (int) substr($lastNumber, strlen($prefix)) + 1
            : 1;

        return $prefix . str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
    }

    private function primaryBillingAddressId(string $contactId): ?int
    {
        return DB::table('billing_addresses')
            ->where('contact_id', $contactId)
            ->orderByDesc('is_primary')
            ->orderBy('id')
            ->value('id');
    }
}
