<?php

namespace Tests\Feature;

use App\Models\LuminousInvoice;
use App\Models\LuminousPayment;
use App\Models\CashBank;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Tests\TestCase;

// Jalankan: php -d extension=pdo_sqlite -d extension=sqlite3 vendor/phpunit/phpunit/phpunit tests/Feature/LuminousTest.php
// Selalu SQLite memori; tidak pernah menyentuh database MySQL asli.
class LuminousTest extends TestCase
{
    private User $luminous;
    private string $bibCompanyId = '11111111-1111-4111-8111-111111111111';
    private string $luminousCompanyId = '22222222-2222-4222-8222-222222222222';

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
            'database.connections.sqlite.foreign_key_constraints' => false,
        ]);
        DB::purge('sqlite');
        $this->assertSame('sqlite', DB::connection()->getDriverName());

        $this->createSchema();
        $this->luminous = User::create(['name' => 'Lum', 'email' => 'lum@test.id', 'role' => 'admin', 'password' => 'secret123']);
        $this->luminous->company_id = $this->luminousCompanyId;
        $this->luminous->save();
    }

    private function createSchema(): void
    {
        Schema::create('companies', function (Blueprint $t) {
            $t->uuid('id')->primary(); $t->string('name'); $t->string('code')->unique(); $t->timestamps();
        });
        DB::table('companies')->insert([
            ['id' => $this->bibCompanyId, 'name' => 'BIB', 'code' => 'BIB'],
            ['id' => $this->luminousCompanyId, 'name' => 'Luminous Risk Management', 'code' => 'LUMINOUS'],
        ]);
        Schema::create('users', function (Blueprint $t) {
            $t->id(); $t->string('name'); $t->string('email')->unique(); $t->string('password');
            $t->string('role')->default('admin'); $t->uuid('company_id'); $t->rememberToken(); $t->timestamps();
        });
        Schema::create('contacts', function (Blueprint $t) {
            $t->uuid('id')->primary(); $t->string('display_name'); $t->string('name')->nullable();
            $t->unsignedBigInteger('created_by')->nullable(); $t->unsignedBigInteger('updated_by')->nullable();
            $t->uuid('company_id'); $t->timestamps();
        });
        Schema::create('contact_types', function (Blueprint $t) {
            $t->id(); $t->uuid('contact_id'); $t->string('type'); $t->uuid('company_id'); $t->timestamps();
        });
        Schema::create('contract_types', function (Blueprint $t) {
            $t->id(); $t->string('code')->nullable(); $t->string('name'); $t->uuid('company_id'); $t->timestamps();
            $t->unique(['company_id', 'code']);
        });
        Schema::create('currencies', function (Blueprint $t) {
            $t->string('code', 3)->primary(); $t->string('name'); $t->timestamps();
        });
        DB::table('currencies')->insert(['code' => 'IDR', 'name' => 'Rupiah', 'created_at' => now(), 'updated_at' => now()]);
        Schema::create('contracts', function (Blueprint $t) {
            $t->uuid('id')->primary(); $t->unsignedBigInteger('contract_type_id')->nullable(); $t->string('contract_status')->default('new');
            $t->string('number');
            $t->uuid('contact_id')->nullable(); $t->date('period_start')->nullable(); $t->date('period_end')->nullable();
            $t->string('currency_code', 3)->nullable(); $t->decimal('exchange_rate', 18, 2)->nullable();
            $t->decimal('amount', 18, 2)->default(0); $t->decimal('coverage_amount', 18, 2)->default(0);
            $t->decimal('gross_premium', 18, 2)->default(0); $t->decimal('discount', 18, 2)->default(0);
            $t->decimal('discount_amount', 18, 2)->default(0);
            $t->decimal('stamp_fee', 18, 2)->default(0); $t->unsignedInteger('installment_count')->default(1);
            $t->text('memo')->nullable();
            $t->string('status')->nullable(); $t->string('policy_number')->nullable(); $t->string('cover_note_number')->nullable();
            $t->unsignedBigInteger('billing_address_id')->nullable();
            $t->unsignedBigInteger('created_by')->nullable(); $t->unsignedBigInteger('updated_by')->nullable();
            $t->uuid('company_id'); $t->timestamps();
        });
        Schema::create('contract_details', function (Blueprint $t) {
            $t->id(); $t->uuid('contract_id'); $t->uuid('insurance_id'); $t->decimal('percentage', 8, 2); $t->decimal('eng_fee', 8, 2); $t->uuid('company_id');
        });
        Schema::create('debit_notes', function (Blueprint $t) {
            $t->uuid('id')->primary(); $t->uuid('contract_id'); $t->uuid('contact_id')->nullable(); $t->string('number');
            $t->date('date')->nullable(); $t->date('due_date')->nullable(); $t->unsignedInteger('installment')->default(0);
            $t->string('currency_code', 3)->nullable(); $t->decimal('exchange_rate', 18, 2)->nullable();
            $t->decimal('amount', 18, 2)->default(0); $t->decimal('gross_premium', 18, 2)->nullable();
            $t->decimal('discount_percent', 8, 2)->nullable(); $t->decimal('discount_amount', 18, 2)->nullable();
            $t->decimal('net_premium_amount', 18, 2)->nullable(); $t->string('status')->nullable();
            $t->string('approval_status')->nullable(); $t->unsignedBigInteger('approved_by')->nullable();
            $t->dateTime('approved_at')->nullable(); $t->unsignedBigInteger('created_by')->nullable();
            $t->unsignedBigInteger('updated_by')->nullable(); $t->unsignedBigInteger('billing_address_id')->nullable();
            $t->uuid('company_id'); $t->timestamps();
        });
        Schema::create('debit_note_details', function (Blueprint $t) {
            $t->id(); $t->uuid('debit_note_id'); $t->uuid('insurance_id'); $t->decimal('amount', 18, 2);
            $t->uuid('company_id'); $t->timestamps();
        });
        Schema::create('cashouts', function (Blueprint $t) {
            $t->uuid('id')->primary(); $t->uuid('debit_note_id'); $t->uuid('insurance_id'); $t->string('number');
            $t->date('date'); $t->date('due_date')->nullable(); $t->string('currency_code', 3)->nullable();
            $t->decimal('exchange_rate', 18, 2)->default(1); $t->decimal('amount', 18, 2)->default(0);
            $t->unsignedInteger('installment_number')->default(1); $t->string('status')->default('pending');
            $t->text('description')->nullable();
            $t->unsignedBigInteger('created_by')->nullable(); $t->unsignedBigInteger('updated_by')->nullable();
            $t->uuid('company_id'); $t->timestamps();
        });
        Schema::create('billing_addresses', function (Blueprint $t) {
            $t->id(); $t->uuid('contact_id'); $t->string('name'); $t->text('address');
            $t->boolean('is_primary')->default(false); $t->uuid('company_id'); $t->timestamps();
        });
        Schema::create('debit_note_billings', function (Blueprint $t) {
            $t->uuid('id')->primary(); $t->uuid('debit_note_id'); $t->string('billing_number');
            $t->date('date'); $t->date('due_date'); $t->decimal('amount', 18, 2);
            $t->decimal('gross_premium', 18, 2)->nullable(); $t->decimal('discount_percent', 8, 2)->nullable();
            $t->decimal('discount_amount', 18, 2)->nullable(); $t->decimal('net_premium_amount', 18, 2)->nullable();
            $t->string('status'); $t->uuid('company_id'); $t->timestamps();
        });
        Schema::create('credit_notes', function (Blueprint $t) {
            $t->id(); $t->uuid('billing_id')->nullable(); $t->uuid('company_id');
            $t->date('date')->nullable(); $t->decimal('amount', 18, 2)->default(0); $t->string('status')->nullable();
        });
        Schema::create('payment_allocations', function (Blueprint $t) {
            $t->id(); $t->uuid('debit_note_billing_id')->nullable(); $t->decimal('allocation', 18, 2)->default(0);
            $t->dateTime('created_at')->nullable(); $t->string('status')->nullable();
            $t->uuid('cashout_id')->nullable(); $t->uuid('company_id');
        });
        Schema::create('chart_of_accounts', function (Blueprint $t) {
            $t->uuid('id')->primary(); $t->unsignedBigInteger('account_category_id')->nullable();
            $t->string('prefix')->nullable(); $t->string('code'); $t->string('name');
            $t->uuid('company_id'); $t->timestamps();
        });
        Schema::create('cash_banks', function (Blueprint $t) {
            $t->uuid('id')->primary(); $t->string('type'); $t->string('transaction_type');
            $t->string('number'); $t->uuid('contact_id'); $t->date('date');
            $t->uuid('chart_of_account_id'); $t->uuid('contra_account_id')->nullable();
            $t->decimal('amount', 18, 2); $t->text('description')->nullable();
            $t->string('status'); $t->uuid('company_id'); $t->timestamps();
        });
        Schema::create('journal_entries', function (Blueprint $t) {
            $t->id(); $t->string('reference')->nullable(); $t->uuid('company_id'); $t->timestamps();
        });

        (require base_path('database/migrations/2026_11_13_000001_add_cashout_payment_and_luminous_source_fields.php'))->up();
        foreach (['2026_10_22_000001_create_luminous_invoices_table', '2026_10_22_000003_add_letter_fields_to_luminous_invoices', '2026_10_22_000004_create_luminous_items_and_payments'] as $file) {
            (require base_path("database/migrations/{$file}.php"))->up();
        }
        foreach (['luminous_invoices', 'luminous_invoice_items', 'luminous_payments'] as $table) {
            Schema::table($table, fn (Blueprint $t) => $t->uuid('company_id')->nullable());
        }
        Schema::table('luminous_invoice_items', function (Blueprint $t) {
            $t->string('cashout_number')->nullable();
            $t->string('debit_note_number')->nullable();
            $t->string('policy_number')->nullable();
            $t->string('insured_name')->nullable();
            $t->string('insurance_name')->nullable();
            $t->date('cashout_date')->nullable();
        });
    }

    private function insurance(string $name): string
    {
        $id = (string) Str::uuid();
        DB::table('contacts')->insert(['id' => $id, 'display_name' => $name, 'company_id' => $this->bibCompanyId]);

        return $id;
    }

    private function luminousClient(string $name): string
    {
        $id = (string) Str::uuid();
        DB::table('contacts')->insert([
            'id' => $id, 'display_name' => $name, 'company_id' => $this->luminousCompanyId,
        ]);
        DB::table('contact_types')->insert([
            'contact_id' => $id, 'type' => 'client', 'company_id' => $this->luminousCompanyId,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        return $id;
    }

    private function cashout(string $insuranceId, float $gross, float $share, float $eng, int $installments = 1, string $status = 'pending'): string
    {
        [$contract, $debitNote, $cashout] = [(string) Str::uuid(), (string) Str::uuid(), (string) Str::uuid()];
        DB::table('contracts')->insert(['id' => $contract, 'number' => 'PL-1', 'gross_premium' => $gross, 'company_id' => $this->bibCompanyId]);
        DB::table('contract_details')->insert(['contract_id' => $contract, 'insurance_id' => $insuranceId, 'percentage' => $share, 'eng_fee' => $eng, 'company_id' => $this->bibCompanyId]);
        DB::table('debit_notes')->insert([
            'id' => $debitNote, 'contract_id' => $contract, 'number' => 'DN-1', 'installment' => $installments,
            'currency_code' => 'IDR', 'exchange_rate' => 1, 'company_id' => $this->bibCompanyId,
        ]);
        DB::table('cashouts')->insert([
            'id' => $cashout, 'debit_note_id' => $debitNote, 'insurance_id' => $insuranceId,
            'number' => 'CSH-' . Str::random(4), 'date' => '2026-05-01', 'status' => $status,
            'currency_code' => 'IDR', 'exchange_rate' => 1, 'company_id' => $this->bibCompanyId,
        ]);

        return $cashout;
    }

    private function bibUser(): User
    {
        $user = User::create(['name' => 'BIB Admin', 'email' => 'bib@test.id', 'role' => 'admin', 'password' => 'secret123']);
        $user->company_id = $this->bibCompanyId;
        $user->save();

        return $user;
    }

    public function test_paid_cashout_creates_luminous_debit_note_and_billing_once(): void
    {
        $insuranceId = $this->insurance('PT Lippo');
        $cashoutId = $this->cashout($insuranceId, 1_000_000, 50, 10, 2);
        $sourceDebitNoteId = DB::table('cashouts')->where('id', $cashoutId)->value('debit_note_id');
        $sourceBillingAddressId = DB::table('billing_addresses')->insertGetId([
            'contact_id' => $insuranceId,
            'name' => 'Original Insured',
            'address' => 'Original insured address',
            'is_primary' => true,
            'company_id' => $this->bibCompanyId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('contracts')->where('id', DB::table('debit_notes')->where('id', $sourceDebitNoteId)->value('contract_id'))
            ->update([
                'policy_number' => 'POL-7788',
                'billing_address_id' => $sourceBillingAddressId,
                'gross_premium' => 1_000_000,
                'discount' => 5,
                'discount_amount' => 50_000,
            ]);
        DB::table('debit_notes')->where('id', $sourceDebitNoteId)
            ->update(['billing_address_id' => $sourceBillingAddressId]);
        $bibUser = $this->bibUser();
        $bibContactId = (string) Str::uuid();
        DB::table('contacts')->insert([
            'id' => $bibContactId,
            'display_name' => 'BIB',
            'name' => 'BIB',
            'company_id' => $this->luminousCompanyId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $billingAddressId = DB::table('billing_addresses')->insertGetId([
            'contact_id' => $bibContactId,
            'name' => 'BIB Office',
            'address' => 'BIB Office address',
            'is_primary' => true,
            'company_id' => $this->luminousCompanyId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($bibUser)
            ->post(route('transaction.cashouts.mark-paid', $cashoutId))
            ->assertRedirect();

        $debitNote = DB::table('debit_notes')->where('source_cashout_id', $cashoutId)->first();
        $this->assertNotNull($debitNote);
        $this->assertSame($this->luminousCompanyId, $debitNote->company_id);
        $this->assertSame((int) $billingAddressId, (int) $debitNote->billing_address_id);
        $this->assertSame(25000.0, (float) $debitNote->amount);
        $this->assertSame('approved', $debitNote->approval_status);
        $this->assertSame($bibUser->id, (int) $debitNote->approved_by);
        $this->assertNotNull($debitNote->approved_at);
        $paidAt = Carbon::parse(DB::table('cashouts')->where('id', $cashoutId)->value('paid_at'));
        $this->assertSame($paidAt->toDateString(), $debitNote->date);
        $this->assertSame($paidAt->addDays(30)->toDateString(), $debitNote->due_date);

        $billing = DB::table('debit_note_billings')->where('debit_note_id', $debitNote->id)->first();
        $this->assertNotNull($billing);
        $this->assertSame($this->luminousCompanyId, $billing->company_id);
        $this->assertSame(25000.0, (float) $billing->amount);
        $this->assertSame('posted', $billing->status);
        $this->assertSame($debitNote->due_date, $billing->due_date);

        $this->actingAs($this->luminous)
            ->get(route('transaction.debit-notes.show', $debitNote->id))
            ->assertOk()
            ->assertSee('PL-1')
            ->assertSee('POL-7788')
            ->assertSee('Insurance Name')
            ->assertSee('PT Lippo')
            ->assertDontSee('Original Insured')
            ->assertSee('Original insured address')
            ->assertSee('1,000,000.00')
            ->assertSee('50,000.00');

        $coaId = (string) Str::uuid();
        DB::table('chart_of_accounts')->insert([
            'id' => $coaId,
            'account_category_id' => 3,
            'prefix' => 'BIB',
            'code' => '1010',
            'name' => 'Operating Bank',
            'company_id' => $this->bibCompanyId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $cashBank = CashBank::create([
            'type' => 'receive',
            'transaction_type' => 'bank_transaction',
            'number' => 'LUM-RECEIVE-001',
            'contact_id' => $insuranceId,
            'date' => $paidAt->toDateString(),
            'chart_of_account_id' => $coaId,
            'amount' => 25000,
            'status' => 'approved',
        ]);
        $this->get(route('transaction.payment-allocations.show', $cashBank->id))
            ->assertOk()
            ->assertSee('Related Debit Note Billings')
            ->assertSee($billing->billing_number);

        $this->actingAs($bibUser);
        $this->post(route('transaction.cashouts.mark-paid', $cashoutId))->assertRedirect();
        $this->assertSame(1, DB::table('debit_notes')->where('source_cashout_id', $cashoutId)->count());
        $this->assertSame(1, DB::table('debit_note_billings')->where('debit_note_id', $debitNote->id)->count());
    }

    public function test_luminous_can_manually_create_a_debit_note_and_billing_in_its_company(): void
    {
        $contactId = $this->insurance('BIB client');
        DB::table('contact_types')->insert([
            'contact_id' => $contactId,
            'type' => 'client',
            'company_id' => $this->bibCompanyId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $billingAddressId = DB::table('billing_addresses')->insertGetId([
            'contact_id' => $contactId,
            'name' => 'BIB client address',
            'address' => 'Shared billing address',
            'is_primary' => true,
            'company_id' => $this->bibCompanyId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($this->luminous)
            ->get(route('luminous.receivables.create'))
            ->assertOk()
            ->assertSee('BIB client');

        $this->post(route('luminous.receivables.store'), [
            'contact_id' => $contactId,
            'currency_code' => 'IDR',
            'exchange_rate' => 1,
            'date' => '2026-11-18',
            'due_date' => '2026-12-18',
            'amount' => 125000,
            'description' => 'Manual consulting fee',
        ])
            ->assertRedirect(route('luminous.receivables.index'));

        $debitNote = DB::table('debit_notes')->where('company_id', $this->luminousCompanyId)->first();
        $this->assertNotNull($debitNote);
        $this->assertNull($debitNote->source_cashout_id);
        $this->assertSame($contactId, $debitNote->contact_id);
        $this->assertSame($billingAddressId, $debitNote->billing_address_id);
        $this->assertSame(125000.0, (float) $debitNote->amount);
        $this->assertSame('approved', $debitNote->approval_status);
        $this->assertSame($this->luminous->id, (int) $debitNote->approved_by);
        $this->assertNotNull($debitNote->approved_at);

        $billing = DB::table('debit_note_billings')->where('debit_note_id', $debitNote->id)->first();
        $this->assertNotNull($billing);
        $this->assertSame($this->luminousCompanyId, $billing->company_id);
        $this->assertSame(125000.0, (float) $billing->amount);
        $this->assertSame('posted', $billing->status);
        $this->assertSame('2026-12-18', $billing->due_date);

        $this->getJson(route('api.report.piutang.index', ['format' => 'json']))
            ->assertOk()
            ->assertJsonFragment(['billing_no' => $billing->billing_number, 'amount' => 125000]);
    }

    public function test_payment_updates_status_and_rejects_overpayment(): void
    {
        $this->actingAs($this->luminous);
        $invoice = LuminousInvoice::create([
            'number' => 'LMN-001', 'recipient' => 'PT Lippo', 'description' => 'x',
            'date' => '2026-05-29', 'due_date' => '2026-06-29', 'amount' => 50000,
            'paid_amount' => 0, 'status' => 'unpaid',
        ]);

        $this->post(route('luminous.invoices.pay', $invoice), ['amount' => 20000, 'date' => '2026-06-01']);
        $this->assertSame('partial', $invoice->fresh()->status);

        $this->post(route('luminous.invoices.pay', $invoice), ['amount' => 40000, 'date' => '2026-06-02'])->assertSessionHasErrors('amount');
        $this->assertSame(1, LuminousPayment::count());

        $this->post(route('luminous.invoices.pay', $invoice), ['amount' => 30000, 'date' => '2026-06-03']);
        $this->assertSame('paid', $invoice->fresh()->status);
        $this->assertSame(0.0, $invoice->fresh()->outstanding_amount);
    }

    public function test_aging_respects_as_of_date_and_buckets(): void
    {
        $this->actingAs($this->luminous);
        $invoice = LuminousInvoice::create([
            'number' => 'LMN-9', 'recipient' => 'PT Lippo', 'description' => 'x', 'date' => '2025-12-01', 'due_date' => '2026-01-01',
            'amount' => 100000, 'paid_amount' => 40000, 'status' => 'partial',
        ]);
        LuminousPayment::create(['luminous_invoice_id' => $invoice->id, 'date' => '2026-03-01', 'amount' => 40000]);

        $early = $this->actingAs($this->luminous)->get(route('luminous.aging', ['as_of_date' => '2026-02-15']))->viewData('rows')->first();
        $this->assertSame([100000.0, 45, 'd60'], [$early['outstanding'], $early['days'], $early['bucket']]);

        $late = $this->get(route('luminous.aging', ['as_of_date' => '2026-03-15']))->viewData('rows')->first();
        $this->assertSame([60000.0, 73, 'd90'], [$late['outstanding'], $late['days'], $late['bucket']]);

        $this->get(route('luminous.aging', ['as_of_date' => '15-03-2026']))->assertOk()->assertViewHas('rows');
        $this->get(route('luminous.aging', ['from_date' => '01-12-2025', 'to_date' => '31-12-2025']))->assertViewHas('rows', fn ($r) => $r->count() === 1);
        $this->get(route('luminous.aging', ['from_date' => '01-01-2026', 'to_date' => '31-01-2026']))->assertViewHas('rows', fn ($r) => $r->isEmpty());
        $this->get(route('luminous.aging', ['as_of_date' => '2026-03-15', 'insurance_id' => $this->insurance('PT Lain')]))->assertSessionHasErrors('insurance_id');

        $this->get(route('luminous.report', ['from_date' => '2026-03-01', 'to_date' => '2026-03-31']))
            ->assertOk()->assertViewHas('receivable', 60000.0);
    }

    public function test_luminous_user_can_use_the_app_and_company_portal_is_restricted(): void
    {
        $bibContactId = $this->insurance('BIB shared contact');
        DB::table('contact_types')->insert([
            'contact_id' => $bibContactId,
            'type' => 'insurance',
            'company_id' => $this->bibCompanyId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->luminousClient('Luminous shared contact');
        $coaId = (string) Str::uuid();
        DB::table('chart_of_accounts')->insert([
            'id' => $coaId,
            'account_category_id' => 3,
            'prefix' => 'BIB',
            'code' => '1010',
            'name' => 'Operating Bank',
            'company_id' => $this->bibCompanyId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->actingAs($this->luminous)->get('/master/users')->assertOk();
        $this->getJson(route('api.chart-of-accounts.select2', ['q' => 'Operating', 'c' => 3]))
            ->assertOk()
            ->assertJsonFragment(['id' => $coaId, 'text' => 'BIB-1010 - Operating Bank']);
        $cashBank = CashBank::create([
            'type' => 'receive',
            'transaction_type' => 'bank_transaction',
            'number' => 'LUM-BR-001',
            'contact_id' => $bibContactId,
            'date' => '2026-11-23',
            'chart_of_account_id' => $coaId,
            'amount' => 1000,
            'status' => 'draft',
        ]);
        $this->assertSame($this->luminousCompanyId, $cashBank->company_id);
        $this->assertSame('Operating Bank', $cashBank->chartOfAccount->name);
        $this->getJson('/api/contact/select2')
            ->assertOk()
            ->assertSee('BIB shared contact')
            ->assertSee('Luminous shared contact');
        $this->getJson('/api/contact/select2?type=insurance')
            ->assertOk()
            ->assertSee('BIB shared contact');
        $this->get(route('luminous.invoices.index'))->assertOk();

        auth()->logout();
        foreach (['admin', 'approver'] as $role) {
            $bib = User::create(['name' => $role, 'email' => "{$role}@test.id", 'role' => $role, 'password' => 'secret123']);
            $bib->company_id = $this->bibCompanyId;
            $bib->save();
            $this->actingAs($bib)->get(route('luminous.invoices.index'))->assertForbidden();
            $this->getJson('/api/contact/select2')
                ->assertOk()
                ->assertSee('BIB shared contact')
                ->assertSee('Luminous shared contact');
            $this->getJson('/api/contact/select2?type=client')
                ->assertOk()
                ->assertSee('Luminous shared contact');
            $this->get(route('home'))->assertDontSee('Tagihan Luminous');
        }
        auth()->logout();
        $this->post(route('luminous.login.store'), ['email' => 'admin@test.id', 'password' => 'secret123'])->assertSessionHasErrors('email');
        $this->assertGuest();
        $this->post(route('luminous.login.store'), ['email' => 'lum@test.id', 'password' => 'secret123'])->assertRedirect(route('luminous.invoices.index'));
        $this->assertAuthenticatedAs($this->luminous);
    }

    public function test_luminous_navigation_only_shows_relevant_menus(): void
    {
        $this->actingAs($this->luminous);
        $navbar = view('components.navbar')->render();

        foreach ([
            'master.contacts.index',
            'transaction.debit-notes.index',
            'transaction.credit-notes.index',
            'transaction.cash-banks.index',
            'transaction.payment-allocations.index',
            'report.piutang.index',
            'report.debit-notes.index',
            'luminous.receivables.index',
            'luminous.report',
        ] as $routeName) {
            $this->assertStringContainsString(route($routeName), $navbar);
        }

        foreach ([
            'master.chart-of-accounts.index',
            'transaction.cashouts.index',
            'report.cashout.index',
        ] as $routeName) {
            $this->assertStringNotContainsString(route($routeName), $navbar);
        }

        $this->get(route('home'))
            ->assertOk()
            ->assertSee(route('report.debit-notes.index'));
    }
}
