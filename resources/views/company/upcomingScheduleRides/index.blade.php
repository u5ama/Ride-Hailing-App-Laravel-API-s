@extends('admin.layouts.master')
@section('css')
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="card mt-3">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 col-xl-3">
                    <div class="breadcrumb-header justify-content-between" style="text-align: center;display: flex;">
                                <h4 class="content-title mb-0 mt-4">{{ config('languageString.upcoming_schedule_rides') }}</h4>
                                <span class="text-muted mt-1 tx-13 ml-2 mb-0"></span>
                    </div>
                </div>
                <div class="col-md-9 col-xl-9">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="breadcrumb-header">
                                <div class="form-group">
                                    <label for="filterWithCategory">{{ config('languageString.filter_categories') }}</label>
                                    <select class="form-control" id="filterWithCategory" name="category" value="">
                                        <option value="">Categories</option>
                                        @foreach($categories as $category)
                                            <option value="{{$category['name']}}">{{$category['name']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="breadcrumb-header justify-content-between">
                                <div class="form-group">
                                    <label for="filterWithCategory">{{ config('languageString.from_date') }}</label>
                                    <input type="date" name="start_date_booking" id="start_date_booking"
                                           class="form-control datepicker-autoclose" placeholder="Please select start date">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="breadcrumb-header justify-content-between">
                                <div class="form-group">
                                    <label for="filterWithCategory">{{ config('languageString.to_date') }}</label>
                                    <input type="date" name="end_date_booking" id="end_date_booking"
                                           class="form-control datepicker-autoclose" placeholder="Please select end date">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="breadcrumb-header justify-content-between">
                                <button class="filterBooking btn btn-outline-primary" type="button" style="margin-top: 30px; width: 100%">
                                    {{ config('languageString.filter_button') }}
                                </button>
                            </div>
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
                                <th>{{ config('languageString.id_title') }}</th>
                                <th>{{ config('languageString.date_time_title') }}</th>
                                <th>{{ config('languageString.transaction_id_title') }}</th>
                                <th>{{ config('languageString.schedule_for_title') }}</th>
                                <th>{{ config('languageString.ride_status_title') }}</th>
                                <th>{{ config('languageString.payment_mode_title') }}</th>
                                <th>{{ config('languageString.category_title') }}</th>
                                <th>{{ config('languageString.schedule_for_title') }}</th>
                                <th>{{ config('languageString.pick_up_location_title') }}</th>
                                <th>{{ config('languageString.drop_off_location_title') }}</th>
                                <th>{{ config('languageString.distance_time_title') }}</th>
                                <th>{{ config('languageString.passenger_detail_title') }}</th>

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
    <script src="{{URL::asset('assets/js/custom/company/upcomingBookingSchedule.js')}}"></script>
    <script src="{{URL::asset('assets/js/modal.js')}}"></script>
@endsection
