@extends('frontend.layouts.app')

@section('title','Transfer')

@section('content')
<div class="transfer pb-5">
    <div class="card">
        <div class="card-body">
            <form action="{{route('transfer.confirm')}}" method="get" autocomplete="off" id="transfer-form">
                @csrf

                <input type="hidden" name="hash_value" value="" class="hash_value">

                <div class="mb-3">
                    <label class="mb-1">From</label>
                    <p class="mb-1 text-muted">{{ $authUser->name }}</p>
                    <p class="mb-1 text-muted">{{ $authUser->phone }}</p>
                </div>

                <div class="form-group">
                    <label for="">
                        To
                        <span class="to_account_info text-danger"></span>
                    </label>
                    <div class="input-group">
                        <input type="number" name="to_phone" id="" class="form-control to_phone">
                          <div class="input-group-append">
                            <span class="input-group-text h-100 w-100 btn btn-outline-dark verify-btn">
                                <i class="fas fa-check-circle"></i>
                            </span>
                        </div>
                    </div>
                    @error('to_phone')
                        <small class="text-danger">{{$message}}</small>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="">Amount MMK</label>
                    <input type="number" name="amount" id="" class="form-control amount">
                    @error('amount')
                        <small class="text-danger">{{$message}}</small>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="">Description</label>
                    <textarea name="description" class="form-control description"></textarea>
                </div>

                <button type="submit" class="btn btn-theme w-100 mt-2 submit-btn">Continue</button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    $(document).ready(function(){
        let checkUser= function(){
            var phone = $('.to_phone').val();
            $.ajax({
                url: '/to-account-verify?phone='+phone,
                type: 'GET',
                success: function(res){
                    if(res.status == 'success'){
                        $('.to_account_info').text('('+res.data['name']+')');
                    }
                    else if(res.status == 'err'){
                        $('.to_account_info').text('(Cannot Send Your Account Yourself)');
                    }else{
                        $('.to_account_info').text('(Valid Account)');
                    }
                },

            })
        }
        $('.to_phone').change(checkUser);
        $('.verify-btn').click(checkUser);

        $('.submit-btn').on('click',function(e){
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
                        $('#transfer-form').submit();
                    }
                }
            })
        });
    });
</script>
@endsection
