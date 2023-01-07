@extends('admin.layouts.master2')

@section('content')
    <!-- Page -->
    <div class="page">
        <div class="container-fluid">
            <div class="row no-gutter">
                <!-- The image half  bg-primary-transparent -->
                <div class="col-md-6 col-lg-6 col-xl-7 d-none d-md-flex bg-primary-transparent">
                    <div class="row wd-100p mx-auto text-center">
                        <div class="col-md-12 col-lg-12 col-xl-12 my-auto mx-auto wd-100p">
                            <img src="{{URL::asset('assets/img/media/login.png')}}"
                                 class="my-auto ht-xl-80p wd-md-100p wd-xl-80p mx-auto" alt="logo">
                        </div>
                    </div>
                </div>
               <div class="col-md-6 col-lg-6 col-xl-5">
                    <div class="login d-flex align-items-center py-2">
                        <!-- Demo content-->
                        <div class="container p-0">
                            <div class="row">
                                <div class="col-md-10 col-lg-10 col-xl-9 mx-auto">
                                    <div class="card-sigin">
                                        <div class="mb-5 d-flex">
                                            <a href="#">
                                                <img src="{{URL::asset('assets/img/brand/logo.png')}}"
                                                     class="sign-favicon" alt="logo">
                                            </a>
                                            <h1 class="main-logo1 ml-1 mr-0 my-auto tx-28"></h1></div>
                                        <div class="card-sigin">
                                            <div class="main-signup-header">
                                                <h2>Welcome back!</h2>
                                                 <form method="POST" action="{{ route('forgotPassword') }}">
                                                    @csrf
                                                    <div class="form-group">
                                                        <label for="email">{{ 'Email' }}</label>
                                                        <input id="email" type="email"
                                                               class="form-control @error('email') is-invalid @enderror"
                                                               name="email"
                                                               value="{{ $email ?? old('email') }}" required
                                                               autocomplete="email"
                                                               autofocus
                                                               placeholder="{{'Email'}}">

                                                        @if ($errors->has('email'))
                                                            <span class="invalid-feedback" role="alert"
                                                                  style="display: block">
                                                                <strong>{{ $errors->first('email') }}</strong>
                                                            </span>
                                                        @endif
                                                    </div>

                                                    <button type="submit"
                                                            class="btn btn-main-primary btn-block">{{'Forgot Password'}}
                                                    </button>

                                                     <a type="button" href="{{route('company::login')}}"
                                                        class="btn btn-main-primary btn-block">{{'Return Signin'}}
                                                     </a>

                                                    @if (Session::has('error_message'))
                                                        <div
                                                            class="alert alert-danger mt-3 p-2">{{ Session::get('error_message') }}</div>
                                                    @endif

                                                    @if (Session::has('success_message'))
                                                        <div
                                                            class="alert alert-success mt-3 p-2">{{ Session::get('success_message') }}</div>
                                                    @endif
                                                </form>
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
@section('script')
    <script>
        // setTimeout(function () {
        //     window.location = "";
        // }, 1000);
    </script>
@endsection
