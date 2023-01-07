<?php

$role_id = auth()->guard('admin')->user()->admin_role;

if($role_id == ''){?>
<!-- main-sidebar -->
<style>
    body{
        background: #efefef !important;
    }
    .header-icon-svgs{
        color: black !important;
    }
    @media (max-width: 414px){
        div.dataTables_wrapper div.dataTables_filter input {
             margin-left: 0px;
        }
        .btn-style {
            position: relative;
            z-index: 1;
            top: 0px;
        }
    }
    .side-menu__label{
        color: black !important;
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
<div class="app-sidebar__overlay" data-toggle="sidebar"></div>
<aside class="app-sidebar sidebar-scroll">
    <div class="main-sidebar-header active">
        <a class="desktop-logo logo-light active" href="{{ route('admin::admin') }}">
            <img src="{{URL::asset('assets/img/brand/logo.png')}}" class="main-logo" alt="logo">
        </a>
        <a class="desktop-logo logo-dark active" href="{{ route('admin::admin') }}">
            <img src="{{URL::asset('assets/img/brand/logo.png')}}" class="main-logo dark-theme" alt="logo">
        </a>
        <a class="logo-icon mobile-logo icon-light active" href="{{ route('admin::admin') }}">
            <img src="{{URL::asset('assets/img/brand/logo.png')}}" class="logo-icon" alt="logo">
        </a>
        <a class="logo-icon mobile-logo icon-dark active" href="{{ route('admin::admin') }}">
            <img src="{{URL::asset('assets/img/brand/logo.png')}}" class="logo-icon dark-theme" alt="logo">
        </a>
    </div>

    <div class="main-sidemenu">
    <!-- <div class="app-sidebar__user clearfix">
            <div class="dropdown user-pro-body">
                <div class="">
                    <img alt="user-img" class="avatar avatar-xl brround"
                         src="{{ URL::asset(auth()->guard('admin')->user()->image) }}"><span
                        class="avatar-status profile-status bg-green"></span>
                </div>
                <div class="user-info">
                    <h4 class="font-weight-semibold mt-3 mb-0">{{  auth()->guard('admin')->user()->name }}</h4>
                    @php /*<span class="mb-0 text-muted">Premium Member</span>*/ @endphp
        </div>
    </div>
</div> -->
        <ul class="side-menu">
            <li class="side-item side-item-category" style="padding-top: 8px;">Main</li>
            <li class="slide">
                <a class="side-menu__item" href="{{ route('admin::admin') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24">
                        <path d="M0 0h24v24H0V0z" fill="none"/>
                        <path d="M5 5h4v6H5zm10 8h4v6h-4zM5 17h4v2H5zM15 5h4v2h-4z" opacity=".3"/>
                        <path
                            d="M3 13h8V3H3v10zm2-8h4v6H5V5zm8 16h8V11h-8v10zm2-8h4v6h-4v-6zM13 3v6h8V3h-8zm6 4h-4V5h4v2zM3 21h8v-6H3v6zm2-4h4v2H5v-2z"/>
                    </svg>
                    <span class="side-menu__label">Dashboard</span>
                </a>
            </li>

            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="{{ url('/' . $page='#') }}">
                    <i class="fa fa-building side-menu__icon"></i>
                    <span class="side-menu__label">Company</span><i class="angle fe fe-chevron-down"></i></a>
                <ul class="slide-menu">
                    <li><a class="slide-item" href="{{ route('admin::company.index') }}">Company List</a></li>
                </ul>
            </li>

            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="{{ url('/' . $page='#') }}">
                    <i class="fa fa-user-secret side-menu__icon"></i>
                    <span class="side-menu__label">Admin</span><i class="angle fe fe-chevron-down"></i></a>
                <ul class="slide-menu">
                    <li><a class="slide-item" href="{{ route('admin::admins.index') }}">Admin List</a></li>
                </ul>
            </li>

            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="{{ url('/' . $page='#') }}">
                    <i class="fa fa-users side-menu__icon"></i>
                    <span class="side-menu__label">Passengers</span><i class="angle fe fe-chevron-down"></i></a>
                <ul class="slide-menu">
                    <li><a class="slide-item" href="{{ route('admin::passenger.index') }}">Passengers List</a></li>
                </ul>
            </li>

            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="{{ url('/' . $page='#') }}">
                    <i class="fa fa-user-tie side-menu__icon"></i>
                    <span class="side-menu__label">Driver</span><i class="angle fe fe-chevron-down"></i></a>
                <ul class="slide-menu">
                    <li><a class="slide-item" href="{{ route('admin::driverStatus.index') }}">Driver Status</a></li>
                    <li><a class="slide-item" href="{{ route('admin::driver_list.index') }}">Driver List</a></li>
                    <li><a class="slide-item" href="{{ route('admin::driverStatusReport.index') }}">Driver Status Report</a></li>
                </ul>
            </li>
            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="{{ url('/' . $page='#') }}">
                    <i class="fa fa-car side-menu__icon"></i>
                    <span class="side-menu__label">Transport</span><i class="angle fe fe-chevron-down"></i></a>
                <ul class="slide-menu">
                    <li><a class="slide-item" href="{{ route('admin::transportType.index') }}">Transport Type</a></li>

                    <li><a class="slide-item" href="{{ route('admin::transportMake.index') }}">Transport Make</a></li>

                    <li><a class="slide-item" href="{{ route('admin::transportModel.index') }}">Transport Model</a></li>

                    <li><a class="slide-item" href="{{ route('admin::transportModelColor.index') }}">Transport Model
                            Color</a></li>
                    <li><a class="slide-item" href="{{ route('admin::transportModelYear.index') }}">Transport Model
                            Year</a></li>
                    <li><a class="slide-item" href="{{ route('admin::transportFuel.index') }}">Transport Fuel Type</a>
                    </li>
                </ul>
            </li>
            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="{{ url('/' . $page='#') }}">
                    <i class="fa fa-bell side-menu__icon"></i>
                    <span class="side-menu__label">Notifications</span><i class="angle fe fe-chevron-down"></i></a>
                <ul class="slide-menu">
                    <li><a class="slide-item" href="{{ route('admin::appNotification.index') }}">Notifications List</a>
                    </li>
                </ul>
            </li>

            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="{{ url('/' . $page='#') }}">
                    <i class="fa fa-coins side-menu__icon"></i>
                    <span class="side-menu__label">Earning Analysis Reports</span><i
                        class="angle fe fe-chevron-down"></i></a>
                <ul class="slide-menu">
                    <li><a class="slide-item" href="{{ route('admin::earningAnalysis.index') }}">Invoices Details</a>
                    </li>
                    <li><a class="slide-item" href="{{ route('admin::dailyEarning.index') }}">Daily Earnings</a></li>
                </ul>
            </li>

            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="{{ url('/' . $page='#') }}">
                    <i class="fa fa-bell side-menu__icon"></i>
                    <span class="side-menu__label">Invoice Plans</span><i class="angle fe fe-chevron-down"></i></a>
                <ul class="slide-menu">
                    <li><a class="slide-item" href="{{ route('admin::farePlanHead.index') }}">Fare Plan</a></li>
                    <li><a class="slide-item" href="{{ route('admin::InvoicePlan.index') }}">Bank Commission</a></li>
                </ul>
            </li>

            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="{{ url('/' . $page='#') }}">
                    <i class="fa fa-credit-card side-menu__icon"></i>
                    <span class="side-menu__label">Payments</span><i class="angle fe fe-chevron-down"></i></a>
                <ul class="slide-menu">
                    <li><a class="slide-item" href="{{ route('admin::creditCards.index') }}">Cards</a></li>
                    <li><a class="slide-item" href="{{ route('admin::promoCode.index') }}">Promo Codes</a></li>
                    <li><a class="slide-item" href="{{ route('admin::voucher.index') }}">Vouchers</a></li>
                    <li><a class="slide-item" href="{{ route('admin::passenger_payments.index') }}">Passenger
                            Payments</a></li>
                </ul>
            </li>

            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="{{ url('/' . $page='#') }}">
                    <i class="fa fa-file-alt side-menu__icon"></i>
                    <span class="side-menu__label">Ledger</span><i
                        class="angle fe fe-chevron-down"></i></a>
                <ul class="slide-menu">
                    <li><a class="slide-item" href="{{ url('admin/ledgerReport')}}">Ledger Reports</a></li>

                </ul>
            </li>


            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="{{ url('/' . $page='#') }}">
                    <i class="fa fa-car-side side-menu__icon"></i>
                    <span class="side-menu__label">Ride Booking</span><i class="angle fe fe-chevron-down"></i></a>
                <ul class="slide-menu">
                    <li><a class="slide-item" href="{{ route('admin::upcomingScheduleRides.index') }}">Upcoming Schedule Rides</a></li>
                    <li><a class="slide-item" href="{{ route('admin::inProgressScheduleRides.index') }}">Inprogress Rides</a></li>

                    <li><a class="slide-item" href="{{ route('admin::rideBookingSchedule.index') }}">Ride Booking
                            Schedule</a></li>
                    <li><a class="slide-item" href="{{ route('admin::rideIgnoredByDriver.index') }}">Ride Ignored By
                            Driver</a></li>
                    <li><a class="slide-item" href="{{ route('admin::rideCancelByPassenger.index') }}">Ride Cancel By
                            Passenger</a></li>
                    <li><a class="slide-item" href="{{ route('admin::rideCancelByDriver.index') }}">Ride Cancel By
                            Driver</a></li>


                    <li><a class="slide-item" href="{{ route('admin::rideStatisticsPassenger.index') }}">Ride Statistics
                            By Passenger</a></li>
                    <li><a class="slide-item" href="{{ route('admin::rideStatisticsDriver.index') }}">Ride Statistics By
                            Driver</a></li>
                </ul>
            </li>

            <li class="side-item side-item-category">Settings</li>

            <li class="slide">
                <a class="side-menu__item" href="{{ route('admin::country.index') }}">
                    <i class="fa fa-globe-europe side-menu__icon"></i>
                    <span class="side-menu__label">Countries</span>
                </a>
            </li>

            <li class="slide">
                <a class="side-menu__item" href="{{ route('admin::paymentSettings.index') }}">
                    <i class="fa fa-credit-card side-menu__icon"></i>
                    <span class="side-menu__label">Payment Gateway</span>
                </a>
            </li>

            <li class="slide">
                <a class="side-menu__item" href="{{ route('admin::geo_fencing.index') }}">
                    <i class="fa fa-map-marker-alt side-menu__icon"></i>
                    <span class="side-menu__label">Geo Fencing</span>
                </a>
            </li>

            <li class="slide">
                <a class="side-menu__item" href="{{ route('admin::EmailSettings.index') }}">
                    <i class="fa fa-envelope side-menu__icon"></i>
                    <span class="side-menu__label">Email Settings</span>
                </a>
            </li>

            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="{{ url('/' . $page='#') }}">
                    <i class="fa fa-sliders-h side-menu__icon"></i>
                    <span class="side-menu__label">Administration</span><i class="angle fe fe-chevron-down"></i></a>
                <ul class="slide-menu">
                    <li><a class="slide-item" href="{{ route('admin::roles.index') }}">Roles</a></li>
                    <li><a class="slide-item" href="{{ route('admin::permissions.index') }}">Permissions</a></li>
                </ul>
            </li>

            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="{{ url('/' . $page='#') }}">
                    <i class="fe fe-aperture side-menu__icon"></i>
                    <span class="side-menu__label">Ref-Module Admin.</span><i class="angle fe fe-chevron-down"></i></a>
                <ul class="slide-menu">
                    <li><a class="slide-item" href="{{ route('admin::referenceModule.index') }}">Module Reference</a>
                    </li>
                    <li><a class="slide-item" href="{{ route('admin::appReference.index') }}">App Reference</a></li>
                    <li><a class="slide-item" href="{{ route('admin::referenceType.index') }}">Reference Type</a></li>
                </ul>
            </li>

            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="{{ url('/' . $page='#') }}">
                    <i class="fa fa-language side-menu__icon"></i>
                    <span class="side-menu__label">Languages Admin.</span><i class="angle fe fe-chevron-down"></i></a>
                <ul class="slide-menu">
                    <li><a class="slide-item" href="{{ route('admin::languages.index') }}">Languages</a></li>
                    <li><a class="slide-item" href="{{ route('admin::languageString.index') }}">Language Strings</a>
                    </li>
                    <li><a class="slide-item" href="{{ route('admin::languageScreen.index') }}">Language Screens</a>
                    </li>
                </ul>
            </li>

            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="{{ url('/' . $page='#') }}">
                    <i class="fa fa-cogs side-menu__icon"></i>
                    <span class="side-menu__label">Base App Admin.</span><i class="angle fe fe-chevron-down"></i></a>
                <ul class="slide-menu">
                    <li><a class="slide-item" href="{{ route('admin::BaseDefaultImage.index') }}">App Default Images</a>
                    </li>
                    <li><a class="slide-item" href="{{ route('admin::BaseAppTheme.index') }}">App Themes</a></li>
                    <li><a class="slide-item" href="{{ route('admin::BaseAppThemeDesign.index') }}">App Themes
                            Design</a></li>
                    <li><a class="slide-item" href="{{ route('admin::BaseAppControl.index') }}">App Controls</a></li>
                    <li><a class="slide-item" href="{{ route('admin::SMTPSetting.index') }}">App SMTP Setting</a></li>
                    <li><a class="slide-item" href="{{ route('admin::FCMSetting.index') }}">App FCM Setting</a></li>
                    <li><a class="slide-item" href="{{ route('admin::BaseAppSocialLink.index') }}">App Social Links</a>
                    </li>
                    <li><a class="slide-item" href="{{ route('admin::currencies.index') }}">Currency</a></li>
                </ul>
            </li>
            <li class="slide">
                <a class="side-menu__item" href="{{ route('admin::page.index') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24">
                        <path d="M0 0h24v24H0V0z" fill="none"></path>
                        <path d="M13 4H6v16h12V9h-5V4zm3 14H8v-2h8v2zm0-6v2H8v-2h8z" opacity=".3"></path>
                        <path
                            d="M8 16h8v2H8zm0-4h8v2H8zm6-10H6c-1.1 0-2 .9-2 2v16c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm4 18H6V4h7v5h5v11z"></path>
                    </svg>
                    <span class="side-menu__label">Pages</span>
                </a>
            </li>

            <li class="slide">
                <a class="side-menu__item" href="{{ route('admin::webpage.index') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24">
                        <path d="M0 0h24v24H0V0z" fill="none"></path>
                        <path d="M13 4H6v16h12V9h-5V4zm3 14H8v-2h8v2zm0-6v2H8v-2h8z" opacity=".3"></path>
                        <path
                            d="M8 16h8v2H8zm0-4h8v2H8zm6-10H6c-1.1 0-2 .9-2 2v16c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm4 18H6V4h7v5h5v11z"></path>
                    </svg>
                    <span class="side-menu__label">Web Pages</span>
                </a>
            </li>

        </ul>
    </div>
</aside>
<!-- main-sidebar -->

<?php }else{
// other user permission
?>

<!-- main-sidebar -->
<div class="app-sidebar__overlay" data-toggle="sidebar"></div>
<aside class="app-sidebar sidebar-scroll">
    <div class="main-sidebar-header active">
        <a class="desktop-logo logo-light active" href="{{ route('admin::admin') }}">
            <img src="{{URL::asset('assets/img/brand/logo.png')}}" class="main-logo" alt="logo">
        </a>
        <a class="desktop-logo logo-dark active" href="{{ route('admin::admin') }}">
            <img src="{{URL::asset('assets/img/brand/logo.png')}}" class="main-logo dark-theme" alt="logo">
        </a>
        <a class="logo-icon mobile-logo icon-light active" href="{{ route('admin::admin') }}">
            <img src="{{URL::asset('assets/img/brand/logo.png')}}" class="logo-icon" alt="logo">
        </a>
        <a class="logo-icon mobile-logo icon-dark active" href="{{ route('admin::admin') }}">
            <img src="{{URL::asset('assets/img/brand/logo.png')}}" class="logo-icon dark-theme" alt="logo">
        </a>
    </div>

    <div class="main-sidemenu">
    <!-- <div class="app-sidebar__user clearfix">
            <div class="dropdown user-pro-body">
                <div class="">
                    <img alt="user-img" class="avatar avatar-xl brround"
                         src="{{ URL::asset(auth()->guard('admin')->user()->image) }}"><span
                        class="avatar-status profile-status bg-green"></span>
                </div>
                <div class="user-info">
                    <h4 class="font-weight-semibold mt-3 mb-0">{{  auth()->guard('admin')->user()->name }}</h4>
                    @php /*<span class="mb-0 text-muted">Premium Member</span>*/ @endphp
        </div>
    </div>
</div> -->


        <ul class="side-menu">
            <li class="side-item side-item-category">Main</li>
            @if ( \App\Utility\Utility::has_permission('admin::admin',auth()->guard('admin')->user()->admin_role))
                <li class="slide">
                    <a class="side-menu__item" href="{{ route('admin::admin') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24">
                            <path d="M0 0h24v24H0V0z" fill="none"/>
                            <path d="M5 5h4v6H5zm10 8h4v6h-4zM5 17h4v2H5zM15 5h4v2h-4z" opacity=".3"/>
                            <path
                                d="M3 13h8V3H3v10zm2-8h4v6H5V5zm8 16h8V11h-8v10zm2-8h4v6h-4v-6zM13 3v6h8V3h-8zm6 4h-4V5h4v2zM3 21h8v-6H3v6zm2-4h4v2H5v-2z"/>
                        </svg>
                        <span class="side-menu__label">Dashboard</span>
                    </a>
                </li>
            @endif

            @if ( \App\Utility\Utility::has_permission('admin::company.index',auth()->guard('admin')->user()->admin_role))
                <li class="slide">
                    <a class="side-menu__item" data-toggle="slide" href="{{ url('/' . $page='#') }}">
                        <i class="fa fa-user side-menu__icon"></i>
                        <span class="side-menu__label">Company</span><i class="angle fe fe-chevron-down"></i></a>
                    <ul class="slide-menu">
                        @if ( \App\Utility\Utility::has_permission('admin::company.index',auth()->guard('admin')->user()->admin_role))
                            <li><a class="slide-item" href="{{ route('admin::company.index') }}">Company List</a></li>
                        @endif
                    </ul>
                </li>
            @endif

            @if ( \App\Utility\Utility::has_permission('admin::passenger.index',auth()->guard('admin')->user()->admin_role))
                <li class="slide">
                    <a class="side-menu__item" data-toggle="slide" href="{{ url('/' . $page='#') }}">
                        <i class="fa fa-user side-menu__icon"></i>
                        <span class="side-menu__label">Passengers</span><i class="angle fe fe-chevron-down"></i></a>
                    <ul class="slide-menu">
                        <li><a class="slide-item" href="{{ route('admin::passenger.index') }}">Passengers List</a></li>
                    </ul>
                </li>
            @endif

            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="{{ url('/' . $page='#') }}">
                    <i class="fa fa-user side-menu__icon"></i>
                    <span class="side-menu__label">Transport</span><i class="angle fe fe-chevron-down"></i></a>
                <ul class="slide-menu">
                    @if ( \App\Utility\Utility::has_permission('admin::transportType.index',auth()->guard('admin')->user()->admin_role))
                        <li><a class="slide-item" href="{{ route('admin::transportType.index') }}">Transport Type</a>
                        </li>
                    @endif

                    @if ( \App\Utility\Utility::has_permission('admin::transportMake.index',auth()->guard('admin')->user()->admin_role))
                        <li><a class="slide-item" href="{{ route('admin::transportMake.index') }}">Transport Make</a>
                        </li>
                    @endif

                    @if ( \App\Utility\Utility::has_permission('admin::transportModel.index',auth()->guard('admin')->user()->admin_role))
                        <li><a class="slide-item" href="{{ route('admin::transportModel.index') }}">Transport Model</a>
                        </li>
                    @endif

                    @if ( \App\Utility\Utility::has_permission('admin::transportModelColor.index',auth()->guard('admin')->user()->admin_role))
                        <li><a class="slide-item" href="{{ route('admin::transportModelColor.index') }}">Transport Model
                                Color</a></li>
                    @endif

                    @if ( \App\Utility\Utility::has_permission('admin::transportModelYear.index',auth()->guard('admin')->user()->admin_role))
                        <li><a class="slide-item" href="{{ route('admin::transportModelYear.index') }}">Transport Model
                                Year</a></li>
                    @endif

                    @if ( \App\Utility\Utility::has_permission('admin::transportFuel.index',auth()->guard('admin')->user()->admin_role))
                        <li><a class="slide-item" href="{{ route('admin::transportFuel.index') }}">Transport Fuel
                                Type</a></li>
                    @endif

                    @if ( \App\Utility\Utility::has_permission('admin::farePlanHead.index',auth()->guard('admin')->user()->admin_role))
                        <li><a class="slide-item" href="{{ route('admin::farePlanHead.index') }}">Fare Plan</a></li>
                    @endif
                </ul>
            </li>


            @if ( \App\Utility\Utility::has_permission('admin::appNotification.index',$role_id))
                <li class="slide">
                    <a class="side-menu__item" data-toggle="slide" href="{{ url('/' . $page='#') }}">
                        <i class="fa fa-bell side-menu__icon"></i>
                        <span class="side-menu__label">Notifications</span><i class="angle fe fe-chevron-down"></i></a>
                    <ul class="slide-menu">
                        <li><a class="slide-item" href="{{ route('admin::appNotification.index') }}">Notifications
                                List</a></li>
                    </ul>
                </li>
            @endif

            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="{{ url('/' . $page='#') }}">
                    <i class="fa fa-bell side-menu__icon"></i>
                    <span class="side-menu__label">Earning Analysis Reports</span><i
                        class="angle fe fe-chevron-down"></i></a>
                <ul class="slide-menu">
                    @if ( \App\Utility\Utility::has_permission('admin::earningAnalysis.index',$role_id))
                        <li><a class="slide-item" href="{{ route('admin::earningAnalysis.index') }}">Invoices
                                Details</a></li>
                    @endif

                    @if ( \App\Utility\Utility::has_permission('admin::dailyEarning.index',$role_id))
                        <li><a class="slide-item" href="{{ route('admin::dailyEarning.index') }}">Daily Earnings</a>
                        </li>
                    @endif @if ( \App\Utility\Utility::has_permission('admin::InvoicePlan.index',$role_id))
                        <li><a class="slide-item" href="{{ route('admin::InvoicePlan.index') }}">Invoices Plan</a></li>
                    @endif
                </ul>
            </li>

            @if ( \App\Utility\Utility::has_permission('admin::creditCards.index',$role_id))
                <li class="slide">
                    <a class="side-menu__item" data-toggle="slide" href="{{ url('/' . $page='#') }}">
                        <i class="fa fa-credit-card side-menu__icon"></i>
                        <span class="side-menu__label">Payments</span><i class="angle fe fe-chevron-down"></i></a>
                    <ul class="slide-menu">
                        <li><a class="slide-item" href="{{ route('admin::creditCards.index') }}">Cards</a></li>
                    </ul>
                </li>
            @endif

            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="{{ url('/' . $page='#') }}">
                    <i class="fa fa-bell side-menu__icon"></i>
                    <span class="side-menu__label">Ride Booking</span><i class="angle fe fe-chevron-down"></i></a>
                <ul class="slide-menu">
                    @if ( \App\Utility\Utility::has_permission('admin::rideBookingSchedule.index',$role_id))
                        <li><a class="slide-item" href="{{ route('admin::rideBookingSchedule.index') }}">Ride Booking
                                Schedule</a></li>
                    @endif

                    @if ( \App\Utility\Utility::has_permission('admin::rideIgnoredByDriver.index',$role_id))
                        <li><a class="slide-item" href="{{ route('admin::rideIgnoredByDriver.index') }}">Ride Ignored By
                                Driver</a></li>
                    @endif

                    @if ( \App\Utility\Utility::has_permission('admin::rideCancelByPassenger.index',$role_id))
                        <li><a class="slide-item" href="{{ route('admin::rideCancelByPassenger.index') }}">Ride Cancel
                                By Passenger</a></li>
                    @endif

                    @if ( \App\Utility\Utility::has_permission('admin::rideCancelByDriver.index',$role_id))
                        <li><a class="slide-item" href="{{ route('admin::rideCancelByDriver.index') }}">Ride Cancel By
                                Driver</a></li>
                    @endif


                    @if ( \App\Utility\Utility::has_permission('admin::rideStatisticsPassenger.index',$role_id))
                        <li><a class="slide-item" href="{{ route('admin::rideStatisticsPassenger.index') }}">Ride
                                Statistics By Passenger</a></li>
                    @endif

                    @if ( \App\Utility\Utility::has_permission('admin::rideStatisticsDriver.index',$role_id))
                        <li><a class="slide-item" href="{{ route('admin::rideStatisticsDriver.index') }}">Ride
                                Statistics By Driver</a></li>
                    @endif
                </ul>
            </li>

            <li class="side-item side-item-category">Settings</li>

            @if ( \App\Utility\Utility::has_permission('admin::country.index',$role_id))
                <li class="slide">
                    <a class="side-menu__item" href="{{ route('admin::country.index') }}">
                        <i class="fa fa-globe-europe side-menu__icon"></i>
                        <span class="side-menu__label">Countries</span>
                    </a>
                </li>
            @endif


            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="{{ url('/' . $page='#') }}">
                    <i class="fa fa-sliders-h side-menu__icon"></i>
                    <span class="side-menu__label">Administration</span><i class="angle fe fe-chevron-down"></i></a>
                <ul class="slide-menu">
                    @if ( \App\Utility\Utility::has_permission('admin::roles.index',$role_id))
                        <li><a class="slide-item" href="{{ route('admin::roles.index') }}">Roles</a></li>
                    @endif

                    @if ( \App\Utility\Utility::has_permission('admin::permissions.index',$role_id))
                        <li><a class="slide-item" href="{{ route('admin::permissions.index') }}">Permissions</a></li>
                    @endif
                </ul>
            </li>

            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="{{ url('/' . $page='#') }}">
                    <i class="fe fe-aperture side-menu__icon"></i>
                    <span class="side-menu__label">Ref-Module Admin.</span><i class="angle fe fe-chevron-down"></i></a>
                <ul class="slide-menu">
                    @if ( \App\Utility\Utility::has_permission('admin::referenceModule.index',$role_id))
                        <li><a class="slide-item" href="{{ route('admin::referenceModule.index') }}">Module
                                Reference</a></li>
                    @endif

                    @if ( \App\Utility\Utility::has_permission('admin::appReference.index',$role_id))
                        <li><a class="slide-item" href="{{ route('admin::appReference.index') }}">App Reference</a></li>
                    @endif

                    @if ( \App\Utility\Utility::has_permission('admin::referenceType.index',$role_id))
                        <li><a class="slide-item" href="{{ route('admin::referenceType.index') }}">Reference Type</a>
                        </li>
                    @endif
                </ul>
            </li>

            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="{{ url('/' . $page='#') }}">
                    <i class="fa fa-language side-menu__icon"></i>
                    <span class="side-menu__label">Languages Admin.</span><i class="angle fe fe-chevron-down"></i></a>
                <ul class="slide-menu">
                    @if ( \App\Utility\Utility::has_permission('admin::languages.index',$role_id))
                        <li><a class="slide-item" href="{{ route('admin::languages.index') }}">Languages</a></li>
                    @endif

                    @if ( \App\Utility\Utility::has_permission('admin::languageString.index',$role_id))
                        <li><a class="slide-item" href="{{ route('admin::languageString.index') }}">Language Strings</a>
                        </li>
                    @endif

                    @if ( \App\Utility\Utility::has_permission('admin::languageScreen.index',$role_id))
                        <li><a class="slide-item" href="{{ route('admin::languageScreen.index') }}">Language Screens</a>
                        </li>
                    @endif
                </ul>
            </li>

            <li class="slide">
                <a class="side-menu__item" data-toggle="slide" href="{{ url('/' . $page='#') }}">
                    <i class="fa fa-sliders-h side-menu__icon"></i>
                    <span class="side-menu__label">Base App Admin.</span><i class="angle fe fe-chevron-down"></i></a>
                <ul class="slide-menu">
                    @if ( \App\Utility\Utility::has_permission('admin::BaseDefaultImage.index',$role_id))
                        <li><a class="slide-item" href="{{ route('admin::BaseDefaultImage.index') }}">App Default
                                Images</a></li>
                    @endif

                    @if ( \App\Utility\Utility::has_permission('admin::BaseAppTheme.index',$role_id))
                        <li><a class="slide-item" href="{{ route('admin::BaseAppTheme.index') }}">App Themes</a></li>
                    @endif

                    @if ( \App\Utility\Utility::has_permission('admin::BaseAppThemeDesign.index',$role_id))
                        <li><a class="slide-item" href="{{ route('admin::BaseAppThemeDesign.index') }}">App Themes
                                Design</a></li>
                    @endif

                    @if ( \App\Utility\Utility::has_permission('admin::BaseAppControl.index',$role_id))
                        <li><a class="slide-item" href="{{ route('admin::BaseAppControl.index') }}">App Controls</a>
                        </li>
                    @endif

                    @if ( \App\Utility\Utility::has_permission('admin::SMTPSetting.index',$role_id))
                        <li><a class="slide-item" href="{{ route('admin::SMTPSetting.index') }}">App SMTP Setting</a>
                        </li>
                    @endif

                    @if ( \App\Utility\Utility::has_permission('admin::FCMSetting.index',$role_id))
                        <li><a class="slide-item" href="{{ route('admin::FCMSetting.index') }}">App FCM Setting</a></li>
                    @endif

                    @if ( \App\Utility\Utility::has_permission('admin::BaseAppSocialLink.index',$role_id))
                        <li><a class="slide-item" href="{{ route('admin::BaseAppSocialLink.index') }}">App Social
                                Links</a></li>
                    @endif
                </ul>
            </li>

            @if ( \App\Utility\Utility::has_permission('admin::page.index',$role_id))
                <li class="slide">
                    <a class="side-menu__item" href="{{ route('admin::page.index') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24">
                            <path d="M0 0h24v24H0V0z" fill="none"></path>
                            <path d="M13 4H6v16h12V9h-5V4zm3 14H8v-2h8v2zm0-6v2H8v-2h8z" opacity=".3"></path>
                            <path
                                d="M8 16h8v2H8zm0-4h8v2H8zm6-10H6c-1.1 0-2 .9-2 2v16c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm4 18H6V4h7v5h5v11z"></path>
                        </svg>
                        <span class="side-menu__label">Pages</span>
                    </a>
                </li>
            @endif

        </ul>
    </div>
</aside>
<!-- main-sidebar -->
<?php } ?>
