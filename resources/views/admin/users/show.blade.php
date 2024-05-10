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
        <div class="col-md-12 mt-4">
            <div class="nav-align-top mb-4">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-prediction" aria-controls="navs-top-prediction" aria-selected="true">Prediction</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-profile" aria-controls="navs-top-profile" aria-selected="false" tabindex="-1">Profile</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-messages" aria-controls="navs-top-messages" aria-selected="false" tabindex="-1">Messages</button>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="navs-top-prediction" role="tabpanel">

                    </div>
                    <div class="tab-pane fade" id="navs-top-profile" role="tabpanel">
                        <p>
                            Donut dragée jelly pie halvah. Danish gingerbread bonbon cookie wafer candy oat cake ice cream. Gummies halvah tootsie roll muffin biscuit icing dessert gingerbread. Pastry ice cream cheesecake fruitcake.
                        </p>
                        <p class="mb-0">
                            Jelly-o jelly beans icing pastry cake cake lemon drops. Muffin muffin pie tiramisu halvah cotton candy liquorice caramels.
                        </p>
                    </div>
                    <div class="tab-pane fade" id="navs-top-messages" role="tabpanel">
                        <p>
                            Oat cake chupa chups dragée donut toffee. Sweet cotton candy jelly beans macaroon gummies cupcake gummi bears cake chocolate.
                        </p>
                        <p class="mb-0">
                            Cake chocolate bar cotton candy apple pie tootsie roll ice cream apple pie brownie cake. Sweet roll icing sesame snaps caramels danish toffee. Brownie biscuit dessert dessert. Pudding jelly jelly-o tart brownie
                            jelly.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection @section('script') @endsection
