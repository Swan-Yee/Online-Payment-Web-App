@extends('backend.layouts.app')

@section('title','Wallet')
@section('wallet-active','mm-active')
@section('content')
<div class="app-page-title">
    <div class="page-title-wrapper">
         <div class="page-title-heading">
             <div class="page-title-icon">
                 <i class="pe-7s-wallet icon-gradient bg-mean-fruit">
                 </i>
             </div>
             <div>
                 Wallet Magagement
             </div>
         </div>
        <div class="page-title-actions"></div>
    </div>
</div>

<div class="py-3">
    <a href="{{ url('admin/wallet/add/amount') }}" class="btn btn-success"><i class="fas fa-plus-circle mr-2"></i>Add Amount</a>
    <a href="{{ url('admin/wallet/reduce/amount') }}" class="btn btn-danger"><i class="fas fa-minus-circle mr-2"></i>Reduce Amount</a>
</div>

<div class="content">
    <div class="card w-100 overflow-hidden">
        <div class="card-body">
            <table class="table table-bordered responsive nowrap Datatable">
                <thead>
                    <th>Account Number</th>
                    <th>Account Person</th>
                    <th>Amount</th>
                    <th>Created at</th>
                    <th>Updated at</th>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            var table=$('.Datatable').DataTable( {
                "responsive": true,
                "processing": true,
                "serverSide": true,
                "ajax": "/admin/wallet/datatable/ssd",
                "columns": [
                    {
                        data: "account_number",
                        name: "account_number",
                    },
                    {
                        data: "account_person",
                        name: "account_person"
                    },
                    {
                        data: "amount",
                        name: "amount"
                    },
                    {
                        data: "created_at",
                        name: "created_at",
                        searchable: false,
                    },
                    {
                        data: "updated_at",
                        name: "updated_at",
                        searchable: false,
                    }
                ],
                "order" : [
                    [ 4, "desc" ]
                ],
            });
        });
    </script>
@endsection
