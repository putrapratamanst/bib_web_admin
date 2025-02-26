<table class="table table-bordered table-sm table-striped">
    <thead>
        <tr>
            <th rowspan="2" class="text-center align-middle">Number</th>
            <th rowspan="2" class="text-center align-middle">Status</th>
            <th rowspan="2" class="text-center align-middle">Group</th>
            <th rowspan="2" class="text-center align-middle">Contact</th>
            <th rowspan="2" class="text-center align-middle">Type</th>
            <th rowspan="2" class="text-center align-middle">Policy Number</th>
            <th rowspan="2" class="text-center align-middle">Period Start</th>
            <th rowspan="2" class="text-center align-middle">Period End</th>
            <th rowspan="2" class="text-center align-middle">Currency</th>
            <th rowspan="2" class="text-center align-middle">Coverage Amount</th>
            <th rowspan="2" class="text-center align-middle">Gross Premium</th>
            <th colspan="2" class="text-center align-middle">Discount</th>
            <th rowspan="2" class="text-center align-middle">Stamp Fee</th>
            <th rowspan="2" class="text-center align-middle">Net Premium</th>
            <th rowspan="2" class="text-center align-middle">DN Number</th>
            <th rowspan="2" class="text-center align-middle">Installment</th>
            <th rowspan="2" class="text-center align-middle">DN Date</th>
            <th rowspan="2" class="text-center align-middle">DN Due Date</th>
            <th rowspan="2" class="text-center align-middle">DN Amount</th>
            <th rowspan="2" class="text-center align-middle">Total CN Amount</th>
            <th rowspan="2" class="text-center align-middle">Insurance</th>
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
                        @if (!in_array($r["number"], $tempNumber))
                            <td>{{ $r["currency_code"] }}</td>
                        @else
                            <td></td>
                        @endif
                        @if (!in_array($r["number"], $tempNumber))
                            <td>{{ $r["coverage_amount"] }}</td>
                        @else
                            <td></td>
                        @endif
                        @if (!in_array($r["number"], $tempNumber))
                            <td>{{ $r["gross_premium"] }}</td>
                        @else
                            <td></td>
                        @endif
                        @if (!in_array($r["number"], $tempNumber))
                            <td>{{ $r["discount"] }}</td>
                        @else
                            <td></td>
                        @endif
                        @if (!in_array($r["number"], $tempNumber))
                            <td>{{ $r["discount_amount"] }}</td>
                        @else
                            <td></td>
                        @endif
                        @if (!in_array($r["number"], $tempNumber))
                            <td>{{ $r["stamp_fee"] }}</td>
                        @else
                            <td></td>
                        @endif
                        @if (!in_array($r["number"], $tempNumber))
                            <td>{{ $r["amount"] }}</td>
                        @else
                            <td></td>
                        @endif
                        @if (!in_array($dn["id"], $tempDNID))
                            <td>{{ $dn["number"] }}</td>
                        @else
                            <td></td>
                        @endif
                        @if (!in_array($dn["id"], $tempDNID))
                            <td>{{ $dn["installment"] }}</td>
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
                            <td>{{ $dn["amount"] }}</td>
                        @else
                            <td></td>
                        @endif
                        @if (!in_array($dn["id"], $tempDNID))
                            <td>{{ $dn["credit_note_amount"] }}</td>
                        @else
                            <td></td>
                        @endif
                        <td>{{ $dd["insurance"] }}</td>
                        <td>{{ $dd["share"] }}</td>
                        <td>{{ $dd["share_amount"] }}</td>
                        <td>{{ $dd["brokerage_fee"] }}</td>
                        <td>{{ $dd["brokerage_fee_amount"] }}</td>
                        <td>{{ $dd["eng_fee"] }}</td>
                        <td>{{ $dd["eng_fee_amount"] }}</td>
                        @if (!in_array($dn["id"], $tempDNID))
                            <td>{{ $dn["nett_income_bib"] }}</td>
                        @else
                            <td></td>
                        @endif
                        <td>{{ $dd["ppn"] }}</td>
                        <td>{{ $dd["pph_23"] }}</td>
                        <td>{{ $dd["tanggal_receive"] }}</td>
                        <td>{{ $dd["nominal_receive"] }}</td>
                        <td>{{ $dd["nominal_receive_billing"] }}</td>
                        <td>{{ $dd["hutang_receive"] }}</td>
                        <td>{{ $dd["piutang_receive"] }}</td>
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
                        @if (!in_array($r["number"], $tempNumber))
                            <td>{{ $r["currency_code"] }}</td>
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