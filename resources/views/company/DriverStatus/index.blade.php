@extends('admin.layouts.master')
@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet"/>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/css/bootstrap-select.min.css"
          integrity="sha512-ARJR74swou2y0Q2V9k0GbzQ/5vJ2RBSoCWokg4zkfM29Fb3vZEQyv0iWBMW/yvKgyHSR/7D64pFMmU8nYmbRkg=="
          crossorigin="anonymous"/>
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
    <div class="card mt-3">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 col-xl-3">
                    <div class="breadcrumb-header justify-content-between" style="text-align: center;display: flex;">
                                <h4 class="content-title mb-0 mt-5">{{ config('languageString.drivers_status_title') }}</h4>
                                <span class="text-muted mt-1 tx-13 ml-2 mb-0 mt-2"></span>
                    </div>
                </div>
                <div class="col-md-9 col-xl-9">
                    <form id="filterForm">
                        <div class="row text-right justify-content-end">
                            <div class="col-md-3">
                                <div class="breadcrumb-header">
                                    <div class="form-group" style="text-align: center;">
                                        <label for="filterByNumber">{{ config('languageString.search_driver_mobile') }}</label>
                                        <select class="form-control" name="filterByDriverNumber[]"
                                                id="filterByNumber" multiple="multiple" data-live-search="true" data-size="5">
        {{--                                    {{ \App\Utility\Utility::create_option("drivers","du_mobile_number","du_mobile_number",$company_id) }}--}}
                                            @if(isset($drivers))
                                                @foreach($drivers as $driver)
                                                    <option value="{{$driver->du_mobile_number}}">{{$driver->du_mobile_number}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <div class="alert-message" id="driverNumberError"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="breadcrumb-header">
                                    <div class="form-group" style="text-align: center;">
                                        <label for="filterByVehicle">{{ config('languageString.search_driver_vehicle') }}</label>
                                        <select class="form-control" name="filterByDriverVehicle[]"
                                                id="filterByVehicle" multiple="multiple" data-live-search="true" data-size="5">
                                            @if(isset($drivers))
                                                @foreach($drivers as $driver)
                                                    <option value="{{$driver->DriverProfile->car_registration}}">{{$driver->DriverProfile->car_registration}}</option>
                                                @endforeach
                                            @endif
        {{--                                    {{ \App\Utility\Utility::create_option("driver_profiles","car_registration","car_registration",$company_id) }}--}}
                                        </select>
                                        <div class="alert-message" id="driverVehicleError"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 text-center">
                                <div class="breadcrumb-header">
                                    <button class="filterDate btn btn-outline-primary" type="submit"
                                            style="margin-top: 30px;width: 100%;">{{ config('languageString.filter_button') }}
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
                    <h5>{{ config('languageString.online_status') }}</h5>
                    <h6 id="onlineDrivers">0</h6>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="offline-card card" style="background: #ef5050eb !important;">
                <div class="card-body">
                    <h5>{{ config('languageString.offline_status') }}</h5>
                    <h6 id="offlineDrivers">0</h6>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="offline-card card" style="background: #e2d32b !important;">
                <div class="card-body">
                    <h5>{{ config('languageString.busy_status') }}</h5>
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
                        <button class="btn btn-outline-info btn-style" onclick="getDrivers()">{{ config('languageString.refresh_button') }}</button>
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

    <!-- (Optional) Latest compiled and minified JavaScript translation files -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/js/bootstrap-select.min.js"
            integrity="sha512-yDlE7vpGDP7o2eftkCiPZ+yuUyEcaBwoJoIhdXv71KZWugFqEphIS3PU60lEkFaz8RxaVsMpSvQxMBaKVwA5xg=="
            crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function () {
            $('.select2').select2();
            $("#filterByNumber").selectpicker({});
            $("#filterByVehicle").selectpicker({});
        });
    </script>
    <script src="{{URL::asset('assets/js/custom/company/DriverStatus.js')}}"></script>
    <script src="{{URL::asset('assets/js/modal.js')}}"></script>
@endsection
