@extends('admin.layouts.master')
@section('css')
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">Add Base APP Reference</h4>
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
                                    <select class="form-control" id="bar_mod_id_ref" name="bar_mod_id_ref" required="">
                                        @if(isset($ref_module))
                                            @foreach($ref_module as $item)
                                                <option value="{{$item->id}}"
                                                        @if($app_ref->bar_mod_id_ref==$item->id) selected @endif>{{$item->barm_name}}</option>

                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="app_or_panel">Reference Type<span class="error">*</span></label>
                                    <select class="form-control" id="bar_ref_type_id" name="bar_ref_type_id" required="">
                                        <option value="">Please Select Reference Module</option>
                                        @if(isset($refsTypes))
                                            @foreach($refsTypes as $item)
                                                <option value="{{$item->id}}"
                                                        @if($app_ref->bar_ref_type_id==$item->id) selected @endif>{{$item->name}}</option>

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
                                    <select class="form-control" id="bar_order_by" name="bar_order_by">
                                        @if(isset($refs))
                                            @foreach($refs as $key=>$ref)
                                                
                                                <option value="{{$key+1}}"
                                                            @if($app_ref->bar_order_by==$key+1) selected @endif >{{$key+1}}</option>
                                                
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
                                           name="bar_icon"
                                           id="bar_icon"
                                           placeholder="Module Icon"/>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="screen_info">Old Module Icon<span
                                            class="error">*</span></label>
                                    <img src="{{asset($app_ref->bar_icon)}}" height=\"50\"/>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="screen_info">Module Image<span
                                            class="error">*</span></label>
                                    <input type="file" class="form-control"
                                           name="bar_image"
                                           id="bar_image"
                                           placeholder="Module Image"/>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="screen_info">Old Module Image<span
                                            class="error">*</span></label>
                                    <img src="{{asset($app_ref->bar_image)}}" height=\"50\"/>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group mb-0 mt-3 justify-content-end">
                                    <div>
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                        <a href="{{ route('admin::appReference.index') }}"
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
    <script src="{{URL::asset('assets/js/custom/AppReference.js')}}?v={{ time() }}"></script>
@endsection
