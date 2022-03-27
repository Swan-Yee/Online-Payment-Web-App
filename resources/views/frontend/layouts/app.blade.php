<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title')</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">

    {{-- Fontawesome --}}
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous"/>

    {{-- Date Picker --}}
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

    <link rel="stylesheet" href="{{asset("frontend/css/styles.css")}}">

    {{-- Google Font (Nunito) --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200;0,300;0,400;0,500;0,700;0,900;1,200&display=swap" rel="stylesheet">

@yield('extra-css')
</head>
<body>
    <div id="app">
        <div class="header-menu">
            <div class="d-flex justify-content-center">
                <div class="col-2 text-center">
                    @if(!request()->is('/'))
                    <a href="#" class="back-btn">
                        <i class="fas fa-angle-left"></i>
                    </a>
                    @endif
                </div>
                <div class="col-8 text-center">
                    <h3>@yield('title')</h3>
                </div>
                <div class="col-2 text-center">
                    <a href="{{route('noti')}}">
                        <i class="fal fa-bell"></i>
                        @if ($unread_noti_count)
                            <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle text-white">
                            </span>
                        @endif
                    </a>
                </div>
            </div>
        </div>

        <div class="content">
            <div class="container">
                @yield('content')
            </div>
        </div>

        <div class="bottom-menu">
            <a href="{{route('qr-scan')}}" class="scan-tab">
                <div class="inside">
                    <i class="fas fa-qrcode"></i>
                </div>
            </a>

            <div class="d-flex justify-content-center">
                <div class="col-3 text-center">
                    <a href="{{route('home')}}">
                        <i class="fas fa-home"></i>
                        <p>Home</p>
                    </a>
                </div>
                <div class="col-3 text-center">
                    <a href="{{route('wallet')}}">
                        <i class="fas fa-wallet"></i>
                        <p>Wallet</p>
                    </a>
                </div>
                <div class="col-3 text-center">
                    <a href="{{url('transaction')}}">
                        <i class="fas fa-exchange-alt"></i>
                        <p>Transaction</p>
                    </a>
                </div>
                <div class="col-3 text-center">
                    <a href="{{route('profile')}}">
                        <i class="fas fa-user"></i>
                        <p>Profile</p>
                    </a>
                </div>
            </div>
        </div>
    </div>
{{-- Jquery --}}
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    {{-- bootstrap JS --}}
  <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>

{{-- Sweet Alert 2 --}}
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script src="{{ asset('frontend/js/jsscroll.js') }}"> </script>

{{-- Date-Picker --}}
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
            <script>
            $(document).ready(function(){
                let token = document.head.querySelector('meta[name="csrf-token"]');
                if(token){
                    $.ajaxSetup({
                        headers: {
                            "X-CSRF_TOKEN" : token.content,
                            "Content-Type" : 'application/json',
                            "Accept" : 'application/json',
                        }
                    })
                }

                $('.back-btn').on('click',function(e){
                    e.preventDefault();
                    window.history.go(-1);
                    return false;
                })
            });

            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            })

            @if(session('create'))
                Toast.fire({
                icon: 'success',
                title: "{{session('create')}}"
                })
            @endif
            @if(session('update'))
                Toast.fire({
                icon: 'success',
                title: "{{session('update')}}"
                })
            @endif

        </script>

@yield('script')
</body>
</html>
