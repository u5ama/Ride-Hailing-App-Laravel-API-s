@extends('admin.layouts.master')
@section('css')
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">Add APP SMTP Credential</h4>
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
                        <input type="hidden" id="form-method" value="add">
                        <div class="row row-sm">

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="screen_info">MAIL DRIVER<span
                                            class="error">*</span></label>
                                    <input type="text" class="form-control"
                                           name="MAIL_DRIVER"
                                           id="MAIL_DRIVER"
                                           placeholder="MAIL DRIVER" required/>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="screen_info">MAIL HOST<span
                                            class="error">*</span></label>
                                    <input type="text" class="form-control"
                                           name="MAIL_HOST"
                                           id="MAIL_HOST"
                                           placeholder="MAIL HOST" required/>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="screen_info">MAIL PORT<span
                                            class="error">*</span></label>
                                    <input type="text" class="form-control"
                                           name="MAIL_PORT"
                                           id="MAIL_PORT"
                                           placeholder="MAIL PORT" required/>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="screen_info">MAIL USERNAME<span
                                            class="error">*</span></label>
                                    <input type="text" class="form-control"
                                           name="MAIL_USERNAME"
                                           id="MAIL_USERNAME"
                                           placeholder="MAIL USERNAME" required/>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="screen_info">MAIL PASSWORD<span
                                            class="error">*</span></label>
                                    <input type="text" class="form-control"
                                           name="MAIL_PASSWORD"
                                           id="MAIL_PASSWORD"
                                           placeholder="MAIL PASSWORD" required/>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="screen_info">MAIL ENCRYPTION<span
                                            class="error">*</span></label>
                                    <input type="text" class="form-control"
                                           name="MAIL_ENCRYPTION"
                                           id="MAIL_ENCRYPTION"
                                           placeholder="MAIL ENCRYPTION" required/>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="screen_info">MAIL FROM ADDRESS<span
                                            class="error">*</span></label>
                                    <input type="text" class="form-control"
                                           name="MAIL_FROM_ADDRESS"
                                           id="MAIL_FROM_ADDRESS"
                                           placeholder="MAIL FROM ADDRESS" required/>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group mb-0 mt-3 justify-content-end">
                                    <div>
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                        <a href="{{ route('admin::SMTPSetting.index') }}"
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
    <script src="{{URL::asset('assets/js/custom/SMTPSetting.js')}}?v={{ time() }}"></script>
@endsection
