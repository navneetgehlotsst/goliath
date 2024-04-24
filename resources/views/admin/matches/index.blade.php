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
        <div class="col-md-12">
            {{-- Get Matches Data --}}
            <div class="card">
                <h5 class="card-header">Matches List</h5>
                <div class="table-responsive text-nowrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Match id</th>
                            <th>Title</th>
                            <th>Short Title</th>
                            <th>Sub Title</th>
                            <th>Format</th>
                            <th>Note</th>
                            <th>Match Date / Time</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0" id="matches_data">
                        @foreach ($matchdata as $matchdatas)
                            <tr>
                                <td>{{$matchdatas['match_id']}}</td>
                                <td>{{$matchdatas['title']}}</td>
                                <td>{{$matchdatas['short_title']}}</td>
                                <td>{{$matchdatas['subtitle']}}</td>
                                <td>{{$matchdatas['format_str']}}</td>
                                <td>{{$matchdatas['status_note']}}</td>
                                <td>{{$matchdatas['date_start_ist']}}</td>
                                <td>{{$matchdatas['status_str']}}</td>
                                <td><a href="{{ route('admin.matches.match.info', $matchdatas['match_id'] ) }}" class="btn btn-primary">Match Info</a></td>
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
