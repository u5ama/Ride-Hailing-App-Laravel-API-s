@extends('admin.layouts.master')
@section('css')
    <style>
        .breadcrumb-header {
            display: flex;
            margin-top: 5px !important;
            margin-bottom: 8px !important;
            width: 100%;
        }
    </style>
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between mt-3">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">Promo Codes</h4>
            </div>
        </div>
        <div class="d-flex my-xl-auto right-content">
            <div class="pr-1 mb-3 mb-xl-0">
                <a href="{{route('admin::promoCode.create')}}" class="btn btn-info  mr-2">
                    <i class="mdi mdi-plus-circle"></i> Add New
                </a>
            </div>
        </div>

    </div>
    <!-- breadcrumb -->
    <div class="card">
        <div class="card-body">
    <div class="row">
        <div class="col-md-2 col-xl-2 justify-content-center" style="    text-align: center;
    display: flex;">
            <h4 class="content-title mb-0 my-auto">Filter</h4>
        </div>
        <div class="col-md-10 col-xl-10">
            <div class="row">

                <div class="col-md-3 mt-1">
                    <div class="form-group">
                        <label for="filterWithCategory">Filter Status</label>
                        <select name="promo_status" id="promo_status" class="form-control" onchange="getStatusData()">
                            <option value="1">Active</option>
                            <option value="0">InActive</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="breadcrumb-header justify-content-between">
                        <div class="form-group">
                            <label for="filterWithCategory">From Date</label>
                            <input type="date" name="start_date_promo" id="start_date_promo"
                                   class="form-control datepicker-autoclose" placeholder="Please select start date">
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="breadcrumb-header justify-content-between">
                        <div class="form-group">
                            <label for="filterWithCategory">To Date</label>
                            <input type="date" name="end_date_promo" id="end_date_promo"
                                   class="form-control datepicker-autoclose" placeholder="Please select end date">
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="breadcrumb-header justify-content-between">
                        <button class="filterPromo btn btn-outline-primary" type="button" style="margin-top: 30px;width: 100%;">
                            Filter
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
                                <th>Id</th>
                                <th>Promo Code</th>
                                <th>Promo Country</th>
                                <th>Promo Status</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Start Time</th>
                                <th>End Time</th>
                                <th>Promo Type</th>
                                <th>Promo Value</th>
                                <th>Promo Value Type</th>
                                <th>Description</th>
                                <th>Action</th>
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
    </div>
    <!-- Container closed -->
    </div>
    <!-- main-content closed -->
    <div class="modal" id="modaldemo3">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content modal-content-demo">
                <div class="modal-header">
                    <h6 class="modal-title">View Role Detail</h6>
                    <button aria-label="Close" class="close" data-dismiss="modal" type="button"><span
                            aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div id="viewRideModelId"></div>
                </div>
                <div class="modal-footer">

                    <button class="btn ripple btn-secondary" data-dismiss="modal" type="button">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{URL::asset('assets/js/custom/PromoCode.js')}}"></script>
    <script src="{{URL::asset('assets/js/modal.js')}}"></script>
    <script>
        function getStatusData() {
            $('#data-table').DataTable().draw(true);
        }

        function updateStatus(id, status) {
            $.ajax({
                type: 'POST',
                url: APP_URL + '/promoCodeStatus' + '/' + id + '/' + status,
                success: function (data) {
                    if (data.message != '') {
                        successToast('Promo Code Status Updated Successfully', 'success');
                        $('#data-table').DataTable().draw(true);
                    } else {
                        successToast(data.message, 'error');
                    }

                }, error: function (data) {
                    console.log('Error:', data)
                }
            })
        }
    </script>
@endsection
