<?php

namespace App\Http\Controllers\Api\Report;

use App\Exports\PiutangReportExport;
use App\Http\Controllers\Controller;
use App\Models\DebitNote;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class PiutangReportController extends Controller
{
    public function index(Request $request)
    {
        $as_of_date = $request->input('as_of_date') ?? now()->toDateString();
        $from_date = $request->input('from_date') ?
            Carbon::parse($request->input('from_date'))->format('Y-m-d') :
            null;

        $to_date = $request->input('to_date') ?
            Carbon::parse($request->input('to_date'))->format('Y-m-d') :
            null;
        $sql = "SELECT 
            a.id, 
            a.number, 
            a.DN, 
            a.currency_code, 
            a.billing_date,
            a.due_date,
            COALESCE(b.CN, 0) as CN, 
            COALESCE(c.allocation, 0) as allocation 
        FROM 
            (
                SELECT 
                    id, 
                    number, 
                    SUM(amount) AS DN, 
                    currency_code,
                    date as billing_date,
                    due_date
                FROM 
                    debit_notes 
                WHERE 
                     (
                        (? IS NULL OR ? IS NULL)
                        AND date <= ?
                    )
                    OR (
                        ? IS NOT NULL 
                        AND ? IS NOT NULL 
                        AND date BETWEEN ? AND ?
                    )
                GROUP BY 
                    id, 
                    number, 
                    currency_code,
                    date,
                    due_date
            ) a 
            LEFT JOIN (
                SELECT 
                    debit_note_id, 
                    SUM(amount) AS CN 
                FROM 
                    credit_notes 
                WHERE 
                    date <= ?
                GROUP BY 
                    debit_note_id
            ) b ON a.id = b.debit_note_id 
            LEFT JOIN (
                SELECT 
                    debit_note_id, 
                    SUM(allocation) AS allocation 
                FROM 
                    payment_allocations 
                WHERE 
                    created_at <= ?
                GROUP BY 
                    debit_note_id
            ) c ON a.id = c.debit_note_id";

        // Menyiapkan binding parameters untuk query utama
        $mainQueryBindings = [
            $from_date,              // ? IS NULL
            $to_date,               // ? IS NULL
            $as_of_date,           // date <= ?
            $from_date,            // ? IS NOT NULL
            $to_date,             // ? IS NOT NULL
            $from_date,          // BETWEEN ? 
            $to_date            // AND ?
        ];

        // Binding parameters untuk subquery credit notes dan payment allocations
        $allBindings = array_merge(
            $mainQueryBindings,
            [$as_of_date],  // untuk credit notes where clause
            [$as_of_date]   // untuk payment allocations where clause
        );
        Log::info("SQL Query:", [
            'query' => vsprintf(str_replace('?', "'%s'", $sql), $allBindings)
        ]);
        $result = DB::select($sql, $allBindings);

        // Transform hasil query ke format yang dibutuhkan
        $formattedResults = [];
        foreach ($result as $row) {
           
            $amount = (float) $row->DN;
            $creditNote = (float) $row->CN;
            $allocation = (float) $row->allocation;

            // Parse dates
            $dueDate = $row->due_date ? Carbon::parse($row->due_date) : null;
            $billingDate = $row->billing_date ? Carbon::parse($row->billing_date) : null;
            $daysUntilDue = $dueDate ? $dueDate->diffInDays(Carbon::now(), false) : null;

            $formattedResults[] = [
                'billing_no' => $row->number,
                'billing_date' => $billingDate ? $billingDate->toDateString() : null,
                'due_date' => $dueDate ? $dueDate->toDateString() : null,
                'days_until_due' => $daysUntilDue,
                'is_overdue' => $daysUntilDue !== null ? $daysUntilDue < 0 : false,
                'amount' => $amount,
                'credit_note' => $creditNote,
                'allocation' => $allocation,
                'outstanding' => $amount - $creditNote - $allocation,
                'currency_code' => $row->currency_code,
                'row_type' => 'debit_note'
            ];
        }

        $debitNotes = collect($formattedResults);

        if ($request->input('format') == "json") {
            return response()->json([
                'report' => $debitNotes
            ]);
        } elseif ($request->input('format') == "excel") {
            return Excel::download(new PiutangReportExport($debitNotes), 'Report_Piutang.xlsx');
        } else {
            return view('api.report.piutang', [
                'report' => $debitNotes
            ]);
        }
    }

    public function indexOld(Request $request)
    {
        $as_of_date = $request->input('as_of_date') ?? now()->toDateString();
        $from_date  = $request->input('from_date');
        $to_date    = $request->input('to_date');
        $client_id  = $request->input('client_id');

        $asOf = Carbon::parse($as_of_date)->endOfDay();

        // Log the query for debugging
        DB::enableQueryLog();

        $query = DebitNote::with([
            'creditNotes',
            'paymentAllocations',
            'contract',
            'contract.creditNotes' => function ($q) use ($asOf) {
                $q->where('date', '<=', $asOf)
                    ->where('status', 'active');
            },
            'debitNoteBillings' => function ($q) {
                $q->where('status', 'paid');
            },
            'contract.contact'
        ])
            // filter range billing date
            ->when($from_date && $to_date, function ($q) use ($from_date, $to_date) {
                $q->whereBetween('date', [
                    Carbon::parse($from_date)->startOfDay(),
                    Carbon::parse($to_date)->endOfDay()
                ]);
            })
            ->when($from_date && !$to_date, function ($q) use ($from_date) {
                $q->where('date', '>=', Carbon::parse($from_date)->startOfDay());
            })
            ->when(!$from_date && $to_date, function ($q) use ($to_date) {
                $q->where('date', '<=', Carbon::parse($to_date)->endOfDay());
            })
            // filter by client (via contract relasi)
            ->when($client_id, function ($q) use ($client_id) {
                $q->whereHas('contract', function ($q2) use ($client_id) {
                    $q2->where('contact_id', $client_id);
                });
            });

        $result = [];

        // Get raw SQL query for debugging
        $rawQuery = $query->toSql();
        $bindings = $query->getBindings();
        Log::info("Raw Query: " . $rawQuery);
        Log::info("Query Bindings:", $bindings);

        $debitNotes = $query->get();

        // Debug: Check debit notes and their billings
        Log::info("Total Debit Notes: " . $debitNotes->count());
        foreach ($debitNotes as $dn) {
            Log::info("========================");
            Log::info("Debit Note: {$dn->number}");
            Log::info("Date: " . $dn->date);
            Log::info("Amount: " . $dn->amount);
            Log::info("Billings count: " . $dn->debitNoteBillings->count());

            // Debug billing relationship
            $billingQuery = $dn->debitNoteBillings()->toSql();
            Log::info("Billing Query for DN {$dn->number}: " . $billingQuery);

            if ($dn->debitNoteBillings->isEmpty()) {
                Log::info("- No billings found for DN {$dn->number}");
            } else {
                foreach ($dn->debitNoteBillings as $billing) {
                    Log::info("- Found Billing:");
                    Log::info("  Number: {$billing->billing_number}");
                    Log::info("  Date: " . $billing->date);
                    Log::info("  Amount: " . $billing->amount);
                    Log::info("  Status: " . $billing->status);
                }
            }
        }
        foreach ($debitNotes as $dn) {
            // Total Credit Notes sampai as_of_date - ambil dari contract dulu
            $credit_total = $dn->contract
                ->creditNotes()
                ->where('debit_note_id', $dn->id)
                ->where('date', '<=', $asOf)
                ->where('status', 'active')
                ->sum('amount');

            // Total Allocation sampai as_of_date
            $alloc_total = $dn->paymentAllocations()
                ->where('created_at', '<=', $asOf)
                ->sum('allocation');

            $amount = (float) $dn->amount;

            // Calculate days until due
            $dueDate = $dn->due_date ? Carbon::parse($dn->due_date) : null;
            $daysUntilDue = $dueDate ? $dueDate->diffInDays($asOf, false) : null;

            // Base data untuk setiap debit note
            $baseData = [
                'billing_no'       => $dn->number,
                'billing_date'     => $dn->date ? Carbon::parse($dn->date)->toDateString() : null,
                'client_name'      => $dn->contract->contact->name ?? 'N/A',
                'due_date'         => $dueDate ? $dueDate->toDateString() : null,
                'days_until_due'   => $daysUntilDue,
                'is_overdue'       => $daysUntilDue !== null ? $daysUntilDue < 0 : false,
                'amount'           => $amount,
                'credit_note'      => $credit_total,
                'allocation'       => $alloc_total,
                'outstanding'      => $amount - $credit_total - $alloc_total,
                'billing_number'   => null,
                'billing_date'     => null,
                'billing_due'      => null
            ];

            // Selalu tampilkan baris debit note parent
            $baseData['row_type'] = 'debit_note';
            $result[] = $baseData;

            // Jika ada billing, tambahkan baris untuk setiap billing di bawah debit note
            if ($dn->debitNoteBillings->isNotEmpty()) {
                // Hitung proporsi credit note untuk setiap billing berdasarkan amount
                $totalBillingAmount = $dn->debitNoteBillings->sum('amount');

                foreach ($dn->debitNoteBillings as $billing) {
                    $billingDueDate = $billing->due_date ? Carbon::parse($billing->due_date) : null;
                    $billingDaysUntilDue = $billingDueDate ? $billingDueDate->diffInDays($asOf, false) : null;

                    // Hitung proporsi credit note dan allocation untuk billing ini
                    $proportion = $totalBillingAmount > 0 ? ($billing->amount / $totalBillingAmount) : 0;
                    $billingCreditNote = $credit_total * $proportion;
                    $billingAllocation = $alloc_total * $proportion;

                    $result[] = [
                        'billing_no'              => '└─ ' . $billing->billing_number,  // Indentasi visual untuk billing
                        'billing_date'            => $billing->date ? Carbon::parse($billing->date)->toDateString() : null,
                        'billing_due'             => $billingDueDate ? $billingDueDate->toDateString() : null,
                        'client_name'             => $dn->contract->contact->name ?? 'N/A',
                        'days_until_due'          => $billingDaysUntilDue,
                        'amount'                  => $billing->amount,
                        'credit_note'             => $billingCreditNote,        // Proporsi credit note
                        'allocation'              => $billingAllocation,        // Proporsi allocation
                        'outstanding'             => $billing->amount - $billingCreditNote - $billingAllocation,
                        'is_overdue'              => $billingDaysUntilDue !== null ? $billingDaysUntilDue < 0 : false,
                        'row_type'                => 'billing',
                        'parent_dn'               => $dn->number
                    ];
                    Log::info("- Billing Data Added");
                }
            }
        }

        $debitNotes = collect($result);

        // Log queries that were executed
        Log::info("Executed Queries:", DB::getQueryLog());

        // Debug: Cetak data billing untuk pengecekan
        foreach ($debitNotes as $index => $data) {
            if (isset($data['billing_number'])) {
                Log::info("Row {$index}: Billing Number: " . $data['billing_number']);
            }
        }

        if ($request->input('format') == "json") {
            return response()->json([
                'report' => $debitNotes
            ]);
        } elseif ($request->input('format') == "excel") {
            return Excel::download(new PiutangReportExport($debitNotes), 'Report_Piutang.xlsx');
        } else {
            return view('api.report.piutang', [
                'report' => $debitNotes
            ]);
        }
    }
}
