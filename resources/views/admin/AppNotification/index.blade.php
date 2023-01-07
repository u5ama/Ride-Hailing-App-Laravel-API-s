@extends('admin.layouts.master')
@section('css')
    <style>
        .breadcrumb-header {
            display: flex;
            margin-top: 5px !important;
            margin-bottom: 8px !important;
            width: 100%;
        }
    </style>
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between mt-4">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">App Notifications</h4>
                <span class="text-muted mt-1 tx-13 ml-2 mb-0"></span>
            </div>
        </div>
        <div class="d-flex my-xl-auto right-content">
            <div class="pr-1 mb-3 mb-xl-0">
                <a href="{{ route('admin::appNotification.create') }}" class="btn btn-info  mr-2">
                    <i class="mdi mdi-plus-circle"></i> Add New
                </a>
            </div>
        </div>
    </div>
    <!-- breadcrumb -->
    <div class="card">
        <div class="card-body">
        <div class="row">
            <div class="col-md-4 col-xl-4">
                <div class="breadcrumb-header justify-content-center d-flex">
                    <h4 style="padding-top: 8%">Filter</h4>
                </div>
            </div>
            <div class="col-md-8 col-xl-8">
                <div class="row">
                    <div class="col-md-4">
                            <div class="form-group">
                                <label for="filterWithCategory">From Date</label>
                                <input type="date" name="start_date_notification" id="start_date_notification"
                                       class="form-control datepicker-autoclose" placeholder="Please select start date">
                            </div>
                    </div>
                    <div class="col-md-4">
                            <div class="form-group">
                                <label for="filterWithCategory">To Date</label>
                                <input type="date" name="end_date_notification" id="end_date_notification"
                                       class="form-control datepicker-autoclose" placeholder="Please select end date">
                            </div>
                    </div>
                    <div class="col-md-3">
                            <button class="filterNotifications btn btn-outline-primary" type="button"
                                    style="margin-top: 30px;width: 100%;">Filter
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
                                <th>Sender Name</th>
                                <th>Recipient Name</th>
                                <th>Notification Type</th>
                                <th>Notification Status</th>
                                <th>Title Text</th>
                                <th>Body Text</th>
                                <th>Activity</th>
                                <th>Created AT</th>
                                <th>Un Read</th>
                                <th>Is Hidden</th>
                                <th>Action</th>

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
    <script src="{{URL::asset('assets/js/custom/AppNotification.js')}}"></script>
@endsection
