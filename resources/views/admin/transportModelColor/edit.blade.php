@extends('admin.layouts.master')
@section('css')
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">Edit Transport Model</h4>

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
                        <input type="hidden" id="edit_value" name="edit_value" value="{{ $transportModelColor->id }}">
                        <input type="hidden" id="form-method" value="edit">
                        <div class="row row-sm">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="type_id">Transport Type<span
                                            class="error">*</span></label>
                                    <select id="type_id" name="type_id" class="form-control" required>
                                        <option value="">Please Select Transport Type</option>
                                        @foreach($transportTypes as $type)

                                            <option value="{{ $type->id }}"
                                                    @if($type->id==$transportModelColor->tmc_tt_ref_id) selected @endif
                                            >{{ $type->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="make_id">Transport Make<span
                                            class="error">*</span></label>
                                    <select id="make_id" name="make_id" class="form-control" required>
                                        <option value="">Please Select Transport Make</option>
                                        @foreach($transportMakes as $make)

                                            <option value="{{ $make->id }}"
                                                    @if($make->id==$transportModelColor->tmc_tm_ref_id) selected @endif
                                            >{{ $make->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="model_id">Transport Model<span
                                            class="error">*</span></label>
                                    <select id="model_id" name="model_id" class="form-control" required>
                                        <option value="">Please Select Transport Model</option>
                                        @foreach($transportModels as $models)

                                            <option value="{{ $models->id }}"
                                                    @if($models->id==$transportModelColor->tmc_tmo_id_ref) selected @endif
                                            >{{ $models->name }}</option>
                                        @endforeach

                                    </select>
                                    <div class="help-block with-errors error"></div>
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
                                               value="{{ $transportModelColor->translateOrNew($language->language_code)->name }}"
                                               placeholder="{{ $language->name }} Name" required/>
                                        <div class="help-block with-errors error"></div>
                                    </div>
                                </div>
                            @endforeach
                            <div class="col-12">
                                <div class="form-group mb-0 mt-3 justify-content-end">
                                    <div>
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                        <a href="{{ route('admin::transportModelColor.index') }}"
                                           class="btn btn-secondary"> Cancel </a>
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
    <script src="{{URL::asset('assets/js/custom/transportModelColor.js')}}?v={{ time() }}"></script>
@endsection
