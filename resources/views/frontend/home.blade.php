@extends('frontend.layouts.app')

@section('title','Magic Pay')

@section('content')
<div class="home">
    <div class="row mb-5">
        <div class="col-12 text-center">
            <p class="profile">
                <img src="https://ui-avatars.com/api/?background=f3f3&name={{$user->name}}" alt="img">
                <h5>{{$user->name}}</h5>
                <p class="text-muted">{{ $user->wallet ? number_format($user->wallet->amount) : 0 }} MMK </p>
            </p>
        </div>
        <div class="col-6">
            <a href="{{route('qr-scan')}}">
                <div class="card shortcut-box">
                    <div class="card-body d-flex flex-column align-items-center">
                        <img src="{{asset('img/qr-code-scan.png')}}" alt="img" class="mb-3">
                        <span>Scan & Pay</span>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6">
            <a href="{{route('qr-receive')}}">
                <div class="card shortcut-box">
                    <div class="card-body d-flex flex-column align-items-center">
                        <img src="{{asset('img/qr-code.png')}}" alt="img" class="mb-3">
                        <span>Receive QR</span>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-12">
            <div class="card my-3 function-box">
                <div class="card-body">
                    <a href="{{route('transfer')}}" class="d-flex justify-content-between">
                        <img src="{{asset('img/transfer-money.png')}}" alt="img">
                        <span>Transfer</span>
                    </a>
                    <hr>

                    <a href="{{route('wallet')}}" class="d-flex justify-content-between">
                        <img src="{{asset('img/wallet.png')}}" alt="img">
                        <span>Wallet</span>
                    </a>
                    <hr>
                    <a href="{{route('transaction')}}" class="d-flex justify-content-between">
                        <img src="{{asset('img/exchange.png')}}" alt="img">
                        <span>Transaction</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
