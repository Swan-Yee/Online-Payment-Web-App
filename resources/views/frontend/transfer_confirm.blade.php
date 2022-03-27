@extends('frontend.layouts.app')

@section('title','Transfer Confirm')

@section('content')
<div class="transfer-confirm">
    <div class="card">
        <div class="card-body">

            @if (session('err'))
                <div class="alert alert-warning alert-dismissable fade show" role="alert">
                    <Strong>{{session('err')}}</Strong>
                </div>
            @endif

            @include('frontend.layouts.flash_message')

            <form action="{{route('transfer.complete')}}" method="POST" id='form'>
                @csrf
                @method('POST')

                <input type="hidden" name="to_phone" value="{{$to_account->phone}}">
                <input type="hidden" name="amount" value="{{$amount}}">
                <input type="hidden" name="description" value="{{$description}}">
                <input type="hidden" name="hash_value" value="{{$hash_value}}">

                <div class="mb-3">
                    <label for="" class="mb-0">
                        <strong>From</strong>
                    </label>
                    <p class="mb-1 text-muted">{{ $from_account->name }}</p>
                    <p class="mb-1 text-muted">{{ $from_account->phone }}</p>
                </div>

                <div class="form-group mb-3">
                    <label for="" class="mb-0">
                        <strong>To</strong>
                    </label>
                    <p class="mb-1 text-muted">{{ $to_account->name }}</p>
                    <p class="mb-1 text-muted">{{ $to_account->phone }}</p>
                </div>

                <div class="form-group mb-3">
                    <label for="" class="mb-0">
                        <strong>Amount MMK</strong>
                    </label>
                    <p class="mb-1 text-muted">{{ $amount }}</p>
                </div>

                <div class="form-group mb-3">
                    <label for="" class="mb-0">
                        <strong>Description</strong>
                    </label>
                    <p class="mb-1 text-muted">
                        @if ($description)
                           {{ $description }}
                        @else
                            No Message
                        @endif
                    </p>
                </div>

                <button type="submit" class="btn btn-theme w-100 mt-2 confirm-btn">Confirm</button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$(document).ready(function(){
    $('.confirm-btn').on('click',function(e){
        e.preventDefault();

        Swal.fire({
            title: '<strong>Please Fill Your Password!</strong>',
            icon: 'info',
            html:'<input type="password" class="form-control password" autofocus />',
            showCloseButton: true,
            showCancelButton: true,
            confirmButtonText: 'Confirm',
            reverseButtons: true,
            })
            .then((result) => {
                if (result.isConfirmed) {
                    var password = $('.password').val();
                    $.ajax({
                        url: '/password-check?password=' + password,
                        type: 'GET',
                        success: function(res){
                            console.log(res);
                            if(res.status == 'success'){
                                $('#form').submit();
                            }
                            else{
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: res.message,
                                })
                            }
                        }
                    })
                }
            })
    });
})
</script>
@endsection
