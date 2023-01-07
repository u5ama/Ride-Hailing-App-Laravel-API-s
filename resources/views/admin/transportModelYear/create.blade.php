@extends('admin.layouts.master')
@section('css')
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">Add Transport Model Year</h4>

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
                                    <label for="tm_type_ref_id">Transport Type<span
                                            class="error">*</span></label>
                                    <select id="type_id" name="type_id" class="form-control" required>
                                        <option value="">Please Select Transport Type</option>
                                        @foreach($transportTypes as $type)
                                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="tmo_tm_id_ref">Transport Make<span
                                            class="error">*</span></label>
                                    <select id="make_id" name="make_id" class="form-control" required>
                                        <option value="">Please Select Transport Make</option>

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

                                    </select>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="model_color_id">Transport Model Color<span
                                            class="error">*</span></label>
                                    <select id="model_color_id" name="model_color_id" class="form-control" required>
                                        <option value="">Please Select Transport Model Color</option>

                                    </select>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="name">
                                        Year<span
                                            class="error">*</span></label>
                                    <input type="text" class="form-control"
                                           name="name"
                                           id="name"
                                           placeholder=" Name" required/>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group mb-0 mt-3 justify-content-end">
                                    <div>
                                        <button type="submit" class="btn btn-primary">submit</button>
                                        <a href="{{ route('admin::transportModelYear.index') }}"
                                           class="btn btn-secondary">cancel</a>
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
    <script src="{{URL::asset('assets/js/custom/transportModelYear.js')}}?v={{ time() }}"></script>
@endsection
