@extends('frontend.layouts.app')

@section('title','Transcation Detail')

@section('content')
    <div class="transaction-detail">
        <div class="card">
            <div class="card-body">
                @if (session('transfer_success'))
                    <div class="alert alert-success alert-dismissable fade show" role="alert">
                        <Strong>{{session('transfer_success')}}</Strong>
                    </div>
                @endif
                <div class="text-center mb-3">
                    @if ($transaction->type == 2)
                        <img src="{{asset('img/online-transfer.png')}}" alt="img">
                    @elseif( $transaction->type == 1 )
                    <img src="{{asset('img/money-back-guarantee.png')}}" alt="img">
                    @endif

                </div>
                <h6 class="text-center @if ($transaction->type == 1)
                    text-success
                    @elseif($transaction->type == 2)
                    text-danger
                @endif">{{ number_format($transaction->amount) }} MMK</h6>
                <hr>
                <div class="d-flex justify-content-between">
                    <p class="mb-0 text-muted">Trx ID</p>
                    <p class="mb-0">{{ $transaction->trx_id }}</p>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <p class="mb-0 text-muted">Refer Number</p>
                    <p class="mb-0">{{ $transaction->ref_no }}</p>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <p class="mb-0 text-muted">Type</p>
                    <p class="mb-0">
                        @if ($transaction->type == 1)
                            <span class="badge badge-pill alert-success">Income</span>
                        @elseif ($transaction->type == 2)
                            <span class="badge badge-pill alert-danger">Expense</span>
                        @endif
                    </p>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <p class="mb-0 text-muted">Amount</p>
                    <p class="mb-0">{{ number_format($transaction->amount) }}</p>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <p class="mb-0 text-muted">Date Time</p>
                    <p class="mb-0">{{ $transaction->created_at }}</p>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <p class="mb-0 text-muted">
                        @if ($transaction->type == 1)
                            From
                        @elseif($transaction->type == 2)
                            To
                        @endif
                    </p>
                    <p class="mb-0">
                        {{ $transaction->source ? $transaction->source->name : '-' }}
                    </p>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <p class="text-muted mb-0">
                        Description
                    </p>
                    <p class="mb-0">
                        {{ $transaction->description }}
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
