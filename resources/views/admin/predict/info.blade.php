@extends('admin.layouts.app')
@section('style')
    <style>

    </style>
@endsection
@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <h5 class="py-2 mb-2">
        <span class="text-primary fw-light">Matches Info</span>
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
                                    <span class="badge bg-label-success">{{$transformedMatch['matchdetail']['status']}}</span>
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
                    <h6>Match Prediction</h6>
                  </div>
                  @foreach ($transformedMatch['matchdetail']['innings'] as $innings)
                  <div class="col-xl-6">
                    <div class="mb-2 fw-bolder">{{$innings['inning_name']}}</div>
                    <div class="demo-inline-spacing">

                      <p>
                        @if (!empty($innings['overs']))
                            @foreach ($innings['overs'] as $matchdatainningsone)
                                <a href="{{ route('admin.predict.user', ['overid' => $matchdatainningsone['over_id'],'matchid' => $transformedMatch['matchdetail']['match_id']]) }}" class="badge badge-center rounded-pill bg-success">
                                    {{$matchdatainningsone['over_number']}}
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
    </div>
</div>




@endsection
@section('script')


@endsection
