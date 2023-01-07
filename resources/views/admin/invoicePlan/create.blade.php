@extends('admin.layouts.master')
@section('css')
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">Add Invoice Bank Commission</h4>
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
                        <input type="hidden" id="edit_value" name="edit_value" value="">
                        <input type="hidden" id="form-method" value="add">
                        <div class="row row-sm">

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="ip_payment_type">Payment Method<span
                                            class="error">*</span></label>
                                    <select class="form-control"
                                           name="ip_payment_type"
                                           id="ip_payment_type"
                                            required>
                                        <option value=""> Select Payment Method</option>
                                        <option value="creditcard">Credit Card</option>
                                        <option value="knet">Knet - Debit Card</option>
                                    </select>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="screen_info">Bank %age Commission<span
                                            class="error">*</span></label>
                                    <input type="number" min="1" max="99" class="form-control"
                                           name="ip_bank_commesion"
                                           id="ip_bank_commesion"
                                           placeholder="Bank %age Commission" required/>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="screen_info">Bank Fixed Commission<span
                                            class="error">*</span></label>
                                    <input type="number" min="1" max="99" class="form-control"
                                           name="ip_bank_fixed_commesion"
                                           id="ip_bank_fixed_commesion"
                                           placeholder="Bank Fixed Commission on Each Transactions" required/>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="screen_info">Flexible Extra Charges<span
                                            class="error">*</span></label>
                                    <input type="number" min="1" max="99" class="form-control"
                                           name="ip_bank_extra_charges"
                                           id="ip_bank_extra_charges"
                                           placeholder="Flexible Extra Charges on Each Transactions" required/>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>
<!--                            <div class="col-12">
                                <div class="form-group">
                                    <label for="screen_info">Whipp Commesion<span
                                            class="error">*</span></label>
                                    <input type="number" min="1" max="99" class="form-control"
                                           name="ip_whipp_commesion"
                                           id="ip_whipp_commesion"
                                           placeholder="Whipp Commesion" required/>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="screen_info">Driver Commesion<span
                                            class="error">*</span></label>
                                    <input type="number" min="1" max="99" class="form-control"
                                           name="ip_driver_commesion"
                                           id="ip_driver_commesion"
                                           placeholder="driver Commesion" required/>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="screen_info">Company Commesion<span
                                            class="error">*</span></label>
                                    <input type="number" min="1" max="99" class="form-control"
                                           name="ip_company_commesion"
                                           id="ip_company_commesion"
                                           placeholder="Company Commesion" required/>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>-->
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="app_or_panel">Is Default<span class="error">*</span></label>
                                    <select class="form-control" id="ip_is_default" name="ip_is_default">
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label>Start date</label>
                                        <input class="form-control" id="ip_start_date" name="ip_start_date"
                                               placeholder="Start date" type="date" autocomplete="off" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label>End Date</label>
                                        <input class="form-control" id="ip_end_date" name="ip_end_date"
                                               placeholder="End Date" type="date" autocomplete="off" required>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group mb-0 mt-3 justify-content-end">
                                    <div>
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                        <a href="{{ route('admin::InvoicePlan.index') }}"
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
    <script src="{{URL::asset('assets/plugins/clockpicker/dist/bootstrap-clockpicker.min.js')}}"></script>
    <script src="{{URL::asset('assets/js/custom/invoicePlan.js')}}?v={{ time() }}"></script>
@endsection
