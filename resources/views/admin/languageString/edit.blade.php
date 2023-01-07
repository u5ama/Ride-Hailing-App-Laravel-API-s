@extends('admin.layouts.master')
@section('css')
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">Edit Language String</h4>
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
                        <input type="hidden" id="edit_value" name="edit_value" value="{{ $language_string->id }}">
                        <input type="hidden" id="form-method" value="edit">
                        <div class="row row-sm">

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="app_or_panel">App Or Panel<span class="error">*</span></label>
                                    <select class="form-control" id="bls_app_or_panel" name="bls_app_or_panel">
                                        <option value="1" @if($language_string->bls_app_or_panel==1) selected @endif>
                                            App
                                        </option>
                                        <option value="2" @if($language_string->bls_app_or_panel==2) selected @endif>
                                            Admin Panel
                                        </option>
                                        <option value="3" @if($language_string->bls_app_or_panel==3) selected @endif>
                                            Company Panel
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="app_or_panel">Driver Or Passenger<span class="error">*</span></label>
                                    <select class="form-control" id="bls_driver_or_passenger"
                                            name="bls_driver_or_passenger">
                                        <option value="1"
                                                @if($language_string->bls_driver_or_passenger== "Driver") selected @endif>
                                            Driver
                                        </option>
                                        <option value="2"
                                                @if($language_string->bls_driver_or_passenger== "Passenger") selected @endif>
                                            Passenger
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="app_or_panel">Screen Family<span class="error">*</span></label>
                                    <select class="form-control" id="bls_screen_family_id" name="bls_screen_family_id">
                                        @if(isset($language_screen_families))
                                            @foreach($language_screen_families as $language_screen_family)
                                                <option value="{{$language_screen_family->id}}"
                                                        @if($language_string->bls_screen_family_id==$language_screen_family->id) selected @endif>{{$language_screen_family->name}}</option>

                                            @endforeach
                                        @endif

                                    </select>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="app_or_panel">Language Screen<span class="error">*</span></label>
                                    <select class="form-control" id="bls_language_screen_ref_id"
                                            name="bls_language_screen_ref_id">
                                        @if(isset($language_screens))
                                            @foreach($language_screens as $language_screen)
                                                <option value="{{$language_screen->id}}"
                                                        @if($language_string->bls_language_screen_ref_id==$language_screen->id) selected @endif>{{$language_screen->blsc_title}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="app_or_panel">Language String Type<span class="error">*</span></label>
                                    <select class="form-control" id="bls_string_type_id" name="bls_string_type_id">
                                        <option value="1" @if($language_string->bls_string_type_id==1) selected @endif>
                                            App Screen
                                        </option>
                                        <option value="2" @if($language_string->bls_string_type_id==2) selected @endif>
                                            Back end
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="screen_name">Screen Info<span
                                            class="error">*</span></label>
                                    <textarea type="text" class="form-control"
                                              name="bls_screen_info"
                                              id="bls_screen_info"
                                              placeholder="Screen Info"
                                              required>{{ $language_string->bls_screen_info }}</textarea>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="screen_name">Language Key Field<span
                                            class="error">*</span></label>
                                    <input type="text" class="form-control"
                                           name="bls_name_key"
                                           id="bls_name_key"
                                           value="{{ $language_string->bls_name_key }}"
                                           placeholder="Language Key Field" disabled/>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>

                            @foreach($languages as $language)
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="{{ $language->language_code }}_name">{{ $language->name }}
                                            Value<span
                                                class="error">*</span></label>
                                        <input type="text" class="form-control"
                                               name="{{ $language->language_code }}_name"
                                               id="{{ $language->language_code }}_name"
                                               value="{{ $language_string->translateOrNew($language->language_code)->name }}"
                                               @if($language->is_rtl == 1)
                                               dir="rtl"
                                               @endif
                                               placeholder="{{ $language->name }} Value" required/>
                                        <div class="help-block with-errors error"></div>
                                    </div>
                                </div>
                            @endforeach

                            <div class="col-12">
                                <div class="form-group mb-0 mt-3 justify-content-end">
                                    <div>
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                        <a href="{{ route('admin::languageString.index') }}"
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
    <script src="{{URL::asset('assets/js/custom/languageString.js')}}?v={{ time() }}"></script>
@endsection
