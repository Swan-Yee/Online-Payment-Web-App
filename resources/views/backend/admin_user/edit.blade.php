@extends('backend.layouts.app')

@section('title','Admin Users')
@section('admin-user-active','mm-active')
@section('content')
<div class="app-page-title">
    <div class="page-title-wrapper">
         <div class="page-title-heading">
             <div class="page-title-icon">
                 <i class="pe-7s-user icon-gradient bg-mean-fruit">
                 </i>
             </div>
             <div>
                 Edit Admin User
             </div>
         </div>
        <div class="page-title-actions"></div>
    </div>
</div>

<div class="py-3">
    @include('backend.layouts.flash')
</div>

<div class="content">
    <div class="card">
        <div class="card-body">
            <form action="{{route('admin.admin-user.update',$admin_user->id)}}" method="post" id="update">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="">Name</label>
                    <input type="text" name="name" class="form-control" value="{{$admin_user->name}}">
                </div>
                <div class="form-group">
                    <label for="">Email</label>
                    <input type="email" name="email" class="form-control" value="{{$admin_user->email}}">
                </div>
                <div class="form-group">
                    <label for="">Phone</label>
                    <input type="number" name="phone" class="form-control" value="{{$admin_user->phone}}">
                </div>
                <div class="form-group">
                    <label for="">Password</label>
                    <input type="password" name="password" class="form-control">
                </div>
                <div class="d-flex justify-content-center">
                    <button class="btn btn-secondary mr-2 back-btn">Cancel</button>
                    <button type="submit" class="btn btn-primary">Edit</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
 {!! JsValidator::formRequest('App\Http\Requests\UpdateAdminUser','#update') !!}
<script>

    </script>
@endsection
