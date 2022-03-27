@extends('frontend.layouts.app')

@section('title','Scan Pay Form')

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

            <form action="{{route('qr-scan.confirm')}}" method="GET" id='form'>
                @csrf
                @method('POST')

                <input type="hidden" name="to_phone" class="to_phone" value="{{$to_account->phone}}">
                <input type="hidden" name="hash_value" class="hash_value" value="">

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
                    <input type="number" name="amount" class="form-control amount" value="3000">
                </div>

                <div class="form-group mb-3">
                    <label for="" class="mb-0">
                        <strong>Description</strong>
                    </label>
                    <textarea name="description" rows="2" class="form-control description"></textarea>
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

            var to_phone = $('.to_phone').val();
            var amount = $('.amount').val();
            var description = $('.description').val();

            $.ajax({
                url: `/transfer-hash?to_phone=${to_phone}&amount=${amount}&description=${description}`,
                type: 'GET',
                success: function(res){
                    if(res.status == 'success'){
                        $('.hash_value').val(res.data);
                        $('#form').submit();
                    }
                }
            });
        });
})
</script>
@endsection
