@extends('admin.layouts.master')
@section('css')
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ config('languageString.add_driver_title') }}</h4>
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
                        <input type="hidden" id="form-method" value="add">
                        <div class="row row-sm">

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="name">{{ config('languageString.driver_name_title') }}<span
                                            class="error">*</span></label>
                                    <input type="text" class="form-control"
                                           name="du_full_name"
                                           id="du_full_name"
                                           placeholder="{{ config('languageString.driver_name_title') }}" value="" required/>
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
{{--                                           pattern="[1-9]{1}[0-9]{9}"--}}
                                           maxlength="10"
                                           placeholder="{{ config('languageString.driver_contact_number') }}" value="" required onkeyup="getCode()"  />
                                    <div class="help-block with-errors error"></div>
                                    <input type="hidden" name="country_code" id="country_code">
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="screen_info">{{ config('languageString.driver_full_contact_number_title') }}<span
                                            class="error">*</span></label>
                                    <input class="form-control"
                                           name="du_full_mobile_number"
                                           id="du_full_mobile_number"

                                           type="text"
                                           placeholder="{{ config('languageString.driver_full_contact_number_title') }}" value="" readonly="" />
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="screen_info">{{ config('languageString.manual_otp_title') }}</label>
                                    <input class="form-control"
                                           name="du_otp_manual"
                                           id="du_otp_manual"
                                           placeholder="{{ config('languageString.manual_otp_title') }}" value=""
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
                                        <option value="0">Manual</option>
                                        <option value="1">Automatic</option>
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
                                           placeholder="{{ config('languageString.driver_user_name_title') }}" value="" required/>
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
                                           placeholder="{{ config('languageString.driver_email_title') }}" value="" required/>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="password">{{ config('languageString.driver_password_title') }}<span
                                            class="error">*</span></label>
                                    <input type="password" class="form-control"
                                           name="password"
                                           id="password"
                                           placeholder="{{ config('languageString.driver_password_title') }}" required/>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>

{{--                            <div class="col-12">--}}
{{--                                <div class="form-group">--}}
{{--                                    <label for="dp_license_number">Driver Licence</label>--}}
{{--                                    <input type="text" class="form-control"--}}
{{--                                           name="dp_license_number"--}}
{{--                                           id="dp_license_number"--}}
{{--                                           placeholder="Driver Licence"/>--}}
{{--                                    <div class="help-block with-errors error"></div>--}}
{{--                                </div>--}}
{{--                            </div>--}}

{{--                            <div class="col-12">--}}
{{--                                <div class="form-group">--}}
{{--                                    <label for="dp_transport_type_id_ref">Driver Transport Type</label>--}}
{{--                                    <select name="dp_transport_type_id_ref" id="dp_transport_type_id_ref" required--}}
{{--                                            class="form-control">--}}
{{--                                        @foreach($transports as $transport)--}}
{{--                                            <option value="{{$transport->id}}">{{$transport->name}}</option>--}}
{{--                                        @endforeach--}}
{{--                                    </select>--}}
{{--                                    <div class="help-block with-errors error"></div>--}}
{{--                                </div>--}}
{{--                            </div>--}}

{{--                            <div class="col-12">--}}
{{--                                <div class="form-group">--}}
{{--                                    <label for="car_registration">Driver Car Registration Number</label>--}}
{{--                                    <input type="text" class="form-control"--}}
{{--                                           name="car_registration"--}}
{{--                                           id="car_registration"--}}
{{--                                           placeholder="Driver Car Registration Number"/>--}}
{{--                                    <div class="help-block with-errors error"></div>--}}
{{--                                </div>--}}
{{--                            </div>--}}

{{--                            <div class="col-12">--}}
{{--                                <div class="form-group">--}}
{{--                                    <label for="dp_date_manufacture">Driver Car Manufacture Date</label>--}}
{{--                                    <input type="date" class="form-control"--}}
{{--                                           name="dp_date_manufacture"--}}
{{--                                           id="dp_date_manufacture"--}}
{{--                                           placeholder="Driver Car Manufacture Date"/>--}}
{{--                                    <div class="help-block with-errors error"></div>--}}
{{--                                </div>--}}
{{--                            </div>--}}

{{--                            <div class="col-12">--}}
{{--                                <div class="form-group">--}}
{{--                                    <label for="dp_date_registration">Driver Car Registration Date</label>--}}
{{--                                    <input type="date" class="form-control"--}}
{{--                                           name="dp_date_registration"--}}
{{--                                           id="dp_date_registration"--}}
{{--                                           placeholder="Driver Car Registration Date"/>--}}
{{--                                    <div class="help-block with-errors error"></div>--}}
{{--                                </div>--}}
{{--                            </div>--}}

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
    <script>

        function getCode() {
            var country_code_string = $(".iti__active").text();
            countrycode = country_code_string.split("+");
            console.log(countrycode[1]);
            $("#country_code").val('+' + countrycode[1]);
            var new_countrycode = "+"+countrycode[1];
            $('#du_full_mobile_number').val(new_countrycode+$("#phone").val());
        }




 $('#phone').bind('keyup paste', function(){
        this.value = this.value.replace(/[^0-9]/g, '');
        $("#du_full_mobile_number").replace(/[^0-9]/g, '');
  });
    </script>
@endsection
