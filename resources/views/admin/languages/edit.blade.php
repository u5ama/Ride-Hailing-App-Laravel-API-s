@extends('admin.layouts.master')
@section('css')
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">Edit Language</h4>
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
                        <input type="hidden" id="edit_value" name="edit_value" value="{{ $language->id }}">
                        <input type="hidden" id="form-method" value="edit">
                        <div class="row row-sm">

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="screen_name">Name<span
                                            class="error">*</span></label>
                                    <input type="text" class="form-control"
                                           name="name"
                                           id="name"
                                           value="{{$language->name}}"
                                           placeholder="Name" required/>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="RigthToLeft">Right To Left<span class="error">*</span></label>
                                    <select class="form-control" id="is_rtl" name="is_rtl">
                                        <option value="1" @if($language->is_rtl==1) selected @endif>Yes</option>
                                        <option value="0" @if($language->is_rtl==0) selected @endif>No</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="language_code">Language Code<span
                                            class="error">*</span></label>
                                    <input type="text" class="form-control"
                                           name="language_code"
                                           id="language_code"
                                           value="{{$language->language_code}}"
                                           placeholder="Language Code" required/>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group mb-0 mt-3 justify-content-end">
                                    <div>
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                        <a href="{{ route('admin::languages.index') }}"
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
    <script src="{{URL::asset('assets/js/custom/languages.js')}}?v={{ time() }}"></script>
@endsection
