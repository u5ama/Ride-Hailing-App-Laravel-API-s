@extends('admin.layouts.master')
@section('css')
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto"> {{ config('languageString.company_profile_title') }}</h4>
                <span class="text-muted mt-1 tx-13 ml-2 mb-0"></span>
            </div>
        </div>
    </div>
    <!-- breadcrumb -->
@endsection
@section('content')
    <!-- row -->
    <style>
        .iti__flag-container {
            max-height: 40px !important;
            background: #141b2d !important;
        }
        .iti--separate-dial-code .iti__selected-flag {
            background-color: #141b2d !important;
            color: white !important;
        }
    </style>
    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" data-parsley-validate="" id="addEditForm" role="form"
                          enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="form-method" value="add">
                        <div class="row row-sm">

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="name">{{ config('languageString.company_name_title') }}<span
                                            class="error">*</span></label>
                                    <input type="text" class="form-control"
                                           name="com_name"
                                           id="com_name"
                                           placeholder="{{ config('languageString.company_name_title') }}" value="{{$companyProfile->com_name}}" required/>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>

                            <div class="row" style="width: 100%;">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="screen_info">{{ config('languageString.company_contact_number_title') }}<span
                                                class="error">*</span></label>
                                        <input type="text" class="form-control"
                                               name="com_contact_number"
                                               id="phone"
                                               placeholder="{{ config('languageString.company_contact_number_title') }}"
                                               value="{{$companyProfile->com_contact_number}}" required
                                               onkeyup="getCode()" maxlength="10"/>
                                        <div class="help-block with-errors error"></div>
                                        <input type="hidden" name="country_code" id="country_code" value="{{$companyProfile->com_country_code}}">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="screen_info">{{ config('languageString.company_full_contact_number_title') }}<span
                                                class="error">*</span></label>
                                        <input type="text" class="form-control"
                                               name="com_full_contact_number"
                                               id="com_full_contact_number"
                                               placeholder="{{ config('languageString.company_full_contact_number_title') }}"
                                               value="{{$companyProfile->com_full_contact_number}}" readonly="" />
                                        <div class="help-block with-errors error"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">c
                                    <label for="com_service_type">{{ config('languageString.company_service_type_title') }}<span
                                            class="error">*</span></label>

                                    <select class="form-control" id="com_service_type" name="com_service_type"
                                            required="">
                                        <option>Select Company Services Type</option>
                                        <option
                                            value="Ride hailing" @if($companyProfile->com_service_type == 'Ride hailing'){{'selected'}} @endif>
                                            Ride hailing
                                        </option>
                                        <option
                                            value="Taxi" @if($companyProfile->com_service_type == 'Taxi') {{'selected'}} @endif >
                                            Taxi
                                        </option>
                                        <option
                                            value="Executive cars" @if($companyProfile->com_service_type == 'Executive cars') {{'selected'}} @endif >
                                            Executive cars
                                        </option>
                                        <option
                                            value="Airport Shuttle" @if($companyProfile->com_service_type == 'Airport Shuttle') {{'selected'}} @endif >
                                            Airport Shuttle
                                        </option>
                                        <option
                                            value="Bikes or Motorbikes" @if($companyProfile->com_service_type == 'Bikes or Motorbikes') {{'selected'}} @endif >
                                            Bikes or Motorbikes
                                        </option>
                                        <option
                                            value="Delivery" @if($companyProfile->com_service_type == 'Delivery') {{'selected'}} @endif >
                                            Delivery
                                        </option>
                                        <option
                                            value="Others" @if($companyProfile->com_service_type == 'Others') {{'selected'}} @endif >
                                            Others
                                        </option>
                                    </select>

                                    <div class="help-block with-errors error">

                                    </div>
                                </div>
                            </div>
                            <div class="row" style="width: 100%">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="screen_info">{{ config('languageString.company_license_num_title') }}</label>
                                        <input type="text" class="form-control"
                                               name="com_license_no"
                                               id="com_license_no"
                                               placeholder="{{ config('languageString.company_license_num_title') }}"
                                               value="{{$companyProfile->com_license_no}}"/>
                                        <div class="help-block with-errors error"></div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="screen_info">{{ config('languageString.company_radius_title') }}</label>
                                        <input type="text" class="form-control"
                                               name="com_radius"
                                               id="com_radius"
                                               placeholder="{{ config('languageString.company_radius_title') }}" value="{{$companyProfile->com_radius}}"
                                        />
                                        <div class="help-block with-errors error"></div>
                                    </div>
                                </div>
                            </div>




                            <div class="col-12">
                                <div class="form-group">
                                    <label for="screen_info">{{ config('languageString.company_time_zone_title') }}</label>
                                    <select name="com_time_zone" id="com_time_zone" class="form-control select2" required="">
                                        <option value="">Select Timezone</option>

                                        @if(isset(auth()->guard('company')->user()->com_name))
                                            {{ \App\Utility\Utility::create_option("time_zones","id","time_zone",auth()->guard('company')->user()->com_time_zone) }}
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="row" style="width: 100%">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="screen_info">{{ config('languageString.company_latitude_title') }}</label>
                                        <input type="text" class="form-control"
                                               name="com_lat"
                                               id="com_lat"
                                               placeholder="{{ config('languageString.company_latitude_title') }}" value="{{$companyProfile->com_lat}}"
                                               />
                                        <div class="help-block with-errors error"></div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="screen_info">{{ config('languageString.company_longitude_title') }}</label>
                                        <input type="text" class="form-control"
                                               name="com_long"
                                               id="com_long"
                                               placeholder="{{ config('languageString.company_longitude_title') }}" value="{{$companyProfile->com_long}}"
                                               />
                                        <div class="help-block with-errors error"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="screen_info">{{ config('languageString.company_use_name_title') }}</label>
                                    <input type="text" class="form-control"
                                           name="com_user_name"
                                           id="com_user_name"
                                           placeholder="{{ config('languageString.company_use_name_title') }}" value="{{$companyProfile->com_user_name}}"
                                           />
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>
                            <div class="row" style="width: 100%">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="screen_info">{{ config('languageString.company_email_title') }}<span
                                                class="error">*</span></label>
                                        <input type="text" class="form-control"
                                               name="email"
                                               id="email"
                                               placeholder="{{ config('languageString.company_email_title') }}" value="{{$companyProfile->email}}" required/>
                                        <div class="help-block with-errors error"></div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="password">{{ config('languageString.reset_password_title') }}</label>
                                        <input type="text" class="form-control"
                                               name="password"
                                               id="password"
                                               placeholder="{{ config('languageString.reset_password_title') }}"/>
                                        <div class="help-block with-errors error"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="com_logo">{{ config('languageString.company_logo_title') }}</label>
                                    <input type="file" class="form-control"
                                           name="com_logo"
                                           id="com_logo"
                                           placeholder="Company Logo" />
                                    <div class="help-block with-errors error"></div>
                                    @if($companyProfile->com_logo)
                                        <img src="{{url($companyProfile->com_logo)}}" alt="" class="img-fluid" style="width: 80px;">
                                    @endif
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group mb-0 mt-3 justify-content-end">
                                    <div>
                                        <button type="submit" class="btn btn-primary">{{ config('languageString.company_submit_button') }}</button>
                                        <a href="{{ route('company::company') }}"
                                           class="btn btn-secondary">{{ config('languageString.cancel_button') }}</a>
                                    </div>
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
    <script src="{{URL::asset('assets/js/custom/company/company.js')}}?v={{ time() }}"></script>
    <script src="{{URL::asset('assets/plugins/telephoneinput/telephoneinput.js')}}"></script>
    <script src="{{URL::asset('assets/plugins/telephoneinput/inttelephoneinput.js')}}"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/16.0.8/js/intlTelInput-jquery.min.js"></script>

    <script>

        function getCode() {
               var code = $("#phone").intlTelInput("getSelectedCountryData").dialCode;
                var phoneNumber = $('#phone').val();
                var name = $("#phone").intlTelInput("getSelectedCountryData").name;
            $("#country_code").val('+' + code);

            $('#com_full_contact_number').val("+"+code+$("#phone").val());


                //alert('Country Code : ' + code + '\nPhone Number : ' + phoneNumber + '\nCountry Name : ' + name);
        }

  $('#phone').bind('keyup paste', function(){
        this.value = this.value.replace(/[^0-9]/g, '');
        $('#com_full_contact_number').replace(/[^0-9]/g, '');

  });




    </script>

     <script type="text/javascript">
        $(function () {
            var code = "{{$companyProfile->com_country_code.$companyProfile->com_contact_number}}"; // Assigning value from model.
            $('#phone').val(code);
            $('#phone').intlTelInput({
                autoHideDialCode: true,
                autoPlaceholder: "ON",
                dropdownContainer: document.body,
                formatOnDisplay: true,
                hiddenInput: "full_number",
                initialCountry: "auto",
                nationalMode: true,
                placeholderNumberType: "MOBILE",
                preferredCountries: ['US'],
                separateDialCode: true
            });
            $('#btnSubmit').on('click', function () {
                var code = $("#phone").intlTelInput("getSelectedCountryData").dialCode;
                var phoneNumber = $('#phone').val();
                var name = $("#phone").intlTelInput("getSelectedCountryData").name;
                alert('Country Code : ' + code + '\nPhone Number : ' + phoneNumber + '\nCountry Name : ' + name);
            });
        });
    </script>
@endsection
