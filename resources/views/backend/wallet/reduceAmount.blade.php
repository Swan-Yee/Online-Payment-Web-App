@extends('backend.layouts.app')

@section('title','Wallet')
@section('wallet-active','mm-active')
@section('extra_css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">
@endsection
@section('content')
<div class="app-page-title">
    <div class="page-title-wrapper">
         <div class="page-title-heading">
             <div class="page-title-icon">
                 <i class="pe-7s-wallet icon-gradient bg-mean-fruit">
                 </i>
             </div>
             <div>
                 Reduce Amount Wallet
             </div>
         </div>
        <div class="page-title-actions">

        </div>
    </div>
</div>

<div class="py-3">
</div>

<div class="content">
    <div class="card w-100 overflow-hidden">
        <div class="card-body">
            @extends('backend.layouts.flash')
            <form action="{{url('admin/wallet/reduce/amount')}}" method="post">
                @csrf
                <label>User</label>
                <select class="select2input form-control" name="user_id">
                    <option value="">-- Please Choose --</option>
                    @foreach ($users as $user)
                        <option value="{{$user->id}}">{{$user->name}}</option>
                    @endforeach
                </select>
                <label class="mt-3">Amount</label>
                <input type="text" name="amount" class="form-control mb-3">
                <label>Description</label>
                <textarea name="description" cols="10" class="form-control mb-3"></textarea>
                <div class="text-center">
                    <button class="btn btn-secondary back-btn">Cancel</button>
                    <button class="btn btn-theme">Confirm</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2input').select2({
                placeholder: "-- Please Choose --",
                allowClear: true,
                theme: 'bootstrap4',
            });
        });

    </script>
@endsection
