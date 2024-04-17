@extends('admin.layouts.app')
@section('style')
    <style>

    </style>
@endsection
@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <h5 class="py-2 mb-2">
        <span class="text-primary fw-light">Questions</span>
    </h5>
    <div class="row">
        <div class="col-xl-12 col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive text-nowrap">
                    <table class="table table-bordered" id="QuestionsTable">
                        <thead>
                            <tr>
                                <th>S.no.</th>
                                <th>Question</th>
                                <th>Question Type</th>
                                <th>Type</th>
                                <th>Conditions</th>
                                <th>Quantity</th>
                                {{--  <th>Action</th>  --}}
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
$('#QuestionsTable').DataTable({
    processing: true,
    ajax: {
      url: "{{route('admin.questions.allquestions')}}",
    },
    order: [],
    columns: [

        {
            data: "id",
        },
        {
            data: "question",
        },
        {
            data: "question_type",
        },
        {
            data: "type",
        },
        {
            data: "conditions",
        },

        {
            data: "quantity",
        },
        //{
          //  data: "action",
            //render: (data,type,row) => {
              //      return '<button type="button" class="btn btn-sm btn-danger" onclick="deletes('+row.id+')">Delete</button>';
            //}
        //}

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
