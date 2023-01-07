@extends('admin.layouts.master2')
@section('css')
    <!-- Sidemenu-respoansive-tabs css -->
    <link href="{{URL::asset('assets/plugins/sidemenu-responsive-tabs/css/sidemenu-responsive-tabs.css')}}"
          rel="stylesheet">

@endsection
@section('content')
    <style>
        .iti__flag-container {
            max-height: 40px !important;
            background: #141b2d !important;
        }
        .iti--separate-dial-code .iti__selected-flag {
            background-color: #141b2d !important;
        }
    </style>
    <!-- Page -->
    <div class="page">
        <div class="container-fluid">
            <div class="row no-gutter">
                <!-- The image half -->
                <div class="col-md-6 col-lg-6 col-xl-7 d-none d-md-flex bg-primary-transparent">
                    <div class="row wd-100p mx-auto text-center">
                        <div class="col-md-12 col-lg-12 col-xl-12 my-auto mx-auto wd-100p">
                            <img src="{{URL::asset('assets/img/media/login.png')}}"
                                 class="my-auto ht-xl-80p wd-md-100p wd-xl-80p mx-auto" alt="logo">
                        </div>
                    </div>
                </div>
                <!-- The content half -->
                <div class="col-md-6 col-lg-6 col-xl-5">
                    <div class="login d-flex align-items-center py-2">
                        <!-- Demo content-->
                        <div class="container p-0">
                            <div class="row">
                                <div class="col-md-10 col-lg-10 col-xl-9 mx-auto">
                                    <div class="card-sigin">
                                        <div class="mt-2"></div>
                                        <div class="card-sigin">
                                            <div class="main-signup-header">

                                                <h5 class="font-weight-semibold mb-4 ">It's free to signup and only
                                                    takes a minute.</h5>
                                                <form method="POST" data-parsley-validate="" id="addEditForm" role="form" action="{{ route('registerPost') }}">
                                                    @csrf
                                                    <input type="hidden" id="form-method" value="add">
                                                    <div class="form-group">
                                                        <label>Company Name</label>
                                                        <input class="form-control"
                                                           placeholder="Enter your company name"
                                                           type="text" id="com_name"
                                                           name="com_name"
                                                           value="{{ old('com_name') }}"
                                                           required>
                                                    </div>

                                                    <div class="form-group">
                                                        <label>Company Contact Number<span
                                                                class="error">*</span></label> <input
                                                            class="form-control"
                                                            placeholder="Enter company contact number" type="text" maxlength="10"
                                                            id="phone" name="com_contact_number" required
                                                            value="{{ old('com_contact_number') }}" onkeyup="getCode()">
                                                        <input type="hidden" name="country_code" id="country_code">
                                                        <input type="hidden" name="com_full_contact_number" id="com_full_contact_number">
                                                        <div class="help-block with-errors error"></div>
                                                        @if ($errors->has('com_contact_number'))
                                                            <span class="invalid-feedback" role="alert"
                                                                  style="display: block">
                                                                <strong>{{ $errors->first('com_contact_number') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>

                                                    <div class="form-group">
                                                        <label>Company Services Type<span
                                                                class="error">*</span></label>
                                                        <select class="form-control" id="com_service_type"
                                                                name="com_service_type" required>
                                                            <option value="">Select Company Services Type</option>
                                                            <option value="Ride hailing">Ride hailing</option>
                                                            <option value="Taxi">Taxi</option>
                                                            <option value="Executive cars">Executive cars</option>

                                                            <option value="Airport Shuttle">Airport Shuttle</option>
                                                            <option value="Bikes or Motorbikes">Bikes or Motorbikes
                                                            </option>
                                                            <option value="Delivery">Delivery</option>
                                                            <option value="Others">Others</option>
                                                        </select>
                                                        <div class="help-block with-errors error"></div>
                                                    </div>

                                                    <div class="form-group">
                                                        <label for="email">Email<span
                                                                class="error">*</span></label>
                                                        <input id="email" type="email"
                                                               class="form-control @error('email') is-invalid @enderror"
                                                               name="email"
                                                               value="{{ old('email') }}" required
                                                               autocomplete="email"
                                                               pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-z]{2,}$"
                                                               autofocus
                                                               placeholder="Enter Email">
                                                        <div class="help-block with-errors error"></div>
                                                        @if ($errors->has('email'))
                                                            <span class="invalid-feedback" role="alert"
                                                                  style="display: block">
                                                                <strong>{{ $errors->first('email') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Password<span
                                                                class="error">*</span></label>
                                                        <input id="password" type="password"
                                                               class="form-control"
                                                               name="password"
                                                               required autocomplete="current-password"
                                                               placeholder="Enter Password">
                                                        <div class="help-block with-errors error"></div>
                                                        @if ($errors->has('password'))
                                                            <span class="invalid-feedback" role="alert"
                                                                  style="display: block">
                                                                <strong>{{ $errors->first('password') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <div class="form-group">
                                                        <button type="submit" class="btn btn-main-primary btn-block">
                                                            Register
                                                        </button>
                                                    </div>
                                                </form>
                                                <div class="main-signin-footer mt-3">
                                                    <a href="{{ url('company/login') }}">
                                                        <button class="btn btn-main-primary btn-block">
                                                            Have already account? continue as Login
                                                        </button>
                                                    </a>
                                                </div>
                                                <div class="main-signin-footer mt-5">
                                                    <p><a href="">Forgot password?</a></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div><!-- End -->
                    </div>
                </div><!-- End -->
            </div>
        </div>
    </div>
    <!-- End Page -->
@endsection
@section('js')
    <!-- Internal TelephoneInput js-->
    <script src="{{URL::asset('assets/plugins/telephoneinput/telephoneinput.js')}}"></script>
    <script src="{{URL::asset('assets/plugins/telephoneinput/inttelephoneinput.js')}}"></script>
<!--    <script type="text/javascript">
        $(function () {
            let $form = $('#addEditForm')
            $form.on('submit', function (e) {
                console.log('Hello');
                e.preventDefault()
                $form.parsley().validate();
                if ($form.parsley().isValid()) {
                    loaderView();
                    let formData = new FormData($('#addEditForm')[0])

                    $.ajax({
                        url: APP_URL + '/registerPost',
                        type: 'POST',
                        dataType: 'json',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function (data) {
                            loaderHide();
                            if (data.success === true) {
                                $form[0].reset()
                                $form.parsley().reset();
                                window.location.href = APP_URL + '/register/success
                                if ($('#form-method').val() === 'edit') {
                                    setTimeout(function () {

                                    }, 1000);
                                }
                            } else if (data.success === false) {
                                console.log('warning');
                                successToast(data.message, 'warning')
                            }
                        },
                        error: function (data) {
                            loaderHide();
                            console.log('Error:', data)
                        }
                    })
                }
            });
        });
    </script>-->
    <script>
        getCode();
        function getCode() {
            var country_code_string = $(".iti__active").text();
            countrycode = country_code_string.split("+");

            $("#country_code").val('+' + countrycode[1]);
            var new_countrycode = "+"+countrycode[1];
            $('#com_full_contact_number').val(new_countrycode+$("#phone").val());
        }


        $('#phone').bind('keyup paste', function(){
        this.value = this.value.replace(/[^0-9]/g, '');
        });
    </script>
@endsection
