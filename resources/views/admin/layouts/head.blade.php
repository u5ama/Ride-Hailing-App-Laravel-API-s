<!-- Title -->
<title> Ride Whipp </title>
<!-- Favicon -->
<link rel="icon" href="{{URL::asset('assets/img/brand/logo.png')}}" type="image/x-icon"/>
<!-- Icons css -->
<link href="{{URL::asset('assets/css/icons.css')}}" rel="stylesheet">
<!--  Custom Scroll bar-->
<link href="{{URL::asset('assets/plugins/mscrollbar/jquery.mCustomScrollbar.css')}}" rel="stylesheet"/>
<!--  Right-sidemenu css -->
<link href="{{URL::asset('assets/plugins/sidebar/sidebar.css')}}" rel="stylesheet">
<!-- Sidemenu css -->
<link href="{{URL::asset('assets/plugins/sweet-alert/sweetalert.css') }}" rel="stylesheet">
<link href="{{URL::asset('assets/plugins/select2/css/select2.min.css')}}" rel="stylesheet">
<link href="{{URL::asset('assets/plugins/fileuploads/css/fileupload.css')}}" rel="stylesheet" type="text/css"/>
@yield('css')
<!-- Maps css -->
<link href="{{URL::asset('assets/plugins/notify/css/notifIt.css') }}" rel="stylesheet"/>
<link href="{{URL::asset('assets/plugins/sweet-alert/sweetalert.css') }}" rel="stylesheet">
<link href="{{URL::asset('assets/plugins/bootstrap-switch-master/dist/css/bootstrap4/bootstrap-switch.css') }}"
      rel="stylesheet">
<link href="{{URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet"/>
<link href="{{URL::asset('assets/plugins/datatable/css/responsive.bootstrap4.min.css') }}" rel="stylesheet"/>
<link href="{{URL::asset('assets/plugins/datatable/css/jquery.dataTables.min.css')}}" rel="stylesheet">
<link href="{{URL::asset('assets/plugins/datatable/css/responsive.dataTables.min.css')}}" rel="stylesheet">
<link href="{{URL::asset('assets/plugins/jqvmap/jqvmap.min.css')}}" rel="stylesheet">
<link href="{{URL::asset('assets/plugins/telephoneinput/telephoneinput.css')}}" rel="stylesheet">
<!-- style css -->
@guest
    @if(isset(auth()->guard('admin')->user()->locale))
        @if(auth()->guard('admin')->user()->locale=='en')
            @if (!request()->is('/'))
                <link href="{{URL::asset('assets/css/style.css')}}" rel="stylesheet">
            @endif
            <link href="{{URL::asset('assets/css/style-dark.css')}}" rel="stylesheet">
            <link rel="stylesheet" href="{{URL::asset('assets/css/sidemenu.css')}}">
            <link href="{{URL::asset('assets/css/skin-modes.css')}}" rel="stylesheet"/>
        @else
            <link href="{{URL::asset('assets/css-rtl/style.css')}}" rel="stylesheet">
            <link href="{{URL::asset('assets/css-rtl/style-dark.css')}}" rel="stylesheet">
            <link rel="stylesheet" href="{{URL::asset('assets/css-rtl/sidemenu.css')}}">
            <link href="{{URL::asset('assets/css-rtl/skin-modes.css')}}" rel="stylesheet"/>
        @endif
    @elseif(isset(auth()->guard('company')->user()->com_locale))
        @if(auth()->guard('company')->user()->com_locale=='en')
            @if (!request()->is('/'))
                <link href="{{URL::asset('assets/css/style.css')}}" rel="stylesheet">
            @endif
            <link href="{{URL::asset('assets/css/style-dark.css')}}" rel="stylesheet">
            <link rel="stylesheet" href="{{URL::asset('assets/css/sidemenu.css')}}">
            <link href="{{URL::asset('assets/css/skin-modes.css')}}" rel="stylesheet"/>
        @else
            <link href="{{URL::asset('assets/css-rtl/style.css')}}" rel="stylesheet">
            <link href="{{URL::asset('assets/css-rtl/style-dark.css')}}" rel="stylesheet">
            <link rel="stylesheet" href="{{URL::asset('assets/css-rtl/sidemenu.css')}}">
            <link href="{{URL::asset('assets/css-rtl/skin-modes.css')}}" rel="stylesheet"/>
        @endif
    @else
        <link href="{{URL::asset('assets/css/style.css')}}" rel="stylesheet">
        <link href="{{URL::asset('assets/css/style-dark.css')}}" rel="stylesheet">
        <link rel="stylesheet" href="{{URL::asset('assets/css/sidemenu.css')}}">
        <link href="{{URL::asset('assets/css/skin-modes.css')}}" rel="stylesheet"/>
    @endif
@endif
<script src='https://api.mapbox.com/mapbox-gl-js/v2.1.1/mapbox-gl.js'></script>
<link href='https://api.mapbox.com/mapbox-gl-js/v2.1.1/mapbox-gl.css' rel='stylesheet' />

<script type="text/javascript">
        @guest
    var APP_URL = {!! json_encode(url('/admin')) !!};
        @endauth
        @auth('admin')
    var APP_URL = {!! json_encode(url('/admin')) !!};
        @endauth

        @auth('company')
    var APP_URL = {!! json_encode(url('/company')) !!};
    @endauth
</script>
