@extends('admin.layouts.app') @section('content')
<style>
    .thumb-image {
        height: 50px;
        width: 50px;
        border: 1px solid lightgray;
        padding: 1px;
    }
    .header-title {
        text-transform: capitalize;
        font-size: ;
    }
    .user-amount-card{
        width: 100%;
    }
</style>



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
                                <p class="text-muted"><strong>Email Address :</strong> <span class="ml-2">{{$user->email}}</span></p>
                                <p class="text-muted"><strong>Phone Number :</strong> <span class="ml-2">{{$user->phone}}</span></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- User data --}}
        <div class="col-md-3 mb-3 d-flex align-items-stretch mt-4">
            <div class="card user-amount-card">
              <div class="card-body d-flex flex-column">
                <h5 class="card-title">Wallet Amount</h5>
                <p class="card-text mb-4 text-center fw-bold">{{$user->wallet}}</p>
              </div>
            </div>
        </div>
        <div class="col-md-3 mb-3 d-flex align-items-stretch mt-4">
            <div class="card user-amount-card">
              <div class="card-body d-flex flex-column">
                <h5 class="card-title">Deposit Amount</h5>
                <p class="card-text mb-4 text-center fw-bold">{{$transactionTypes['add-wallet']}}</p>
              </div>
            </div>
        </div>
        <div class="col-md-3 mb-3 d-flex align-items-stretch mt-4">
            <div class="card user-amount-card">
              <div class="card-body d-flex flex-column">
                <h5 class="card-title">Fee Amount</h5>
                <p class="card-text mb-4 text-center fw-bold">{{$transactionTypes['pay']}}</p>
              </div>
            </div>
        </div>
        <div class="col-md-3 mb-3 d-flex align-items-stretch mt-4">
            <div class="card user-amount-card">
              <div class="card-body d-flex flex-column">
                <h5 class="card-title">Winning Amount</h5>
                <p class="card-text mb-4 text-center fw-bold">{{$transactionTypes['winning-amount']}}</p>
              </div>
            </div>
        </div>
        <div class="col-md-3 mb-3 d-flex align-items-stretch mt-4">
            <div class="card user-amount-card">
              <div class="card-body d-flex flex-column">
                <h5 class="card-title">Withdraw Amount</h5>
                <p class="card-text mb-4 text-center fw-bold">{{$transactionTypes['withdrawal-amount']}}</p>
              </div>
            </div>
        </div>

        <div class="col-md-3 mb-3 d-flex align-items-stretch mt-4">
            <div class="card user-amount-card">
              <div class="card-body d-flex flex-column">
                <h5 class="card-title">Leader board position by amount</h5>
                <p class="card-text mb-4 text-center fw-bold">1</p>
              </div>
            </div>
        </div>

        <div class="col-md-3 mb-3 d-flex align-items-stretch mt-4">
            <div class="card user-amount-card">
              <div class="card-body d-flex flex-column">
                <h5 class="card-title">Leader board position by Winning</h5>
                <p class="card-text mb-4 text-center fw-bold">1</p>
              </div>
            </div>
        </div>

        <div class="col-md-12 mt-4">
            <div class="nav-align-top mb-4">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-prediction" aria-controls="navs-top-prediction" aria-selected="true">Prediction Matches</button>
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
                                                    <th>Date/Time</th>
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
                                                        <td><img src="{{$match['competitionMatch']->teama_img}}" width="10%" alt=""> V/S <img src="{{$match['competitionMatch']->teamb_img}}" width="10%" alt=""></td>
                                                        <td>
                                                            <a href="{{ route("admin.users.match.prediction", ['user' => $user->id, 'matchId' => $match['competitionMatch']->match_id]) }}" class="btn btn-sm btn-primary">Match Detail</a>
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
