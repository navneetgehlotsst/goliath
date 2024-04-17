@extends('admin.layouts.app')
@section('style')
@endsection

@section('content')
    <!-- Content -->

<div class="container-fluid flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-lg-12 mb-4 order-0">
            <div class="card">
                <div class="d-flex align-items-end row">
                    <div class="col-sm-7">
                        <div class="card-body">
                            <h5 class="card-title text-primary">Congratulations {{Auth::user()->full_name}}! 🎉</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card">
               <h4 class="card-title mb-1 text-nowrap pt-3 ps-3">Income</h4>
               <div class="d-flex row">
                  <div class="col-3">
                        <div class="card-body">
                           <h5 class="card-title text-primary mb-1">$ 2000</h5>
                           <p class="d-block mb-4 pb-1 text-muted">Total Amount Added</p>
                        </div>
                  </div>
                  <div class="col-3">
                        <div class="card-body">
                           <h5 class="card-title text-primary mb-1">$ 500</h5>
                           <p class="d-block mb-4 pb-1 text-muted">Prize</p>
                        </div>
                  </div>
                  <div class="col-3">
                     <div class="card-body">
                        <h5 class="card-title mb-1">$ 2500</h5>
                        <p class="d-block mb-4 pb-1 text-muted">Total Revenue</p>
                     </div>
                  </div>
               </div>
            </div>
            {{-- <div class="card mt-2">
               <h4 class="card-title mb-1 text-nowrap pt-3 ps-3">Users</h4>
               <div id="chart"></div>
            </div>
            <div class="card mt-2">
               <h4 class="card-title mb-1 text-nowrap pt-3 ps-3">Video</h4>
               <div id="chartVideo"></div>
            </div>
            <div class="card mt-2">
               <h4 class="card-title mb-1 text-nowrap pt-3 ps-3">Video View</h4>
               <div id="chartviewVideo"></div>
            </div> --}}
      </div>
      <div class="col-lg-4">
         <div class="card">
            <h4 class="card-title mb-1 text-nowrap pt-3 ps-3">Users</h4>
            <div class="d-flex row">
              <div class="col-4">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2 pb-1">
                        <div class="avatar me-2">
                            <span class="avatar-initial rounded bg-label-primary">
                            <i class='bx bx-user'></i>
                            </span>
                        </div>
                        <h4 class="ms-1 mb-0">5</h4>
                    </div>
                    <p class="mb-1">Total Users</p>
                </div>
              </div>
              <div class="col-4">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2 pb-1">
                        <div class="avatar me-2">
                            <span class="avatar-initial rounded bg-label-primary">
                            <i class='bx bx-user'></i>
                            </span>
                        </div>
                        <h4 class="ms-1 mb-0">4</h4>
                    </div>
                    <p class="mb-1">Active Users</p>
                </div>
              </div>
              <div class="col-4">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2 pb-1">
                        <div class="avatar me-2">
                            <span class="avatar-initial rounded bg-label-primary">
                            <i class='bx bx-user'></i>
                            </span>
                        </div>
                        <h4 class="ms-1 mb-0">1</h4>
                    </div>
                    <p class="mb-1">Inactive Users</p>
                </div>
              </div>
            </div>
         </div>
         <div class="card mt-2">
            <h4 class="card-title mb-1 text-nowrap pt-3 ps-3">Total winning</h4>
            <div class="d-flex row">
              <div class="col-4">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2 pb-1">
                        <div class="avatar me-2">
                            <span class="avatar-initial rounded bg-label-primary">
                            <i class='bx bx-user'></i>
                            </span>
                        </div>
                        <h4 class="ms-1 mb-0">5</h4>
                    </div>
                    <p class="mb-1">Total Year</p>
                </div>
              </div>
              <div class="col-4">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2 pb-1">
                        <div class="avatar me-2">
                            <span class="avatar-initial rounded bg-label-primary">
                            <i class='bx bx-user'></i>
                            </span>
                        </div>
                        <h4 class="ms-1 mb-0">4</h4>
                    </div>
                    <p class="mb-1">Month</p>
                </div>
              </div>
              <div class="col-4">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2 pb-1">
                        <div class="avatar me-2">
                            <span class="avatar-initial rounded bg-label-primary">
                            <i class='bx bx-user'></i>
                            </span>
                        </div>
                        <h4 class="ms-1 mb-0">1</h4>
                    </div>
                    <p class="mb-1">Last Match</p>
                </div>
              </div>
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
