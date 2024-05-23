@extends('admin.layouts.app')
@section('style')
    <style>

    </style>
@endsection
@section('content')
<div class="container-fluid flex-grow-1 container-p-y">
    <h5 class="py-2 mb-2">
        <span class="text-primary fw-light">Leaderboard(Prediction Winning)</span>
    </h5>
    <div class="row">
        <div class="col-xl-12">
            <div class="nav-align-top mb-4">
                <ul class="nav nav-pills mb-3 nav-fill" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-pills-justified-daily" aria-controls="navs-pills-justified-daily" aria-selected="true">Daily</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-pills-justified-monthly" aria-controls="navs-pills-justified-monthly" aria-selected="false" tabindex="-1">Monthly</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-pills-justified-yearly" aria-controls="navs-pills-justified-yearly" aria-selected="false" tabindex="-1">Yearly</button>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="navs-pills-justified-daily" role="tabpanel">
                        <div class="table-responsive text-nowrap">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Position</th>
                                        <th>Name</th>
                                        <th>Prediction Winning</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                    @php
                                        $dalysn = 1;
                                    @endphp
                                    @foreach ($topDailyPredictions as $dailyPredictiondata)
                                        <tr>
                                            <td>{{$dalysn++}}</td>
                                            <td>{{$dailyPredictiondata['name']}}</td>
                                            <td>{{$dailyPredictiondata['total_winning']}}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="navs-pills-justified-monthly" role="tabpanel">
                        <div class="table-responsive text-nowrap">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Position</th>
                                        <th>Name</th>
                                        <th>Prediction Winning</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                    @php
                                        $monthsn = 1;
                                    @endphp
                                    @foreach ($topMonthlyPredictions as $topMonthlyPrediction)
                                        <tr>
                                            <td>{{$monthsn++}}</td>
                                            <td>{{$topMonthlyPrediction['name']}}</td>
                                            <td>{{$topMonthlyPrediction['total_winning']}}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="navs-pills-justified-yearly" role="tabpanel">
                        <div class="table-responsive text-nowrap">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Position</th>
                                        <th>Name</th>
                                        <th>Prediction Winning</th>
                                    </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                    @php
                                        $yealysn = 1;
                                    @endphp
                                    @foreach ($topYearlyPredictions as $topYearlyPrediction)
                                        <tr>
                                            <td>{{$yealysn++}}</td>
                                            <td>{{$topYearlyPrediction['name']}}</td>
                                            <td>{{$topYearlyPrediction['total_winning']}}</td>
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
