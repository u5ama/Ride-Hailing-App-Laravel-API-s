@extends('admin.layouts.master')
@section('css')
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
@endsection

@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">View Page</h4>
                <span class="text-muted mt-1 tx-13 ml-2 mb-0">/ Setting</span>
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
                    <input type="hidden" id="edit_value" name="edit_value" value="{{ $page->id }}">
                    <input type="hidden" id="form-method" value="edit">
                    <div class="row row-sm">
                        @foreach($languages as $key=>$language)
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="{{ $language->language_code }}_name">{{ $language->name }} Name<span
                                            class="error">*</span></label>
                                    <input type="text" class="form-control"
                                           name="{{ $language->language_code }}_name"
                                           id="{{ $language->language_code }}_name"
                                           value="{{ $page->translateOrNew($language->language_code)->name }}"
                                           placeholder="{{ $language->name }} Name" required/>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>

                            <div class="col-12 mb-3">
                                <label for="{{ $language->language_code }}_description">{{ $language->name }}
                                    Description
                                    <span class="error">*</span></label>
                                <textarea id="{{ $language->language_code }}_description" class="description"
                                          name="{{ $language->language_code }}_description"
                                >{{ $page->translateOrNew($language->language_code)->description }}</textarea>
                            </div>
                        @endforeach

                        <div class="col-12">
                            <div class="form-group mb-0 mt-3 justify-content-end">
                                <div>

                                    <a href="{{ route('admin::page.index') }}" class="btn btn-secondary">Cancel</a>
                                </div>
                            </div>
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
    <!-- main-content closed -->
@endsection
@section('js')
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
    <script src="{{URL::asset('assets/js/custom/page.js')}}?v={{ time() }}"></script>
@endsection
