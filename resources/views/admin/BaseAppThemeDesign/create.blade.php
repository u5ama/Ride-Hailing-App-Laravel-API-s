@extends('admin.layouts.master')
@section('css')
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">Add Base APP Theme</h4>
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
                                    <label for="app_or_panel">Select Theme<span class="error">*</span></label>
                                    <select class="form-control" id="batd_theme_ref_id" name="batd_theme_ref_id">
                                        @if(count($themes) > 0)
                                            @foreach($themes as $theme)
                                                <option value="{{$theme->id}}">{{$theme->bat_theme_name}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="app_or_panel">Select Screen<span class="error">*</span></label>
                                    <select class="form-control" id="batd_language_screen_ref_id"
                                            name="batd_language_screen_ref_id">
                                        @if(isset($language_screens))
                                            @foreach($language_screens as $language_screen)
                                                <option
                                                    value="{{$language_screen->id}}">{{$language_screen->blsc_title}}</option>

                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="screen_info">Screen Info<span
                                            class="error">*</span></label>
                                    <input type="text" class="form-control"
                                           name="batd_screen_info"
                                           id="batd_screen_info"
                                           placeholder="Screen Info" required/>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="app_or_panel">Select Device Type<span class="error">*</span></label>
                                    <select class="form-control" id="batd_device_type" name="batd_device_type">
                                        <option value="IOS">IOS</option>
                                        <option value="Android">Android</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="screen_info">Design Key Field<span
                                            class="error">*</span></label>
                                    <input type="text" class="form-control"
                                           name="batd_design_key_field"
                                           id="batd_design_key_field"
                                           placeholder="Design Key Field" required/>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="screen_info">Design Value<span
                                            class="error">*</span></label>
                                    <input type="text" class="form-control"
                                           name="batd_design_value"
                                           id="batd_design_value"
                                           placeholder="Design Value" required/>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="screen_info">Color Code<span
                                            class="error">*</span></label>
                                    <input type="text" class="form-control"
                                           name="batd_color_code"
                                           id="batd_color_code"
                                           placeholder="Color Code" required/>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="screen_info">Description<span
                                            class="error">*</span></label>
                                    <textarea type="text" class="form-control"
                                              name="batd_description"
                                              id="batd_description"
                                              placeholder="Description" required></textarea>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group mb-0 mt-3 justify-content-end">
                                    <div>
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                        <a href="{{ route('admin::BaseAppThemeDesign.index') }}"
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
    <script src="{{URL::asset('assets/js/custom/BaseAppThemeDesign.js')}}?v={{ time() }}"></script>
@endsection
