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
        <div class="col-sm-6 col-lg-2 mb-4 mt-4">
            <div class="card card-border-shadow-primary h-100">
              <div class="card-body">
                <div class="d-flex align-items-center mb-2 pb-1">
                  <h5 class="ms-1 mb-0">Current Balance 2500</h5>
                </div>
              </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-2 mb-4 mt-4">
            <div class="card card-border-shadow-primary h-100">
              <div class="card-body">
                <div class="d-flex align-items-center mb-2 pb-1">
                  <h5 class="ms-1 mb-0">Total Winning Amount 3000</h5>
                </div>
              </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-2 mb-4 mt-4">
            <div class="card card-border-shadow-primary h-100">
              <div class="card-body">
                <div class="d-flex align-items-center mb-2 pb-1">
                  <h5 class="">Total Winning Amount <br> <span class="text-center ml-3 mt-2" style="margin-left: 34%;"> 3000 </span> </h5>
                </div>
              </div>
            </div>
        </div>

        <div class="col-md-12 mt-4">
            <div class="nav-align-top mb-4">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-prediction" aria-controls="navs-top-prediction" aria-selected="true">Prediction Matches</button>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="navs-top-prediction" role="tabpanel">
                        <div class="row">
                            @foreach ($datamatches as $key => $match)
                                @php
                                    $returncompetion = Helper::CompetionDetail($match['competitionMatch']->competiton_id);
                                @endphp
                                <div class="col-md-2">
                                    <div class="card text-center">
                                        <div class="card-body">
                                            {{$match['competitionMatch']->match}}
                                            <p class="mt-2">{{$match['competitionMatch']->match_start_date}}/{{$match['competitionMatch']->match_start_time}}</p>
                                            <p class="card-text"><img src="{{$match['competitionMatch']->teama_img}}" width="10%" alt=""> V/S <img src="{{$match['competitionMatch']->teamb_img}}" width="10%" alt=""></p>
                                            <a href="{{ route("admin.users.match.prediction", ['user' => $user->id, 'matchId' => $match['competitionMatch']->match_id]) }}" class="btn btn-sm btn-primary">Match Detail</a>
                                        </div>
                                      </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection @section('script') @endsection
