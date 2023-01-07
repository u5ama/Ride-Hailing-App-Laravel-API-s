<!-- main-header -->
<div class="main-header sticky side-header nav nav-item">
    <div class="container-fluid">
        <div class="main-header-left ">
            <div class="responsive-logo">
                <a href="{{ url('/' . $page='index') }}">
                    <img src="{{URL::asset('assets/img/brand/logo.png')}}"
                         class="logo-1" alt="logo">
                </a>
                <a href="{{ url('/' . $page='index') }}">
                    <img src="{{URL::asset('assets/img/brand/logo.png')}}"
                         class="dark-logo-1" alt="logo">
                </a>
                <a href="{{ url('/' . $page='index') }}">
                    <img src="{{URL::asset('assets/img/brand/logo.png')}}"
                         class="logo-2" alt="logo">
                </a>
                <a href="{{ url('/' . $page='index') }}">
                    <img src="{{URL::asset('assets/img/brand/logo.png')}}"
                         class="dark-logo-2" alt="logo">
                </a>
            </div>
            <div class="app-sidebar__toggle" data-toggle="sidebar">
                <a class="open-toggle" href="#"><i class="header-icon fe fe-align-left"></i></a>
                <a class="close-toggle" href="#"><i class="header-icons fe fe-x"></i></a>
            </div>

        </div>
        <div class="main-header-right">
            <div class="nav nav-item  navbar-nav-right ml-auto">
                <div class="ml-3 mt-3">
                    <h5>@if(isset(auth()->guard('admin')->user()->name))  @else {{ auth()->guard('company')->user()->com_name }} @endif</h5>
                </div>
                @php @endphp
                <div class="nav-item full-screen fullscreen-button">

                    <a class="new nav-link full-screen-link" href="#">
                        <svg xmlns="http://www.w3.org/2000/svg" class="header-icon-svgs" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                             class="feather feather-maximize">
                            <path
                                d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"></path>
                        </svg>
                    </a>
                </div>
                <div class="dropdown main-profile-menu nav nav-item nav-link">
                    <a class="profile-user d-flex" href="">
                        <img alt=""
                             src="@if(isset(auth()->guard('admin')->user()->profile_pic)){{URL::asset(auth()->guard('admin')->user()->profile_pic)}} @else {{URL::asset('assets/img/brand/logo.png')}} @endif">
                    </a>
                    <div class="dropdown-menu">
                        <div class="main-header-profile bg-primary p-3">
                            <div class="d-flex wd-100p">
                                <div class="main-img-user">
                                    <img alt="" class="img-fluid"
                                         src="@if(isset(auth()->guard('admin')->user()->profile_pic)){{URL::asset(auth()->guard('admin')->user()->profile_pic)}}@else {{URL::asset('assets/img/brand/logo.png')}} @endif">
                                </div>
                                <div class="ml-3 my-auto">
                                    <h6>@if(isset(auth()->guard('admin')->user()->name)) {{ auth()->guard('admin')->user()->name }} @else {{ auth()->guard('company')->user()->com_name }} @endif</h6>
                                </div>
                            </div>
                        </div>
                        <a class="dropdown-item"
                           href="@if(isset(auth()->guard('company')->user()->com_name)) {{ route('company::profile') }} @else {{''}} @endif"><i
                                class="bx bx-user-circle"></i>{{ config('languageString.profile_title') }}</a>
                        @if( (isset(auth()->guard('admin')->user()->panel_mode) && auth()->guard('admin')->user()->panel_mode==2) || (isset(auth()->guard('company')->user()->com_panel_mode) && auth()->guard('company')->user()->com_panel_mode==2 ) )
                            @if(isset(auth()->guard('company')->user()->com_panel_mode))
                                <a class="dropdown-item" href="{{ route('company::changeThemes',[1]) }}"><i
                                        class="bx bx-slider-alt"></i> {{ config('languageString.dark_themes_title') }}</a>
                            @else
                                <a class="dropdown-item" href="{{ route('admin::changeThemes',[1]) }}"><i
                                        class="bx bx-slider-alt"></i> {{ config('languageString.dark_themes_title') }}</a>
                            @endif
                        @else
                            @if(isset(auth()->guard('company')->user()->com_panel_mode))
                                <a class="dropdown-item" href="{{ route('company::changeThemes',[2]) }}"><i
                                        class="bx bx-slider-alt"></i> {{ config('languageString.light_themes_title') }}</a>
                            @else
                                <a class="dropdown-item" href="{{ route('admin::changeThemes',[2]) }}"><i
                                        class="bx bx-slider-alt"></i> {{ config('languageString.light_themes_title') }}</a>
                            @endif
                        @endif

                        @if(isset(auth()->guard('admin')->user()->locale))
                            @if(isset(auth()->guard('admin')->user()->locale) && auth()->guard('admin')->user()->locale=='en')
                                <a class="dropdown-item" href="{{ route('admin::changeThemesMode',['ar']) }}"><i
                                        class="bx bx-slider-alt"></i> العربية</a>
                            @else
                                <a class="dropdown-item" href="{{ route('admin::changeThemesMode',['en']) }}"><i
                                        class="bx bx-slider-alt"></i> English</a>
                            @endif
                        @else
                            @if(isset(auth()->guard('company')->user()->com_locale) && auth()->guard('company')->user()->com_locale=='en')
                                <a class="dropdown-item" href="{{ route('company::changeThemesMode',['ar']) }}"><i
                                        class="bx bx-slider-alt"></i> العربية</a>
                            @else
                                <a class="dropdown-item" href="{{ route('company::changeThemesMode',['en']) }}"><i
                                        class="bx bx-slider-alt"></i> English</a>
                            @endif
                        @endif


                       {{-- @if( isset(auth()->guard('admin')->user()->locale) && auth()->guard('admin')->user()->locale=='en')
                            @if(isset(auth()->guard('company')->user()->com_locale))
                                <a class="dropdown-item" href="{{ route('company::changeThemesMode',['en']) }}"><i
                                        class="bx bx-slider-alt"></i> RTL</a>
                            @else
                                <a class="dropdown-item" href="{{ route('admin::changeThemesMode',['en']) }}"><i
                                        class="bx bx-slider-alt"></i> RTL</a>
                            @endif
                        @else
                            @if(isset(auth()->guard('company')->user()->com_locale))
                                <a class="dropdown-item" href="{{ route('company::changeThemesMode',['en']) }}"><i
                                        class="bx bx-slider-alt"></i> LTR</a>
                            @else
                                <a class="dropdown-item" href="{{ route('admin::changeThemesMode',['en']) }}"><i
                                        class="bx bx-slider-alt"></i> LTR</a>
                            @endif
                        @endif--}}

                        @if( isset(auth()->guard('admin')->user()->locale) && auth()->guard('admin')->user()->locale=='en')
                                <a type="button" class="dropdown-item" href="javascript:void(0)" data-target="#timezoneSettings" data-toggle="modal"><i
                                        class="bx bx-time"></i> {{ config('languageString.time_zone_settings') }}</a>
                        @else
                                <a type="button" class="dropdown-item" href="javascript:void(0)" data-target="#timezoneSettings" data-toggle="modal"><i
                                        class="bx bx-time"></i> {{ config('languageString.time_zone_settings') }}</a>
                        @endif

                        @if(isset(auth()->guard('company')->user()->com_name))
                            <a class="dropdown-item" href="{{ route('company::companyLogout') }}"
                               onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                                <i class="bx bx-log-out"></i>
                                {{ config('languageString.sign_out_title') }}
                            </a>
                            <form id="logout-form" action="{{ route('company::companyLogout') }}" method="POST"
                                  style="display: none;">
                                @csrf
                            </form>
                        @else
                            <a class="dropdown-item" href="{{ route('admin::logout') }}"
                               onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                                <i class="bx bx-log-out"></i>
                                {{ config('languageString.sign_out_title') }}
                            </a>
                            <form id="logout-form" action="{{ route('admin::logout') }}" method="POST"
                                  style="display: none;">
                                @csrf
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .select2{
        width: 100% !important;
    }
</style>
<div class="modal" id="timezoneSettings">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content modal-content-demo">
            <div class="modal-header">
                <h6 class="modal-title">Timezone Settings</h6>
                <button aria-label="Close" class="close" data-dismiss="modal" type="button"><span
                        aria-hidden="true">&times;</span></button>
            </div>
            <form method="POST" data-parsley-validate="" id="addEditTimeZone" role="form">
                @csrf
                <div class="modal-body">
                    <div class="container">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                <select name="userTimeZone" id="userTimeZone" class="form-control select2" required="">
                                    <option value="">Select Timezone</option>
                                    @if(isset(auth()->guard('admin')->user()->name))
                                    {{ \App\Utility\Utility::create_option("time_zones","id","time_zone",auth()->guard('admin')->user()->time_zone_id) }}
                                    @endif

                                     @if(isset(auth()->guard('company')->user()->com_name))

                                     {{ \App\Utility\Utility::create_option("time_zones","id","time_zone",auth()->guard('company')->user()->com_time_zone) }}

                                     @endif

                                </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn ripple btn-primary" type="submit">Submit</button>
                    <button class="btn ripple btn-secondary" data-dismiss="modal" type="button">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /main-header -->
