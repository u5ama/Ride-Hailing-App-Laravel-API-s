@extends('admin.layouts.master')
@section('css')
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">Add Currency</h4>
                <span class="text-muted mt-1 tx-13 ml-2 mb-0"></span>
            </div>
        </div>
    </div>
    <!-- breadcrumb -->
@endsection
@section('content')
    <!-- row -->
    <div class="row row-sm">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" data-parsley-validate="" id="addEditForm" role="form">
                        @csrf
                        <input type="hidden" id="edit_value" name="edit_value" value="">
                        <input type="hidden" id="form-method" value="add">

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label> Title:</label>
                                    <input class="form-control" id="cu_title" name="cu_title" placeholder="Title"
                                           type="text" required>
                                </div>
                            </div><!-- input-group -->
                        </div><!-- col-4 -->

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label>Currency Code</label><!-- input-group-text -->
                                    <input class="form-control" id="cu_code" name="cu_code" placeholder="Code"
                                           type="text" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label>Symbol Left</label><!-- input-group-text -->
                                    <input class="form-control" id="cu_symbol_left" name="cu_symbol_left" placeholder=""
                                           type="text" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label>Symbol Right</label><!-- input-group-text -->
                                    <input class="form-control" id="cu_symbol_right" name="cu_symbol_right"
                                           placeholder="" type="text">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            {{--</div><br>
                            <div class="row">--}}
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label>Decimal Place</label><!-- input-group-text -->
                                    <input class="form-control" id="cu_decimal_places" name="cu_decimal_places"
                                           placeholder="" type="text" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label>Value</label><!-- input-group-text -->
                                    <input class="form-control" id="cu_value" name="cu_value" placeholder=""
                                           type="number" step="any" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-2">
                                <div class="form-group">
                                    <button type="reset" class="btn btn-danger">{{ 'Reset' }}</button>
                                    <button type="submit" class="btn btn-primary">{{ 'Save' }}</button>
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
    <script src="{{URL::asset('assets/js/custom/currencies.js')}}"></script>
    <!-- Internal Select2.min js -->
    <script src="{{URL::asset('assets/plugins/select2/js/select2.min.js')}}"></script>
    <!-- Internal form-elements js -->
    <script src="{{URL::asset('assets/js/form-elements.js')}}"></script>
@endsection
