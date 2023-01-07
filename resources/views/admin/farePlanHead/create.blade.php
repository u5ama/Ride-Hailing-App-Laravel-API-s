@extends('admin.layouts.master')
@section('css')
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">Add Fare Plan</h4>
                <span class="text-muted mt-1 tx-13 ml-2 mb-0"></span>
            </div>
        </div>
    </div>
    <!-- breadcrumb -->
@endsection
@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" integrity="sha512-mSYUmp1HYZDFaVKK//63EcZq4iFWFjxSL+Z3T/aCt4IO9Cejm03q3NKKYN6pFQzY0SBOr8h+eCIAZHPXcpZaNw==" crossorigin="anonymous" />
    <!-- row -->
    <div class="row row-sm">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" data-parsley-validate="" id="addEditForm" role="form">
                        @csrf
                        <input type="hidden" id="edit_value" name="edit_value" value="">
                        <input type="hidden" id="form-method" value="add">

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label> Name:</label>
                                    <input class="form-control" id="fph_plan_name" name="fph_plan_name" placeholder=""
                                           type="text" required>
                                </div>
                            </div><!-- input-group -->
                        </div><!-- col-4 -->

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label>Description</label><!-- input-group-text -->
                                    <input class="form-control" id="fph_description" name="fph_description"
                                           placeholder="Description" type="text" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label>Type:</label>
                                    <select class="form-control" id="fph_fare_type" name="fph_fare_type">
                                        <option value="intercity">Inner City</option>
                                        <option value="outercity">Outer City</option>

                                    </select>
                                </div><!-- input-group -->
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label>Country:</label>
                                    <select id="fph_country_id" name="fph_country_id" class="form-control select2"
                                            required>
                                        <option value="">Please Select One</option>
                                        @foreach($countries as $country)
                                            <option value="{{ $country->id }}">{{ $country->name }}</option>
                                        @endforeach
                                    </select>
                                </div><!-- input-group -->
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>Vat</label>
                                    <input class="form-control" id="fph_vat_per" name="fph_vat_per" placeholder="Vat"
                                           type="text">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label> Tax</label>
                                    <input class="form-control" id="fph_tax_per" name="fph_tax_per" placeholder="Tax"
                                           type="text">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label> Start Date </label>
                                    <input class="form-control" id="start_date" name="start_date"
                                           placeholder="Start Date" type="text" autocomplete="off" required>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label> End Date</label>
                                    <input class="form-control" id="end_date" name="end_date" placeholder="End Date"
                                           type="text" autocomplete="off" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label>Default / Optional:</label>
                                    <select class="form-control" id="fph_is_default" name="fph_is_default" required>
                                        <option value="">Please Select One</option>
                                        <option value="default">Default</option>
                                        <option value="optional">Optional</option>
                                    </select>
                                </div><!-- input-group -->
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <button type="reset" class="btn btn-danger">{{ 'Reset' }}</button>
                                    <button type="submit" class="btn btn-primary">{{ 'Save' }}</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- /row -->

    </div>
    <!-- Container closed -->
    </div>
    <!-- main-content closed -->
@endsection
@section('js')
    <script src="{{URL::asset('assets/js/custom/farePlanHead.js')}}"></script>
    <!--Internal  jquery.maskedinput js -->
    <script src="{{URL::asset('assets/plugins/jquery.maskedinput/jquery.maskedinput.js')}}"></script>
    <!--Internal  spectrum-colorpicker js -->
    <script src="{{URL::asset('assets/plugins/spectrum-colorpicker/spectrum.js')}}"></script>
    <!-- Internal Select2.min js -->
    <script src="{{URL::asset('assets/plugins/select2/js/select2.min.js')}}"></script>
    <!--Internal Ion.rangeSlider.min js -->
    <script src="{{URL::asset('assets/plugins/ion-rangeslider/js/ion.rangeSlider.min.js')}}"></script>
    <!-- Ionicons js -->
    <script src="{{URL::asset('assets/plugins/jquery-simple-datetimepicker/jquery.simple-dtpicker.js')}}"></script>
    <!--Internal  pickerjs js -->
    <script src="{{URL::asset('assets/plugins/pickerjs/picker.min.js')}}"></script>
    <!-- Internal form-elements js -->
    <script src="{{URL::asset('assets/js/form-elements.js')}}"></script>
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
