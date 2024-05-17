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
                  <span class="avatar-initial rounded bg-label-primary"><i class='bx bxs-user'></i></span>
                </div>
                <h4 class="ms-1 mb-0">{{$usercount}}</h4>
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
                    <span class="avatar-initial rounded bg-label-primary"><i class='bx bxs-calendar' ></i></span>
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
                    <span class="avatar-initial rounded bg-label-primary"><i class='bx bx-coin-stack' ></i></span>
                    </div>
                    <h4 class="ms-1 mb-0">5000</h4>
                </div>
                <p class="mb-1">Amount Earned by platform</p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3 mb-4">
            <div class="card card-border-shadow-primary h-100">
                <div class="card-body">
                <div class="d-flex align-items-center mb-2 pb-1">
                    <div class="avatar me-2">
                    <span class="avatar-initial rounded bg-label-primary"><i class='bx bx-coin-stack' ></i></span>
                    </div>
                    <h4 class="ms-1 mb-0">1500</h4>
                </div>
                <p class="mb-1">Amount distributed as winnings</p>
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
@endsection
