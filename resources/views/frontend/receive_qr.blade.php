@extends('frontend.layouts.app')

@section('title',"QR Receive")

@section('content')
<section class="receive_qr">
    <div class="card">
        <div class="card-body">
            <p class="text-center mb-0">Scan Me to pay</p>
            <div class="text-center">
                <img src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(300)->generate($authUser->phone)) !!} ">
            </div>
            <div class="text-center mb-0">
                <p class="mb-0">{{$authUser->name}}</p>
                <p>{{$authUser->phone}}</p>
            </div>
        </div>
    </div>
</section>
@endsection
