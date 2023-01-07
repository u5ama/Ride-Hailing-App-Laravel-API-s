@extends('admin.layouts.master')
@section('css')
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
@endsection

@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">Edit Web Page</h4>
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
                    <form method="POST" data-parsley-validate="" id="addEditForm" role="form">
                        @csrf
                        <input type="hidden" id="edit_value" name="edit_value" value="{{ $page->id }}">
                        <input type="hidden" id="form-method" value="edit">
                        <div class="row row-sm">

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="screen_info">Web Page Name<span
                                            class="error">*</span></label>
                                    <input type="text" class="form-control"
                                           name="page_name"
                                           id="page_name"
                                           value="{{$page->page_name}}"
                                           placeholder="Web Page Name" required/>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="screen_info">Slug<span
                                            class="error">*</span></label>
                                    <input type="text" class="form-control"
                                           name="slug"
                                           id="slug"
                                           value="{{$page->slug}}"
                                           pattern="[^' ']+"
                                           placeholder="Slug" required/>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="driver_or_passenger">Driver Or Passenger<span
                                            class="error">*</span></label>
                                    <select class="form-control" id="driver_or_passenger" name="driver_or_passenger">
                                        <option value="1" @if($page->app_type == "Driver") selected @endif>Driver
                                        </option>
                                        <option value="2" @if($page->app_type == "Passenger") selected @endif>
                                            Passenger
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="app_or_panel">Select Order<span class="error">*</span></label>
                                <select class="form-control" id="page_order" name="page_order">

                                    @if(count($indexes)>0)
                                        @foreach($indexes as $key=>$indexe)
                                            @if(count($indexes)>0)
                                                <option value="{{$key+1}}"
                                                        @if($page->page_order == $key+1) selected @endif>{{$key+1}}</option>
                                            @else
                                                <option value="{{1}}"
                                                        @if($page->page_order == 1) selected @endif>{{1}}</option>
                                            @endif
                                        @endforeach
                                    @else
                                        <option value="{{1}}" @if($page->page_order == 1) selected @endif>{{1}}</option>
                                    @endif
                                </select>
                            </div>

                            @foreach($languages as $key=>$language)

                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="{{ $language->language_code }}_name">{{ $language->name }} Name<span
                                                class="error">*</span></label>
                                        <input type="text" class="form-control"
                                               name="{{ $language->language_code }}_name"
                                               id="{{ $language->language_code }}_name"
                                               value="{{ $page->translateOrNew($language->language_code)->name }}"
                                               @if($language->is_rtl == 1)
                                               dir="rtl"
                                               @endif
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
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                        <a href="{{ route('admin::webpage.index') }}" class="btn btn-secondary">Cancel</a>
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
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
    <script src="{{URL::asset('assets/js/custom/webPage.js')}}?v={{ time() }}"></script>
@endsection
