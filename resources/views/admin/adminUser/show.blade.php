@extends('admin.layouts.master')
@section('css')
    <link href="{{URL::asset('assets/plugins/fancybox/jquery.fancybox.css')}}" rel="stylesheet">
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ $admin->name }} Detail</h4>
            </div>
        </div>
    </div>
    <!-- breadcrumb -->
@endsection
@section('content')
    <!-- row opened -->
    <div class="row row-sm">
        <div class="col-xl-6 py-2">
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="card-title mb-3">Admin Detail</h6>
                    <div class="row border-top border-bottom p-2">
                        <div class="col-md-6">
                            <h6 class="mb-0"> Admin Name </h6>
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-0">{{ $admin->name }}</h6>
                        </div>
                    </div>

                    <div class="row  border-bottom p-2">
                        <div class="col-md-6">
                            <h6 class="mb-0">Admin Number</h6>
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-0">{{ $admin->mobile_no }}</h6>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-xl-6 py-2">
            <div class="card card h-100">
                <div class="card-body">
                    <h6 class="card-title mb-3">Company Further Detail</h6>
                    <div class="row border-top border-bottom p-2">
                        <div class="col-md-6">
                            <h6 class="mb-0">Admin Email</h6>
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-0">{{ $admin->email }}</h6>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- /row -->
    </div>
    <!-- Container closed -->
    </div>

    <div class="modal fade" id="globalModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="globalModalTitle">Modal title</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="globalModalDetails"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                            data-dismiss="modal"></button>
                </div>
            </div>
        </div>
    </div>
    <!-- main-content closed -->
@endsection
@section('js')
    <script src="{{URL::asset('assets/plugins/fancybox/jquery.fancybox.js')}}"></script>
    <script src="{{URL::asset('assets/js/custom/CompanyDetail.js')}}?v={{ time() }}"></script>
@endsection
