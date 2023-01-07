@extends('admin.layouts.master')
@section('css')
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">Language String</h4>
                <span class="text-muted mt-1 tx-13 ml-2 mb-0"></span>
            </div>
        </div>
        <div class="d-flex my-xl-auto right-content">
            <div class="pr-1 mb-3 mb-xl-0">
                <a href="{{ route('admin::languageString.create') }}" class="btn btn-info  mr-2">
                    <i class="mdi mdi-plus-circle"></i> Add New
                </a>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12 input-group">
                            <select id="panel"
                                    class="form-control"
                                    required>
                                <option value="">{{ config('languageString.select_option') }}</option>
                                <option value="1">{{ config('languageString.app') }}</option>
                                <option value="2">{{ config('languageString.admin_panel') }}</option>
                                <option value="3">{{ config('languageString.company_panel') }}</option>
                            </select>
                            <div class="input-group-append">
                                <button type="button" class="btn btn-info" value="filter" id="filter"
                                        aria-describedby="basic-addon2">
                                    {{ config('languageString.Filter') }}
                                </button>
                            </div>
                        </div>
                    </div>
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
                                <th>For</th>
                                <th>Screen Name</th>
                                <th>String Type</th>
                                <th>Screen Info</th>
                                <th>Key</th>
                                <th>Value</th>
                                <th>View Screen</th>
                                <th>status</th>
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
    <script src="{{URL::asset('assets/js/custom/languageString.js')}}"></script>
@endsection
