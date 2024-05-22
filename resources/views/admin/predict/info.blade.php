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
        <div class="col-12">
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
                            <a href="javascript:void(0)" data-overid="{{ $matchdatainningsone['over_id'] }}" data-matchid="{{ $transformedMatch['matchdetail']['match_id'] }}" class="badge badge-center rounded-pill bg-success getPredictionData">
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
            <div id="userprediction" class="table-responsive text-nowrap d-none">
                <table id="getpredictUser" class="table">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Prediction Score</th>
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




@endsection
@section('script')

<script>
    $(document).ready(function(){
        $('.getPredictionData').click(function(){
            var preicturl = '{{ route('admin.predict.user') }}';
            var overid = $(this).data("overid");
            var matchid = $(this).data("matchid");

            $.ajax({
                url: preicturl,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}', // Include CSRF token
                    overid: overid,
                    matchid: matchid
                },
                success: function(response) {
                    var tbody = $('#getpredictUser tbody');
                    tbody.empty(); // Clear the table
                    $.each(response.data, function(index, pridectuser){

                        $('#userprediction').removeClass('d-none');
                        var status;
                        if (pridectuser.win_count == 8) {
                            status = '<p>Goliath</p>';
                        } else if (pridectuser.win_count >= 5 && pridectuser.win_count < 8) {
                            status = '<p>Winner</p>';
                        } else {
                            status = '<p>Loser</p>';
                        }

                        var row = '<tr>' +
                            '<td>' + pridectuser.full_name + '</td>' +
                            '<td>' + pridectuser.win_count + '/8</td>' +
                            '<td>' + status + '</td>' +
                            '</tr>';
                        tbody.append(row);
                    });
                }
            });
        });
    });
</script>

@endsection
