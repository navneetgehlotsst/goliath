@extends('admin.layouts.app')
@section('style')
    <style>

    </style>
@endsection
@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    {{-- <h5 class="py-2 mb-2">
        <span class="text-primary fw-bold">Completed Predictions</span>
    </h5> --}}
    <h5 class="py-3 mb-4">
        <span class="text-muted fw-light"><a href="{{route('admin.dashboard')}}">Home</a> / </span> Completed Predictions
    </h5>
    <div class="row mb-5">
        <div class="col-md-12 col-lg-12">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <h6 class="text-primary fw-bold">Filter predictions by date</h6>
                        <div class="col-md-6">
                            <input type="text" class="form-control" name="daterange" value="" />
                        </div>
                    </div>
                    <div class="table-responsive text-nowrap">
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
        let startdate = null;
        let enddate = null;

        function initializeDataTable() {
            if ($.fn.DataTable.isDataTable('#PredictionsTable')) {
                $('#PredictionsTable').DataTable().destroy();
            }

            $('#PredictionsTable').DataTable({
                processing: true,
                searching: false,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.predict.allpridicted') }}",
                    data: function (d) {
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                    }
                },
                order: [],
                columns: [
                    {
                        data: null,
                        render: (data, type, row) => {
                            console.log('Row data:', row);  // Log row data to debug
                            return `<figure class="figure" style="text-align: center;">
                                        <img src="${row.competition_match.teama_img}" alt="" class="predicted_match_all" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="" />
                                        ${row.competition_match.teama_name}
                                    </figure>
                                    VS
                                    <figure class="figure" style="text-align: center;">
                                        <img src="${row.competition_match.teamb_img}" alt="" class="predicted_match_all" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="" />
                                        ${row.competition_match.teamb_name}
                                    </figure>`
                        }
                    },
                    {
                        data: null,
                        render: (data, type, row) => {
                            var updatedate = new Date(row.updated_at);
                            var formattedDate = updatedate.getFullYear() + "-" +
                                                ('0' + (updatedate.getMonth() + 1)).slice(-2) + "-" +
                                                ('0' + updatedate.getDate()).slice(-2);
                            return formattedDate;
                        }
                    },
                    {
                        data: null,
                        render: (data, type, row) => {
                            const matchId = row.competition_match ? row.competition_match.match_id : '';
                            const detailUrl = `{{ route('admin.predict.info', ':id') }}`.replace(':id', matchId);
                            return `<a href="${detailUrl}" class="btn btn-sm btn-primary">Prediction Details</a>`;
                        }
                    }
                ]
            });
        }

        // Initialize the date range picker
        $('input[name="daterange"]').daterangepicker({
            opens: 'left'
        }, function(start, end, label) {
            startdate = start.format('YYYY-MM-DD');
            enddate = end.format('YYYY-MM-DD');
            $('#start_date').val(startdate);  // Update hidden input
            $('#end_date').val(enddate);      // Update hidden input
            initializeDataTable();  // Redraw the table with new date range
        });

        // Create hidden inputs to store start and end dates
        $('body').append('<input type="hidden" id="start_date" />');
        $('body').append('<input type="hidden" id="end_date" />');

        // Initial table draw
        initializeDataTable();
    });

</script>
@endsection
