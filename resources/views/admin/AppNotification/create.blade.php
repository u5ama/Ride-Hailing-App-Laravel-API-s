@extends('admin.layouts.master')
@section('css')

@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">Send Notification</h4>
                <span class="text-muted mt-1 tx-13 ml-2 mb-0"></span>
            </div>
        </div>
    </div>
    <!-- breadcrumb -->
@endsection
@section('content')
    <!-- row -->
    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" data-parsley-validate="" id="addEditForm" role="form">
                        @csrf
                        <input type="hidden" id="form-method" value="add">
                        <div class="row row-sm">

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="app_or_panel">Select Target<span class="error">*</span></label>
                                    <select class="form-control" id="target_type" name="target_type" required>
                                        <option value="">Select Target Type</option>
                                        <option value="all_whipp">All</option>
                                        <option value="app">App</option>
                                        <option value="device">Device</option>
                                        <option value="select_country">Country</option>
                                        <option value="select_customer">Select User List</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12" id="country_drop">
                                <div class="form-group">
                                    <p class="mg-b-10">Country</p>
                                    <select class="form-control select2" id="country_id" name="country_id">
                                        <option label="Choose one">
                                        </option>
                                        @foreach($countries as $row)
                                            <option value="{{$row->code}}">{{$row->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-12" id="app_drop">
                                <div class="form-group">
                                    <p class="mg-b-10">Select App</p>
                                    <select class="form-control" id="app_type" name="app_type">
                                        <option label="Choose App"></option>
                                        <option value="all_drivers">Driver</option>
                                        <option value="all_passenger">Passenger</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-12" id="device_drop">
                                <div class="form-group">
                                    <p class="mg-b-10">Select Device</p>
                                    <select class="form-control" id="device_type" name="device_type">
                                        <option label="Choose Device"></option>
                                        <option value="all_androids">Android</option>
                                        <option value="all_ios">IOS</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="screen_info">Title<span
                                            class="error">*</span></label>
                                    <input type="text" class="form-control"
                                           name="title"
                                           id="title"
                                           placeholder="Title" required/>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="screen_info">Message<span
                                            class="error">*</span></label>
                                    <textarea type="text" class="form-control"
                                              name="description"
                                              id="description"
                                              placeholder="Message" required></textarea>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>

                            <div class="col-12" id="user_drop">
                                <div class="form-group">
                                    <p class="mg-b-10">User List</p>
                                    <select class="form-control select2" multiple="multiple" id="userlist"
                                            name="user[]">
                                    </select>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group mb-0 mt-3 justify-content-end">
                                    <div>
                                        <button type="submit" class="btn btn-primary">Send</button>
                                        <a href="{{ route('admin::appNotification.index') }}"
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
    <script type="text/javascript">
        $("#user_id").hide();


    </script>
    <script src="{{URL::asset('assets/js/custom/AppNotification.js')}}?v={{ time() }}"></script>
    <!--Internal  Datepicker js -->
    <script src="{{URL::asset('assets/plugins/jquery-ui/ui/widgets/datepicker.js')}}"></script>
    <!--Internal  jquery.maskedinput js -->
    <script src="{{URL::asset('assets/plugins/jquery.maskedinput/jquery.maskedinput.js')}}"></script>
    <!--Internal  spectrum-colorpicker js -->
    <script src="{{URL::asset('assets/plugins/spectrum-colorpicker/spectrum.js')}}"></script>
    <!-- Internal Select2.min js -->
    <script src="{{URL::asset('assets/plugins/select2/js/select2.min.js')}}"></script>
    <!--Internal Ion.rangeSlider.min js -->
    <script src="{{URL::asset('assets/plugins/ion-rangeslider/js/ion.rangeSlider.min.js')}}"></script>
    <!--Internal  jquery-simple-datetimepicker js -->
    <script src="{{URL::asset('assets/plugins/amazeui-datetimepicker/js/amazeui.datetimepicker.min.js')}}"></script>
    <!-- Ionicons js -->
    <script src="{{URL::asset('assets/plugins/jquery-simple-datetimepicker/jquery.simple-dtpicker.js')}}"></script>
    <!--Internal  pickerjs js -->
    <script src="{{URL::asset('assets/plugins/pickerjs/picker.min.js')}}"></script>
    <!-- Internal form-elements js -->
    <script src="{{URL::asset('assets/js/form-elements.js')}}"></script>

@endsection
