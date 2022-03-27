@extends('frontend.layouts.app')

@section('title', $notification->data['title'])

@section('content')
<section class="noti-detail">
    <div class="card">
        <div class="card-body">
            <div class="text-center">
                <img src="{{asset('img/notification.png')}}" alt="img"  style="width: 220px">
                <div class="">
                    <h6>{{$notification->data['title']}}</h6>
                    <p class="text-muted">{{$notification->data['message']}}</p>
                    <p>{{$notification->created_at}}</p>
                    <a href="{{route('home')}}" class="btn btn-theme">Continue</a>
                </div>
            </div>

        </div>
    </div>
</section>
@endsection
