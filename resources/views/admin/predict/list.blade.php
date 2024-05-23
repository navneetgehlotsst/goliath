@extends('admin.layouts.app')
@section('style')
    <style>

    </style>
@endsection
@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <h5 class="py-2 mb-2">
        <span class="text-primary fw-bold">Predictions</span>
    </h5>
    <div class="row mb-5">
        <div class="col-md-12 col-lg-12">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="table-responsive text-nowrap">
                        <div class="row">
                            <h6 class="text-primary fw-bold">Date Filter</h6>
                            <div class="col-md-4">
                                <input type="date" class="form-control" id="start_date">
                            </div>
                            <div class="col-md-4">
                                <input type="date" class="form-control" id="end_date">
                            </div>
                        </div>
                        <!-- Date filter inputs -->
                        <table class="table table-bordered" id="PredictionsTable">
                            <thead>
                                <tr>
                                    <th>Match</th>
                                    <th>Match Date</th>
                                    <th>Action</th>
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
    $(document).ready(function() {
        var dataTable = $('#PredictionsTable').DataTable({
            processing: true,
            searching: false,
            serverSide: true, // Enable server-side processing
            ajax: {
                url: "{{ route('admin.predict.allpridicted') }}",
                data: function (d) {
                    // Add start and end date parameters to the AJAX request
                    d.start_date = $('#start_date').val();
                    d.end_date = $('#end_date').val();
                }
            },
            order: [],
            columns: [
                {
                    // Define column data
                    data: null,
                    // Render function for custom HTML
                    render: (data, type, row) => {
                        console.log('Row data:', row);  // Log row data to debug
                        // Return HTML for displaying team logos
                        return `<img src="${row.competition_match.teama_img}" alt="" class="predicted_match_logo" data-bs-toggle="tooltip" data-bs-placement="top" title="${row.competition_match.teama_name}" />
                                V/S
                                <img src="${row.competition_match.teamb_img}" alt="" class="predicted_match_logo" data-bs-toggle="tooltip" data-bs-placement="top" title="${row.competition_match.teamb_name}" />`;
                    }
                },
                {
                    // Define column data
                    data: null,
                    // Render function for displaying match start date
                    // Convert the string to a Date object
                    render: (data, type, row) => {
                        // Parse the updated_at string to a Date object
                        var updatedate = new Date(row.updated_at);
                        // Format the date to yyyy-mm-dd
                        var formattedDate = updatedate.getFullYear() + "-" +
                                            ('0' + (updatedate.getMonth() + 1)).slice(-2) + "-" +
                                            ('0' + updatedate.getDate()).slice(-2);
                        return formattedDate;
                    }
                },
                {
                    // Define column data
                    data: null,
                    // Render function for generating match detail link
                    render: (data, type, row) => {
                        const matchId = row.competition_match ? row.competition_match.match_id : '';
                        const detailUrl = `{{ route('admin.predict.info', ':id') }}`.replace(':id', matchId);
                        return `<a href="${detailUrl}" class="btn btn-sm btn-primary">Match Detail</a>`;
                    }
                }
            ]
        });

        // Update table when the date range filters change
        $('#start_date, #end_date').change(function() {
            dataTable.draw(); // Redraw the table with new date range
        });
    });
</script>
@endsection
