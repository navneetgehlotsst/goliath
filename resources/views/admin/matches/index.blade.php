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
        <div class="col-xl-12 col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive text-nowrap">
                        <table class="table table-bordered" id="MatchesTable">
                            <thead>
                                <tr>
                                    <th>Match</th>
                                    <th>Short Title</th>
                                    <th>Format</th>
                                    <th>Match Date / Time</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ( $CompetitionMatchData as $CompetitionMatchDatas)
                                    <tr>
                                        <td>{{$CompetitionMatchDatas->match}}</td>
                                        <td><img src="{{$CompetitionMatchDatas->teama_img}}" alt="" width="10%"> V/S <img src="{{$CompetitionMatchDatas->teamb_img}}" alt="" width="10%"></td>
                                        <td>{{$CompetitionMatchDatas->formate}}</td>
                                        <td>{{$CompetitionMatchDatas->match_start_date}} / {{$CompetitionMatchDatas->match_start_time}}</td>
                                        <td>
                                            @if($CompetitionMatchDatas->status == 'Completed')
                                                <span class="badge bg-success">Completed</span>
                                            @elseif ($CompetitionMatchDatas->status == 'Scheduled')
                                                <span class="badge bg-info">Scheduled</span>
                                            @elseif ($CompetitionMatchDatas->status == 'Live')
                                                <span class="badge bg-warning">Live</span>
                                            @else
                                                <span class="badge bg-danger">Cancelled</span>
                                            @endif
                                        </td>
                                        <td><a href="{{ route('admin.match.info', $CompetitionMatchDatas->match_id ) }}" class="btn btn-primary">Match Info</a></td>
                                    </tr>
                                @endforeach
                            </tbody>
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
    $('#MatchesTable').DataTable({
        processing: true,
        ordering: false
    });
</script>
@endsection
