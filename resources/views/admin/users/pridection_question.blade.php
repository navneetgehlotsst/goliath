@extends('admin.layouts.app')
@section('style')
    <style>

    </style>
@endsection
@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    {{-- <h5 class="py-2 mb-2">
        <span class="text-primary fw-light">Predicted Answers</span>
    </h5> --}}
    <h5 class="py-3 mb-4">
        <span class="text-muted fw-light"><a href="{{route('admin.dashboard')}}">Home</a> / <a href="{{route('admin.users.index')}}">Users </a> / <a href="{{ route("admin.users.show", $userid) }}">User Detail</a> / <a href="{{ route("admin.users.match.prediction", ['user' => $userid, 'matchId' => $matchid]) }}">Match Information</a> / </span> Predicted Answers
    </h5>
    <div class="row mb-5">
        <div class="col-md-12 col-lg-12">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="table-responsive text-nowrap">
                            <h4>Total Correct Answers :- {{$predictedData['correct_counts']}}</h4>
                            <h5> Total Amount Won :- @if ($predictedData['correct_counts'] >= 5) {{$predictedData['winning_amount']}} @else 0  @endif</h5>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Question</th>
                                        <th>User Answer</th>
                                        <th>Result</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($predictedData['user_prediction'] as $user_prediction)
                                        <tr>
                                            <td>{{$user_prediction->question}}</td>
                                            <td class="text-capitalize">{{$user_prediction->your_answer}}</td>
                                            <td>
                                                @if($user_prediction->your_result == "ND")
                                                    Not Declared
                                                @elseif($user_prediction->your_result == "W")
                                                    Won
                                                @else
                                                    Loss
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
</div>




@endsection
@section('script')


@endsection
