@extends('admin.layouts.master')
@section('css')
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="card mt-3">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 col-xl-3" style="text-align: center;display: flex;justify-content: center;">
                    <h4 class="content-title mb-0 mt-4">Inprogress Rides</h4>
                </div>
                <div class="col-md-9 col-xl-9">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="breadcrumb-header">
                            </div>
                        </div>
                        <div class="col-md-2">
                                <div class="form-group">
                                    <label for="filterWithCategory">Filter Categories</label>
                                    <select class="form-control" id="filterWithCategory" name="category" value="">
                                        <option value="">Categories</option>
                                        @foreach($categories as $category)
                                            <option value="{{$category['name']}}">{{$category['name']}}</option>
                                        @endforeach
                                    </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                                <div class="form-group">
                                    <label for="filterWithCategory">From Date</label>
                                    <input type="date" name="start_date_booking" id="start_date_booking"
                                           class="form-control datepicker-autoclose" placeholder="Please select start date">
                                </div>
                        </div>
                        <div class="col-md-3">
                                <div class="form-group">
                                    <label for="filterWithCategory">To Date</label>
                                    <input type="date" name="end_date_booking" id="end_date_booking"
                                           class="form-control datepicker-autoclose" placeholder="Please select end date">
                                </div>
                        </div>
                        <div class="col-md-2">
                                <button class="filterBooking btn btn-outline-primary" type="button" style="margin-top: 30px;width: 100%">
                                    Filter
                                </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('content')
    <!-- row opened -->
    <div class="row row-sm">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table mg-b-0 text-md-nowrap" id="data-table">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>DateTime</th>
                                <th>TransactionID</th>
                                <th>Ride Status</th>
                                <th>Payment Mode</th>
                                <th>Category</th>
                                <th>Schedule Ride on</th>
                                <th>Schedule for</th>
                                <th>Ride Tracking URL</th>
                                <th>Pickup Location</th>
                                <th>Dropoff Location</th>
                                <th>Distance & Time</th>
                                <th>Passenger Detail</th>
                                <th>Driver Detail</th>

                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!--/div-->
    </div>
    <!-- /row -->
    </div>
    <!-- Container closed -->
    </div>
    <!-- main-content closed -->

@endsection
@section('js')
    <script src="{{URL::asset('assets/js/custom/inProgressScheduleRides.js')}}"></script>
    <script src="{{URL::asset('assets/js/modal.js')}}"></script>

@endsection
