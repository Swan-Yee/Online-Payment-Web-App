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
                 Admin User Magagement
             </div>
         </div>
        <div class="page-title-actions"></div>
    </div>
</div>

<div class="py-3">
    <a href="{{route('admin.admin-user.create')}}" class="btn btn-primary">
        <i class="fas fa-plus-circle"></i> Create Admin User
    </a>
</div>

<div class="content">
    <div class="card w-100 overflow-hidden">
        <div class="card-body">
            <table class="table table-bordered responsive nowrap Datatable">
                <thead>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>IP</th>
                    <th>User Agent</th>
                    <th>Created at</th>
                    <th>Updated at</th>
                    <th>Action</th>
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
                "ajax": "/admin/admin-user/datatable/ssd",
                "columns": [
                    {
                        data: "name",
                        name: "name",
                    },
                    {
                        data: "email",
                        name: "email"
                    },
                    {
                        data: "phone",
                        name: "phone"
                    },
                    {
                        data: "ip",
                        name: "ip",
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: "user_agent",
                        name: "user_agent",
                        searchable: false,
                        orderable: false
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
                    },
                    {
                        data: "action",
                        name: "action",
                        searchable: false,
                        orderable: false
                    }
                ],
                "order" : [
                    [ 6, "desc" ]
                ],
            });

            $(document).on('click','.delete',function(e){
                e.preventDefault();

                var id = $(this).data('id');

                Swal.fire({
                    title: 'Are You Sure To delete?',
                    showCancelButton: true,
                    confirmButtonText: 'Confirm',
                    }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/admin/admin-user/' + id,
                            type: "DELETE",
                            success: function(){
                                table.ajax.reload();
                            }
                        })
                    }
                })
            });
        });
    </script>
@endsection
