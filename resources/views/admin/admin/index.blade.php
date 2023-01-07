@extends('admin.layouts.master')
@section('css')
<!-- Internal Morris Css-->
<link href="{{URL::asset('assets/plugins/morris.js/morris.css')}}" rel="stylesheet">
<style>
    .highcharts-figure, .highcharts-data-table table {
        min-width: 300px;
        max-width: 660px;
        margin: 1em auto;
    }
    .highcharts-data-table table {
        font-family: Verdana, sans-serif;
        border-collapse: collapse;
        border: 1px solid #EBEBEB;
        margin: 10px auto;
        text-align: center;
        width: 100%;
        max-width: 500px;
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

    g.highcharts-exporting-group {
        display: none;
    }
    text.highcharts-credits {
        display: none;
    }
</style>
@endsection
@section('page-header')
<div class="breadcrumb-header justify-content-between">
    <div class="left-content">
        <div>
          <h2 class="main-content-title tx-24 mg-b-1 mg-b-lg-1">Welcome back!</h2>
          <p class="mg-b-0">Whipp Monitoring Dashboard.</p>
        </div>
    </div>
    <div class="main-dashboard-header-right">
        <div>
            <label class="tx-13">Gross Value</label>
            <h5 id="grossValue">{{$grossValue}}</h5>
        </div><div>
            <label class="tx-13">Bank Comm</label>
            <h5 id="bankCom">{{$bankCom}}</h5>
        </div>
        <div>
            <label class="tx-13">Net Value</label>
            <h5 id="netInvoice">{{$netInvoice}}</h5>
        </div>
        <div>
            <label class="tx-13">Whipp</label>
            <h5 id="whipp">{{$whipp}}</h5>
        </div>
        <div>
            <label class="tx-13">Driver</label>
            <h5 id="driver">{{$driver}}</h5>
        </div>
    </div>
</div>
<hr style="background-color: white;height: 1px;border-top: 1px solid white">

@endsection
@section('content')
            <br>
            <div class="row row-sm">
                    <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
                         <div class="form-group">
                                <label for="filterWithCategory">From Date</label>
                                <input type="date" name="start_date" id="start_date" class="form-control datepicker-autoclose" min='2019-01-01' placeholder="Please select start date">
                            </div>
                    </div>
                    <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
                         <div class="form-group">
                                <label for="filterWithCategory">To Date</label>
                                <input type="date" name="end_date" id="end_date" class="form-control datepicker-autoclose" min='2019-01-01' placeholder="Please select start date">
                            </div>
                    </div>

                    <div class="col-xl-6 col-lg-6 col-md-6 col-xm-12">
                        <div class="row">
                            <div class="col-xl-3 col-lg-3 col-md-6 col-xm-12">
                                <div class="form-group">
                                    <button class="filterDate btn btn-outline-primary" type="button" style="margin-top: 30px;width: 100%;">Filter</button>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-3 col-md-6 col-xm-12">
                                <div class="form-group">
                                    <button class="btn btn-outline-primary" type="button" style="margin-top: 30px;width: 100%;" onclick="getDataFilter('today')">Today</button>
                                </div>
                            </div>

                            <div class="col-xl-3 col-lg-3 col-md-6 col-xm-12">
                                <div class="form-group">
                                    <button class=" btn btn-outline-primary" type="button" style="margin-top: 30px;width: 100%;" onclick="getDataFilter('month')">This Month</button>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-3 col-md-6 col-xm-12">
                                <div class="form-group">
                                    <button class=" btn btn-outline-primary" type="button" style="margin-top: 30px;width: 100%;" onclick="getDataFilter('year')">This Year</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

               <div class="row row-sm">
                    <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
                        <div class="card overflow-hidden sales-card bg-primary-gradient">
                            <div class="pl-3 pt-3 pr-3 pb-2 pt-0">
                                <div class="">
                                    <h6 class="mb-3 tx-12 text-white">TOTAL USERS</h6>
                                </div>
                                <div class="pb-0 mt-0">
                                    <div class="d-flex">
                                        <div class="">
                                            <h4 class="tx-20 font-weight-bold mb-1 text-white" id="userCount">{{$userCount}}</h4>

                                        </div>
                                        <span class="float-right my-auto ml-auto">

                                        </span>
                                    </div>
                                </div>
                            </div>
                            <span id="compositeline" class="pt-1">5,9,5,6,4,12,18,14,10,15,12,5,8,5,12,5,12,10,16,12</span>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
                        <div class="card overflow-hidden sales-card bg-danger-gradient">
                            <div class="pl-3 pt-3 pr-3 pb-2 pt-0">
                                <div class="">
                                    <h6 class="mb-3 tx-12 text-white">TOTAL DRIVERS</h6>
                                </div>
                                <div class="pb-0 mt-0">
                                    <div class="d-flex">
                                        <div class="">
                                            <h4 class="tx-20 font-weight-bold mb-1 text-white" id="driverCount">{{$driverCount}}</h4>

                                        </div>
                                        <span class="float-right my-auto ml-auto">

                                        </span>
                                    </div>
                                </div>
                            </div>
                            <span id="compositeline2" class="pt-1">3,2,4,6,12,14,8,7,14,16,12,7,8,4,3,2,2,5,6,7</span>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
                        <div class="card overflow-hidden sales-card bg-success-gradient">
                            <div class="pl-3 pt-3 pr-3 pb-2 pt-0">
                                <div class="">
                                    <h6 class="mb-3 tx-12 text-white">TOTAL COMPANIES</h6>
                                </div>
                                <div class="pb-0 mt-0">
                                    <div class="d-flex">
                                        <div class="">
                                            <h4 class="tx-20 font-weight-bold mb-1 text-white" id="companyCount">{{$companyCount}}</h4>

                                        </div>
                                        <span class="float-right my-auto ml-auto">


                                        </span>
                                    </div>
                                </div>
                            </div>
                            <span id="compositeline3" class="pt-1">5,10,5,20,22,12,15,18,20,15,8,12,22,5,10,12,22,15,16,10</span>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
                        <div class="card overflow-hidden sales-card bg-warning-gradient">
                            <div class="pl-3 pt-3 pr-3 pb-2 pt-0">
                                <div class="">
                                    <h6 class="mb-3 tx-12 text-white">TOTAL RIDES</h6>
                                </div>
                                <div class="pb-0 mt-0">
                                    <div class="d-flex">
                                        <div class="">
                                            <h4 class="tx-20 font-weight-bold mb-1 text-white" id="ridesCount">{{$ridesCount}}</h4>

                                        </div>
                                        <span class="float-right my-auto ml-auto">

                                        </span>
                                    </div>
                                </div>
                            </div>
                            <span id="compositeline4" class="pt-1">5,9,5,6,4,12,18,14,10,15,12,5,8,5,12,5,12,10,16,12</span>
                        </div>
                    </div>
                </div>
                <hr style="background-color: white;height: 1px;border-top: 1px solid white">
            <!-- breadcrumb -->

            <div class="breadcrumb-header justify-content-between">
                <div class="my-auto">
                    <div class="d-flex">
                        <h4 class="content-title mb-0 my-auto">What's Happening Today</h4>
                    </div>
                </div>
            </div>
            <!-- breadcrumb -->
            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="main-content-label mg-b-3">
                                Ride Schedule Statistics
                            </div>
                            <figure class="highcharts-figure">
                                <div id="container" style="height: 230px; margin-left: -20px"></div>
                            </figure>
                        </div>
                    </div>
                </div><!-- col-6 -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="main-content-label mg-b-3">
                                Cancel By Passengers, Ignored By Drivers
                            </div>
                            <figure class="highcharts-figure">
                                <div id="container1" style="height: 230px;"></div>
                            </figure>
                        </div>
                    </div>
                </div><!-- col-6 -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="main-content-label mg-b-3">
                                Earning by Drivers, Company
                            </div>
                            <figure class="highcharts-figure">
                                <div id="container2" style="height: 230px;"></div>
                            </figure>
                        </div>
                    </div>
                </div><!-- col-6 -->
            </div>

        <input type="hidden"  id="waitingCount" value="{{$waitingCount}}">
        <input type="hidden"  id="acceptedCount" value="{{$acceptedCount}}">
        <input type="hidden"  id="completedCount" value="{{$completedCount}}">
        <input type="hidden"  id="drivingCount" value="{{$drivingCount}}">
        <input type="hidden"  id="requestedCount" value="{{$requestedCount}}">
        <input type="hidden"  id="rejectedCount" value="{{$rejectedCount}}">
        <input type="hidden"  id="ridesCancelByPAssengerCount" value="{{$ridesCancelByPAssengerCount}}">
        <input type="hidden"  id="rideIgnoredCount" value="{{$rideIgnoredCount}}">

        <input type="hidden"  id="driversEarningCount" value="{{$driversEarningCount}}">
        <input type="hidden"  id="companyEarningCount" value="{{$companyEarningCount}}">
        <input type="hidden"  id="whippEarningCount" value="{{$whippEarningCount}}">

        <!-- main-content closed -->
@endsection
@section('js')
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/series-label.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>
<script>
    const waitingCount = $("#waitingCount").val();
    const acceptedCount = $("#acceptedCount").val();
    const completedCount = $("#completedCount").val();
    const drivingCount = $("#drivingCount").val();
    const requestedCount = $("#requestedCount").val();
    const rejectedCount = $("#rejectedCount").val();
    const ridesCancelByPAssengerCount = $("#ridesCancelByPAssengerCount").val();
    const rideIgnoredCount = $("#rideIgnoredCount").val();

    const driversEarningCount = $("#driversEarningCount").val();
    const companyEarningCount = $("#companyEarningCount").val();
    const whippEarningCount = $("#whippEarningCount").val();

    // Radialize the colors
    Highcharts.setOptions({
        colors: Highcharts.map(Highcharts.getOptions().colors, function (color) {
            return {
                radialGradient: {
                    cx: 0.5,
                    cy: 0.3,
                    r: 0.7
                },
                stops: [
                    [0, color],
                    [1, Highcharts.color(color).brighten(-0.3).get('rgb')] // darken
                ]
            };
        })
    });

    // Build the chart
    Highcharts.chart('container', {
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie'
        },
        title: {
            text: ''
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}</b>'
        },
        accessibility: {
            point: {
                valueSuffix: ''
            }
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '<b>{point.name}</b>: {point.percentage:.1f} ',
                    connectorColor: 'silver'
                }
            }
        },
        series: [{
            name: 'Share',
            data: [
                { name: 'Waiting', y: parseInt(waitingCount) },
                { name: 'Accepted', y: parseInt(acceptedCount) },
                { name: 'Completed', y: parseInt(completedCount) },
                { name: 'Driving', y: parseInt(drivingCount) },
                { name: 'Requested', y: parseInt(requestedCount) },
                { name: 'Rejected', y: parseInt(rejectedCount) }
            ]
        }]
    });

    // Build the chart
    Highcharts.chart('container1', {
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie'
        },
        title: {
            text: ''
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}</b>'
        },
        accessibility: {
            point: {
                valueSuffix: ''
            }
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '<b>{point.name}</b>: {point.percentage:.1f} ',
                    connectorColor: 'silver'
                }
            }
        },
        series: [{
            name: 'Share',
            data: [
                { name: 'Cancel By Passenger', y: parseInt(ridesCancelByPAssengerCount) },
                { name: 'Rides Ignored', y: parseInt(rideIgnoredCount) },
            ]
        }]
    });

    // Build the chart
    Highcharts.chart('container2', {
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie'
        },
        title: {
            text: ''
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}</b>'
        },
        accessibility: {
            point: {
                valueSuffix: ''
            }
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '<b>{point.name}</b>: {point.percentage:.1f} ',
                    connectorColor: 'silver'
                }
            }
        },
        series: [{
            name: 'Share',
            data: [
                { name: 'Driver Earning', y: parseInt(driversEarningCount) },
                { name: 'Company Earning', y: parseInt(rideIgnoredCount) },
                { name: 'Whipp Earning', y: parseInt(whippEarningCount) },
            ]
        }]
    });

    $('.filterDate').click(function(){
        var _token=$('input[name=_token]').val();
        const start_date = $('#start_date').val();
        const end_date = $('#end_date').val();
        $.ajax({
            type: "POST",
            url:  APP_URL + '/getDataFilterDashboard',
            data:{_token:_token,from_date:start_date, to_date:end_date},
            success: function(data){
               $("#userCount").text(data.userCount);
               $("#driverCount").text(data.driverCount);
               $("#companyCount").text(data.companyCount);
               $("#ridesCount").text(data.ridesCount);

                $("#bankCom").text(data.bankCom);
                $("#netInvoice").text(data.netInvoice);
                $("#grossValue").text(data.grossValue);
                $("#driver").text(data.driver);
                $("#whipp").text(data.whipp);
            }
        });
    });

    function getDataFilter(parm){
         var _token=$('input[name=_token]').val();
         $('#start_date').val('');
         $('#end_date').val('');
        if(parm == 'today'){
         today  = 'today';
         this_month  = '';
         this_year = '';
        }
        if(parm == 'month'){
          this_month  = 'month';
          today  = '';
          this_year = '';
        }
        if(parm == 'year'){
           this_year = 'year';
           today  = '';
           this_month  = '';
        }

        $.ajax({
            type: "POST",
            url:  APP_URL + '/getDataFilterDashboard',
            data:{_token:_token,today:today, this_month:this_month,this_year:this_year},
            success: function(data){
               $("#userCount").text(data.userCount);
               $("#driverCount").text(data.driverCount);
               $("#companyCount").text(data.companyCount);
               $("#ridesCount").text(data.ridesCount);

                $("#bankCom").text(data.bankCom);
                $("#netInvoice").text(data.netInvoice);
                $("#grossValue").text(data.grossValue);
                $("#driver").text(data.driver);
                $("#whipp").text(data.whipp);
            }
        });
    }

    // $(document).on('click', '#userTimeZone', function () {
    //     loaderView();
    //     // var test = $("#make_id_sel option:selected").val();
    //     // console.log(test);
    //     $.ajax({
    //         type: 'GET',
    //         url: APP_URL + '/getTimeZone',
    //         data: {user_type: $("#user_type").val()},
    //         dataType: 'html',
    //         success: function (data) {
    //             $("#userTimeZone").html(data);
    //             loaderHide();
    //         }, error: function (data) {
    //             console.log('Error:', data)
    //         }
    //     })
    // });



</script>
<!--Internal  Datepicker js -->
<script src="{{URL::asset('assets/plugins/jquery-ui/ui/widgets/datepicker.js')}}"></script>
<!-- Internal Select2 js-->
<script src="{{URL::asset('assets/plugins/select2/js/select2.min.js')}}"></script>
<!--Internal  Morris js -->
<script src="{{URL::asset('assets/plugins/raphael/raphael.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/morris.js/morris.min.js')}}"></script>

<!--Internal  Chart.bundle js -->
<script src="{{URL::asset('assets/plugins/chart.js/Chart.bundle.min.js')}}"></script>
<!-- Moment js -->

<!--Internal  Flot js-->
<!--Internal  index js -->
<script src="{{URL::asset('assets/js/index.js')}}"></script>
<script src="{{URL::asset('assets/js/jquery.vmap.sampledata.js')}}"></script>
@endsection
