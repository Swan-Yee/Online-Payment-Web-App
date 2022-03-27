@extends('frontend.layouts.app')

@section('content')
<section class="qr-scan">
    <div class="card">
        <div class="card-body">
            @include('frontend.layouts.flash_message');
            <div class="text-center">
                <img src="{{asset('img/qr_scan.svg')}}" alt="scan" style="width: 220px">
            </div>
            <div class="text-center mt-3">
                <h6 class="mb-1">Click Button To Pay with QR</h6>
                <button class="btn btn-theme" data-toggle="modal" data-target="#scanAndPay">
                    Scan
                </button>
            </div>

        </div>
    </div>
        <!-- Modal -->
        <div class="modal fade" id="scanAndPay" tabindex="-1" role="dialog" aria-labelledby="scanAndPayLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="scanAndPayLabel">Modal title</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <video id="scanner" style="width: 100%; Height: 240px;"></video>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
            </div>
        </div>
        </div>
</section>
@endsection

@section('script')
<script src="{{asset('frontend/js/qr-scanner.umd.min.js')}}"></script>
<script>
    var videoElem = document.getElementById('scanner');
    const qrScanner = new QrScanner(videoElem, function(result){
        if(result){
            qrScanner.stop();
            $('#scanAndPay').modal('hide');

            var to_phone = result;
            window.location.replace(`/scan-pay-form?phone=${to_phone}`);
        }
    });

    $('#scanAndPay').on('shown.bs.modal',function(event){
        qrScanner.start();
    });

    $('#scanAndPay').on('hidden.bs.modal',function(event){
        qrScanner.stop();
    });
</script>
@endsection
