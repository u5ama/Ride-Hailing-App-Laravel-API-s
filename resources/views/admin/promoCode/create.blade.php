@extends('admin.layouts.master')
@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet"/>
    <style>
        .select2 {
            width: 100% !important;
            height: auto !important;
        }
    </style>
@endsection
@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" integrity="sha512-mSYUmp1HYZDFaVKK//63EcZq4iFWFjxSL+Z3T/aCt4IO9Cejm03q3NKKYN6pFQzY0SBOr8h+eCIAZHPXcpZaNw==" crossorigin="anonymous" />
    <div class="card  mt-5">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <h4 class="panel-heading">{{ 'Add Promo Code' }}</h4>

                        <div class="panel-body">
                            <form method="POST" data-parsley-validate="" id="addPromoForm" role="form">
                                @csrf
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label">{{ 'Promo Country' }}</label>
                                            <select name="promo_country" id="promo_country" class="form-control select2" required>
                                                <option value="">Select Country</option>
                                                @foreach($countries as $country)
                                                    <option value="{{$country->id}}" selected>{{$country->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label">{{ 'Promo Code' }}</label>
                                            <input type="text" class="form-control" name="promo_code"
                                                   value="{{ old('promo_code') }}" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">{{ 'Start Date' }}</label>
                                            <input type="text" class="form-control" name="start_date" id="start_date"
                                                   value="{{ old('start_date') }}" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">{{ 'End Date' }}</label>
                                            <input type="text" class="form-control" name="end_date" id="end_date"
                                                   value="{{ old('end_date') }}" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">{{ 'Start Time' }}</label>
                                            <input type="time" class="form-control" name="start_time"
                                                   value="{{ old('start_time') }}" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">{{ 'End Time' }}</label>
                                            <input type="time" class="form-control" name="end_time"
                                                   value="{{ old('end_time') }}" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label">{{ 'Promo Type' }}</label>
                                            <select name="promo_type" id="promo_type" class="form-control" required>
                                                <option value="" selected>Select Type</option>
                                                <option value="public">Public</option>
                                                <option value="private">Private</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">{{ 'Promo Value' }}</label>
                                            <input type="text" class="form-control" name="promo_value"
                                                   value="{{ old('promo_value') }}" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">{{ 'Promo Value Type' }}</label>
                                            <select name="promo_value_type" id="promo_value_type" class="form-control" required>
                                                <option value="" selected>Select Value Type</option>
                                                <option value="value">Amount</option>
                                                <option value="percentage">Percentage</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label">{{ 'Description' }}</label>
                                            <textarea class="form-control"
                                                      name="admin_remarks">{{ old('admin_remarks') }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <button type="reset" class="btn btn-danger">{{ 'Reset' }}</button>
                                        <button type="submit" class="btn btn-primary">{{ 'Save' }}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{URL::asset('assets/js/custom/PromoCode.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function () {
            $('.select2').select2();
        });
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js" integrity="sha512-T/tUfKSV1bihCnd+MxKD0Hm1uBBroVYBOYSk1knyvQ9VyZJpc/ALb4P0r6ubwVPSGB2GvjeoMAJJImBG12TiaQ==" crossorigin="anonymous"></script>
    <script>
        $(function() {
            $('#start_date').datepicker({
                startDate: '-0d',
                format: 'yyyy-mm-dd',
                setDate: new Date()
            });

            $('#end_date').datepicker({
                startDate: '-0d',
                format: 'yyyy-mm-dd',
                setDate: new Date()
            });
        });
    </script>
@endsection

