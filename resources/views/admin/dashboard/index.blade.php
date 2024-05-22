@extends('admin.layouts.app')
@section('style')
@endsection

@section('content')
<!-- Content -->

<div class="container-fluid flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-sm-6 col-lg-3 mb-4">
            <div class="card card-border-shadow-primary h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2 pb-1">
                        <div class="avatar me-2">
                            <span class="avatar-initial rounded bg-label-primary"><i class="bx bxs-user"></i></span>
                        </div>
                        <h4 class="ms-1 mb-0">{{$userCount}}</h4>
                    </div>
                    <p class="mb-1">Total Users</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3 mb-4">
            <div class="card card-border-shadow-primary h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2 pb-1">
                        <div class="avatar me-2">
                            <span class="avatar-initial rounded bg-label-primary"><i class="bx bxs-calendar"></i></span>
                        </div>
                        <h4 class="ms-1 mb-0">{{$predictionMonthCount}}</h4>
                    </div>
                    <p class="mb-1">Total Predictions</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3 mb-4">
            <div class="card card-border-shadow-primary h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2 pb-1">
                        <div class="avatar me-2">
                            <span class="avatar-initial rounded bg-label-primary"><i class="bx bx-coin-stack"></i></span>
                        </div>
                        <h4 class="ms-1 mb-0">5000</h4>
                    </div>
                    <p class="mb-1">Total Amount Earned by platform</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3 mb-4">
            <div class="card card-border-shadow-primary h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2 pb-1">
                        <div class="avatar me-2">
                            <span class="avatar-initial rounded bg-label-primary"><i class="bx bx-coin-stack"></i></span>
                        </div>
                        <h4 class="ms-1 mb-0">1500</h4>
                    </div>
                    <p class="mb-1">Total Amount distributed as winnings</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-lg-3 order-2 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title m-0 me-2">Recently Completed Predictions</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        @foreach ($latestPredictions as $latestPrediction)
                        <a href="{{ route('admin.predict.info', $latestPrediction['competitionMatch']->match_id ) }}">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <img src="{{$latestPrediction['competitionMatch']->teama_img}}" alt="" class="predicted_match_logo" />
                                V/S
                                <img src="{{$latestPrediction['competitionMatch']->teamb_img}}" alt="" class="predicted_match_logo" />
                            </li>
                        </a>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-9 col-lg-9 order-2 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="card-title m-0 me-2">Monthly Predictions</h5>
                </div>
                <div class="card-body">
                    <div id="monthlyprediction"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- / Content -->

<!-- Footer -->

<!-- / Footer -->

@endsection

@section('script')
<script>
    var golithwinnerjson = <?php echo $golithwinnerjson ?>;
    var winnerjson = <?php echo $winnerjson ?>;
    var loserjson = <?php echo $loserjson ?>;
    var monthjson = <?php echo $monthjson ?>;

    var options = {
        series: [
            {
                name: "Goliath Winners",
                data: golithwinnerjson,
            },
            {
                name: "Other Category Winners",
                data: winnerjson,
            },
            {
                name: "Losers",
                data: loserjson,
            },
        ],
        chart: {
            type: "bar",
            height: 500,
            toolbar: {
                show: false
            }
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: "55%",
                endingShape: "rounded",
            },
        },
        dataLabels: {
            enabled: false,
        },
        stroke: {
            show: true,
            width: 2,
            colors: ["transparent"],
        },
        xaxis: {
            categories: monthjson,
        },
        yaxis: {
            title: {
                text: "Users",
            },
        },
        fill: {
            opacity: 1,
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return val;
                },
            },
        },
    };

    var chart = new ApexCharts(document.querySelector("#monthlyprediction"), options);
    chart.render();
</script>
@endsection
