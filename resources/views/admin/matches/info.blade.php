@extends('admin.layouts.app')
@section('style')
    <style>

    </style>
@endsection
@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <h5 class="py-3 mb-4">
        <span class="text-muted fw-light"><a href="{{route('admin.dashboard')}}">Home</a> / <a href="{{ $previousURL }}">Competitions</a> /  <a href="{{ route('admin.match.index', $transformedMatch['matchdetail']['competition_id']) }}">Matches</a> / </span> Match Info
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
                                @elseif ($transformedMatch['matchdetail']['status'] == 'Live')
                                    <span class="badge bg-label-success">{{$transformedMatch['matchdetail']['status']}}</span>
                                @else
                                    <span class="badge bg-label-danger">{{$transformedMatch['matchdetail']['status']}}</span>
                                @endif
                                <br>
                                @php
                                    $datetime = $transformedMatch['matchdetail']['match_start_time'];
                                    $returnresult = Helper::timezone($datetime);

                                @endphp
                                <span>Date & Time :- {{$transformedMatch['matchdetail']['match_start_date']}} / {{$returnresult}}</span>
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
                    <h6>Match Innings</h6>
                    <div class="demo-inline-spacing">
                        <span class="badge rounded-pill bg-label-danger">Completed Over</span>
                        <span class="badge rounded-pill bg-danger">On Going Over</span>
                        <span class="badge rounded-pill bg-label-success">Available Over</span>
                        <span class="badge rounded-pill bg-label-warning">Not Available Over</span>
                        <span class="badge rounded-pill bg-success">Predicted Over</span>
                    </div>
                  </div>
                  @foreach ($transformedMatch['matchdetail']['innings'] as $innings)
                  <div class="col-xl-6">
                    <div class="mb-2 fw-bolder">{{$innings['inning_name']}}</div>
                    <div class="demo-inline-spacing">
                      <p>
                        @foreach ($innings['overs'] as $matchdatainningsone)
                            <a href="{{ route('admin.match.question', ['overid' => $matchdatainningsone['over_id']]) }}"
                                class="badge badge-center rounded-pill
                                    @if($innings['inning_status'] == 'Completed')
                                        bg-label-danger
                                    @else
                                        @if($matchdatainningsone['over_status'] == 'Completed')
                                            bg-label-danger
                                        @elseif($matchdatainningsone['over_status'] == 'Ongoing')
                                            bg-danger
                                        @elseif($matchdatainningsone['over_status'] == 'Available')
                                            bg-label-success
                                        @elseif($matchdatainningsone['over_status'] == 'Predicted')
                                            bg-success
                                        @else
                                            bg-label-warning
                                        @endif
                                    @endif
                                    mb-2
                                    @if($innings['inning_status'] == 'Completed')
                                        matchdisable
                                    @else
                                        @if($matchdatainningsone['over_status'] == 'Completed')
                                            matchdisable
                                        @elseif($matchdatainningsone['over_status'] == 'Ongoing')
                                            matchdisable
                                        @elseif($matchdatainningsone['over_status'] == 'Available')
                                            bg-label-success
                                        @elseif($matchdatainningsone['over_status'] == 'Predicted')
                                            matchdisable
                                        @else
                                            matchdisable
                                        @endif
                                    @endif">
                                {{$matchdatainningsone['over_number']}}
                            </a>
                        @endforeach
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
