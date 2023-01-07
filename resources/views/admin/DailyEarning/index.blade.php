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
                <div class="col-md-3 col-xl-3 justify-content-center" style="    text-align: center;
    display: flex;">
                    <h4 class="content-title mb-0 my-auto">Daily Earnings</h4>
                </div>
                <div class="col-md-9 col-xl-9">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="breadcrumb-header">
                                <div class="form-group">
                                    <label for="companyfilterWithCategory">Filter Ride Status</label>
                                    <select class="form-control" id="companyfilterWithCategory" name="companyfilterWithCategory" value="">
                                        <option value="">Select Ride Status</option>
                                        <option value="Completed">Completed</option>
                                        <option value="Cancelled">Cancelled</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="breadcrumb-header justify-content-between">
                                <div class="form-group">
                                    <label for="company_filter">Select Company</label>
                                    <select class="form-control" name="company_filter" id="company_filter"
                                            onchange="checkCompany()">
                                        <option value="">Select Company</option>
                                        @foreach($companies as $company)
                                            <option value="{{$company->id}}">{{$company->com_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="breadcrumb-header justify-content-between">
                                <div class="form-group">
                                    <label for="filterWithCategory">From Date</label>
                                    <input type="date" name="start_date_earning" id="start_date_earning"
                                           class="form-control datepicker-autoclose" placeholder="Please select start date">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="breadcrumb-header justify-content-between">
                                <div class="form-group">
                                    <label for="filterWithCategory">To Date</label>
                                    <input type="date" name="end_date_earning" id="end_date_earning"
                                           class="form-control datepicker-autoclose" placeholder="Please select end date">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="breadcrumb-header justify-content-between">
                                <button class="filterDailyEarning btn btn-outline-primary" type="button"
                                        style="margin-top: 30px;width: 100%">Filter
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
                                <th>Invoice Date</th>
                                <th>Ride Status</th>
                                <th>Category</th>
                                <th>Payment Mode</th>
                                <th>Invoice (Customer)</th>
                                <th>Bank Comm <br>5%</th>
                                <th>Net Invoice</th>
                                <th>Driver</th>
                                <th>Whipp Gross earning</th>
                                <th>Agents Commission</th>
                                <th>Whipp Net earning</th>
                            </tr>
                            </thead>
                          <tfoot>
                            <tr>
                                <th colspan="4" style="text-align:right"></th>
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
    <script src="{{URL::asset('assets/js/custom/DailyEarning.js')}}"></script>
    <script src="{{URL::asset('assets/js/modal.js')}}"></script>
    <script>
        function checkCompany() {
            $('#data-table').DataTable().draw(true);
        }
    </script>
@endsection
