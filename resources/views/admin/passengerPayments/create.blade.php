@extends('admin.layouts.master')
@section('css')
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">Add Passenger Payments</h4>
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
                                    <label> Username:</label>
                                    <input class="form-control" id="pgs_username" name="pgs_username"
                                           placeholder="Username" type="text" required>
                                </div>
                            </div><!-- input-group -->
                        </div><!-- col-4 -->

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label> Merchant Id:</label>
                                    <input class="form-control" id="pgs_merchant_id" name="pgs_merchant_id"
                                           placeholder="Merchant ID" type="text" required>
                                </div>
                            </div><!-- input-group -->
                        </div><!-- col-4 -->

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label> Base URL:</label>
                                    <input class="form-control" id="pgs_base_url" name="pgs_base_url"
                                           placeholder="Base URL" type="text" required>
                                </div>
                            </div><!-- input-group -->
                        </div><!-- col-4 -->

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label> Password:</label>
                                    <input class="form-control" id="pgs_password" name="pgs_password"
                                           placeholder="Password" type="text" required>
                                </div>
                            </div><!-- input-group -->
                        </div><!-- col-4 -->

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label>API Key</label><!-- input-group-text -->
                                    <input class="form-control" id="pgs_api_key" name="pgs_api_key"
                                           placeholder="API Key" type="text" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label>Payment Gateway</label><!-- input-group-text -->
                                    <select id="pgs_payment_gateway" name="pgs_payment_gateway" class="form-control"
                                            required>
                                        <option value="">Please Select Payment gateway</option>
                                        <option value="cc">Credit Card</option>
                                        <option value="knet">K-Net</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label>White Labeled</label><!-- input-group-text -->
                                    <select id="pgs_whitelabled" name="pgs_whitelabled" class="form-control" required>
                                        <option value="">Please Select White Labeled</option>
                                        <option value="0">No</option>
                                        <option value="1">Yes</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label>Success URL</label><!-- input-group-text -->
                                    <input class="form-control" id="pgs_success_url" name="pgs_success_url"
                                           placeholder="Success URL" type="text" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label>Error URL</label><!-- input-group-text -->
                                    <input class="form-control" id="pgs_error_url" name="pgs_error_url"
                                           placeholder="Error URL" type="text" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label>Currency Code</label><!-- input-group-text -->
                                    <select id="pgs_currency_code" name="pgs_currency_code" class="form-control"
                                            required>
                                        <option value="">Please Select Currency Code</option>
                                        <option value="KWD">KWD</option>
                                        <option value="SAR">SAR</option>
                                        <option value="USD">USD</option>
                                        <option value="BHD">BHD</option>
                                        <option value="EUR">EUR</option>
                                        <option value="OMR">OMR</option>
                                        <option value="QAR">QAR</option>
                                        <option value="AED">AED</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label>Environment</label><!-- input-group-text -->
                                    <select id="pgs_gateway_type" name="pgs_gateway_type" class="form-control" required>
                                        <option value="">Please Select Payment Environment</option>
                                        <option value="production">Production</option>
                                        <option value="sandbox">Sandbox</option>
                                        <option value="test_env">Test</option>
                                    </select>
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
    <script src="{{URL::asset('assets/js/custom/paymentGatewaySettings.js')}}"></script>
    <!-- Internal Select2.min js -->
    <script src="{{URL::asset('assets/plugins/select2/js/select2.min.js')}}"></script>
    <!-- Internal form-elements js -->
    <script src="{{URL::asset('assets/js/form-elements.js')}}"></script>
@endsection
