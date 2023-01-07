@extends('admin.layouts.master')
@section('css')

<style type="text/css">
    table.dataTable tfoot th, table.dataTable tfoot td {
    padding: 10px 18px 6px 18px;
    border-top: 1px solid #555b6a !important;

    font-size: 18px !important;
}
</style>

@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="card mt-3">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 col-xl-3">
                    <div class="breadcrumb-header justify-content-between" style="text-align: center;display: flex;">
                                <h4 class="content-title mb-0 mt-4">{{ config('languageString.daily_earnings') }}</h4>
                                <span class="text-muted mt-1 tx-13 ml-2 mb-0"></span>
                    </div>
                </div>
                <div class="col-md-9 col-xl-9">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="breadcrumb-header">
                                <div class="form-group">
                                    <label for="companyfilterWithCategory">{{ config('languageString.filter_ride_status') }}</label>
                                    <select class="form-control" id="companyfilterWithCategory" name="companyfilterWithCategory" value="">
                                        <option value="">Select Ride Status</option>
                                        <option value="Completed">Completed</option>
                                        <option value="Cancelled">Cancelled</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="breadcrumb-header justify-content-between">
                                <div class="form-group">
                                    <label for="filterWithCategory">{{ config('languageString.from_date') }}</label>
                                    <input type="date" name="start_date_earning" id="start_date_earning"
                                           class="form-control datepicker-autoclose" placeholder="Please select start date">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="breadcrumb-header justify-content-between">
                                <div class="form-group">
                                    <label for="filterWithCategory">{{ config('languageString.to_date') }}</label>
                                    <input type="date" name="end_date_earning" id="end_date_earning"
                                           class="form-control datepicker-autoclose" placeholder="Please select end date">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="breadcrumb-header justify-content-between">
                                <button class="filterDailyEarning btn btn-outline-primary" type="button"
                                        style="margin-top: 30px; width: 100%;"> {{ config('languageString.filter_button') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
                                <th>{{ config('languageString.invoice_date_title') }}</th>
                                <th>{{ config('languageString.ride_status_title') }}</th>
                                <th>{{ config('languageString.category_title') }}</th>
                                <th>{{ config('languageString.payment_mode_title') }}</th>
                                <th>{{ config('languageString.invoice_customer_title') }}</th>
                                <th>{{ config('languageString.bank_comm_title') }}</th>
                                <th>{{ config('languageString.net_invoice_title') }}</th>
                                <th>{{ config('languageString.driver_title') }}</th>
                                <th>{{ config('languageString.whipp_gross_earning_title') }}</th>
                                <th>{{ config('languageString.agents_commission_title') }}</th>
                                <th>{{ config('languageString.whipp_net_earning_title') }}</th>

                            </tr>
                            </thead>

                            <tfoot>
                            <tr>
                                <th colspan="4" style="text-align:right;font-weight: bold;"></th>
                                <th> </th>
                                <th style="text-align:right"></th>
                                 <th></th>
                                  <th></th>
                                   <th></th>
                                   <th></th>
                                   <th></th>
                            </tr>
                        </tfoot>

                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!--/div-->
    </div>
    <!-- /row -->
@endsection
@section('js')
    <script src="{{URL::asset('assets/js/custom/company/DailyEarning.js')}}"></script>
    <script src="{{URL::asset('assets/js/modal.js')}}"></script>
    <script>
        function checkCompany() {
            $('#data-table').DataTable().draw(true);
        }
    </script>
@endsection
