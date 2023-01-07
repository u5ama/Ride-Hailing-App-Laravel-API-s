@extends('admin.layouts.master')
@section('css')
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">Add Base APP Reference Type</h4>
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
                        <input type="hidden" id="edit_value" name="edit_value" value="{{ $app_ref->id }}">
                        <input type="hidden" id="form-method" value="edit">
                        <input type="hidden" id="form-method" value="add">
                        <div class="row row-sm">

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="app_or_panel">Reference Module<span class="error">*</span></label>
                                    <select class="form-control" id="bart_mod_id_ref" name="bart_mod_id_ref">
                                        @if(isset($ref_module))
                                            @foreach($ref_module as $item)
                                                <option value="{{$item->id}}"
                                                        @if($app_ref->bart_mod_id_ref==$item->id) selected @endif>{{$item->barm_name}}</option>

                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>

                            @foreach($languages as $language)
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="{{ $language->language_code }}_name">{{ $language->name }} Name<span
                                                class="error">*</span></label>
                                        <input type="text" class="form-control"
                                               name="{{ $language->language_code }}_name"
                                               id="{{ $language->language_code }}_name"
                                               value="{{ $app_ref->translateOrNew($language->language_code)->name }}"
                                               placeholder="{{ $language->name }} Name" required/>
                                        <div class="help-block with-errors error"></div>
                                    </div>
                                </div>
                            @endforeach
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="app_or_panel">Order By<span class="error">*</span></label>
                                    <select class="form-control" id="bart_order_by" name="bart_order_by">
                                        @if(isset($refs))
                                            @foreach($refs as $key=>$ref)
                                                @if(count($refs)>=1)
                                                    <option value="{{$key+1}}"
                                                            @if($app_ref->bart_order_by==$item->id) selected @endif >{{$key+1}}</option>
                                                @else
                                                    <option value="{{1}}"
                                                            @if($app_ref->bart_order_by==$item->id) selected @endif >{{1}}</option>
                                                @endif
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="screen_info">Module Icon<span
                                            class="error">*</span></label>
                                    <input type="file" class="form-control"
                                           name="bart_icon"
                                           id="bart_icon"
                                           placeholder="Module Icon"/>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>
                            @if(isset($app_ref->bart_icon) && !empty($app_ref->bart_icon))
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="screen_info">Old Module Icon<span
                                                class="error">*</span></label>
                                        <img src="{{asset($app_ref->bart_icon)}}" height=\"50\"/>
                                        <div class="help-block with-errors error"></div>
                                    </div>
                                </div>
                            @endif
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="screen_info">Module Image<span
                                            class="error">*</span></label>
                                    <input type="file" class="form-control"
                                           name="bart_image"
                                           id="bart_image"
                                           placeholder="Module Image"/>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>
                            @if(isset($app_ref->bart_image) && !empty($app_ref->bart_image))
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="screen_info">Old Module Image<span
                                                class="error">*</span></label>
                                        <img src="{{asset($app_ref->bart_image)}}" height=\"50\"/>
                                        <div class="help-block with-errors error"></div>
                                    </div>
                                </div>
                            @endif

                            <div class="col-12">
                                <div class="form-group mb-0 mt-3 justify-content-end">
                                    <div>
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                        <a href="{{ route('admin::referenceType.index') }}"
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
    <script src="{{URL::asset('assets/js/custom/ReferenceType.js')}}?v={{ time() }}"></script>
@endsection
