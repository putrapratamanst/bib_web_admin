@extends('layouts.app')

@section('title', 'Detail Cashout')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-money-bill-wave me-2"></i>
                Cashout Details
            </h5>
            <div class="float-end">
                @if($cashout->status === 'pending')
                    <form method="POST" action="{{ route('transaction.cashouts.mark-paid', $cashout->id) }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Mark this cashout as paid?')">
                            <i class="fas fa-check me-1"></i> Mark as Paid
                        </button>
                    </form>
                    <form method="POST" action="{{ route('transaction.cashouts.mark-cancelled', $cashout->id) }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Cancel this cashout?')">
                            <i class="fas fa-times me-1"></i> Cancel
                        </button>
                    </form>
                @endif
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-muted mb-3">Cashout Information</h6>
                    
                    <div class="row mb-2">
                        <div class="col-4"><strong>Cashout Number:</strong></div>
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
        </div>
        <div class="card-footer">
            <a href="{{ route('transaction.cashouts.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back to List
            </a>
        </div>
    </div>
</div>
@endsection