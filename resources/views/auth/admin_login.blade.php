@extends('frontend.layouts.app_plain')

@section('title','Admin Login')

@section('content')
<div class="container">
    <div class="row justify-content-center align-items-center" style="min-height: 100vh">
        <div class="col-md-6">
            <div class="card py-3 auth-form">
                <div class="text-center">
                    <h3>Admin Login</h3>
                    <small class="text-muted">Fill the form to login</small>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.login') }}">
                        @csrf
                        <div class="form-group mb-2">
                            <label for="phone">Phone</label>
                            <input type="phone" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror">
                            @error('phone')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror">
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-theme btn-lg w-100 mt-3">Login</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
