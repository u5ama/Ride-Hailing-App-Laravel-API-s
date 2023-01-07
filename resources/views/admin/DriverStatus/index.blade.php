@extends('admin.layouts.master')
@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet"/>

    <style>
        #map_wrapper {
            height: 600px;
        }

        #map_canvas {
            width: 100%;
            height: 100%;
        }

        .btn-style {
            position: absolute;
            z-index: 1;
            top: 28px;
        }

        .online-card {
            background: #ef5050eb !important;
            color: white;
        }

        .offline-card {
            background: #ef5050eb !important;
            color: white;
        }

        .select2 {
            width: 100% !important;
            height: auto !important;
        }

        .breadcrumb-header {
            display: block !important;
        }

        .btn-light {
            background: white !important;
        }

        .btn-light:focus, .btn-light.focus {
            background: white !important;
        }

        .alert-message {
            color: red;
        }
    </style>
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="card mt-4">
        <div class="card-body">
    <div class="row">
        <div class="col-md-3 col-xl-3">
            <div class="breadcrumb-header justify-content-between">
                <div class="my-auto">
                    <div class="d-flex justify-content-center">
                        <h4 class="content-title mb-0 mt-4">Driver Status</h4>
                        <span class="text-muted mt-1 tx-13 ml-2 mb-0"></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-9 col-xl-9">
            <form id="filterForm">
                <div class="row text-right justify-content-end">
                    <div class="col-md-2">
                        <div class="breadcrumb-header">
                            <div class="form-group">
                                <label for="filterWithCountry">Filter Country</label>
                                <select class="form-control select2" id="filterWithCountry" name="country"
                                        onchange="getDrivers(); getCompanies(this.value)">
                                    <option selected value="52">Country</option>
                                    @foreach($countries as $country)
                                        <option value="{{$country->id}}">{{$country->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="breadcrumb-header">
                            <div class="form-group" style="text-align: center;">
                                <label for="filterByCompany">Filter Company</label>
                                <select class="form-control select2" name="filterByCompany" id="filterByCompany"
                                        onchange="getDriversNumbers(this.value); getDriversVehicles(this.value)">
                                </select>
                                <div class="alert-message" id="companyId"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="breadcrumb-header">
                            <div class="form-group" style="text-align: center;">
                                <label for="filterByNumber">Search Driver Mobile#</label>
                                <select class="form-control select2" name="filterByDriverNumber[]" id="filterByNumber"
                                        multiple="multiple">
                                    <option value="">Select Mobile Number</option>
                                    @foreach($drivers as $driver)
                                        <option value="{{$driver->du_mobile_number}}">{{$driver->du_mobile_number}}</option>
                                    @endforeach
                                </select>
                                <div class="alert-message" id="driverNumberError"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="breadcrumb-header">
                            <div class="form-group" style="text-align: center;">
                                <label for="filterByVehicle">Search Driver Vehicle#</label>
                                <select class="form-control select2" name="filterByDriverVehicle[]" id="filterByVehicle"
                                        multiple="multiple">
                                    <option value="">Select Vehicle Number</option>
                                    @foreach($vehicles as $vehicle)
                                        @if(!empty($vehicle->driverProf->car_registration))
                                        <option value="{{$vehicle->driverProf->car_registration}}">{{$vehicle->driverProf->car_registration}}</option>
                                        @endif
                                    @endforeach
                                </select>
                                <div class="alert-message" id="driverVehicleError"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 text-center">
                        <div class="breadcrumb-header">
                            <button class="filterDate btn btn-outline-primary" type="submit"
                                    style="margin-top: 30px;width: 90%;">Filter
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
        </div>
    </div>
    <div class="row text-center">
        <div class="col-md-4">
            <div class="online-card card" style="background: #55b755 !important;">
                <div class="card-body">
                    <h5>Online</h5>
                    <h6 id="onlineDrivers">0</h6>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="offline-card card" style="background: #ef5050eb !important;">
                <div class="card-body">
                    <h5>Offline</h5>
                    <h6 id="offlineDrivers">0</h6>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="offline-card card" style="background: #e2d32b !important;">
                <div class="card-body">
                    <h5>Busy</h5>
                    <h6 id="busyDrivers">0</h6>
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
                    <div id="map_wrapper" class="text-center">
                        <button class="btn btn-outline-info btn-style" onclick="getDrivers()">Refresh</button>
                        <div id="map_canvas" class="mapping"></div>
                    </div>
                </div>
            </div>
        </div>
        <!--/div-->
    </div>
    <!-- /row -->
@endsection
@section('js')

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function () {
            $('.select2').select2();
            $("#filterByNumber").select2({});
            $("#filterByVehicle").select2({});
        });
    </script>
    <script src="{{URL::asset('assets/js/custom/DriverStatus.js')}}"></script>
    <script src="{{URL::asset('assets/js/modal.js')}}"></script>
@endsection
