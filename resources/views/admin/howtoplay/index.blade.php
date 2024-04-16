@extends('admin.layouts.app')
@section('style')
    <style>

    </style>
@endsection
@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <h5 class="py-2 mb-2">
        <span class="text-primary fw-light">How To Play</span>
    </h5>
    <div class="row">
        <div class="col-xl-12 col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive text-nowrap">
                    <table class="table table-bordered" id="howtoplayTable">
                        <thead>
                            <tr>
                                <th>Title</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
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
$('#howtoplayTable').DataTable({
    processing: true,
    ajax: {
      url: "{{route('admin.how-to-play.allhowtoplay')}}",
    },
    order: [],
    columns: [

        {
            data: "title",
            render: (data,type,row) => {
                return row.title;
            }
        },

    ],
});



function deletes(userid){
    Swal.fire({
        title: 'Are you sure?',
        text: "You want to delete this contacts!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes'
    }).then((result) => {
        if(result.isConfirmed == true) {
            var url = '{{ route("admin.contacts.destroy", ":userid") }}';
            url = url.replace(':userid', userid);
            $.ajax({
                type: "DELETE",
                url: url,
                data: {'_token': "{{ csrf_token() }}"},
                success: function(response) {
                    if(response.success){
                        setFlesh('success','Contacts Deleted Successfully');
                        $('#contactsTable').DataTable().ajax.reload();
                    }else{
                        setFlesh('error','There is some problem to delete feedback!Please try again');
                    }
                }
            });
        }
    })
}






</script>
@endsection
