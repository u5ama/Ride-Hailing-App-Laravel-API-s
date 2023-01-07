@extends('admin.layouts.master')
@section('css')
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">Edit Driver</h4>
                <span class="text-muted mt-1 tx-13 ml-2 mb-0"></span>
            </div>
        </div>
    </div>
    <!-- breadcrumb -->
@endsection
@section('content')
    <!-- row -->
    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" data-parsley-validate="" id="addEditForm" role="form">
                        @csrf
                        <input type="hidden" id="edit_value" name="edit_value" value="{{ $driver->id }}">
                        <input type="hidden" id="form-method" value="edit">
                        <input type="hidden" id="form-method" value="add">
                        <div class="row row-sm">

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="name">{{ config('languageString.driver_name_title') }}<span
                                            class="error">*</span></label>
                                    <input type="text" class="form-control"
                                           name="du_full_name"
                                           id="du_full_name"
                                           placeholder="{{ config('languageString.driver_name_title') }}" value="{{$driver->du_full_name}}" required/>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="screen_info">{{ config('languageString.driver_contact_number') }}<span
                                            class="error">*</span></label>
                                    <input type="text" class="form-control"
                                           name="du_mobile_number"
                                           id="phone"
                                           maxlength="10"
                                           placeholder="{{ config('languageString.driver_contact_number') }}" value="{{$driver->du_mobile_number}}"
                                           required onkeyup="getCode()"/>
                                    <div class="help-block with-errors error"></div>
                                    <input type="hidden" name="country_code" id="country_code" value="{{$driver->du_country_code}}">
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="screen_info">{{ config('languageString.driver_full_contact_number_title') }}<span
                                            class="error">*</span></label>
                                    <input type="text" class="form-control"
                                           name="du_full_mobile_number"
                                           id="du_full_mobile_number"
                                           placeholder="{{ config('languageString.driver_full_contact_number_title') }}"

                                           value="{{$driver->du_full_mobile_number}}" readonly="" />
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="screen_info">{{ config('languageString.manual_otp_title') }}</label>
                                    <input class="form-control"
                                           name="du_otp_manual"
                                           id="du_otp_manual"
                                           placeholder="{{ config('languageString.manual_otp_title') }}" value="{{$driver->du_otp_manual}}"
                                           oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength); if (this.value.length < this.minlength) this.value = this.value.slice(0, this.minlength);"
                                           type = "number"
                                           minlength = "6"
                                           maxlength = "6"/>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="screen_info">{{ config('languageString.manual_otp_flag_title') }}</label>
                                    <select name="du_otp_flag" id="du_otp_flag" class="form-control">
                                        <option value="">Select OTP Flag</option>
                                        <option value="0" @if(isset($driver->du_otp_flag) && $driver->du_otp_flag==0) {{'selected'}} @endif>Manual</option>
                                        <option value="1" @if(isset($driver->du_otp_flag) && $driver->du_otp_flag==1) {{'selected'}} @endif>Automatic</option>
                                    </select>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="screen_info">{{ config('languageString.driver_user_name_title') }}<span
                                            class="error">*</span></label>
                                    <input type="text" class="form-control"
                                           name="du_user_name"
                                           id="du_user_name"
                                           placeholder="{{ config('languageString.driver_user_name_title') }}" value="{{$driver->du_user_name}}" required/>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="screen_info">{{ config('languageString.driver_email_title') }}<span
                                            class="error">*</span></label>
                                    <input type="email" class="form-control"
                                           name="email"
                                           id="email"
                                           placeholder="{{ config('languageString.driver_email_title') }}" value="{{$driver->email}}" required/>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="password">{{ config('languageString.driver_password_title') }}</label>
                                    <input type="password" class="form-control"
                                           name="password"
                                           id="password"
                                           placeholder="{{ config('languageString.driver_password_title') }}"/>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>

                            {{--<div class="col-12">
                                <div class="form-group">
                                    <label for="dp_license_number">Driver Licence</label>
                                    <input type="text" class="form-control"
                                           name="dp_license_number"
                                           id="dp_license_number"
                                           placeholder="Driver Licence"
                                           value="@if(isset($driver_profile->dp_license_number)){{$driver_profile->dp_license_number}}@endif"/>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="dp_transport_type_id_ref">Driver Transport Type</label>
                                    <select name="dp_transport_type_id_ref" id="dp_transport_type_id_ref" required
                                            class="form-control">
                                        @foreach($transports as $transport)
                                            <option
                                                value="{{$transport->id}}"
                                                @if(isset($driver_profile->dp_transport_type_id_ref) && (  $driver_profile->dp_transport_type_id_ref == $transport->id) ){{'selected'}} @endif>{{$transport->name}}</option>
                                        @endforeach
                                    </select>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="car_registration">Driver Car Registration Number</label>
                                    <input type="text" class="form-control"
                                           name="car_registration"
                                           id="car_registration"
                                           placeholder="Driver Car Registration Number"
                                           value="@if(isset($driver_profile->car_registration)){{$driver_profile->car_registration}}@endif"/>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="dp_date_manufacture">Driver Car Manufacture Date</label>
                                    <input type="date" class="form-control"
                                           name="dp_date_manufacture"
                                           id="dp_date_manufacture"
                                           placeholder="Driver Car Manufacture Date"
                                           value="@if(isset($driver_profile->dp_date_manufacture)){{$driver_profile->dp_date_manufacture}}@endif"/>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="dp_date_registration">Driver Car Registration Date</label>
                                    <input type="date" class="form-control"
                                           name="dp_date_registration"
                                           id="dp_date_registration"
                                           placeholder="Driver Car Registration Date"
                                           value="@if(isset($driver_profile->dp_date_registration)){{$driver_profile->dp_date_registration}}@endif"/>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>--}}

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="image">{{ config('languageString.driver_image_title') }}</label>
                                    <input type="file" class="form-control"
                                           name="du_profile_pic"
                                           id="du_profile_pic"
                                           placeholder="Driver Image"/>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group mb-0 mt-3 justify-content-end">
                                    <div>
                                        <button type="submit" class="btn btn-primary">{{ config('languageString.save_and_next_next_button') }}</button>
                                        <a href="{{ url('company/driver') }}"
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
    <script src="{{URL::asset('assets/js/custom/company/driver.js')}}?v={{ time() }}"></script>
    <script src="{{URL::asset('assets/plugins/telephoneinput/telephoneinput.js')}}"></script>
    <script src="{{URL::asset('assets/plugins/telephoneinput/inttelephoneinput.js')}}"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/16.0.8/js/intlTelInput-jquery.min.js"></script>

    <script>

        function getCode() {
               var code = $("#phone").intlTelInput("getSelectedCountryData").dialCode;
                var phoneNumber = $('#phone').val();
                var name = $("#phone").intlTelInput("getSelectedCountryData").name;
            $("#country_code").val('+' + code);

            $('#du_full_mobile_number').val("+"+code+$("#phone").val());


//alert('Country Code : ' + code + '\nPhone Number : ' + phoneNumber + '\nCountry Name : ' + name);
        }

  $('#phone').bind('keyup paste', function(){
        this.value = this.value.replace(/[^0-9]/g, '');
        $("#du_full_mobile_number").replace(/[^0-9]/g, '');
  });




    </script>
    <script type="text/javascript">
     $(function () {
     var code = "{{$driver->du_country_code.$driver->du_mobile_number}}"; // Assigning value from model.
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
