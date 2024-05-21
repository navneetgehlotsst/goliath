@extends('admin.layouts.app')
@section('style')
    <style>

    </style>
@endsection
@section('content')



<div class="container-fluid flex-grow-1 container-p-y">
    <h5 class="py-2 mb-2">
        <span class="text-primary fw-light">User Match Predictions</span>
    </h5>
    <div class="row mb-5">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row gy-3">
                        <div class="col-xl-12">
                            <div class="table-responsive text-nowrap">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>User</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-border-bottom-0">
                                        @foreach ($predictionUsers as $predictionUser )
                                            <tr>
                                                <td>
                                                    {{$predictionUser->full_name}}
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
</div>

@endsection
@section('script')

@endsection
