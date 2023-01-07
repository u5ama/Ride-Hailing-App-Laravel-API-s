@extends('admin.layouts.master')
@section('css')
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">Add Transport Type</h4>

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
                            @foreach($languages as $language)
                                <div class="col-12">
                                    <div class="form-group">
                                        <label for="{{ $language->language_code }}_name">{{ $language->name }} Name<span
                                                class="error">*</span></label>
                                        <input type="text" class="form-control"
                                               name="{{ $language->language_code }}_name"
                                               id="{{ $language->language_code }}_name"
                                               placeholder="{{ $language->name }}  Name" required/>
                                        <div class="help-block with-errors error"></div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-group">
                                        <label
                                            for="{{ $language->language_code }}_ttt_description">{{ $language->name }}
                                            Description<span
                                                class="error">*</span></label>
                                        <input type="text" class="form-control"
                                               name="{{ $language->language_code }}_ttt_description"
                                               id="{{ $language->language_code }}_ttt_description"
                                               placeholder="{{ $language->name }}  Description" required/>
                                        <div class="help-block with-errors error"></div>
                                    </div>
                                </div>
                            @endforeach

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="min_seats">min_seats<span class="error">*</span></label>
                                    <input type="number" class="form-control"
                                           name="tt_min_seats"
                                           id="tt_min_seats" required/>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>


                                <div class="col-12">
                                <div class="form-group">
                                    <label for="max_seats">max_seats<span class="error">*</span></label>
                                    <input type="number" class="form-control"
                                           name="tt_max_seats"
                                           id="tt_max_seats" required/>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>


                                <div class="col-12">
                                <div class="form-group">
                                    <label for="image">Image<span class="error">*</span></label>
                                    <input type="file" class="form-control dropify"
                                           name="type_image"
                                           id="type_image" required/>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="type_marker">Marker<span class="error">*</span></label>
                                    <input type="file" class="form-control dropify"
                                           name="type_marker"
                                           id="type_marker" required/>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group mb-0 mt-3 justify-content-end">
                                    <div>
                                        <button type="submit" class="btn btn-primary">submit</button>
                                        <a href="{{ route('admin::transportType.index') }}" class="btn btn-secondary">cancel</a>
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
    <script src="{{URL::asset('assets/js/custom/transportType.js')}}?v={{ time() }}"></script>
@endsection
