<?php

namespace App\Http\Controllers\Api\Report;

use App\Exports\ConsoleReportExport;
use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\ContractDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class ConsoleReportController extends Controller
{
    public function index(Request $request)
    {
        $from_date = $request->input('from_date');
        $to_date = $request->input('to_date');

        $from_date = $from_date ? $from_date : date('Y-m-01');
        $to_date = $to_date ? $to_date : date('Y-m-t');

        $from_date = date('Y-m-d', strtotime($from_date));
        $to_date = date('Y-m-d', strtotime($to_date));

        $report = Contract::with(['details', 'debitNotes' => function ($query) {
                $query->orderBy('installment', 'asc');
            }])
            ->whereBetween('period_start', [$from_date, $to_date])
            // ->where('policy_number', '1101092300102')
            // ->where('number', 'C-000102')
            // ->where('number', 'C-000059')
            // ->where('number', 'C-000178')
            // ->where('number', 'C-000206')
            // ->where('number', 'C-000172')
            ->orderBy('number', 'asc')
            ->get()
            ->map(function ($c) {
                $details = $c->debitNotes->map(function ($dn) use ($c) {
                    $total_brokerage_fee = $c->details->reduce(function ($carry, $detail) use ($dn, $c) {
                        $share_amount = $dn->amount * $detail->percentage / 100;
                        $brokerage_fee_amount = $share_amount * ($detail->brokerage_fee - $c->discount) / 100;
                        return $carry + $brokerage_fee_amount;
                    }, 0);

                    $total_eng_fee = $c->details->reduce(function ($carry, $detail) use ($dn, $c) {
                        // eng fee di ambil dari gross premi
                        $share_amount = $c->gross_premium * $detail->percentage / 100;
                        $eng_fee_amount = $share_amount * $detail->eng_fee / 100;
                        return $carry + $eng_fee_amount;
                    }, 0);

                    return [
                        'id' => $dn->id,
                        'number' => $dn->number,
                        'installment' => $dn->installment,
                        'date' => $dn->date_formatted,
                        'due_date' => $dn->due_date_formatted,
                        'amount' => $dn->amount * 1,
                        'credit_note_amount' => $dn->credit_note_amount * 1,
                        'total_brokerage_fee' => $total_brokerage_fee,
                        'total_eng_fee' => $total_eng_fee,
                        'nett_income_bib' => $total_brokerage_fee - $dn->credit_note_amount,
                        'details' => $c->details->map(function ($detail) use ($dn, $c) {
                            $share_amount = $dn->amount * $detail->percentage / 100;
                            $brokerage_fee_amount = $share_amount * ($detail->brokerage_fee - $c->discount) / 100;

                            $gross_premium = ($c->gross_premium *$detail->percentage / 100) * 1;
                            $eng_fee_amount = $gross_premium * $detail->eng_fee / 100;

                            $tanggal_receive = $dn->receiveDate;
                            $nominal_receive = $dn->receiveAmount * 1;
                            $nominal_receive_billing = $share_amount * 1;
                            $hutang_receive = $nominal_receive - $nominal_receive_billing < 0 ? 0 : $nominal_receive - $nominal_receive_billing;
                            $piutang_receive = $nominal_receive - $nominal_receive_billing > 0 ? 0 : $nominal_receive - $nominal_receive_billing;

                            $tanggal_pay = $dn->payDate;
                            $nominal_pay = $dn->payAmount * 1;
                            $nominal_pay_billing = $share_amount - ($brokerage_fee_amount * 1.02) - $eng_fee_amount;
                            $hutang_pay = $nominal_pay - $nominal_pay_billing < 0 ? 0 : $nominal_pay - $nominal_pay_billing;
                            $piutang_pay = $nominal_pay - $nominal_pay_billing > 0 ? 0 : $nominal_pay - $nominal_pay_billing;

                            return [
                                'insurance' => $detail->insurance->display_name,
                                'share' => $detail->percentage_formatted,
                                'share_amount' => $share_amount * 1,
                                'brokerage_fee' => $detail->brokerage_fee_formatted,
                                'brokerage_fee_amount' => $brokerage_fee_amount,
                                'eng_fee' => $detail->eng_fee_formatted,
                                'eng_fee_amount' => $eng_fee_amount,
                                'ppn' => $brokerage_fee_amount * 0,
                                'pph_23' => $brokerage_fee_amount * 0.02,
                                'tanggal_receive' => $tanggal_receive,
                                'nominal_receive' => $nominal_receive,
                                'nominal_receive_billing' => $nominal_receive_billing,
                                'hutang_receive' => $hutang_receive,
                                'piutang_receive' => $piutang_receive,
                                'tanggal_pay' => $tanggal_pay,
                                'nominal_pay' => $nominal_pay,
                                'nominal_pay_billing' => $nominal_pay_billing,
                                'hutang_pay' => $hutang_pay,
                                'piutang_pay' => $piutang_pay,
                            ];
                        }),
                    ];
                });

                // (gross * share) - (discount * share) - (brokerage fee) - (eng fee) + biaya polis + pph23
        
                return [
                    'number' => $c->number,
                    'contract_status' => Str::ucfirst($c->contract_status),
                    'contact_group' => $c->contact ? $c->contact->contactGroup ? $c->contact->contactGroup->display_name : 'N/A' : 'N/A',
                    'contact' => $c->contact ? $c->contact->display_name : 'N/A',
                    'contract_type' => $c->contractType->name,
                    'policy_number' => $c->policy_number,
                    'period_start' => $c->period_start_formatted,
                    'period_end' => $c->period_end_formatted,
                    'installment_count' => $c->installment_count,
                    'currency_code' => $c->currency_code,
                    'coverage_amount' => $c->coverage_amount * 1,
                    'gross_premium' => $c->gross_premium * 1,
                    'discount' => $c->discount_formatted,
                    'discount_amount' => $c->discount_amount * 1,
                    'stamp_fee' => $c->stamp_fee * 1,
                    'amount' => $c->amount * 1,
                    'debit_notes' => $details,
                ];
            });

        if ($request->input('format') == "json") {
            return response()->json([
                'report' => $report
            ]);
        }
        else if ($request->input('format') == "excel") {
            return Excel::download(new ConsoleReportExport($report), 'report.xlsx');
        }
        else {
            return view('api.report.console', [
                'report' => $report
            ]);
        }
    }
}
