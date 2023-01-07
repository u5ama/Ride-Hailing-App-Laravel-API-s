@extends('admin.layouts.master')
@section('css')
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">Add Driver</h4>
                <span class="text-muted mt-1 tx-13 ml-2 mb-0"></span>
            </div>
        </div>
    </div>
    <!-- breadcrumb -->
@endsection
@section('content')
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
    <!-- row -->
    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" data-parsley-validate="" id="addEditForm" role="form">
                        @csrf
                        <input type="hidden" id="form-method" value="add">
                        <input type="hidden" id="company_id" name="company_id" value="{{ $company_id }}">
                        <div class="row row-sm">

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="name">Driver Name<span
                                            class="error">*</span></label>
                                    <input type="text" class="form-control"
                                           name="du_full_name"
                                           id="du_full_name"
                                           placeholder="Driver Name" value="" required/>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="screen_info">Driver Contact Number<span
                                            class="error">*</span></label>
                                    <input type="text" class="form-control"
                                           name="du_mobile_number"
                                           id="phone"   maxlength="10"
                                           placeholder="Driver Contact Number" value="" required onkeyup="getCode()"/>
                                    <div class="help-block with-errors error"></div>
                                    <input type="hidden" name="country_code" id="country_code">
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="screen_info">Driver Full Contact Number<span
                                            class="error">*</span></label>
                                    <input type="text" class="form-control"
                                           name="du_full_mobile_number"
                                           id="du_full_mobile_number"
                                           required
                                           placeholder="Driver Full Contact Number" value="" readonly="" />
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="screen_info">Manual Otp</label>
                                    <input class="form-control"
                                           name="du_otp_manual"
                                           id="du_otp_manual"
                                           placeholder="Driver Manual Otp" value=""
                                           oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength); if (this.value.length < this.minlength) this.value = this.value.slice(0, this.minlength);"
                                           type = "number"
                                           minlength = "6"
                                           maxlength = "6"/>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="screen_info">Manual Otp Flag</label>
                                    <select name="du_otp_flag" id="du_otp_flag" class="form-control">
                                        <option value="0">Manual</option>
                                        <option value="1">Automatic</option>
                                    </select>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="screen_info">Driver User Name<span
                                            class="error">*</span></label>
                                    <input type="text" class="form-control"
                                           name="du_user_name"
                                           id="du_user_name"
                                           placeholder="driver User Name" value="" required/>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="screen_info">Driver Email<span
                                            class="error">*</span></label>
                                    <input type="email" class="form-control"
                                           name="email"
                                           id="email"
                                           placeholder="Driver Email" value="" required/>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="password">Driver Password</label>
                                    <input type="password" class="form-control"
                                           name="password"
                                           id="password"
                                           placeholder="Driver Password"/>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="dp_license_number">Driver Licence</label>
                                    <input type="text" class="form-control"
                                           name="dp_license_number"
                                           id="dp_license_number"
                                           placeholder="Driver Licence"/>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="dp_transport_type_id_ref">Driver Transport Type</label>
                                    <select name="dp_transport_type_id_ref" id="dp_transport_type_id_ref" required
                                            class="form-control">
                                        @foreach($transports as $transport)
                                            <option value="{{$transport->id}}">{{$transport->name}}</option>
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
                                           placeholder="Driver Car Registration Number"/>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="dp_date_manufacture">Driver Car Manufacture Date</label>
                                    <input type="date" class="form-control"
                                           name="dp_date_manufacture"
                                           id="dp_date_manufacture"
                                           placeholder="Driver Car Manufacture Date"/>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="dp_date_registration">Driver Car Registration Date</label>
                                    <input type="date" class="form-control"
                                           name="dp_date_registration"
                                           id="dp_date_registration"
                                           placeholder="Driver Car Registration Date"/>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="image">Driver Image</label>
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
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                        <a href="{{ url('admin/company/'.$company_id) }}"
                                           class="btn btn-secondary">Cancel</a>
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
    <script src="{{URL::asset('assets/js/custom/driver.js')}}?v={{ time() }}"></script>
    <script src="{{URL::asset('assets/plugins/telephoneinput/utils.js')}}"></script>
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
  });
    </script>
@endsection
