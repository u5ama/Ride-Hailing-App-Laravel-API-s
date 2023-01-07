<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=0'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="X-UA-Compatible" content="IE=9"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="Description" content="Bootstrap Responsive Admin Web Dashboard HTML5 Template">
    @include('admin.layouts.head')
</head>

@php($class='dark-theme')
@if(Auth::user()->panel_mode==2)
    @php($class='')
@endif
<body class="main-body app sidebar-mini {{ $class }}">
<!-- Loader -->
<div id="global-loader">
    <img src="{{URL::asset('assets/img/loader.svg')}}" class="loader-img" alt="Loader">
</div>
<!-- /Loader -->
@if(Auth::user()->user_type=='admin')
    @include('admin.layouts.main-sidebar')
@else
    @include('admin.layouts.dealer-sidebar')
@endif
<!-- main-content -->
<div class="main-content app-content">
@include('admin.layouts.main-header')
<!-- container -->
    <div class="container-fluid">
@yield('page-header')
@yield('content')

@include('admin.layouts.models')
@include('admin.layouts.footer')
@include('admin.layouts.footer-scripts')
</body>
</html>
