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
                                {{$matchdata['title']}}
                            </h4>
                            <h6 class="text-center">
                                {{$matchdata['short_title']}}
                            </h6>
                        </div>
                        {{-- team A Data --}}
                        <div class="col-md-6 text-center">
                            <p>
                                <span class="fw-bold fs-5">
                                    {{$matchdata['teama']['name']}}
                                </span>
                                <br>
                                {{$matchdata['teama']['scores_full'] ?? ''}}
                            </p>
                            <img src="{{$matchdata['teama']['logo_url']}}" width="10%" alt="">
                        </div>
                        {{-- team B Data --}}
                        <div class="col-md-6 text-center">
                            <p>
                                <span class="fw-bold fs-5">
                                    {{$matchdata['teamb']['name']}}
                                </span>
                                <br>
                                {{$matchdata['teamb']['scores_full'] ?? ''}}
                            </p>
                            <img src="{{$matchdata['teamb']['logo_url']}}" width="10%" alt="">
                        </div>
                        {{-- Match result --}}
                        <div class="col-md-12">
                            <h6 class="text-center">
                                {{$matchdata['status_note']}}
                            </h6>
                            <div class="text-center">
                                @if ($matchdata['status_str'] == 'Scheduled')
                                    <span class="badge bg-label-secondary">{{$matchdata['status_str']}}</span>
                                @elseif ($matchdata['status_str'] == 'Completed')
                                    <span class="badge bg-label-success">{{$matchdata['status_str']}}</span>
                                @else
                                <span class="badge bg-label-danger">{{$matchdata['status_str']}}</span>
                                @endif
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
                        <span class="badge rounded-pill bg-success">Selected Over</span>
                        <span class="badge rounded-pill bg-label-success">Upcoming Over</span>
                        <span class="badge rounded-pill bg-secondary">Pridicted Over</span>
                        <span class="badge rounded-pill bg-label-warning">Available Over</span>
                    </div>
                  </div>
                  <div class="col-xl-6">
                    <div class="mb-2 fw-bolder">Innings 1</div>
                    <div class="demo-inline-spacing">
                      <p>
                        @foreach ($GetMatchdata as $matchdatainningsone)
                            @if ($matchdatainningsone->innings == '1' )
                                <a href="{{ route('admin.matches.question', ['overid' => $matchdatainningsone->innings_overs_id]) }}" class="badge badge-center rounded-pill bg-primary mb-2 text-white">{{$matchdatainningsone->overs}}</a>
                            @endif
                        @endforeach
                      </p>
                    </div>
                  </div>
                  <div class="col-xl-6">
                    <div class="mb-2 fw-bolder">Innings 2</div>
                    <div class="demo-inline-spacing">
                      <p>
                        @foreach ($GetMatchdata as $matchdatainningstwo)
                            @if ($matchdatainningstwo->innings == '2' )
                                <a href="{{ route('admin.matches.question', ['overid' => $matchdatainningsone->innings_overs_id]) }}" class="badge badge-center rounded-pill bg-primary mb-2 text-white">{{$matchdatainningstwo->overs}}</a>
                            @endif
                        @endforeach
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
    </div>
</div>




@endsection
@section('script')


@endsection
