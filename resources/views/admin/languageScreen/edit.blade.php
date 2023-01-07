@extends('admin.layouts.master')
@section('css')
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">Add Language String</h4>
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
                        <input type="hidden" id="edit_value" name="edit_value" value="{{ $language_screen->id }}">
                        <input type="hidden" id="form-method" value="edit">
                        <div class="row row-sm">

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="app_or_panel">Screen Family<span class="error">*</span></label>
                                    <select class="form-control" id="blsc_screen_family_id"
                                            name="blsc_screen_family_id">
                                        @if(isset($language_screen_families))
                                            @foreach($language_screen_families as $language_screen_family)
                                                <option value="{{$language_screen_family->id}}"
                                                        @if($language_screen->blsc_screen_family_id==$language_screen_family->id) selected @endif>{{$language_screen_family->name}}</option>

                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="screen_name">Screen Title<span
                                            class="error">*</span></label>
                                    <input type="text" class="form-control"
                                           name="blsc_title"
                                           id="blsc_title"
                                           value="{{$language_screen->blsc_title}}"
                                           placeholder="Screen Title" required/>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="screen_name">Screen Image<span
                                            class="error">*</span></label>
                                    <input type="file" class="form-control"
                                           name="blsc_image"
                                           id="blsc_image"
                                           placeholder="Screen Image"/>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="screen_name">Old Image<span
                                        class="error">*</span></label>
                                <img src="{{asset($language_screen->blsc_image)}}" height=\"50\"/>
                                <div class="help-block with-errors error"></div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-group mb-0 mt-3 justify-content-end">
                                <div>
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                    <a href="{{ route('admin::languageScreen.index') }}"
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
    <script src="{{URL::asset('assets/js/custom/languageScreen.js')}}?v={{ time() }}"></script>
@endsection
