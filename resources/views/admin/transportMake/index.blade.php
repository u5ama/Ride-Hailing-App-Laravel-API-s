@extends('admin.layouts.master')
@section('css')
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">Transport Make</h4>

            </div>
        </div>
        <div class="d-flex my-xl-auto right-content">
            <div class="pr-1 mb-3 mb-xl-0">
                <a href="{{ route('admin::transportMake.create') }}" class="btn btn-info  mr-2">
                    <i class="mdi mdi-plus-circle"></i> Add new
                </a>
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
                                <th>{{ 'Id' }}</th>
                                <th>{{ 'Type' }}</th>
                                <th>{{ 'Name' }}</th>
                                <th>{{ 'Actions' }}</th>
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
    <script>
        const title = "{{ 'destroy_brand ?' }}";
        const text = "{{ 'confirm_message' }}";
        const confirmButtonText = "{{ 'yes_delete_it' }}";
        const cancelButtonText = "{{ 'no_cancel_plx' }}";
    </script>
    <script src="{{URL::asset('assets/js/custom/transportMake.js')}}"></script>
@endsection
