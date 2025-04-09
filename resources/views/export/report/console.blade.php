<!DOCTYPE html>
<html>

<head>
    <style>
        th {
            background-color: #0000FF;
            color: white;
            font-weight: bold;
            text-align: center;
        }

        td {
            text-align: center;
        }
    </style>
</head>

<body>

<table class="table table-bordered table-sm table-striped">
            <thead>
                <tr>
                    <th rowspan="2" class="text-center align-middle">Number</th>
                    <th rowspan="2" class="text-center align-middle">Status</th>
                    <th rowspan="2" class="text-center align-middle">Group</th>
                    <th rowspan="2" class="text-center align-middle">Contact</th>
                    <th rowspan="2" class="text-center align-middle">Insurance</th>
                    <th rowspan="2" class="text-center align-middle">Inst</th>
                    <th rowspan="2" class="text-center align-middle">Type</th>
                    <th rowspan="2" class="text-center align-middle">Policy Number</th>
                    <th colspan="4" class="text-center align-middle">Debit Note</th>
                    <th colspan="2" class="text-center align-middle">Credit Note</th>
                    <th rowspan="2" class="text-center align-middle">Period Start</th>
                    <th rowspan="2" class="text-center align-middle">Period End</th>
                    <th colspan="4" class="text-center align-middle">Special Table Marine Cargo</th>
                    <th rowspan="2" class="text-center align-middle">Coverage Amount</th>
                    <th rowspan="2" class="text-center align-middle">Gross Premium</th>
                    <th colspan="2" class="text-center align-middle">Discount</th>
                    <th rowspan="2" class="text-center align-middle">Stamp Fee</th>
                    <th rowspan="2" class="text-center align-middle">Net Premium</th>
                    <th rowspan="2" class="text-center align-middle">Total CN Amount</th>
                    <th colspan="2" class="text-center align-middle">Share</th>
                    <th colspan="2" class="text-center align-middle">Brokerage Fee</th>
                    <th colspan="2" class="text-center align-middle">Eng Fee</th>
                    <th rowspan="2" class="text-center align-middle">Nett Income BIB</th>
                    <th rowspan="2" class="text-center align-middle">PPN</th>
                    <th rowspan="2" class="text-center align-middle">PPh 23</th>
                    <th colspan="3" class="text-center align-middle">Tertanggung Bayar ke BIB</th>
                    <th colspan="2" class="text-center align-middle">Selisih Bayar</th>
                    <th colspan="3" class="text-center align-middle">BIB Bayar ke Asuransi</th>
                    <th colspan="2" class="text-center align-middle">Selisih Bayar</th>
                </tr>
                <tr>
                    <th>DN Number</th>
                    <th>DN Date</th>
                    <th>DN Due Date</th>
                    <th>DN Amount</th>

                    <th>CN Number</th>
                    <th>CN Date</th>

                    <th>No. BL</th>
                    <th>No. INV</th>
                    <th>RATE INSURANCE</th>
                    <th>RATE INSURED</th>


                    <th>Percentage</th>
                    <th>Amount</th>
                    <th>Percentage</th>
                    <th>Amount</th>
                    <th>Percentage</th>
                    <th>Amount</th>
                    <th>Percentage</th>
                    <th>Amount</th>
                    <th>Tanggal</th>
                    <th>Pembayaran</th>
                    <th>Tagihan</th>
                    <th>Hutang</th>
                    <th>Piutang</th>

                    <th>Tanggal</th>
                    <th>Pembayaran</th>
                    <th>Tagihan</th>
                    <th>Hutang</th>
                    <th>Piutang</th>
                </tr>
            </thead>
            <tbody>
                @php
                $tempNumber = [];
                $tempDNID = [];
                @endphp

                @forelse ($data as $r)
                @forelse ($r['debit_notes'] as $dn)
                @forelse ($dn['details'] as $dd)
                <tr>
                    @if (!in_array($r["number"], $tempNumber))
                    <td>{{ $r["number"] }}</td>
                    @else
                    <td></td>
                    @endif
                    @if (!in_array($r["number"], $tempNumber))
                    <td>{{ $r["contract_status"] }}</td>
                    @else
                    <td></td>
                    @endif
                    @if (!in_array($r["number"], $tempNumber))
                    <td>{{ $r["contact_group"] }}</td>
                    @else
                    <td></td>
                    @endif
                    @if (!in_array($r["number"], $tempNumber))
                    <td>{{ $r["contact"] }}</td>
                    @else
                    <td></td>
                    @endif
                    <td>{{ $dd["insurance"] }}</td>
                    @if (!in_array($dn["id"], $tempDNID))
                    <td>{{ $dn["installment"] }}</td>
                    @else
                    <td></td>
                    @endif
                    @if (!in_array($r["number"], $tempNumber))
                    <td>{{ $r["contract_type"] }}</td>
                    @else
                    <td></td>
                    @endif
                    @if (!in_array($r["number"], $tempNumber))
                    <td>{{ $r["policy_number"] }}</td>
                    @else
                    <td></td>
                    @endif
                    @if (!in_array($dn["id"], $tempDNID))
                    <td>{{ $dn["number"] }}</td>
                    @else
                    <td></td>
                    @endif
                    @if (!in_array($dn["id"], $tempDNID))
                    <td>{{ $dn["date"] }}</td>
                    @else
                    <td></td>
                    @endif
                    @if (!in_array($dn["id"], $tempDNID))
                    <td>{{ $dn["due_date"] }}</td>
                    @else
                    <td></td>
                    @endif
                    @if (!in_array($dn["id"], $tempDNID))
                    <td>
                        @if ($dn["currency_code"] === 'IDR')
                        IDR {{ number_format($dn["amount"], 0, ',', '.') }}
                        @elseif ($dn["currency_code"] === 'USD')
                        USD {{ number_format($dn["amount"], 2, '.', ',') }}
                        @else
                        {{ $dn["currency_code"] }} {{ number_format($dn["amount"], 2) }}
                        @endif
                    </td>
                    @else
                    <td></td>
                    @endif
                    @if (!in_array($dn["id"], $tempDNID))
                    <td>--CN Number--</td>
                    @else
                    <td></td>
                    @endif
                    @if (!in_array($dn["id"], $tempDNID))
                    <td>--CN Date--</td>
                    @else
                    <td></td>
                    @endif
                    @if (!in_array($r["number"], $tempNumber))
                    <td>{{ $r["period_start"] }}</td>
                    @else
                    <td></td>
                    @endif
                    @if (!in_array($r["number"], $tempNumber))
                    <td>{{ $r["period_end"] }}</td>
                    @else
                    <td></td>
                    @endif
                    <td>--No. BL--</td>
                    <td>--No. INV--</td>
                    <td>--RATE INSURANCE--</td>
                    <td>--RATE INSURED--</td>
                    @if (!in_array($r["number"], $tempNumber))
                    <td>
                        @if ($r["currency_code"] === 'IDR')
                        IDR {{ number_format($r["coverage_amount"], 0, ',', '.') }}
                        @elseif ($r["currency_code"] === 'USD')
                        USD {{ number_format($r["coverage_amount"], 2, '.', ',') }}
                        @else
                        {{ $r["currency_code"] }} {{ number_format($r["coverage_amount"], 2) }}
                        @endif
                    </td>
                    @else
                    <td></td>
                    @endif
                    @if (!in_array($r["number"], $tempNumber))
                    <td>
                        @if ($r["currency_code"] === 'IDR')
                        IDR {{ number_format($r["gross_premium"], 0, ',', '.') }}
                        @elseif ($r["currency_code"] === 'USD')
                        USD {{ number_format($r["gross_premium"], 2, '.', ',') }}
                        @else
                        {{ $r["currency_code"] }} {{ number_format($r["gross_premium"], 2) }}
                        @endif
                    </td>
                    @else
                    <td></td>
                    @endif
                    @if (!in_array($r["number"], $tempNumber))
                    <td>{{ $r["discount"] }}</td>
                    @else
                    <td></td>
                    @endif
                    @if (!in_array($r["number"], $tempNumber))
                    <td>
                        @if ($r["currency_code"] === 'IDR')
                        IDR {{ number_format($r["discount_amount"], 0, ',', '.') }}
                        @elseif ($r["currency_code"] === 'USD')
                        USD {{ number_format($r["discount_amount"], 2, '.', ',') }}
                        @else
                        {{ $r["currency_code"] }} {{ number_format($r["discount_amount"], 2) }}
                        @endif
                    </td>
                    @else
                    <td></td>
                    @endif
                    @if (!in_array($r["number"], $tempNumber))
                    <td>
                        @if ($r["currency_code"] === 'IDR')
                        IDR {{ number_format($r["stamp_fee"], 0, ',', '.') }}
                        @elseif ($r["currency_code"] === 'USD')
                        USD {{ number_format($r["stamp_fee"], 2, '.', ',') }}
                        @else
                        {{ $r["currency_code"] }} {{ number_format($r["stamp_fee"], 2) }}
                        @endif
                    </td>
                    @else
                    <td></td>
                    @endif
                    @if (!in_array($r["number"], $tempNumber))
                    <td>
                        @if ($r["currency_code"] === 'IDR')
                        IDR {{ number_format($r["amount"], 0, ',', '.') }}
                        @elseif ($r["currency_code"] === 'USD')
                        USD {{ number_format($r["amount"], 2, '.', ',') }}
                        @else
                        {{ $r["currency_code"] }} {{ number_format($r["amount"], 2) }}
                        @endif
                    </td>
                    @else
                    <td></td>
                    @endif

                    @if (!in_array($dn["id"], $tempDNID))
                    <td>
                        @if ($dn["currency_code"] === 'IDR')
                        IDR {{ number_format($dn["credit_note_amount"], 0, ',', '.') }}
                        @elseif ($dn["currency_code"] === 'USD')
                        USD {{ number_format($dn["credit_note_amount"], 2, '.', ',') }}
                        @else
                        {{ $dn["currency_code"] }} {{ number_format($dn["credit_note_amount"], 2) }}
                        @endif
                    </td>
                    @else
                    <td></td>
                    @endif
                    <td>{{ $dd["share"] }}</td>
                    <td>
                        @if ($dd["currency_code"] === 'IDR')
                        IDR {{ number_format($dd["share_amount"], 0, ',', '.') }}
                        @elseif ($dd["currency_code"] === 'USD')
                        USD {{ number_format($dd["share_amount"], 2, '.', ',') }}
                        @else
                        {{ $dd["currency_code"] }} {{ number_format($dd["share_amount"], 2) }}
                        @endif
                    </td>
                    <td>{{ $dd["brokerage_fee"] }}</td>
                    <td>
                        @if ($dd["currency_code"] === 'IDR')
                        IDR {{ number_format($dd["brokerage_fee_amount"], 0, ',', '.') }}
                        @elseif ($dd["currency_code"] === 'USD')
                        USD {{ number_format($dd["brokerage_fee_amount"], 2, '.', ',') }}
                        @else
                        {{ $dd["currency_code"] }} {{ number_format($dd["brokerage_fee_amount"], 2) }}
                        @endif
                    </td>
                    <td>{{ $dd["eng_fee"] }}</td>
                    <td>
                        @if ($dd["currency_code"] === 'IDR')
                        IDR {{ number_format($dd["eng_fee_amount"], 0, ',', '.') }}
                        @elseif ($dd["currency_code"] === 'USD')
                        USD {{ number_format($dd["eng_fee_amount"], 2, '.', ',') }}
                        @else
                        {{ $dd["currency_code"] }} {{ number_format($dd["eng_fee_amount"], 2) }}
                        @endif
                    </td>
                    @if (!in_array($dn["id"], $tempDNID))
                    <td>
                        @if ($dn["currency_code"] === 'IDR')
                        IDR {{ number_format($dn["nett_income_bib"], 0, ',', '.') }}
                        @elseif ($dn["currency_code"] === 'USD')
                        USD {{ number_format($dn["nett_income_bib"], 2, '.', ',') }}
                        @else
                        {{ $dn["currency_code"] }} {{ number_format($dn["nett_income_bib"], 2) }}
                        @endif
                    </td>
                    @else
                    <td></td>
                    @endif
                    <td>{{ $dd["ppn"] }}</td>
                    <td>
                        @if ($dd["currency_code"] === 'IDR')
                        IDR {{ number_format($dd["pph_23"], 0, ',', '.') }}
                        @elseif ($dd["currency_code"] === 'USD')
                        USD {{ number_format($dd["pph_23"], 2, '.', ',') }}
                        @else
                        {{ $dd["currency_code"] }} {{ number_format($dd["pph_23"], 2) }}
                        @endif
                    </td>
                    <td>{{ $dd["tanggal_receive"] }}</td>
                    <td>{{ $dd["nominal_receive"] }}</td>
                    <td>
                        @if ($dd["currency_code"] === 'IDR')
                        IDR {{ number_format($dd["nominal_receive_billing"], 0, ',', '.') }}
                        @elseif ($dd["currency_code"] === 'USD')
                        USD {{ number_format($dd["nominal_receive_billing"], 2, '.', ',') }}
                        @else
                        {{ $dd["currency_code"] }} {{ number_format($dd["nominal_receive_billing"], 2) }}
                        @endif
                    </td>
                    <td>
                        @if ($dd["currency_code"] === 'IDR')
                        IDR {{ number_format($dd["hutang_receive"], 0, ',', '.') }}
                        @elseif ($dd["currency_code"] === 'USD')
                        USD {{ number_format($dd["hutang_receive"], 2, '.', ',') }}
                        @else
                        {{ $dd["currency_code"] }} {{ number_format($dd["hutang_receive"], 2) }}
                        @endif
                    </td>
                    <td>
                        @if ($dd["currency_code"] === 'IDR')
                        IDR {{ number_format($dd["piutang_receive"], 0, ',', '.') }}
                        @elseif ($dd["currency_code"] === 'USD')
                        USD {{ number_format($dd["piutang_receive"], 2, '.', ',') }}
                        @else
                        {{ $dd["currency_code"] }} {{ number_format($dd["piutang_receive"], 2) }}
                        @endif
                    </td>
                    <td>{{ $dd["tanggal_pay"] }}</td>
                    <td>{{ $dd["nominal_pay"] }}</td>
                    <td>{{ $dd["nominal_pay_billing"] }}</td>
                    <td>{{ $dd["hutang_pay"] }}</td>
                    <td>{{ $dd["piutang_pay"] }}</td>
                </tr>
                @php $tempNumber[] = $r["number"]; @endphp
                @php $tempDNID[] = $dn["id"]; @endphp
                @empty
                <tr>
                    @if (!in_array($r["number"], $tempNumber))
                    <td>{{ $r["number"] }}</td>
                    @else
                    <td></td>
                    @endif
                    @if (!in_array($r["number"], $tempNumber))
                    <td>{{ $r["contract_status"] }}</td>
                    @else
                    <td></td>
                    @endif
                    @if (!in_array($r["number"], $tempNumber))
                    <td>{{ $r["contact_group"] }}</td>
                    @else
                    <td></td>
                    @endif
                    @if (!in_array($r["number"], $tempNumber))
                    <td>{{ $r["contact"] }}</td>
                    @else
                    <td></td>
                    @endif
                    @if (!in_array($r["number"], $tempNumber))
                    <td>{{ $r["contract_type"] }}</td>
                    @else
                    <td></td>
                    @endif
                    @if (!in_array($r["number"], $tempNumber))
                    <td>{{ $r["policy_number"] }}</td>
                    @else
                    <td></td>
                    @endif
                    <td>{{ $r["period_start"] }} </td>
                    <td>{{ $r["period_end"] }} </td>
                    <td>{{ $r["coverage_amount"] }} </td>
                    <td>{{ $r["gross_premium"] }} </td>
                    <td>{{ $r["discount"] }} </td>
                    <td>{{ $r["discount_amount"] }} </td>
                    <td>{{ $r["stamp_fee"] }} </td>
                    <td>{{ $r["amount"] }} </td>
                    <td>{{ $dn["number"] }} </td>
                    <td>{{ $dn["installment"] }} </td>
                    <td>{{ $dn["date"] }} </td>
                    <td>{{ $dn["due_date"] }} </td>
                    <td>{{ $dn["amount"] }} </td>
                    <td>{{ $dn["credit_note_amount"] }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>

                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                @php $tempNumber[] = $r["number"]; @endphp
                @endforelse
                @empty
                <tr>
                    <td>{{ $r["number"] }}</td>
                    <td>{{ $r["contract_status"] }}</td>
                    <td>{{ $r["contact_group"] }}</td>
                    <td>{{ $r["contact"] }}</td>
                    <td>{{ $r["contract_type"] }}</td>
                    <td>{{ $r["currency_code"] }}</td>
                    <td>{{ $r["policy_number"] }}</td>
                    <td>{{ $r["period_start"] }} </td>
                    <td>{{ $r["period_end"] }} </td>
                    <td>{{ $r["coverage_amount"] }} </td>
                    <td>{{ $r["gross_premium"] }} </td>
                    <td>{{ $r["discount"] }} </td>
                    <td>{{ $r["discount_amount"] }} </td>
                    <td>{{ $r["stamp_fee"] }} </td>
                    <td>{{ $r["amount"] }} </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>

                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                @endempty
                @empty
                <tr>
                    <td class="text-center" colspan="21">No Data</td>
                </tr>
                @endforelse
            </tbody>
        </table>
</body>
</html>