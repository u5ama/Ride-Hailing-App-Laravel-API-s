@extends('admin.layouts.master')
@section('css')
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">Edit Payment Gateway Setting</h4>
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
                        <input type="hidden" id="edit_value" name="edit_value" value="{{$payment->id}}">
                        <input type="hidden" id="form-method" value="add">

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label> Username:</label>
                                    <input class="form-control" id="pgs_username" name="pgs_username"
                                           placeholder="Username" type="text" value="{{$payment->pgs_username}}"
                                           required>
                                </div>
                            </div><!-- input-group -->
                        </div><!-- col-4 -->

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label> Merchant Id:</label>
                                    <input class="form-control" id="pgs_merchant_id" name="pgs_merchant_id"
                                           placeholder="Merchant ID" type="text" value="{{$payment->pgs_merchant_id}}"
                                           required>
                                </div>
                            </div><!-- input-group -->
                        </div><!-- col-4 -->

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label> Base URL:</label>
                                    <input class="form-control" id="pgs_base_url" name="pgs_base_url"
                                           placeholder="Base URL" type="text" value="{{$payment->pgs_base_url}}"
                                           required>
                                </div>
                            </div><!-- input-group -->
                        </div><!-- col-4 -->

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label> Password:</label>
                                    <input class="form-control" id="pgs_password" name="pgs_password"
                                           placeholder="Password" type="text" value="{{$payment->pgs_password}}"
                                           required>
                                </div>
                            </div><!-- input-group -->
                        </div><!-- col-4 -->

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label>API Key</label><!-- input-group-text -->
                                    <input class="form-control" id="pgs_api_key" name="pgs_api_key"
                                           placeholder="API Key" type="text" value="{{$payment->pgs_api_key}}" required>
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
                                        <option
                                            value="cc" @if($payment->pgs_payment_gateway == 'cc'){{'selected'}} @endif>
                                            Credit Card
                                        </option>
                                        <option
                                            value="knet" @if($payment->pgs_payment_gateway == 'knet'){{'selected'}} @endif>
                                            K-Net
                                        </option>
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
                                        <option value="0" @if($payment->pgs_whitelabled == '0'){{'selected'}} @endif>
                                            No
                                        </option>
                                        <option value="1" @if($payment->pgs_whitelabled == '1'){{'selected'}} @endif>
                                            Yes
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label>Success URL</label><!-- input-group-text -->
                                    <input class="form-control" id="pgs_success_url" name="pgs_success_url"
                                           placeholder="Success URL" type="text" value="{{$payment->id}}" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label>Error URL</label><!-- input-group-text -->
                                    <input class="form-control" id="pgs_error_url" name="pgs_error_url"
                                           placeholder="Error URL" type="text" value="{{$payment->id}}" required>
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
                                        <option
                                            value="KWD" @if($payment->pgs_currency_code == 'KWD'){{'selected'}} @endif>
                                            KWD
                                        </option>
                                        <option
                                            value="SAR" @if($payment->pgs_currency_code == 'SAR'){{'selected'}} @endif>
                                            SAR
                                        </option>
                                        <option
                                            value="USD" @if($payment->pgs_currency_code == 'USD'){{'selected'}} @endif>
                                            USD
                                        </option>
                                        <option
                                            value="BHD" @if($payment->pgs_currency_code == 'BHD'){{'selected'}} @endif>
                                            BHD
                                        </option>
                                        <option
                                            value="EUR" @if($payment->pgs_currency_code == 'EUR'){{'selected'}} @endif>
                                            EUR
                                        </option>
                                        <option
                                            value="OMR" @if($payment->pgs_currency_code == 'OMR'){{'selected'}} @endif>
                                            OMR
                                        </option>
                                        <option
                                            value="QAR" @if($payment->pgs_currency_code == 'QAR'){{'selected'}} @endif>
                                            QAR
                                        </option>
                                        <option
                                            value="AED" @if($payment->pgs_currency_code == 'AED'){{'selected'}} @endif>
                                            AED
                                        </option>
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
                                        <option
                                            value="production" @if($payment->pgs_gateway_type == 'production'){{'selected'}} @endif>
                                            Production
                                        </option>
                                        <option
                                            value="sandbox" @if($payment->pgs_gateway_type == 'sandbox'){{'selected'}} @endif>
                                            Sandbox
                                        </option>
                                        <option
                                            value="test_env" @if($payment->pgs_gateway_type == 'test_env'){{'selected'}} @endif>
                                            Test
                                        </option>
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
    <!-- <script src="{{URL::asset('assets/js/form-elements.js')}}"></script> -->
@endsection
