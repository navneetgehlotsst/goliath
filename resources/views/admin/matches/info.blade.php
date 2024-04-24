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
        <div class="col-md-12">
            <div class="card">
                <h5 class="card-header">Match Questions</h5>
                <div class="table-responsive text-nowrap">
                  <table class="table">
                    <thead>
                      <tr>
                        <th>Over</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @foreach ($addQuestionsdata as  $addQuestion)
                            <tr>
                                <td>{{ $addQuestion->over }}</td>
                                <td><a href="" class="btn btn-secondary">Change Question</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                  </table>
                </div>
              </div>
        </div>
    </div>
</div>




@endsection
@section('script')


@endsection
