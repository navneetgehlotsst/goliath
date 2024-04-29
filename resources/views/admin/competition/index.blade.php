@extends('admin.layouts.app')
@section('style')
    <style>

    </style>
@endsection
@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <h5 class="py-2 mb-2">
        <span class="text-primary fw-light">Competitions</span>
    </h5>
    <div class="row">
        <div class="col-md-12">

            {{-- Select Compitions --}}
            <div class="card mb-4">
                <h5 class="card-header">Select Competitions</h5>
                <div class="card-body">
                    @php
                        $competitionsByStatus = Helper::getCompetitionsStatus();
                    @endphp
                    <div>
                        <select class="form-select" id="competitions_type" name="competitions_type">
                            @foreach ( $competitionsByStatus as $key => $competitions)
                                <option value="{{$key}}">{{$competitions}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>


            {{-- Get Compition Data --}}
            <div class="card" id="competitionlist">
                <h5 class="card-header">Competitions List</h5>
                <div class="table-responsive text-nowrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Competition id</th>
                                <th>Competitions</th>
                                <th>Category</th>
                                <th>Format</th>
                                <th>Date Start / Date End</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0" id="competitions_data">

                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>




@endsection
@section('script')
<script>
    $(document).ready(function(){
        function populateTable(status, page) {
            $.ajax({
                url: "{{ route('admin.competition.get.data') }}",
                type: "GET",
                data: { status: status , page: page },
                success: function(response){
                    // Populate the table with the response data
                    var tableBody = $('#competitions_data');
                    tableBody.empty(); // Clear existing data

                    $.each(response, function(index, row){
                        $("#competitionlist").removeClass("d-none");
                        var matchurl = "{{ route('admin.match.index', [':cId', ':pagedata']) }}";
                        matchurl = matchurl.replace(':cId', row.cid);
                        matchurl = matchurl.replace(':pagedata', page);
                        var newRow = $('<tr>');
                        newRow.append($('<td>').html('<a href="'+matchurl+'" class="link-primary">'+row.cid+'</a>'));
                        newRow.append($('<td>').text(row.title));
                        newRow.append($('<td>').text(row.category));
                        newRow.append($('<td>').text(row.game_format));
                        newRow.append($('<td>').text(row.datestart+' / '+row.dateend));
                        newRow.append($('<td>').text(row.status));
                        // Add more columns as needed
                        tableBody.append(newRow);
                    });
                }
            });
        }

        // Call populateTable on document ready
        populateTable($('#competitions_type').val(), '1');

        $('#competitions_type').change(function(){
            var status = $(this).val();
            var page = '1';
            populateTable(status, page);
        });
    });

</script>

@endsection
