@extends('admin.layouts.master')
@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet"/>

    <style>
        .panel-title > .small, .panel-title > .small > a, .panel-title > a, .panel-title > small, .panel-title > small > a {
            color: inherit;
        }

        [role=button] {
            cursor: pointer;
        }

        .checkmark {
            border-radius: 10px;
        }

        .c-container input:checked ~ .checkmark {
            background-color: #2ecc71;
        }

        .params-panel {
            border: 1px solid #CCC;
            overflow: hidden;
            padding: 15px;
            border-radius: 4px;
        }

        .select2 {
            width: 100% !important;
        }

        .select2-hidden-accessible {
            border: 0 !important;
            clip: rect(0 0 0 0) !important;
            height: 1px !important;
            margin: -1px !important;
            overflow: hidden !important;
            padding: 0 !important;
            position: absolute !important;
            width: 1px !important;
        }

        .form-control {
            background-color: #FFF;
            border-radius: 4px;
            color: #66615b;
            font-size: 14px;
            transition: background-color 0.3s ease 0s;
            padding: 7px 18px;
            height: 35px;
            -webkit-box-shadow: none;
            box-shadow: none;
        }

        .panel-default > .panel-heading {
            padding: 15px 15px;
            background: #FFF;
        }

        .panel-title {
            font-size: 18px;
        }

        .c-container {
            display: block;
            position: relative;
            padding-left: 35px;
            margin-bottom: 23px;
            cursor: pointer;
            font-size: 14px;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        .c-container input {
            position: absolute;
            opacity: 0;
            cursor: pointer;
        }

        .checkmark {
            position: absolute;
            top: 0;
            left: 0;
            height: 20px;
            width: 20px;
            background-color: #bdc3c7;
        }

        .panel {
            margin-bottom: 20px;
            background-color: #fff;
            border: 1px solid transparent;
            border-radius: 4px;
            -webkit-box-shadow: 0 1px 1px rgba(0, 0, 0, .05);
            box-shadow: 0 1px 1px rgba(0, 0, 0, .05);
        }

        .panel-default {
            border-color: #ddd;
        }

        .panel-group .panel {
            margin-bottom: 0;
            border-radius: 4px;
        }

        .clear {
            clear: both;
        }

        .panel-group {
            margin-bottom: 20px;
        }
    </style>
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">Permissions</h4>
                <span class="text-muted mt-1 tx-13 ml-2 mb-0">/ Settings</span>
            </div>
        </div>
    </div>
    <!-- breadcrumb -->
@endsection
@section('content')
    <!-- row opened -->
    <div class="row row-sm">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel panel-default">

                                <div class="panel-body">
                                    <form method="post" class="validate" autocomplete="off"
                                          action="{{ url('admin/permission/store') }}">
                                        {{ csrf_field() }}

                                        <div class="form-group params-panel" style="background: #bdc3c7;">
                                            <label class="control-label">{{ 'Select Permission Role' }}</label>
                                            <select class="form-control select2" onchange="showRole(this);"
                                                    name="role_id" required>
                                                <option value="">{{ 'Select One' }}</option>
                                                {{ \App\Utility\Utility::create_option("permission_roles","id","role_name",$role_id) }}
                                            </select>
                                        </div>
                                        <div class="panel-group clear">

                                            @foreach($permission as $key=>$val)
                                                <div class="panel panel-default">
                                                    <div class="panel-heading">
                                                        <h4 class="panel-title">
                                                            <a role="button" data-toggle="collapse"
                                                               href="#collapse-{{ explode("\\",$key)[4] }}">
                                                                <i class="fa fa-angle-double-right"
                                                                   aria-hidden="true"></i>
                                                                {{ str_replace("Controller","",explode("\\",$key)[4]) }}
                                                            </a>
                                                        </h4>
                                                    </div>
                                                    <div id="collapse-{{ explode("\\",$key)[4] }}"
                                                         class="panel-collapse collapse">
                                                        <div class="panel-body">
                                                            <table>
                                                                @foreach($val as $name => $url)
                                                                    <tr>
                                                                        <td><label
                                                                                class="c-container">{{ str_replace("index","list",$name) }}
                                                                                <input name="permissions[]"
                                                                                       value="{{ $name }}"
                                                                                       type="checkbox" {{ array_search($name,$permission_list) !== FALSE ? "checked" : "" }}><span
                                                                                    class="checkmark"></span></td>
                                                                    </tr>
                                                                @endforeach
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                        <div class="form-group">
                                            <div class="col-md-12">
                                                <button type="submit"
                                                        class="btn btn-primary btn-block">{{ 'Save Permission' }}</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--/div-->
    </div>
    <!-- /row -->
    </div>
    <!-- Container closed -->
    </div>
    <!-- main-content closed -->
@endsection
@section('js')
    {{--    <script src="{{URL::asset('assets/js/custom/page.js')}}"></script>--}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function () {
            $('.select2').select2();
        });
    </script>
    <script>
        function showRole(elem) {
            if ($(elem).val() == "") {
                return;
            }
            window.location = "<?php echo url('admin/permission/control') ?>/" + $(elem).val();
        }
    </script>
@endsection
