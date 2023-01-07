@extends('admin.layouts.master')
@section('css')
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">Add Base APP FCM Credential</h4>
                <span class="text-muted mt-1 tx-13 ml-2 mb-0"></span>
            </div>
        </div>
    </div>
    <!-- breadcrumb -->
@endsection
@section('content')
    <!-- row -->
    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" data-parsley-validate="" id="addEditForm" role="form">
                        @csrf
                        <input type="hidden" id="edit_value" name="edit_value" value="{{ $base_app_FCM->id }}">
                        <input type="hidden" id="form-method" value="edit">
                        <input type="hidden" id="form-method" value="add">
                        <div class="row row-sm">

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="screen_info">PROJECT NAME<span
                                            class="error">*</span></label>
                                    <input type="text" class="form-control"
                                           name="project_name"
                                           id="project_name"
                                           value="{{ $base_app_FCM->project_name }}"
                                           placeholder="PROJECT NAME" required/>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="screen_info">FCM SERVER KEY<span
                                            class="error">*</span></label>
                                    <input type="text" class="form-control"
                                           name="FCM_SERVER_KEY"
                                           id="FCM_SERVER_KEY"
                                           value="{{ $base_app_FCM->FCM_SERVER_KEY }}"
                                           placeholder="FCM SERVER KEY" required/>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="screen_info">FCM SENDER ID<span
                                            class="error">*</span></label>
                                    <input type="text" class="form-control"
                                           name="FCM_SENDER_ID"
                                           id="FCM_SENDER_ID"
                                           value="{{ $base_app_FCM->FCM_SENDER_ID }}"
                                           placeholder="FCM SENDER ID" required/>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group mb-0 mt-3 justify-content-end">
                                    <div>
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                        <a href="{{ route('admin::FCMSetting.index') }}"
                                           class="btn btn-secondary">Cancel</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- /row -->

    </div>
    <!-- Container closed -->
    </div>
    <!-- main-content closed -->
@endsection
@section('js')
    <script src="{{URL::asset('assets/js/custom/FCMSetting.js')}}?v={{ time() }}"></script>
@endsection
