@extends('admin.layouts.app')
@section('style')

@endsection
@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <h5 class="py-3 mb-4">
        <span class="text-muted fw-light"><a href="{{route('admin.dashboard')}}">Home</a> /</span> Total Registered Users
    </h5>
    <div class="row">
        <div class="col-xl-12 col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive text-nowrap">
                        <table class="table table-bordered" id="usersTable">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Wallet</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ( $users as $user )
                                    <tr>
                                        <td>{{$user->full_name}}</td>
                                        <td>{{$user->email ?? '-'}}</td>
                                        <td>{{$user->phone ?? '-'}}</td>
                                        <td>{{$user->wallet ?? '0'}}</td>
                                        <td>
                                            @if($user->status == "active")
                                                <span class="badge bg-label-success me-1">Active</span>
                                            @else
                                                <span class="badge bg-label-danger me-1">Disable Account</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($user->status == "active")
                                                <button type="button" class="btn btn-sm btn-danger" onclick="userStatus({{$user->id}},`inactive`)">Disable Account</button>
                                            @else
                                                <button type="button" class="btn btn-sm btn-success" onclick="userStatus({{$user->id}},`active`)">Active</button>
                                            @endif
                                            <a href="{{ route("admin.users.show", $user->id) }}" class="btn btn-sm btn-primary">View Detail</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script>
$('#usersTable').DataTable({
    processing: true,
});



function userStatus(userid,status){
    var message = '';
    if(status == 'active'){
        message = 'User able to login after active!';
    }else{
        message = 'User cannot login after Inactive!';
    }


    Swal.fire({
        title: 'Are you sure?',
        text: message,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Okey'
    }).then((result) => {
        if(result.isConfirmed == true) {
            $.ajax({
                type: "POST",
                url: "{{route('admin.users.status')}}",
                data: {'userid':userid,'status':status,'_token': "{{ csrf_token() }}"},
                success: function(response) {
                    if(response.success){
                        if(status == 1){
                            setFlesh('success','User Activate Successfully');
                        }else{
                            setFlesh('success','User Inactivate Successfully');
                        }
                        location.reload();
                    }else{
                        setFlesh('error','There is some problem to change status!Please contact to your server adminstrator');
                    }
                }
            });
        }else{
            location.reload();
        }
    })
}


function deleteUser(userid){
    Swal.fire({
        title: 'Are you sure?',
        text: "You want to delete this user!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes'
    }).then((result) => {
        if(result.isConfirmed == true) {
            var url = '{{ route("admin.users.destroy", ":userid") }}';
            url = url.replace(':userid', userid);
            $.ajax({
                type: "DELETE",
                url: url,
                data: {'_token': "{{ csrf_token() }}"},
                success: function(response) {
                    if(response.success){
                        setFlesh('success','User Deleted Successfully');
                        $('#usersTable').DataTable().ajax.reload();
                    }else{
                        setFlesh('error','There is some problem to delete user!Please contact to your server adminstrator');
                    }
                }
            });
        }
    })
}
</script>
@endsection
