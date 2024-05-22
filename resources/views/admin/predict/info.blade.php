@extends('admin.layouts.app')
@section('style')
    <style>

    </style>
@endsection
@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <h5 class="py-2 mb-2">
        <span class="text-primary fw-light">Match Information</span>
    </h5>
    <div class="row mb-5">
        <div class="col-md-12 col-lg-12">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <h4 class="text-center">
                                {{$transformedMatch['matchdetail']['match']}}
                            </h4>
                            <h6 class="text-center">
                                {{$transformedMatch['matchdetail']['short_title']}}
                            </h6>
                        </div>
                        {{-- team A Data --}}
                        <div class="col-md-6 text-center">
                            <p>
                                <span class="fw-bold fs-5">
                                    @if($transformedMatch['matchdetail']['teama']['name'] == "TBA")
                                        TBA (To be announced)
                                    @else
                                        {{$transformedMatch['matchdetail']['teama']['name']}}
                                    @endif
                                </span>
                                <br>
                                {{$transformedMatch['matchdetail']['teama']['scores_full'] ?? ''}}
                            </p>
                            <img src="{{$transformedMatch['matchdetail']['teama']['logo_url']}}" class="teamlogo" alt="">
                        </div>
                        {{-- team B Data --}}
                        <div class="col-md-6 text-center">
                            <p>
                                <span class="fw-bold fs-5">
                                    @if($transformedMatch['matchdetail']['teamb']['name'] == "TBA")
                                        TBA (To be announced)
                                    @else
                                        {{$transformedMatch['matchdetail']['teamb']['name']}}
                                    @endif
                                </span>
                                <br>
                                {{$transformedMatch['matchdetail']['teamb']['scores_full'] ?? ''}}
                            </p>
                            <img src="{{$transformedMatch['matchdetail']['teamb']['logo_url']}}" class="teamlogo" alt="">
                        </div>
                        {{-- Match result --}}
                        <div class="col-md-12">
                            <h6 class="text-center">
                                {{$transformedMatch['matchdetail']['note']}}
                            </h6>
                            <div class="text-center">
                                @if ($transformedMatch['matchdetail']['status'] == 'Scheduled')
                                    <span class="badge bg-label-secondary">{{$transformedMatch['matchdetail']['status']}}</span>
                                @elseif ($transformedMatch['matchdetail']['status'] == 'Completed')
                                    <span class="badge bg-label-success">{{$transformedMatch['matchdetail']['status']}}</span>
                                @else
                                    <span class="badge bg-label-danger">{{$transformedMatch['matchdetail']['status']}}</span>
                                @endif
                                <br>
                                <span>Date & Time :- {{$transformedMatch['matchdetail']['match_start_date']}} / {{$transformedMatch['matchdetail']['match_start_time']}}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card mb-4">
              <div class="card-body">
                <div class="row gy-3">
                  <div class="col-md-12">
                    <h5>Below are the overs where predictions have been placed</h5>
                  </div>
                  @foreach ($transformedMatch['matchdetail']['innings'] as $innings)
                  <div class="col-xl-6">
                    <div class="mb-2 fw-bolder">{{$innings['inning_name']}}</div>
                    <div class="demo-inline-spacing">

                      <p>
                        @if (!empty($innings['overs']))
                            @foreach ($innings['overs'] as $matchdatainningsone)
                            <a href="javascript:void(0)" data-inningname ="{{ $innings['inning_name'] }}" data-overnumber ="{{ $matchdatainningsone['over_number'] }}" data-overid="{{ $matchdatainningsone['over_id'] }}" data-matchid="{{ $transformedMatch['matchdetail']['match_id'] }}" class="badge badge-center rounded-pill bg-success getPredictionData">
                                {{ $matchdatainningsone['over_number'] }}
                            </a>
                            @endforeach
                        @else
                            <span>No Pridiction In this Innings</span>
                        @endif
                      </p>
                    </div>
                  </div>
                  @endforeach
                </div>
              </div>
            </div>
        </div>
        <div class="col-md-12">
            <div id="userprediction" class="card mb-4 d-none">
                <div class="card-body">
                  <div class="row gy-3">
                    <div class="col-md-12">
                      <h5 id="predictedText">Prediction Result</h5>
                    </div>
                    <div class="table-responsive text-nowrap">
                        <table id="getpredictUser" class="table table-bordered" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>Username</th>
                                    <th>Prediction Correct Answer</th>
                                    <th>Prediction Wrong Answer</th>
                                    <th>Total Predict Questions</th>
                                    <th>Prediction Result</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                            </tbody>
                        </table>
                    </div>
                  </div>
                </div>
              </div>
        </div>
    </div>
</div>
@endsection
@section('script')

<script>
    $(document).ready(function () {
        // Cache the table and user prediction container
        const $tableElement = $("#getpredictUser");
        const $userPrediction = $("#userprediction");

        // Initialize DataTable
        const table = $tableElement.DataTable({
            processing: true,
            ordering: true,
            searching: true,
            paging: true,
            columnDefs: [{ searchable: false, targets: 3 }]
        });

        // Event delegation for click events
        $(document).on("click", ".getPredictionData", function () {
            const preicturl = "{{ route('admin.predict.user') }}";
            const overid = $(this).data("overid");
            const matchid = $(this).data("matchid");
            const inningname = $(this).data("inningname");
            const overnumber = $(this).data("overnumber");

            $.ajax({
                url: preicturl,
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    overid: overid,
                    matchid: matchid,
                },
                success: function (response) {
                    if (response.status == true) {
                        // Clear existing data
                        table.clear();

                        // Generate new rows
                        const rows = response.data.map((pridectuser) => {
                            const worong = 8 - pridectuser.win_count;
                            const totalquestion = 8;
                            let status;
                            if (pridectuser.win_count === 8) {
                                status = "<p>Goliath Winner</p>";
                            } else if (pridectuser.win_count >= 5 && pridectuser.win_count < 8) {
                                status = "<p>Winner</p>";
                            } else {
                                status = "<p>Loser</p>";
                            }
                            return [pridectuser.full_name, pridectuser.win_count, worong, totalquestion, status];
                        });

                        // Add rows to the table
                        table.rows.add(rows);

                        // Draw the table with new data
                        table.draw();

                        // Show user prediction section if hidden
                        $userPrediction.removeClass("d-none");
                    }else{
                         // Show user prediction section if hidden
                         $userPrediction.addClass("d-none");
                         alert('Match Prediction Result Not Found');
                    }
                    $('#predictedText').text('Displaying results for the '+overnumber+'th over of '+inningname);

                },
                error: function (xhr, status, error) {
                    console.error("AJAX Error:", status, error);
                },
            });
        });
    });
</script>

@endsection
