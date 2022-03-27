@if ($errors->any())
    @foreach ($errors->all() as $error)
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        {{$error}}
    </div>
    @endforeach
@endif

{{-- @section('scripts')
<script>
  Swal.fire({
    position: 'top-end',
    icon: false,
    title: 'Your work has been saved',
    showConfirmButton: false,
    timer: 1500
    })
</script>
@endsection --}}
