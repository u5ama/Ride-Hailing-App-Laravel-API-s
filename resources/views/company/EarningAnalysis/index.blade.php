@extends('admin.layouts.master')
@section('css')
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="card mt-3">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 col-xl-3">
                    <div class="breadcrumb-header justify-content-between" style="text-align: center;display: flex;">
                                <h4 class="content-title mb-0 mt-4">{{ config('languageString.invoices_details') }}</h4>
                                <span class="text-muted mt-1 tx-13 ml-2 mb-0"></span>
                    </div>
                </div>
                <div class="col-md-9 col-xl-9">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="breadcrumb-header">
                                <div class="form-group">
                                    <label for="filterWithCategory">{{ config('languageString.filter_ride_status') }}</label>
                                    <select class="form-control" id="filterWithStatus" name="status" value="">
                                        <option value="">Select Ride Status</option>
                                        <option value="Completed">Completed</option>
                                        <option value="Cancelled">Cancelled</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="breadcrumb-header">
                                <div class="form-group">
                                    <label for="filterWithCategory">{{ config('languageString.filter_categories') }}</label>
                                    <select class="form-control" id="filterWithCategory" name="category" value="">
                                        <option value="">Categories</option>
                                        @foreach($dataInv['categories'] as $category)
                                            <option value="{{$category['name']}}">{{$category['name']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="breadcrumb-header justify-content-between">
                                <div class="form-group">
                                    <label for="filterWithCategory">{{ config('languageString.from_date') }}</label>
                                    <input type="date" name="start_date" id="start_date"
                                           class="form-control datepicker-autoclose" placeholder="Please select start date">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="breadcrumb-header justify-content-between">
                                <div class="form-group">
                                    <label for="filterWithCategory">{{ config('languageString.to_date') }}</label>
                                    <input type="date" name="end_date" id="end_date" class="form-control datepicker-autoclose"
                                           placeholder="Please select end date">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="breadcrumb-header justify-content-between">
                                <button class="filterDate btn btn-outline-primary" type="button" style="margin-top: 30px; width: 100%">
                                    {{ config('languageString.filter_button') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row text-center">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5>{{ config('languageString.bank_commission_title') }}</h5>
                    <h6 id="bankCom">KWD {{$dataInv['bankCom']}}</h6>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5>{{ config('languageString.net_invoice') }}</h5>
                    <h6 id="netInvoice">KWD {{$dataInv['netInvoice']}}</h6>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5>{{ config('languageString.driver') }}</h5>
                    <h6 id="driverInc">KD {{$dataInv['driver']}}</h6>
                </div>
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
                    <div class="table-responsive">
                        <table class="table mg-b-0 text-md-nowrap" id="data-table">
                            <thead>
                            <tr>
                                <th>{{ config('languageString.action_title') }}</th>
                                <th>{{ config('languageString.invoice_date_title') }}</th>
                                <th>{{ config('languageString.ride_or_inv_tilte') }}</th>
                                <th>{{ config('languageString.trans_id_title') }}</th>
                                <th>{{ config('languageString.ride_status_title') }}</th>
                                <th>{{ config('languageString.category_title') }}</th>
                                <th>{{ config('languageString.payment_mode_title') }}</th>
                                <th>{{ config('languageString.invoice_customer_title') }}</th>
                                <th>{{ config('languageString.bank_comm_title') }}</th>
                                <th>{{ config('languageString.net_invoice_title') }}</th>
                                <th>{{ config('languageString.driver_title') }}</th>
                                <th>{{ config('languageString.agents_commission_from_whipp_title') }}</th>
                                <th>{{ config('languageString.company_net_earning_title') }}</th>
                                <th>{{ config('languageString.passenger_detail_title') }}</th>
                                <th>{{ config('languageString.driver_detail_title') }}</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!--/div-->
    </div>
    <!-- /row -->
    <div class="modal" id="modaldemo44">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content modal-content-demo">
                <div class="modal-header">
                    <h6 class="modal-title">View Invoice Details</h6>
                    <button aria-label="Close" class="close" data-dismiss="modal" type="button"><span
                            aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div id="globalModalInvoiceDetails"></div>
                </div>
                <div class="modal-footer">

                    <button class="btn ripple btn-secondary" data-dismiss="modal" type="button">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{URL::asset('assets/js/custom/company/InvoicesDetails.js')}}"></script>
    <script src="{{URL::asset('assets/js/modal.js')}}"></script>
@endsection
