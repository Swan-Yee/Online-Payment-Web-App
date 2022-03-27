@extends('frontend.layouts.app')

@section('title','Transcation')

@section('content')
    <div class="transaction">
        <div class="card mb-3">
            <div class="card-body">
                <h6>Filter</h6>
                <div class="row">
                    <div class="col-6">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text p-1">Date</div>
                            </div>
                            <input type="text" name="" class="form-control date" value="{{ request()->date }}" placeholder="All"/>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text p-1">Type</div>
                            </div>
                            <select class="custom-select type form-control" id="">
                                <option value="">All</option>
                                <option value="1" @if (request()->type == 1)
                                    selected
                                @endif>Income</option>
                                <option value="2" @if (request()->type == 2)
                                    selected
                                @endif>Expense</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h6 class="mb-0">Transcations</h6>
            <span class="badge bg-primary text-white show-all">Show All</span>
        </div>
        <div class="infinite-scorll">
            @if ($transactions->total())
                @foreach ($transactions as $transaction)
                    <a href="{{route('transaction.detail',$transaction->trx_id)}}">
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <h5>Trx ID : {{ $transaction->trx_id }}</h5>
                                    @if ($transaction->type == 1)
                                        <div class="d-flex align-items-baseline">
                                            <h5 class="mr-1">+</h5>
                                            <p class="text-success">{{$transaction->amount}} MMK</p>
                                        </div>
                                    @elseif($transaction->type == 2)
                                        <div class="d-flex align-items-baseline">
                                            <h5 class="mr-1">-</h5>
                                            <p class="text-danger">{{$transaction->amount}} MMK</p>
                                        </div>
                                    @endif
                                </div>
                                <p class="text-muted">
                                    @if ($transaction->type == 1)
                                        From
                                    @elseif( $transaction->type ==2 )
                                    To
                                    @endif
                                    {{ $transaction->source ? $transaction->source->name : '' }}
                                </p>
                                <p class="text-muted mb-0">
                                    {{ $transaction->created_at }}
                                </p>
                            </div>
                        </div>
                    </a>
                @endforeach
                {{ $transactions->links() }}
            @else
                <div class="card">
                    <div class="card-body text-center">
                        <span class="h4 text-danger">No Data To Show</span>
                    </div>
                </div>
            @endif
        </div>
    </div>
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

            $('.type').on('change',function(){
                var type = $('.type').val();
                var date = $('.date').val();

                history.pushState(null,'',`?date=${date}&type=${type}`);
                window.location.reload();
            });

            $('.date').daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                "autoApply" : false,
                "autoUpdateInput" : false,
                "locale" : {
                    "format" : "YYYY-MM-DD"
                },
            });

            $('.date').on('apply.daterangepicker',function(ev,picker){
                $(this).val(picker.startDate.format('YYYY-MM-DD HH:mm:ss'));

                var date = $('.date').val();
                var type = $('.type').val();

                history.pushState(null,'',`?date=${date}&type=${type}`);
                window.location.reload();
            });

            $('.date').on('cancel.daterangepicker',function(ev,picker){
                $(this).val('');

                var date = $('.date').val();
                var type = $('.type').val();

                history.pushState(null,'',`?date=${date}&type=${type}`);
                window.location.reload();
            })

            $('.show-all').on('click',function(){
                var url = window.location.href;
                var url = url.split('?')[0];
                history.pushState(null,'',url);
                window.location.reload();
            });
            </script>
@endsection
