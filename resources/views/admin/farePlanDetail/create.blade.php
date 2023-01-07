@extends('admin.layouts.master')
@section('css')
    <link rel="stylesheet" type="text/css"
          href="{{URL::asset('assets/plugins/clockpicker/dist/bootstrap-clockpicker.min.css')}}">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

    <style>
        table.table td a {
            cursor: pointer;
            display: inline-block;
            margin: 0 5px;
            min-width: 24px;
        }

        table.table td a.delete {
            color: #E34724;
        }

        table.table td i {
            font-size: 19px;
        }

        table.table td a.delete_extra {
            color: #E34724;
        }

        table.table td a.delete_detail {
            color: #E34724;
        }


    </style>

@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">Fare Plan Detail</h4>
                <span class="text-muted mt-1 tx-13 ml-2 mb-0"></span>
            </div>
        </div>
    </div>
    <!-- breadcrumb -->
@endsection
@section('content')
    <!-- row -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" integrity="sha512-mSYUmp1HYZDFaVKK//63EcZq4iFWFjxSL+Z3T/aCt4IO9Cejm03q3NKKYN6pFQzY0SBOr8h+eCIAZHPXcpZaNw==" crossorigin="anonymous" />
    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="col-12 table-responsive" style="overflow-x:auto;">
                        <div class="col-12 border-bottom border-top mb-3  p-3 bg-light">
                            <div class="main-content-label mb-0"> Fare Plan Head</div>
                        </div>
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Fare Plan Name</th>
                                <th>Description</th>
                                <th>Country</th>
                                <th>Fare Plan Type</th>
                                <th>VAT</th>
                                <th>Tax</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Default/Optional</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tr>
                                <form method="POST" data-parsley-validate="" id="EditPlanHeadForm" role="form">
                                    <td><input type="hidden" class="form-control" name="edit_value" id="edit_value" value="{{$FarePlanHead->id}}">{{$FarePlanHead->id}}</td>
                                    <td><input type="text" class="form-control" name="fph_plan_name" id="fph_plan_name" value="{{$FarePlanHead->fph_plan_name}}" style="width: auto !important;"></td>
                                    <td><input type="text" class="form-control" name="fph_description" id="fph_description" value="{{$FarePlanHead->fph_description}}" style="width: auto !important;"></td>
                                    <td>
                                        @if(isset($FarePlanHead->country->name))
                                            <input type="hidden" class="form-control" name="fph_country_id" id="fph_country_id" value="{{$FarePlanHead->country->id}}">
                                            {{$FarePlanHead->country->name}}
                                        @endif
                                    </td>
                                    <td>
                                        <select class="form-control" id="fph_fare_type" name="fph_fare_type">
                                            <option value="intercity" @if($FarePlanHead->fph_fare_type == 'intercity'){{'selected'}} @endif >Inner City</option>
                                            <option value="outercity" @if($FarePlanHead->fph_fare_type == 'outercity'){{'selected'}} @endif>Outer City</option>
                                        </select>
                                    </td>
                                    <td><input type="text" class="form-control" name="fph_vat_per" id="fph_vat_per" value="{{$FarePlanHead->fph_vat_per}}" style="width: auto !important;"></td>
                                    <td><input type="text" class="form-control" name="fph_tax_per" id="fph_tax_per" value="{{$FarePlanHead->fph_tax_per}}" style="width: auto !important;"></td>

                                    <td><input type="text" class="form-control" name="fph_start_date" id="fph_start_date" value="{{$FarePlanHead->fph_start_date}}" min="<?php echo date("Y-m-d"); ?>"></td>
                                    <td><input type="text" class="form-control" name="fph_end_date" id="fph_end_date" value="{{$FarePlanHead->fph_end_date}}" min="<?php echo date("Y-m-d"); ?>"></td>
                                    <td>
                                        <select class="form-control" id="fph_is_default" name="fph_is_default" required>
                                            <option value="">Please Select One</option>
                                            <option value="default" @if($FarePlanHead->fph_is_default == 'default'){{'selected'}} @endif>Default</option>
                                            <option value="optional" @if($FarePlanHead->fph_is_default == 'optional'){{'selected'}} @endif>Optional</option>
                                        </select>
                                    </td>
                                    <td>
                                        <button class="btn btn-primary" type="submit">Update</button>
                                    </td>
                                </form>
                            </tr>
                        </table>
                    </div>
                    <form method="POST" data-parsley-validate="" id="addEditForm" role="form">
                        @csrf
                        <input type="hidden" id="FarePlanHeadId" name="FarePlanHeadId" value="{{$FarePlanHead->id}}">
                        <input type="hidden" id="form-method" value="add">
                        <div class="row row-sm">

                            <div class="col-12">

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="col-12 border-bottom border-top mb-3  p-3 bg-light">
                                            <div class="main-content-label mb-0">Add & Edit & View Fare Plan
                                                <b>Details</b></div>
                                        </div>
                                    </div>
                                    <input type="hidden" id="fpd_country_id" name="fpd_country_id"
                                           value="{{$FarePlanHead->fph_country_id}}">
                                    <div class="help-block with-errors error"></div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label for="tm_type_ref_id">Transport Type<span
                                                    class="error">*</span></label>
                                            <select id="fpd_transport_type_id" name="fpd_transport_type_id"
                                                    class="form-control" required>
                                                <option value="">Please Select Transport Type</option>
                                                @foreach($transportTypes as $type)
                                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                                @endforeach

                                            </select>
                                            <div class="help-block with-errors error"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <button type="button" class="btn btn-info add-new mt-4"
                                                    style="    margin-top: 30px !important;"><i class="fa fa-plus"></i>
                                                Add New Row
                                            </button>
                                            <br><br>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div id="selectAlert"></div>
                                    </div>

                                </div>

                                <div class="col-12" style="overflow-x:auto;">

                                    <table class="table table-bordered" id="tbls">
                                        <thead>
                                        <tr>

                                            <th>Base.Fare</th>
                                            <th>Cancel.Charges <a href="#" data-toggle="tooltip" data-placement="right"
                                                                  title="Customer or Driver Cancel Charges (Based on Control add Minutes in Control if driver or passenger after that lets say 3 minutes cancel the ride after accepted he will be charged with this rate)!"><i
                                                        class="fa fa-question-circle-o" aria-hidden="true"
                                                        style="font-size: 16px;"></i></a></th>
                                            <th>Cancel.Charges Per Minute <a href="#" data-toggle="tooltip" data-placement="right"
                                                                  title="Customer or Driver Cancel Charges Per minute lets say 3 minutes cancel the ride after accepted he will be charged with this rate)!"><i
                                                        class="fa fa-question-circle-o" aria-hidden="true"
                                                        style="font-size: 16px;"></i></a></th>
                                            <th>Per KM Charges <a href="#" data-toggle="tooltip" data-placement="right"
                                                                  title="Per KM Fare (Drive Time Fare) This will x with the KM Driver Drive the car!"><i
                                                        class="fa fa-question-circle-o" aria-hidden="true"
                                                        style="font-size: 16px;"></i></a></th>
                                            <th>Per Minute Charges <a href="#" data-toggle="tooltip"
                                                                      data-placement="right"
                                                                      title="Per Minute Fare (Drive Time Fare)th the Minutes Driver Drive the car !"><i
                                                        class="fa fa-question-circle-o" aria-hidden="true"
                                                        style="font-size: 16px;"></i></a></th>
                                            <th>Per KM Charges Before Pickup <a href="#" data-toggle="tooltip"
                                                                                data-placement="right"
                                                                                title="Per KM Fare Before Pickup (Add the KM in Control lets say Value is 5 then the distance driver drive before pickup added to customer total!"><i
                                                        class="fa fa-question-circle-o" aria-hidden="true"
                                                        style="font-size: 16px;"></i></a></th>
                                            <th>Per Minute Charges Before Pickup <a href="#" data-toggle="tooltip"
                                                                                    data-placement="right"
                                                                                    title="Per Minute Fare Before Pickup (Add the Minute in Control lets say Value is 5 then the time driver drive before pickup added to customer total!"><i
                                                        class="fa fa-question-circle-o" aria-hidden="true"
                                                        style="font-size: 16px;"></i></a></th>
                                            <th>Initial Waiting Minutes <a href="#" data-toggle="tooltip"
                                                                           data-placement="right"
                                                                           title="When Driver Arrived at Customer Location the wait time start after control app time added till he click on Start Ride (The Difference Between Arrive and before start Ride is wait Time)!"><i
                                                        class="fa fa-question-circle-o" aria-hidden="true"
                                                        style="font-size: 16px;"></i></a></th>
                                            <th>Estimate.percentage<a href="#" data-toggle="tooltip" data-placement="right"
                                                                      title="This is %age to estimate the customer when he is looking for a ride. If the actual ride value amount is coming 1 the extra % will be added and display an estimated amount as 1 to 1.500 as a range."><i
                                                        class="fa fa-question-circle-o" aria-hidden="true"
                                                        style="font-size: 16px;"></i></a></th>
                                            <th>start.time</th>
                                            <th>end.time</th>
                                            <th id="extra_charges">Fare.Plan.Detail.Extra.Charges<a href="#" data-toggle="tooltip" data-placement="right"
                                                                                                    title="This is the extra charges will be enabled upon completing of other fareplan fields, You will be able to add extra charges upon editing th e fareplan"><i
                                                        class="fa fa-question-circle-o" aria-hidden="true"
                                                        style="font-size: 16px;"></i></a></th>
                                            <th>Actions</th>
                                        </tr>
                                        </thead>
                                        <tbody id="fareDetail">

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-group mb-0 mt-3 justify-content-end">
                                <div>
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                    <a href="{{ route('admin::farePlanHead.index') }}"
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
    <div class="modal" id="select2modal">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content modal-content-demo">
                <div class="modal-header">
                    <h6 class="modal-title">Extra Charges (Plan Detail ID# -
                        <spam id="planDetailID"></spam>
                        )
                    </h6>
                    <button aria-label="Close" class="close" data-dismiss="modal" type="button"><span
                            aria-hidden="true">&times;</span></button>
                </div>

                <form method="POST" data-parsley-validate="" id="addEditExtraForm" role="form">
                    @csrf

                    <div class="modal-body">
                        <div class="col-sm-4">
                            <button type="button" class="btn btn-info add-new_extra"><i class="fa fa-plus"></i> Add New
                                Row
                            </button>
                        </div>
                        <br>

                        <input type="hidden" name="efc_plan_head_id" id="efc_plan_head_id">
                        <input type="hidden" name="efc_plan_detail_id" id="efc_plan_detail_id">
                        <div class="row">

                            <div class="col-12" style="overflow-x:auto;">
                                <table class="table table-bordered" id="tbl_add">
                                    <thead>
                                    <tr>

                                        <th>Extra.Fare.Key</th>
                                        <th>Extra.Fare.Info</th>
                                        <th>Extra.Fare.Charge</th>
                                        <th>Actions</th>

                                    </tr>
                                    </thead>
                                    <tbody id="fareExtraChargeModal">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">

                        <button type="submit" class="btn ripple btn-primary">Submit</button>
                        <button class="btn ripple btn-secondary" data-dismiss="modal" type="button">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- main-content closed -->
@endsection
@section('js')
    <script src="{{URL::asset('assets/plugins/clockpicker/dist/bootstrap-clockpicker.min.js')}}"></script>
    <script src="{{URL::asset('assets/js/custom/farePlanDetail.js')}}?v={{ time() }}"></script>
    <script src="{{URL::asset('assets/js/modal.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js" integrity="sha512-T/tUfKSV1bihCnd+MxKD0Hm1uBBroVYBOYSk1knyvQ9VyZJpc/ALb4P0r6ubwVPSGB2GvjeoMAJJImBG12TiaQ==" crossorigin="anonymous"></script>
    <script>
        $(function() {
            $('#fph_start_date').datepicker({
                startDate: '-0d',
                format: 'yyyy-mm-dd',
                setDate: new Date()
            });

            $('#fph_end_date').datepicker({
                startDate: '-0d',
                format: 'yyyy-mm-dd',
                setDate: new Date()
            });
        });
    </script>
@endsection
