@extends('admin.layouts.master')
@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet"/>
    <style>
        .select2 {
            width: 100% !important;
            height: auto !important;
        }
    </style>
@endsection
@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" integrity="sha512-mSYUmp1HYZDFaVKK//63EcZq4iFWFjxSL+Z3T/aCt4IO9Cejm03q3NKKYN6pFQzY0SBOr8h+eCIAZHPXcpZaNw==" crossorigin="anonymous" />
    <div class="card  mt-5">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <h4 class="panel-heading">{{ 'Add Voucher' }}</h4>

                        <div class="panel-body">
                            <form method="POST" data-parsley-validate="" id="addVoucherForm" role="form">
                                @csrf

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">{{ 'Voucher Code' }}</label>
                                            <input type="text" class="form-control" name="voucher_code"
                                                   value="{{ old('voucher_code') }}" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">{{ 'Voucher Amount' }}</label>
                                            <input type="text" class="form-control" name="vc_amount"
                                                   value="{{ old('vc_amount') }}" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">{{ 'Issue Date' }}</label>
                                            <input type="text" class="form-control" name="issue_date" id="issue_date"
                                                   value="{{ old('issue_date') }}" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">{{ 'Expiry Date' }}</label>
                                            <input type="text" class="form-control" name="expiry_date" id="expiry_date"
                                                   value="{{ old('expiry_date') }}" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">{{ 'Issue Time' }}</label>
                                            <input type="time" class="form-control" name="issue_time"
                                                   value="{{ old('issue_time') }}" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="control-label">{{ 'Expiry Time' }}</label>
                                            <input type="time" class="form-control" name="expiry_time"
                                                   value="{{ old('expiry_time') }}" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label">{{ 'Voucher Status' }}</label>
                                            <select name="voucher_status" id="voucher_status" class="form-control" required>
                                                <option value="" selected>Select Status</option>
                                                <option value="1">Active</option>
                                                <option value="0">InActive</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <button type="reset" class="btn btn-danger">{{ 'Reset' }}</button>
                                        <button type="submit" class="btn btn-primary">{{ 'Save' }}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{URL::asset('assets/js/custom/VoucherCode.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function () {
            $('.select2').select2();
        });
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js" integrity="sha512-T/tUfKSV1bihCnd+MxKD0Hm1uBBroVYBOYSk1knyvQ9VyZJpc/ALb4P0r6ubwVPSGB2GvjeoMAJJImBG12TiaQ==" crossorigin="anonymous"></script>
    <script>
        $(function() {
            $('#issue_date').datepicker({
                startDate: '-0d',
                format: 'yyyy-mm-dd',
                setDate: new Date()
            });

            $('#expiry_date').datepicker({
                startDate: '-0d',
                format: 'yyyy-mm-dd',
                setDate: new Date()
            });
        });
    </script>
@endsection

