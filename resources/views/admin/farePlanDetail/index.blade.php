@extends('admin.layouts.master')
@section('css')
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">Fare Plan Head</h4>
                <span class="text-muted mt-1 tx-13 ml-2 mb-0"></span>
            </div>
        </div>
    </div>
    <div class="row row-sm">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" data-parsley-validate="" id="addEditForm" role="form">
                        @csrf
                        <input type="hidden" id="edit_value" name="edit_value" value="">
                        <input type="hidden" id="form-method" value="add">
                        <div class="main-content-label mg-b-5">
                            Fare Plan Head
                        </div>

                        <div class="row row-sm">
                            <div class="col-lg-4 mg-t-20 mg-lg-t-0">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            Fare Plan Name:
                                        </div>
                                    </div>
                                    <input class="form-control" id="fph_plan_name" name="fph_plan_name" placeholder=""
                                           type="text">
                                </div><!-- input-group -->
                            </div><!-- col-4 -->
                            <div class="col-lg-4 mg-t-20 mg-lg-t-0">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            Description
                                        </div><!-- input-group-text -->
                                    </div><!-- input-group-prepend -->
                                    <input class="form-control" id="fph_description" name="fph_description"
                                           placeholder="Description" type="text">
                                </div>
                            </div>
                            <div class="col-lg-3 mg-t-20 mg-lg-t-0">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            Type:
                                        </div>
                                    </div><!-- input-group-prepend -->
                                    <select class="form-control" id="fph_fare_type" name="fph_fare_type">
                                        <option value="intercity">Inner City</option>
                                        <option value="outercity">Outer City</option>

                                    </select>
                                </div><!-- input-group -->
                            </div>

                            <div class="col-lg-1 mg-t-20 mg-lg-t-0">
                                <div class="input-group">

                                    <button type="submit" class="btn btn-primary" id="btn_txt">Add</button>
                                </div>
                            </div>
                        </div>
                    </form>
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
                                <th>ID</th>
                                <th>Fare Plan Name</th>
                                <th>Fare Plan Type</th>
                                <th>Status</th>
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
@endsection
@section('js')
    <script src="{{URL::asset('assets/js/custom/farePlanHead.js')}}"></script>
@endsection
