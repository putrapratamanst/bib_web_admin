@extends('layouts.app')

@section('title', 'Detail Hutang Asuransi')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-money-bill-wave me-2"></i>
                Detail Hutang Asuransi
            </h5>
            <div class="float-end">
                @if($cashout->status === 'pending')
                    <form method="POST" action="{{ route('transaction.cashouts.mark-paid', $cashout->id) }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Mark this as paid?')">
                            <i class="fas fa-check me-1"></i> Mark as Paid
                        </button>
                    </form>
                    <form method="POST" action="{{ route('transaction.cashouts.mark-cancelled', $cashout->id) }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Cancel this?')">
                            <i class="fas fa-times me-1"></i> Cancel
                        </button>
                    </form>
                @endif
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-muted mb-3">Informasi Hutang Asuransi</h6>
                    
                    <div class="row mb-2">
                        <div class="col-4"><strong>Nomor Hutang:</strong></div>
                        <div class="col-8">{{ $cashout->number }}</div>
                    </div>
                    
                    <div class="row mb-2">
                        <div class="col-4"><strong>Date:</strong></div>
                        <div class="col-8">{{ $cashout->date_formatted }}</div>
                    </div>
                    
                    <div class="row mb-2">
                        <div class="col-4"><strong>Due Date:</strong></div>
                        <div class="col-8">{{ $cashout->due_date_formatted }}</div>
                    </div>
                    
                    <div class="row mb-2">
                        <div class="col-4"><strong>Status:</strong></div>
                        <div class="col-8">
                            @php
                                $badgeClass = match($cashout->status) {
                                    'pending' => 'bg-warning',
                                    'paid' => 'bg-success',
                                    'cancelled' => 'bg-danger',
                                    default => 'bg-secondary'
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ ucfirst($cashout->status) }}</span>
                        </div>
                    </div>
                    
                    <div class="row mb-2">
                        <div class="col-4"><strong>Currency:</strong></div>
                        <div class="col-8">{{ $cashout->currency_code }}</div>
                    </div>
                    
                    <div class="row mb-2">
                        <div class="col-4"><strong>Exchange Rate:</strong></div>
                        <div class="col-8">{{ $cashout->exchange_rate_formatted }}</div>
                    </div>
                    
                    <div class="row mb-2">
                        <div class="col-4"><strong>Amount:</strong></div>
                        <div class="col-8">
                            <strong class="text-primary">{{ $cashout->currency_code }} {{ $cashout->amount_formatted }}</strong>
                        </div>
                    </div>
                    
                    @if($cashout->description)
                        <div class="row mb-2">
                            <div class="col-4"><strong>Description:</strong></div>
                            <div class="col-8">{{ $cashout->description }}</div>
                        </div>
                    @endif
                </div>
                
                <div class="col-md-6">
                    <h6 class="text-muted mb-3">Related Information</h6>
                    
                    <div class="row mb-2">
                        <div class="col-4"><strong>Debit Note:</strong></div>
                        <div class="col-8">
                            <a href="{{ route('transaction.debit-notes.show', $cashout->debitNote->id) }}">
                                {{ $cashout->debitNote->number }}
                            </a>
                        </div>
                    </div>
                    
                    <div class="row mb-2">
                        <div class="col-4"><strong>Contract:</strong></div>
                        <div class="col-8">
                            <a href="{{ route('transaction.contracts.show', $cashout->debitNote->contract->id) }}">
                                {{ $cashout->debitNote->contract->number }}
                            </a>
                        </div>
                    </div>
                    
                    <div class="row mb-2">
                        <div class="col-4"><strong>Client:</strong></div>
                        <div class="col-8">{{ $cashout->debitNote->contract->contact->display_name }}</div>
                    </div>
                    
                    <div class="row mb-2">
                        <div class="col-4"><strong>Insurance Company:</strong></div>
                        <div class="col-8">
                            <strong class="text-info">{{ $cashout->insurance->display_name }}</strong>
                        </div>
                    </div>
                    
                    <hr class="my-3">
                    
                    <h6 class="text-muted mb-3">Audit Information</h6>
                    
                    <div class="row mb-2">
                        <div class="col-4"><strong>Created:</strong></div>
                        <div class="col-8">
                            {{ $cashout->created_at->format('d M Y H:i') }}
                            @if($cashout->createdBy)
                                <small class="text-muted">by {{ $cashout->createdBy->name }}</small>
                            @endif
                        </div>
                    </div>
                    
                    @if($cashout->updated_at != $cashout->created_at)
                        <div class="row mb-2">
                            <div class="col-4"><strong>Last Updated:</strong></div>
                            <div class="col-8">
                                {{ $cashout->updated_at->format('d M Y H:i') }}
                                @if($cashout->updatedBy)
                                    <small class="text-muted">by {{ $cashout->updatedBy->name }}</small>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            @if($cashout->debitNote && $cashout->debitNote->contract)
            <hr class="my-4">
            
            <h6 class="text-muted mb-3">Cashout Breakdown</h6>
            
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead class="table-light">
                        <tr>
                            <th>Premi</th>
                            <th>Share 25%</th>
                            <th>Basos</th>
                            <th>Diskon</th>
                            <th>Komisi</th>
                            <th>PPh</th>
                            <th>Polis+stamp</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $contract = $cashout->debitNote->contract;
                            $contractDetail = $contract->details->where('insurance_id', $cashout->insurance_id)->first();
                            
                            // Gross Premium dari contract
                            $grossPremium = $contract->gross_premium;
                            
                            // Percentage share untuk insurance ini (dari contract detail)
                            $percentage = $contractDetail ? $contractDetail->percentage : 0;
                            
                            // Share 25% = gross premium * percentage / 100
                            $share25 = $grossPremium * ($percentage / 100);
                            
                            // Brokerage fee (Basos) = share * brokerage_fee%
                            $brokerageFeePercent = $contractDetail ? $contractDetail->brokerage_fee : 0;
                            $basos = $share25 * ($brokerageFeePercent / 100);
                            
                            // Diskon amount
                            $discountAmount = $contract->discount_amount;
                            $discountShare = $discountAmount * ($percentage / 100);
                            
                            // Komisi = brokerage fee (same as basos in this case)
                            $komisi = $basos;
                            
                            // PPh = 2% of komisi (or from eng_fee if available)
                            $engFeePercent = $contractDetail ? $contractDetail->eng_fee : 2;
                            $pph = $komisi * ($engFeePercent / 100);
                            
                            // Polis + stamp
                            $polisStamp = $contract->stamp_fee * ($percentage / 100);
                        @endphp
                        <tr>
                            <td class="text-end">{{ $contract->currency_code }} {{ number_format($grossPremium, 2, ',', '.') }}</td>
                            <td class="text-end">
                                {{ number_format($percentage, 2, ',', '.') }}%
                                <br>
                                <small class="text-muted">{{ $contract->currency_code }} {{ number_format($share25, 2, ',', '.') }}</small>
                            </td>
                            <td class="text-end">
                                {{ number_format($brokerageFeePercent, 2, ',', '.') }}%
                                <br>
                                <small class="text-muted">{{ $contract->currency_code }} {{ number_format($basos, 2, ',', '.') }}</small>
                            </td>
                            <td class="text-end">{{ $contract->currency_code }} {{ number_format($discountShare, 2, ',', '.') }}</td>
                            <td class="text-end">{{ $contract->currency_code }} {{ number_format($komisi, 2, ',', '.') }}</td>
                            <td class="text-end">
                                {{ number_format($engFeePercent, 2, ',', '.') }}%
                                <br>
                                <small class="text-muted">{{ $contract->currency_code }} {{ number_format($pph, 2, ',', '.') }}</small>
                            </td>
                            <td class="text-end">{{ $contract->currency_code }} {{ number_format($polisStamp, 2, ',', '.') }}</td>
                        </tr>
                    </tbody>
                    <tfoot class="table-secondary">
                        <tr>
                            <th colspan="6" class="text-end">Total Cashout Amount:</th>
                            <th class="text-end text-primary">{{ $cashout->currency_code }} {{ $cashout->amount_formatted }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @endif
        </div>
        <div class="card-footer">
            <a href="{{ route('transaction.cashouts.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back to List
            </a>
        </div>
    </div>
</div>
@endsection