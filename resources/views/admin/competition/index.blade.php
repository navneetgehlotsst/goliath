@extends('admin.layouts.app')
@section('style')
    <style>

    </style>
@endsection
@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <h5 class="py-2 mb-2">
        <span class="text-primary fw-light">{{$titel}} Competitions</span>
    </h5>
    <div class="row">
        <div class="col-xl-12 col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive text-nowrap">
                        <table class="table table-bordered" id="competitionTable">
                            <thead>
                                <tr>
                                    <th>Competition ID</th>
                                    <th>Competition Name</th>
                                    <th>Category</th>
                                    <th>Format</th>
                                    <th>Start Date / End Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ( $CompetitionLiveData as $CompetitionDatas)
                                    <tr>
                                        <td><a href="{{ route('admin.match.index', $CompetitionDatas->competiton_id) }}">{{$CompetitionDatas->competiton_id}}</a></td>
                                        <td>{{$CompetitionDatas->title}}</td>
                                        <td>{{$CompetitionDatas->type}}</td>
                                        <td>{{$CompetitionDatas->competition_type}}</td>
                                        <td>{{$CompetitionDatas->date_start}} / {{$CompetitionDatas->date_end}}</td>
                                        <td>
                                            @if($CompetitionDatas->status == 'result')
                                                <span class="badge bg-success">Completed</span>
                                            @elseif ($CompetitionDatas->status == 'live')
                                                <span class="badge bg-info">Live</span>
                                            @else
                                                <span class="badge bg-warning">Upcoming</span>
                                            @endif

                                        </td>
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
    $('#competitionTable').DataTable({
        processing: true,
    });
</script>
@endsection
