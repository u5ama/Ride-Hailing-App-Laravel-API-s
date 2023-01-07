<style>
    body{
        background: #efefef !important;
    }
    .side-menu .side-menu__icon{
        color: black;
        fill: black;
    }
    .angle {
        color: black !important;
    }
    .header-icon{
        color: black !important;
    }
    .main-sidebar-header{
        border-bottom: 1px solid white !important;
        border-right: 1px solid white !important;
    }
    </style>
<!-- main-sidebar -->
<div class="app-sidebar__overlay" data-toggle="sidebar"></div>
<aside class="app-sidebar sidebar-scroll">
    <div class="main-sidebar-header active">
        <a class="desktop-logo logo-light active" href="{{ route('company::company') }}">
            <img src="{{URL::asset('assets/img/brand/logo.png')}}" class="main-logo" alt="logo">
        </a>
        <a class="desktop-logo logo-dark active" href="{{ route('company::company') }}">
            <img src="{{URL::asset('assets/img/brand/logo.png')}}" class="main-logo dark-theme" alt="logo">
        </a>
        <a class="logo-icon mobile-logo icon-light active" href="{{ route('company::company') }}">
            <img src="{{URL::asset('assets/img/brand/logo.png')}}" class="logo-icon" alt="logo">
        </a>
        <a class="logo-icon mobile-logo icon-dark active" href="{{ route('company::company') }}">
            <img src="{{URL::asset('assets/img/brand/logo.png')}}" class="logo-icon dark-theme" alt="logo">
        </a>
    </div>

    <div class="main-sidemenu">
        <div class="app-sidebar__user clearfix">
            <div class="dropdown user-pro-body">
                <div class="">

                </div>
                <div class="user-info">
                    <h4 class="font-weight-semibold mt-3 mb-0">{{ auth()->guard('company')->user()->name }}</h4>
                    @php /*<span class="mb-0 text-muted">Premium Member</span>*/ @endphp
                </div>
            </div>
        </div>
        <ul class="side-menu">
            <li class="side-item side-item-category">{{ config('languageString.main_title') }}</li>
            <li class="slide">
                <a class="side-menu__item" href="{{ url('company/company') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24">
                        <path d="M0 0h24v24H0V0z" fill="none"/>
                        <path d="M5 5h4v6H5zm10 8h4v6h-4zM5 17h4v2H5zM15 5h4v2h-4z" opacity=".3"/>
                        <path
                            d="M3 13h8V3H3v10zm2-8h4v6H5V5zm8 16h8V11h-8v10zm2-8h4v6h-4v-6zM13 3v6h8V3h-8zm6 4h-4V5h4v2zM3 21h8v-6H3v6zm2-4h4v2H5v-2z"/>
                    </svg>
                    <span class="side-menu__label">{{ config('languageString.dashboard') }}</span>
                </a>
            </li>
            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="{{ url('/' . $page='#') }}">
                    <i class="fa fa-user-tie side-menu__icon"></i>
                    <span class="side-menu__label">{{ config('languageString.drivers_menu_title') }}</span><i class="angle fe fe-chevron-down"></i></a>
                <ul class="slide-menu">
                    <li><a class="slide-item" href="{{ url('company/driver') }}">{{ config('languageString.drivers_list_title') }}</a></li>
                    <li><a class="slide-item" href="{{ url('company/driverStatus') }}">{{ config('languageString.drivers_status_title') }}</a></li>
                    <li><a class="slide-item" href="{{ route('company::driverStatusReport.index') }}">{{ config('languageString.drivers_status_report') }}</a></li>
                </ul>
            </li>

            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="{{ url('/' . $page='#') }}">
                    <i class="fa fa-coins side-menu__icon"></i>
                    <span class="side-menu__label">{{ config('languageString.earning_analysis_reports') }}</span><i
                        class="angle fe fe-chevron-down"></i></a>
                <ul class="slide-menu">
                    <li><a class="slide-item" href="{{ url('company/earningAnalysis')}}">{{ config('languageString.invoices_details') }}</a></li>
                    <li><a class="slide-item" href="{{ url('company/dailyEarning')}}">{{ config('languageString.daily_earnings') }}</a></li>
                </ul>
            </li>
            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="{{ url('/' . $page='#') }}">
                    <i class="fa fa-car-side side-menu__icon"></i>
                    <span class="side-menu__label">{{ config('languageString.ride_booking_menu_title') }}</span><i class="angle fe fe-chevron-down"></i></a>
                <ul class="slide-menu">
                    <li><a class="slide-item" href="{{ route('company::upcomingScheduleRides.index') }}">{{ config('languageString.upcoming_schedule_rides') }}</a></li>
                    <li><a class="slide-item" href="{{ route('company::inProgressScheduleRides.index') }}">{{ config('languageString.inprogress_rides') }}</a></li>
                    <li><a class="slide-item" href="{{ route('company::rideBookingSchedule.index') }}">{{ config('languageString.ride_booking_schedule') }}</a></li>
                </ul>
            </li>
            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="{{ url('/' . $page='#') }}">
                    <i class="fa fa-file-alt side-menu__icon"></i>
                    <span class="side-menu__label">{{ config('languageString.ledger_menu_title') }}</span><i
                        class="angle fe fe-chevron-down"></i></a>
                <ul class="slide-menu">
                    <li><a class="slide-item" href="{{ url('company/ledgerReport')}}">{{ config('languageString.ledger_reports') }}</a></li>

                </ul>
            </li>
        </ul>
    </div>
</aside>
<!-- main-sidebar -->
