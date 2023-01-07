@extends('admin.layouts.master')
@section('css')
    <link href="{{URL::asset('assets/plugins/select2/css/select2.min.css')}}" rel="stylesheet">
    <link href="{{URL::asset('assets/plugins/amazeui-datetimepicker/css/amazeui.datetimepicker.css')}}"
          rel="stylesheet">
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="card mt-3">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 col-xl-3 justify-content-center" style="text-align: center;display: flex;">
                    <h4 class="content-title mb-0 my-auto">Passenger Payments</h4>
                </div>
                <div class="col-md-9 col-xl-9">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="breadcrumb-header">

                            </div>
                        </div>
                        <div class="col-md-2">
                        </div>
                        <div class="col-md-3">
                            <div class="breadcrumb-header justify-content-between">
                                <div class="form-group">
                                    <label for="filterWithCategory">From Date</label>
                                    <input type="date" name="start_date_payment" id="start_date_payment"
                                           class="form-control datepicker-autoclose" placeholder="Please select start date">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="breadcrumb-header justify-content-between">
                                <div class="form-group">
                                    <label for="filterWithCategory">To Date</label>
                                    <input type="date" name="end_date_payment" id="end_date_payment"
                                           class="form-control datepicker-autoclose" placeholder="Please select end date">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="breadcrumb-header justify-content-between">
                                <button class="filterPayment btn btn-outline-primary" type="button" style="margin-top: 30px;width: 100%">
                                    Filter
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- breadcrumb -->
@endsection
@section('content')
    <!-- row opened -->
    <div class="row row-sm">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive" style="overflow-x:auto;">
                        <table class="table mg-b-0 text-md-nowrap" id="data-table">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Passenger Name</th>
                                <th>Payment ID</th>
                                <th>Result</th>
                                <th>Post Date</th>
                                <th>Transaction ID</th>
                                <th>Reference</th>
                                <th>Tracking ID</th>
                                <th>Auth</th>
                                <th>Created At</th>
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
    <script src="{{URL::asset('assets/js/custom/passengerPayment.js')}}"></script>
    <script src="{{URL::asset('assets/plugins/select2/js/select2.min.js')}}"></script>


@endsection
