@extends('frontend.layouts.app')

@section('title','Notification')

@section('content')
<section class="notification">
    <div class="d-flex mb-3 justify-content-between">
        <h6 class="mb-0">Notifications</h6>
        <a href="{{ url('/noti/complete') }}" class="badge badge-primary">Make as Read all</a>
    </div>
    <div class="infinite-scorll">
        @if ($notifications->total())
            @foreach ($notifications as $notification)
                <a href="{{route('noti.show',$notification->id)}}">
                    <div class="card mb-3
                    @if (is_null($notification->read_at))
                        btn-theme text-white
                    @endif
                    shadow-sm">
                        <div class="card-body">
                            <p class="h6">
                                <i class="fal fa-bell
                                @if (is_null($notification->read_at))
                                    noti-shake
                                @endif
                                "></i>
                            {{\Illuminate\Support\Str::limit($notification->data['title'], 15)}}
                            </p>
                            <small class="d-block mb-2">
                                {{\Illuminate\Support\Str::limit($notification->data['message'], 40)}}
                            </small>
                            <small class="d-block">
                                {{ $notification->created_at->diffForHumans() }}
                            </small>
                        </div>
                    </div>
                </a>
            @endforeach
            {{ $notifications->links() }}
        @else
            <div class="card">
                <div class="card-body text-center">
                    <span class="h4 text-danger">No Data To Show</span>
                </div>
            </div>
        @endif
    </div>
</section>
@endsection

@section('script')
    <script>
            $('ul.pagination').hide();
            $(function() {
                $('.infinite-scorll').jscroll({
                    autoTrigger: true,
                    loadingHtml: '<img class="text-center" src="/img/loading.gif" alt="Loading..." />',
                    padding: 0,
                    nextSelector: '.pagination li.active + li a',
                    contentSelector: 'div.infinite-scorll',
                    callback: function() {
                        $('ul.pagination').remove();
                    }
                });
            });
            </script>
@endsection
