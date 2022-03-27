@extends('frontend.layouts.app')

@section('title','Wallet')

@section('content')
<div class="wallet">
    <div class="card my-card">
        <div class="card-body">
            <div class="mb-3">
                <span>Balance</span>
                <h3>{{ number_format($authUser->wallet ? $authUser->wallet->amount : 0) }} MMK</h3>
            </div>
            <div class="mb-3">
                <span>Account Name</span>
                <h3>{{ $authUser->wallet ? $authUser->wallet->account_number : '-'}}</h3>
            </div>
            <div>
                <p>{{ $authUser->name }}</p>
            </div>
        </div>
    </div>
</div>
@endsection

