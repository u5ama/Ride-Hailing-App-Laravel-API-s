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
                                            <a href="{{route('company::login')}}">
                                                <img src="{{URL::asset('assets/img/brand/logo.png')}}"
                                                     class="sign-favicon" alt="logo">
                                            </a>
                                            <h1 class="main-logo1 ml-1 mr-0 my-auto tx-28"></h1></div>
                                        <div class="card-sigin">
                                            <div class="main-signup-header">
                                                <h2>Reset Your Password!</h2>
                                                <form method="POST" action="{{ route('resetPassword.update') }}">
                                                    @csrf

                                                    <input type="hidden" name="token" value="{{ $token }}">

                                                    <div class="form-group">
                                                        <label for="email">{{ __('Email Address') }}</label>
                                                            <input id="email" type="email"
                                                                   class="form-control @error('email') is-invalid @enderror" name="email"
                                                                   value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>

                                                            @error('email')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                            @enderror
                                                        </div>

                                                    <div class="form-group">
                                                        <label for="password">{{ __('Password') }}</label>
                                                            <input id="password" type="password"
                                                                   class="form-control @error('password') is-invalid @enderror" name="password"
                                                                   required autocomplete="new-password">

                                                            @error('password')
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                            @enderror
                                                        </div>

                                                    <div class="form-group">
                                                        <label for="password-confirm">{{ __('Confirm Password') }}</label>
                                                            <input id="password-confirm" type="password" class="form-control"
                                                                   name="password_confirmation" required autocomplete="new-password">
                                                    </div>

                                                    <button type="submit"
                                                            class="btn btn-main-primary btn-block">{{'Reset Password'}}
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

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header" style="text-align: center; font-size: 18px;font-weight: 600;">{{ __('Reset Password') }}</div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('resetPassword.update') }}">
                            @csrf

                            <input type="hidden" name="token" value="{{ $token }}">

                            <div class="form-group row">
                                <label for="email"
                                       class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                                <div class="col-md-6">
                                    <input id="email" type="email"
                                           class="form-control @error('email') is-invalid @enderror" name="email"
                                           value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>

                                    @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="password"
                                       class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

                                <div class="col-md-6">
                                    <input id="password" type="password"
                                           class="form-control @error('password') is-invalid @enderror" name="password"
                                           required autocomplete="new-password">

                                    @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="password-confirm"
                                       class="col-md-4 col-form-label text-md-right">{{ __('Confirm Password') }}</label>

                                <div class="col-md-6">
                                    <input id="password-confirm" type="password" class="form-control"
                                           name="password_confirmation" required autocomplete="new-password">
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-success">
                                        {{ __('Reset Password') }}
                                    </button>
                                </div>
                            </div>

                            @if (Session::has('error_message'))
                                <div class="alert alert-danger mt-3 p-2">{{ Session::get('error_message') }}</div>
                            @endif

                            @if (Session::has('success_message'))
                                <div class="alert alert-success mt-3 p-2">{{ Session::get('success_message') }}</div>
                            @endif

                            @if (count($errors) > 0)
                                <div class="alert alert-danger mt-3 p-2">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        // setTimeout(function () {
        //     var BASE_URL = 'http://app.ride.hi5host.com';
        //     window.location.href = BASE_URL + '/login';
        // }, 1000);
    </script>
@endsection
