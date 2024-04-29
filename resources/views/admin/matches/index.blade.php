@extends('admin.layouts.app')
@section('style')
    <style>

    </style>
@endsection
@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <h5 class="py-2 mb-2">
        <span class="text-primary fw-light">Matches</span>
    </h5>
    <div class="row">
        

        <div class="col-xl-12">
            <div class="nav-align-top mb-4">
              <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item" role="presentation">
                  <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-home" aria-controls="navs-top-home" aria-selected="true">Live</button>
                </li>
                <li class="nav-item" role="presentation">
                  <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-profile" aria-controls="navs-top-profile" aria-selected="false" tabindex="-1">Scheduled</button>
                </li>
              </ul>
              <div class="tab-content">
                <div class="tab-pane fade active show" id="navs-top-home" role="tabpanel">
                    @if($matchliveddata)
                        <div class="col-md-12">
                            <div class="card">
                                <h5 class="card-header">Matches List</h5>
                                <div class="table-responsive text-nowrap">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th scope="col" class="col-3">Title</th>
                                                <th scope="col" class="col-3">Short Title</th>
                                                <th scope="col" class="col-3">Format</th>
                                                <th scope="col" class="col-3">Match Date / Time</th>
                                                <th scope="col" class="col-3">Status</th>
                                                <th scope="col" class="col-3">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="table-border-bottom-0" id="matches_data">
                                            @foreach ($matchliveddata as $matchlivedatas)
                                                <tr>
                                                    <td>{{$matchlivedatas['title']}}</td>
                                                    <td><img src="{{$matchlivedatas['teama']['logo_url']}}" alt="" width="10%"> V/S <img src="{{$matchlivedatas['teamb']['logo_url']}}" alt="" width="10%"></td>
                                                    <td>{{$matchlivedatas['format_str']}}</td>
                                                    <td>{{$matchlivedatas['date_start_ist']}}</td>
                                                    <td>{{$matchlivedatas['status_str']}}</td>
                                                    <td><a href="{{ route('admin.matches.info', $matchlivedatas['match_id'] ) }}" class="btn btn-primary">Match Info</a></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="col-lg-12">
                            <div class="demo-inline-spacing d-flex">
                                <p>No Matches Found</p>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="tab-pane fade" id="navs-top-profile" role="tabpanel">
                    @if($matchscheduleddata)
                        <div class="col-md-12">
                            <div class="card">
                                <h5 class="card-header">Matches List</h5>
                                <div class="table-responsive text-nowrap">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th scope="col" class="col-3">Title</th>
                                                <th scope="col" class="col-3">Short Title</th>
                                                <th scope="col" class="col-3">Format</th>
                                                <th scope="col" class="col-3">Match Date / Time</th>
                                                <th scope="col" class="col-3">Status</th>
                                                <th scope="col" class="col-3">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="table-border-bottom-0" id="matches_data">
                                            @foreach ($matchscheduleddata as $scheduledmatchdatas)
                                                <tr>
                                                    <td>{{$scheduledmatchdatas['title']}}</td>
                                                    <td><img src="{{$scheduledmatchdatas['teama']['logo_url']}}" alt="" width="10%"> V/S <img src="{{$scheduledmatchdatas['teamb']['logo_url']}}" alt="" width="10%"></td>
                                                    <td>{{$scheduledmatchdatas['format_str']}}</td>
                                                    <td>{{$scheduledmatchdatas['date_start_ist']}}</td>
                                                    <td>{{$scheduledmatchdatas['status_str']}}</td>
                                                    <td><a href="{{ route('admin.matches.info', $scheduledmatchdatas['match_id'] ) }}" class="btn btn-primary">Match Info</a></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="demo-inline-spacing d-flex justify-content-end">
                            <nav aria-label="Page navigation">
                                <ul class="pagination">
                                @for ($pages = 1 ; $pages <= $scheduledpagecount ; $pages++ )
                                    <li class="page-item @if ($pages == $page) active @endif">
                                        <a class="page-link" href="{{ route('admin.matches.index', ['cid' => $cId, 'page' => $pages]) }}">{{ $pages }}</a>
                                    </li>
                                @endfor
                                </ul>
                            </nav>
                            </div>
                        </div>
                    @else
                        <div class="col-lg-12">
                            <div class="demo-inline-spacing d-flex">
                                <p>No Matches Found</p>
                            </div>
                        </div>
                    @endif
                </div>
              </div>
            </div>
          </div>
    </div>
</div>




@endsection
@section('script')


@endsection
