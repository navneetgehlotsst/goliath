@extends('admin.layouts.app')
@section('style')
    <style>

    </style>
@endsection
@section('content')



<div class="container-fluid flex-grow-1 container-p-y">
    {{-- <h5 class="py-2 mb-2">
        <span class="text-primary fw-light">Change Question</span>
    </h5> --}}
    <h5 class="py-3 mb-4">
        <span class="text-muted fw-light"><a href="{{route('admin.dashboard')}}">Home</a> / <a href="{{ $previousURL }}">Competitions</a> /  <a href="{{ route('admin.match.index', $competiton_id) }}">Matches</a> / <a href="{{ route('admin.match.info', $match_id ) }}">Match Info</a> / </span> Change Question
    </h5>
    <div class="row mb-5">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row gy-3">
                        <div class="col-xl-12">
                            <div class="table-responsive text-nowrap">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Questions</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-border-bottom-0">
                                        @foreach ($inningsQuestionsData as $inningsQuestionsDatas )
                                            <tr>
                                                <td>
                                                    {{$inningsQuestionsDatas->loadquestion->question}}
                                                    <input class="question" type="hidden" name="questionid" value="{{$inningsQuestionsDatas->loadquestion->question}}">
                                                </td>
                                                <td><button type="button" class="btn btn-primary" onclick="changequestion(`{{$inningsQuestionsDatas->id}}`,`{{$inningsQuestionsDatas->question}}`)">Change Question</button></td>
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
    </div>
</div>


<div class="modal fade" id="ChnageQuestionModel" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel1">Modal title</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="changeques">
            @csrf
            <div class="modal-body">
              <div class="row">
                <div class="col mb-3">
                  <input type="hidden" name="inningquestion" id="inningquestion" value="">
                  <label for="questionList" class="form-label">Available Supplement Questions</label>
                  <select id="questionList" name="questionid" class="form-select form-select-sm" required>
                    <option>Select Question</option>
                    @foreach ($questionList as $questionLists)
                        <option value="{{$questionLists->id}}">{{$questionLists->question}}</option>
                    @endforeach
                  </select>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-primary">Save changes</button>
            </div>
        </form>
      </div>
    </div>
  </div>


@endsection
@section('script')
    <script>
        function changequestion(id,title) {
            $("#ChnageQuestionModel").modal('show');
            $("#exampleModalLabel1").text(title);
            $('#inningquestion').val(id);
        }

        $('#changeques').on('submit', function(e){
            e.preventDefault();
            $.ajax({
                type: "POST",
                url: "{{ route('admin.match.chnagequestion') }}",
                data: $(this).serialize(),
                success: function(response){
                    console.log(response.message);
                    $("#ChnageQuestionModel").modal('hide');
                    Toast.fire({
                        icon: 'success',
                        title: response.message
                    })
                    // Do something with the response if needed
                    location.reload()
                },
                error: function(xhr, status, error){
                    console.error(xhr.responseText);
                    // Handle error if needed
                    Toast.fire({
                        icon: 'error',
                        title: xhr.responseText
                    })
                }
            });
        });
    </script>
@endsection
