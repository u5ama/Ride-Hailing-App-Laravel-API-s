@extends('admin.layouts.master')
@section('css')
    <style>
        .highcharts-figure, .highcharts-data-table table {
            margin: 0em 0em;
        }

        #container {
            height: 230px;
        }

        .highcharts-data-table table {
            font-family: Verdana, sans-serif;
            border-collapse: collapse;
            border: 1px solid #EBEBEB;
            margin: 10px auto;
            text-align: center;
            width: 100%;
            max-width: 100%;
        }

        .highcharts-data-table caption {
            padding: 1em 0;
            font-size: 1.2em;
            color: #555;
        }

        .highcharts-data-table th {
            font-weight: 600;
            padding: 0.5em;
        }

        .highcharts-data-table td, .highcharts-data-table th, .highcharts-data-table caption {
            padding: 0.5em;
        }

        .highcharts-data-table thead tr, .highcharts-data-table tr:nth-child(even) {
            background: #f8f8f8;
        }

        .highcharts-data-table tr:hover {
            background: #f1f7ff;
        }

        .highcharts-credits {
            display: none;
        }

        path.highcharts-button-symbol {
            display: none;
        }

        .btn-style {
            text-align: end;
            margin-top: 15px;
        }
    </style>
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="row">
        <div class="col-md-6">
            <div class="breadcrumb-header justify-content-between">
                <div class="my-auto">
                    <div class="d-flex">
                        <h4 class="content-title mb-0 my-auto">Ride Cancel By Driver</h4>
                        <span class="text-muted mt-1 tx-13 ml-2 mb-0"></span>
                    </div>
                </div>

            </div>
        </div>
        <div class="col-md-6 btn-style">
            <div class="dropdown show">
                <a class="btn btn-outline-info dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Graph Filter
                </a>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                    <a class="dropdown-item" data-type="weekly" id="recGraph" href="javascript:void(0)">Weekly</a>
                    <a class="dropdown-item" data-type="monthly" id="recGraph" href="javascript:void(0)">Monthly</a>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <figure class="highcharts-figure">
                <div id="container"></div>
            </figure>
        </div>
    </div>
    <div class="card mt-3">
        <div class="card-body">
            <div class="row">
                <div class="col-md-1">
                </div>
                <div class="col-md-2 col-xl-2"  style="text-align: center;display: flex;justify-content: center;">
                   <h4 class="content-title mb-0 mt-4">Filter</h4>
                </div>
                <div class="col-md-9 col-xl-9">
                    <div class="row">
                        <div class="col-md-1">
                            <div class="breadcrumb-header">

                            </div>
                        </div>
                        <div class="col-md-2">
                        </div>

                        <div class="col-md-3">
                                <div class="form-group">
                                    <label for="filterWithCategory">From Date</label>
                                    <input type="date" name="start_date_cancel_driv" id="start_date_cancel_driv"
                                           class="form-control datepicker-autoclose" placeholder="Please select start date">
                                </div>
                        </div>
                        <div class="col-md-3">
                                <div class="form-group">
                                    <label for="filterWithCategory">To Date</label>
                                    <input type="date" name="end_date_cancel_driv" id="end_date_cancel_driv"
                                           class="form-control datepicker-autoclose" placeholder="Please select end date">
                                </div>
                        </div>
                        <div class="col-md-2">
                                <button class="filterDriverCancel btn btn-outline-primary" type="button"
                                        style="margin-top: 30px;width: 100%">Filter
                                </button>
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
                    <div class="table-responsive">
                        <table class="table mg-b-0 text-md-nowrap" id="data-table">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Passenger</th>
                                <th>Driver</th>
                                <th>Reason</th>
                                <th>Comments</th>
                                <th>Cancel At</th>
                                <th>Ride Detail</th>

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
    <div class="modal" id="modaldemo3">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content modal-content-demo">
                <div class="modal-header">
                    <h6 class="modal-title">View Ride Detail</h6>
                    <button aria-label="Close" class="close" data-dismiss="modal" type="button"><span
                            aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div id="viewRideModelId"></div>
                </div>
                <div class="modal-footer">

                    <button class="btn ripple btn-secondary" data-dismiss="modal" type="button">Close</button>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('js')
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/series-label.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>
    <script src="{{URL::asset('assets/js/custom/rideCancelByDriver.js')}}"></script>
    <script src="{{URL::asset('assets/js/modal.js')}}"></script>
@endsection
