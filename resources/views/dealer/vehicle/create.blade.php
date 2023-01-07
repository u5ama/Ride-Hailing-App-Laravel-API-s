@extends('admin.layouts.master')
@section('css')
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">Add Vehicle</h4>
                <span class="text-muted mt-1 tx-13 ml-2 mb-0">/ Vehicle</span>
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
                                    <label for="company_address_id">Branch <span class="error">*</span></label>
                                    <select id="company_address_id" name="company_address_id" class="form-control">
                                        <option value="">Please Select Branch</option>
                                        @foreach ($company_addresses as $company_address)
                                     <option value="{{$company_address->id}}"> 
                                        {{$company_address->address}} </option>
                                        @endforeach
                                    </select>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>

                            <div class="col-3">
                                <div class="form-group">
                                    <label for="daily_amount">Hourly Amount<span
                                            class="error">*</span></label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon1">$</span>
                                        </div>
                                        <input type="text" name="hourly_amount" id="hourly_amount" class="form-control float"
                                               placeholder="Hourly Amount" aria-label="hourly_amount" aria-describedby="basic-addon1">
                                    </div>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>

                            <div class="col-3">
                                <div class="form-group">
                                    <label for="daily_amount">Daily Amount<span
                                            class="error">*</span></label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon1">$</span>
                                        </div>
                                        <input type="text" name="daily_amount" id="daily_amount" class="form-control float"
                                               placeholder="Daily Amount" aria-label="daily_amount" aria-describedby="basic-addon1">
                                    </div>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>

                            <div class="col-3">
                                <div class="form-group">
                                    <label for="car_name">Weekly Amount<span
                                            class="error">*(Per Day)</span></label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon2">$</span>
                                        </div>
                                        <input type="text" name="weekly_amount" id="weekly_amount"
                                               class="form-control float"
                                               placeholder="Weekly Amount" aria-label="weekly_amount"
                                               aria-describedby="basic-addon1">
                                    </div>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>

                            <div class="col-3">
                                <div class="form-group">
                                    <label for="car_name">Monthly Amount<span
                                            class="error">*(Per Day)</span></label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon3">$</span>
                                        </div>
                                        <input type="text" name="monthly_amount" id="monthly_amount"
                                               class="form-control float"
                                               placeholder="Monthly Amount" aria-label="monthly_amount"
                                               aria-describedby="basic-addon3">
                                    </div>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>

                            <div class="col-3">
                                <div class="form-group">
                                    <label for="image">Brand<span class="error">*</span></label>
                                    <select id="make" name="make" class="form-control">
                                        <option value="">Please Select Brand</option>
                                        @foreach($makes as $make)
                                            <option value="{{ $make->id }}">{{ $make->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>

                            <div class="col-3">
                                <div class="form-group">
                                    <label for="image">Model<span class="error">*</span></label>
                                    <select id="model" name="model" class="form-control">
                                        <option value="">Please Select model</option>
                                    </select>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="form-group">
                                <label for="image">Model Year<span class="error">*</span></label>
                                    <select id="year" name="year" class="form-control">
                                        <option value="">Please Select Year</option>
                                        @foreach($modelyears as $modelyyear)
                                            <option value="{{ $modelyyear->id }}">{{ $modelyyear->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>
                             <div class="col-3">
                                <div class="form-group">
                                    <label for="color">Color<span class="error">*</span></label>
                                    <select id="color" name="color" class="form-control">
                                        <option value="">Please Select Color</option>
                                        @foreach($colors as $color)
                                            <option value="{{ $color->id }}">{{ $color->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>
                            <div class="col-12" id="ryde"></div>

                            

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="image">Featured<span class="error">*</span></label>
                                    <select id="featured" name="featured[]" class="form-control" multiple>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="image">Extra<span class="error">*</span></label>
                                    <table class="table">
                                        @foreach($extras as $key=>$extra)
                                            <tr>
                                                <td>{{ $extra->name }}</td>
                                                <td>
                                                    <input type="text" name="price[{{ $extra->id }}][]"
                                                           id="price_{{ $key }}"
                                                           class="form-control" placeholder="Price">
                                                </td>
                                            </tr>
                                        @endforeach
                                    </table>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>


                            <div class="col-12">
                                <div class="form-group mb-0 mt-3 justify-content-end">
                                    <div>
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                        <a href="{{ route('dealer::vehicle.index') }}"
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
    <script src="{{URL::asset('assets/js/custom/dealer/vehicle.js')}}?v={{ time() }}"></script>
@endsection
