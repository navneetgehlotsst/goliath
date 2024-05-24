@extends('admin.layouts.app') @section('content')



<div class="container-fluid flex-grow-1 container-p-y">
    <h5 class="py-2 mb-2">
        <span class="text-primary fw-light">User Detail</span>
    </h5>
    <div class="row mt-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <h4 class="header-title">
                                <span>
                                    @if(!empty($user->avatar) && file_exists(public_path('/').$user->avatar))
                                    <img src="{{asset($user->avatar)}}" alt="User Image" class="thumb-image rounded-circle" />
                                    @else <img src="{{asset("assets/admin/img/avatars/no-user.jpg")}}" alt="User Image" class="thumb-image rounded-circle"> @endif
                                </span>
                                <span class="text-primary">{{$user->first_name}} {{$user->last_name}}</span>
                            </h4>
                            <hr />
                            <div class="text-left mb-3">
                                @if (!empty($user->email))
                                    <p class="text-muted"><strong>Email Address :</strong> <span class="ml-2">{{$user->email}}</span></p>
                                @else
                                    <p class="text-muted"><strong>Phone Number :</strong> <span class="ml-2">{{$user->phone}}</span></p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- User data --}}
        <div class="col-sm-6 col-lg-2 mt-4 mb-4">
            <div class="card card-border-shadow-primary h-100">
                <div class="card-body">
                <div class="d-flex align-items-center mb-2 pb-1">
                    <div class="avatar me-2">
                        <span class="avatar-initial rounded bg-label-primary"><i class='bx bx-coin-stack'></i></span>
                    </div>
                    <h4 class="ms-1 mb-0">{{$transactionTypes['add-wallet']}}</h4>
                </div>
                <p class="mb-1">Total amount deposited on Goliath</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-2 mt-4 mb-4">
            <div class="card card-border-shadow-primary h-100">
                <div class="card-body">
                <div class="d-flex align-items-center mb-2 pb-1">
                    <div class="avatar me-2">
                    <span class="avatar-initial rounded bg-label-primary"><i class='bx bx-coin-stack'></i></span>
                    </div>
                    <h4 class="ms-1 mb-0">{{$transactionTypes['pay']}}</h4>
                </div>
                <p class="mb-1">Total prediction fee paid</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-2 mt-4 mb-4">
            <div class="card card-border-shadow-primary h-100">
                <div class="card-body">
                <div class="d-flex align-items-center mb-2 pb-1">
                    <div class="avatar me-2">
                    <span class="avatar-initial rounded bg-label-primary"><i class='bx bx-coin-stack'></i></span>
                    </div>
                    <h4 class="ms-1 mb-0">{{$transactionTypes['winning-amount']}}</h4>
                </div>
                <p class="mb-1">Total amount won</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-2 mt-4 mb-4">
            <div class="card card-border-shadow-primary h-100">
                <div class="card-body">
                <div class="d-flex align-items-center mb-2 pb-1">
                    <div class="avatar me-2">
                    <span class="avatar-initial rounded bg-label-primary"><i class='bx bx-coin-stack'></i></span>
                    </div>
                    <h4 class="ms-1 mb-0">{{$transactionTypes['withdrawal-amount']}}</h4>
                </div>
                <p class="mb-1">Total amount withdrawn</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-2 mt-4 mb-4">
            <div class="card card-border-shadow-primary h-100">
                <div class="card-body">
                <div class="d-flex align-items-center mb-2 pb-1">
                    <div class="avatar me-2">
                    <span class="avatar-initial rounded bg-label-primary"><i class='bx bx-coin-stack'></i></span>
                    </div>
                    <h4 class="ms-1 mb-0">{{$user->wallet}}</h4>
                </div>
                <p class="mb-1">Current wallet balance</p>
                </div>
            </div>
        </div>
        <div class="col-lg-2 mb-4 mt-4">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center mb-2 pb-1">
                    <div class="avatar me-2">
                        <span class="avatar-initial rounded bg-label-primary"><i class='bx bx-coin-stack'></i></span>
                    </div>
                    <h6 class="ms-1 mb-0">Leaderboard</h6>
                </div>
                <div class="card-body row g-4">
                    <div class="col-md-6 pe-md-4 card-separator">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <h6 class="mb-0">Position by total winnings</h6>
                        </div>
                        <div class="d-flex justify-content-between" style="position: relative;">
                            <div class="mt-auto">
                                <p class="mb-2">0</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 ps-md-4">
                        <div class="card-title d-flex align-items-start justify-content-between">
                            <h6 class="mb-0">Position by total earnings</h6>
                        </div>
                        <div class="d-flex justify-content-between" style="position: relative;">
                            <div class="mt-auto">
                                <p class="mb-2">0</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12 mt-4">
            <div class="nav-align-top mb-4">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-prediction" aria-controls="navs-top-prediction" aria-selected="true">Predicted Matches</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-transactionlist" aria-controls="navs-top-prediction" aria-selected="true">Transaction List</button>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="navs-top-prediction" role="tabpanel">
                        <div class="row">
                            <div class="card">
                                <div class="card-body">
                                    <div class="table-responsive text-nowrap">
                                        <table class="table table-bordered" id="PredictionTable">
                                            <thead>
                                                <tr>
                                                    <th>Match</th>
                                                    <th>Match Date & Time</th>
                                                    <th>Teams</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                    @foreach ($datamatches as $key => $match)

                                                    @php
                                                        $returncompetion = Helper::CompetionDetail($match['competitionMatch']->competiton_id);
                                                    @endphp
                                                    <tr>
                                                        <td>{{$match['competitionMatch']->match}}</td>
                                                        <td>{{$match['competitionMatch']->match_start_date}}/{{$match['competitionMatch']->match_start_time}}</td>
                                                        <td><img class="predicted_match_logo" src="{{$match['competitionMatch']->teama_img}}" alt=""> V/S <img class="predicted_match_logo" src="{{$match['competitionMatch']->teamb_img}}" alt=""></td>
                                                        <td>
                                                            <a href="{{ route("admin.users.match.prediction", ['user' => $user->id, 'matchId' => $match['competitionMatch']->match_id]) }}" class="btn btn-sm btn-primary">Prediction Detail</a>
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
                    <div class="tab-pane fade" id="navs-top-transactionlist" role="tabpanel">
                        <div class="row">
                            <div class="card">
                                <div class="card-body">
                                    <div class="table-responsive text-nowrap">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Transiction id</th>
                                                    <th>Amount</th>
                                                    <th>Transiction Type</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if ($transactionscount != 0)
                                                    @foreach ($transactions as $key => $transaction)
                                                        <tr>
                                                            <td>{{$transaction->transaction_id}}</td>
                                                            <td>{{$transaction->amount}}</td>
                                                            <td>{{$transaction->transaction_type}}</td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                        <tr>
                                                            <td>No Data Found</td>
                                                        </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
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
<script>
    $('#PredictionTable').DataTable({
        processing: true,
    });
</script>
@endsection
