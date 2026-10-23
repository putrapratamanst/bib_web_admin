<?php

namespace Tests\Feature;

use App\Models\LuminousInvoice;
use App\Models\LuminousPayment;
use App\Models\User;
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
        $this->luminous = User::create(['name' => 'Lum', 'email' => 'lum@test.id', 'role' => 'luminous', 'password' => 'secret123']);
    }

    private function createSchema(): void
    {
        Schema::create('users', function (Blueprint $t) {
            $t->id(); $t->string('name'); $t->string('email')->unique(); $t->string('password');
            $t->string('role')->default('admin'); $t->rememberToken(); $t->timestamps();
        });
        Schema::create('contacts', function (Blueprint $t) {
            $t->uuid('id')->primary(); $t->string('display_name'); $t->timestamps();
        });
        Schema::create('contracts', function (Blueprint $t) {
            $t->uuid('id')->primary(); $t->string('number'); $t->decimal('gross_premium', 18, 2); $t->uuid('contact_id')->nullable();
            $t->string('policy_number')->nullable(); $t->string('cover_note_number')->nullable(); $t->timestamps();
        });
        Schema::create('contract_details', function (Blueprint $t) {
            $t->id(); $t->uuid('contract_id'); $t->uuid('insurance_id'); $t->decimal('percentage', 8, 2); $t->decimal('eng_fee', 8, 2);
        });
        Schema::create('debit_notes', function (Blueprint $t) {
            $t->uuid('id')->primary(); $t->uuid('contract_id'); $t->string('number'); $t->unsignedInteger('installment')->default(0);
            $t->uuid('billing_address_id')->nullable(); $t->timestamps();
        });
        Schema::create('cashouts', function (Blueprint $t) {
            $t->uuid('id')->primary(); $t->uuid('debit_note_id'); $t->uuid('insurance_id'); $t->string('number');
            $t->date('date'); $t->string('status')->default('pending'); $t->decimal('amount', 18, 2)->default(0); $t->timestamps();
        });

        foreach (['2026_10_22_000001_create_luminous_invoices_table', '2026_10_22_000003_add_letter_fields_to_luminous_invoices', '2026_10_22_000004_create_luminous_items_and_payments'] as $file) {
            (require base_path("database/migrations/{$file}.php"))->up();
        }
    }

    private function insurance(string $name): string
    {
        $id = (string) Str::uuid();
        DB::table('contacts')->insert(['id' => $id, 'display_name' => $name]);

        return $id;
    }

    private function cashout(string $insuranceId, float $gross, float $share, float $eng, int $installments = 1, string $status = 'pending'): string
    {
        [$contract, $debitNote, $cashout] = [(string) Str::uuid(), (string) Str::uuid(), (string) Str::uuid()];
        DB::table('contracts')->insert(['id' => $contract, 'number' => 'PL-1', 'gross_premium' => $gross]);
        DB::table('contract_details')->insert(['contract_id' => $contract, 'insurance_id' => $insuranceId, 'percentage' => $share, 'eng_fee' => $eng]);
        DB::table('debit_notes')->insert(['id' => $debitNote, 'contract_id' => $contract, 'number' => 'DN-1', 'installment' => $installments]);
        DB::table('cashouts')->insert(['id' => $cashout, 'debit_note_id' => $debitNote, 'insurance_id' => $insuranceId, 'number' => 'CSH-' . Str::random(4), 'date' => '2026-05-01', 'status' => $status]);

        return $cashout;
    }

    private function invoiceForm(string $insuranceId, array $cashoutIds, string $number = 'LMN-001'): array
    {
        return [
            'cashout_ids' => $cashoutIds, 'number' => $number,
            'subject' => 'Tagihan Biaya Sosialisasi', 'city' => 'Jakarta', 'date' => '2026-05-29', 'due_date' => '2026-06-29',
            'description' => 'Biaya Sosialisasi', 'bank_name' => 'Mandiri', 'bank_account' => '118', 'account_name' => 'PT Luminous',
            'signatory_name' => 'Eko', 'signatory_title' => 'Collection Team',
        ];
    }

    public function test_create_lists_all_unbilled_basos_without_filter(): void
    {
        $lippo = $this->insurance('PT Lippo');
        $mega = $this->insurance('PT Mega');
        $this->cashout($lippo, 1_000_000, 50, 10);          // 50.000
        $this->cashout($lippo, 1_000_000, 50, 10, 2);       // 25.000 (cicilan)
        $this->cashout($mega, 2_000_000, 50, 10);           // 100.000
        $this->cashout($lippo, 1_000_000, 50, 10, 1, 'cancelled');
        $this->cashout($mega, 1_000_000, 50, 0);            // tanpa Basos

        $response = $this->actingAs($this->luminous)->get(route('luminous.invoices.create'))->assertOk();

        $this->assertEqualsCanonicalizing([50000.0, 25000.0, 100000.0], $response->viewData('cashouts')->pluck('basos')->all());
        $response->assertSee('PT Lippo')->assertSee('PT Mega');
    }

    public function test_store_uses_server_side_basos_and_blocks_double_billing_and_mixed_insurance(): void
    {
        $ins = $this->insurance('PT Lippo');
        $a = $this->cashout($ins, 1_000_000, 50, 10);
        $b = $this->cashout($ins, 1_000_000, 50, 10, 2);

        $this->actingAs($this->luminous)
            ->post(route('luminous.invoices.store'), $this->invoiceForm($ins, [$a, $b]) + ['amount' => 999999])
            ->assertRedirect(route('luminous.invoices.index'));

        $invoice = LuminousInvoice::firstOrFail();
        $this->assertSame(75000.0, (float) $invoice->amount);
        $this->assertSame('PT Lippo', $invoice->recipient);
        $this->assertSame($ins, $invoice->insurance_id);
        $this->assertCount(2, $invoice->items);

        $this->post(route('luminous.invoices.store'), $this->invoiceForm($ins, [$a], 'LMN-002'))->assertSessionHasErrors('cashout_ids');
        $this->assertSame(1, LuminousInvoice::count());

        $c = $this->cashout($ins, 1_000_000, 50, 10);
        $other = $this->cashout($this->insurance('PT Lain'), 1_000_000, 50, 10);
        $this->post(route('luminous.invoices.store'), $this->invoiceForm($ins, [$c, $other], 'LMN-003'))->assertSessionHasErrors('cashout_ids');
        $this->assertSame(1, LuminousInvoice::count());

        $this->get(route('luminous.invoices.print', $invoice))->assertOk()->assertSee('Rincian Terlampir')->assertSee('75.000,00');
    }

    public function test_payment_updates_status_and_rejects_overpayment(): void
    {
        $ins = $this->insurance('PT Lippo');
        $this->actingAs($this->luminous)->post(route('luminous.invoices.store'), $this->invoiceForm($ins, [$this->cashout($ins, 1_000_000, 50, 10)]));
        $invoice = LuminousInvoice::firstOrFail();

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
        $invoice = LuminousInvoice::create([
            'number' => 'LMN-9', 'recipient' => 'PT Lippo', 'description' => 'x', 'date' => '2025-12-01', 'due_date' => '2026-01-01',
            'amount' => 100000, 'paid_amount' => 40000, 'status' => 'partial',
        ]);
        LuminousPayment::create(['luminous_invoice_id' => $invoice->id, 'date' => '2026-03-01', 'amount' => 40000]);

        $early = $this->actingAs($this->luminous)->get(route('luminous.aging', ['as_of_date' => '2026-02-15']))->viewData('rows')->first();
        $this->assertSame([100000.0, 45, 'd60'], [$early['outstanding'], $early['days'], $early['bucket']]);

        $late = $this->get(route('luminous.aging', ['as_of_date' => '2026-03-15']))->viewData('rows')->first();
        $this->assertSame([60000.0, 73, 'd90'], [$late['outstanding'], $late['days'], $late['bucket']]);

        $this->get(route('luminous.aging', ['as_of_date' => '15-03-2026']))->assertViewHas('rows', fn ($r) => $r->count() === 1 && $r->first()['days'] === 73);
        $this->get(route('luminous.aging', ['from_date' => '01-12-2025', 'to_date' => '31-12-2025']))->assertViewHas('rows', fn ($r) => $r->count() === 1);
        $this->get(route('luminous.aging', ['from_date' => '01-01-2026', 'to_date' => '31-01-2026']))->assertViewHas('rows', fn ($r) => $r->isEmpty());
        $this->get(route('luminous.aging', ['as_of_date' => '2026-03-15', 'insurance_id' => $this->insurance('PT Lain')]))->assertViewHas('rows', fn ($r) => $r->isEmpty());

        $this->get(route('luminous.report', ['from_date' => '2026-03-01', 'to_date' => '2026-03-31']))
            ->assertOk()->assertViewHas('receivable', 60000.0);
    }

    public function test_luminous_user_is_locked_to_luminous_routes_and_has_own_login(): void
    {
        $this->actingAs($this->luminous)->get('/master/users')->assertRedirect(route('luminous.invoices.index'));
        $this->getJson('/api/contact/select2')->assertForbidden();
        $this->get(route('luminous.invoices.index'))->assertOk();

        auth()->logout();
        foreach (['admin', 'approver'] as $role) {
            $bib = User::create(['name' => $role, 'email' => "{$role}@test.id", 'role' => $role, 'password' => 'secret123']);
            $this->actingAs($bib)->get(route('luminous.invoices.index'))->assertForbidden();
            $this->get(route('home'))->assertDontSee('Tagihan Luminous');
        }
        auth()->logout();
        $this->post(route('luminous.login.store'), ['email' => 'admin@test.id', 'password' => 'secret123'])->assertSessionHasErrors('email');
        $this->assertGuest();
        $this->post(route('luminous.login.store'), ['email' => 'lum@test.id', 'password' => 'secret123'])->assertRedirect(route('luminous.invoices.index'));
        $this->assertAuthenticatedAs($this->luminous);
    }
}
