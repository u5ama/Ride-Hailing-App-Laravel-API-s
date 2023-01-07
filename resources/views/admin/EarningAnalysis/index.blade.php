@extends('admin.layouts.master')
@section('css')
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="card mt-3">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 col-xl-3 justify-content-center" style="text-align: center;display: flex;">
                    <h4 class="content-title mb-0 my-auto">Invoices Details</h4>
                </div>
                <div class="col-md-9 col-xl-9">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="breadcrumb-header">
                                <div class="form-group">
                                    <label for="filterWithCategory">Filter Ride Status</label>
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
                                    <label for="filterWithCategory">Filter Categories</label>
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
                                    <label for="filterWithCategory">From Date</label>
                                    <input type="date" name="start_date" id="start_date"
                                           class="form-control datepicker-autoclose" placeholder="Please select start date">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="breadcrumb-header justify-content-between">
                                <div class="form-group">
                                    <label for="filterWithCategory">To Date</label>
                                    <input type="date" name="end_date" id="end_date" class="form-control datepicker-autoclose"
                                           placeholder="Please select end date">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="breadcrumb-header justify-content-between">
                                <button class="filterDate btn btn-outline-primary" type="button" style="margin-top: 30px;width: 100%">
                                    Filter
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row text-center">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5>Bank Commission</h5>
                    <h6 id="bankCom">KWD {{$dataInv['bankCom']}}</h6>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5>Net Invoice</h5>
                    <h6 id="netInvoice">KWD {{$dataInv['netInvoice']}}</h6>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5>Whipp</h5>
                    <h6 id="whippInc">KWD {{$dataInv['whipp']}}</h6>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5>Driver</h5>
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
                                <th>Action</th>
                                <th>Invoice Date</th>
                                <th>Ride# or Inv#</th>
                                <th>Trans Id#</th>
                                <th>Ride Status</th>
                                <th>Category</th>
                                <th>Payment Mode</th>
                                <th>Invoice (Customer)</th>
                                <th>Bank Comm <br> 5%</th>
                                <th>Net Invoice</th>
                                <th>Driver <br> 80%</th>
                                <th>Whipp Gross Earning <br> 20% </th>
                                <th>Agents Commission <br> 20% from Whipp 20%</th>
                                <th>Whipp Net earning</th>
                                <th>Passenger Detail</th>
                                <th>Driver Detail</th>

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
    <script src="{{URL::asset('assets/js/custom/InvoicesDetails.js')}}"></script>
    <script src="{{URL::asset('assets/js/modal.js')}}"></script>
@endsection
