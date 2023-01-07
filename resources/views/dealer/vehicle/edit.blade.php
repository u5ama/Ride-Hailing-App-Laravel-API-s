@extends('admin.layouts.master')
@section('css')
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">Edit Vehicle</h4>
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
                        <input type="hidden" id="edit_value" name="edit_value" value="{{ $vehicle->id }}">
                        <input type="hidden" id="form-method" value="edit">
                        <div class="row row-sm">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="company_address_id">Branch <span class="error">*</span></label>
                                    <select id="company_address_id" name="company_address_id" class="form-control">
                                        <option value="">Please Select Branch</option>
                                        @foreach ($company_addresses as $company_address)
                                     <option value="{{$company_address->id}}" @if($vehicle->company_address_id==$company_address->id) selected @endif> 
                                        {{$company_address->address}} </option>
                                        @endforeach
                                    </select>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>



                            <div class="col-3">
                                <div class="form-group">
                                    <label for="car_name">Hourly Amount<span
                                            class="error">*</span></label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon1">$</span>
                                        </div>
                                        <input type="text" name="hourly_amount" id="hourly_amount" class="form-control float"
                                               value="{{ $vehicle->hourly_amount }}" required
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
                                               placeholder="Daily Amount" value="{{ $vehicle->daily_amount }}" required aria-label="daily_amount" aria-describedby="basic-addon1">
                                    </div>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>

                            <div class="col-3">
                                <div class="form-group">
                                    <label for="car_name">Weekly Amount<span
                                            class="error">*</span></label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon2">$</span>
                                        </div>
                                        <input type="text" name="weekly_amount" id="weekly_amount"
                                               class="form-control float" value="{{ $vehicle->weekly_amount }}" required
                                               placeholder="Weekly Amount" aria-label="weekly_amount"
                                               aria-describedby="basic-addon1">
                                    </div>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>

                            <div class="col-3">
                                <div class="form-group">
                                    <label for="car_name">Monthly Amount<span
                                            class="error">*</span></label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon3">$</span>
                                        </div>
                                        <input type="text" name="monthly_amount" id="monthly_amount"
                                               class="form-control float" value="{{ $vehicle->monthly_amount }}"
                                               placeholder="Monthly Amount" aria-label="monthly_amount" required
                                               aria-describedby="basic-addon3">
                                    </div>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>


                            <div class="col-3">
                                <div class="form-group">
                                    <label for="image">Brand<span class="error">*</span></label>
                                    <select id="make" name="make" class="form-control" required>
                                        <option value="">Please Select Make</option>
                                        @foreach($makes as $make)
                                            <option value="{{ $make->id }}"
                                                    @if($vehicle->ryde->brand_id==$make->id) selected @endif>{{ $make->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>

                            <div class="col-3">
                                <div class="form-group">
                                    <label for="image">Model<span class="error">*</span></label>
                                    <select id="model" name="model" class="form-control" required>
                                        @foreach($brand_models as $brand_model)
                                            <option value="{{ $brand_model->id }}"
                                                    @if($vehicle->ryde->brand_model_id==$brand_model->id) selected @endif
                                            >{{ $brand_model->name }}</option>
                                        @endforeach
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
                                            <option value="{{ $modelyyear->id }}" @if($vehicle->ryde->model_year_id==$modelyyear->id) selected @endif>{{ $modelyyear->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>

                             <div class="col-3">
                                <div class="form-group">
                                    <label for="image">Color<span class="error">*</span></label>
                                    <select id="color" name="color" class="form-control">
                                        <option value="">Please Select Color</option>
                                        @foreach($colors as $color)
                                            <option value="{{ $color->id }}" @if($vehicle->color_id==$color->id) selected @endif>{{ $color->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>

                            <div class="col-12" id="ryde">
                                @include('admin.vehicle.rydeshow')
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="image">Featured<span class="error">*</span></label>
                                    <select id="featured" name="featured[]" class="form-control" multiple>
                                        <?php
                                        $categoryVehicle_array = [];
                                        foreach ($vehicle->categoryVehicle as $categoryFeature) {
                                            // $vehicleFeature[]=$vehicleFeature->feature_id;
                                            $categoryVehicle_array[] = $categoryFeature->category_id;
                                        }

                                        ?>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}"  @if(in_array($category->id,$categoryVehicle_array)) selected @endif>{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="help-block with-errors error"></div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label for="image">Extra<span class="error">*</span></label>
                                    <table class="table">

                                        <?php
                                        $vehicleExtra_array = [];
                                        foreach ($vehicle->vehicleExtra as $vehicleExtra) {
                                            $vehicleExtra_array[$vehicleExtra->extra_id] = $vehicleExtra->price;
                                        }

                                        ?>

                                        @foreach($extras as $key=>$extra)

                                            <tr>
                                                <td>{{ $extra->name }}</td>
                                                <td>
                                                    <input type="text" name="price[{{ $extra->id }}][]"
                                                           id="price_{{ $key }}"
                                                           value="{{ $vehicleExtra_array[$extra->id] }}"
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
