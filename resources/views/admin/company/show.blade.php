@extends('admin.layouts.master')
@section('css')
    <link href="{{URL::asset('assets/plugins/fancybox/jquery.fancybox.css')}}" rel="stylesheet">
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ $company->com_name }} Detail</h4>
            </div>
        </div>

        <div class="d-flex my-xl-auto right-content">
            <div class="pr-1 mb-3 mb-xl-0">
                <a href="{{ route('admin::driver.addDriver',[$company->id]) }}"
                   class="btn btn-info  mr-2">
                    <i class="mdi mdi-plus-circle"></i> Add New Driver
                </a>
            </div>
        </div>
    </div>
    <!-- breadcrumb -->
@endsection
@section('content')
    <!-- row opened -->
    <div class="row row-sm">
        <div class="col-xl-6 py-2">
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="card-title mb-3">Company Detail</h6>
                    <div class="row border-top border-bottom p-2">
                        <div class="col-md-6">
                            <h6 class="mb-0"> Company Name </h6>
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-0">{{ $company->com_name }}</h6>
                        </div>
                    </div>

                    <div class="row  border-bottom p-2">
                        <div class="col-md-6">
                            <h6 class="mb-0">Contact Number</h6>
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-0">{{ $company->com_contact_number }}</h6>
                        </div>
                    </div>

                    <div class="row border-bottom p-2">
                        <div class="col-md-6">
                            <h6 class="mb-0">Company Full Contact Number</h6>
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-0">{{ $company->com_full_contact_number }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6 py-2">
            <div class="card card h-100">
                <div class="card-body">
                    <h6 class="card-title mb-3">Company Further Detail</h6>
                    <div class="row border-top border-bottom p-2">
                        <div class="col-md-6">
                            <h6 class="mb-0">Company License Number</h6>
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-0">{{ $company->com_license_no }}</h6>
                        </div>
                    </div>
                    <div class="row border-bottom p-2">
                        <div class="col-md-6">
                            <h6 class="mb-0">Radius</h6>
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-0">{{ $company->com_radius }}</h6>
                        </div>
                    </div>

                    <div class="row border-bottom p-2">
                        <div class="col-md-6">
                            <h6 class="mb-0">{{ 'Email' }}</h6>
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-0">{{ $company->email }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="border-bottom mb-3">
                        <h6 class="card-title">Drivers</h6>
                    </div>
                    <div class="table-responsive">
                        <table class="table mg-b-0 text-md-nowrap" id="data-table">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Transport Type</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Contact Number</th>
                                <th>Created at</th>
                                <th>Driver Registration In App</th>
                                <th>Status</th>
                                <th>Change Status</th>
                                <th>Manual OTP</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>

                            @if(isset($drivers) && !empty($drivers))
                            @foreach($drivers as $driver)
                                @php $transport_type = ''; @endphp
                                @if(isset($driver->driverProf) && $driver->driverProf != null)
                                    <?php



                                    if(isset($driver->driverProf->dp_transport_type_id_ref)){
                                        $id_type = $driver->driverProf->dp_transport_type_id_ref;
                                        if(!empty($id_type)) {

                                        $t_type =  \App\TransportType::listsTranslations('name')->where('transport_types.id',$id_type)->first();

                                          $transport_type = $t_type['name'];
                                    }

                                    }

                                    ?>
                                @endif
                                <tr>
                                    <td>{{ $driver->id }}</td>
                                    <td>@if(!empty($driver->du_profile_pic))
                                            <img src="{{asset($driver->du_profile_pic)}}" width="100" heigh="100">
                                        @else
                                            @php $url = asset('assets/default/driver.png') @endphp
                                            <img src="{{$url}}" width="100" heigh="100">
                                        @endif
                                    </td>
                                    <td> {{$transport_type}}</td>
                                    <td>{{ $driver->du_full_name }}</td>
                                    <td>{{ $driver->email }}</td>
                                    <td>{{ $driver->du_full_mobile_number }}</td>
                                    <td>{{ $driver->du_created_at }}</td>
                                    <td>
                                                @if($driver->du_is_reg_active == "0")
                                                    @php
                                                        $class_reg = "badge badge-danger";
                                                        $name_reg = "Not Allow";
                                                    @endphp
                                                @endif

                                                @if($driver->du_is_reg_active == '1')
                                                    @php
                                                        $class_reg = "badge badge-success";
                                                        $name_reg = "Allow";
                                                    @endphp
                                                @endif

                                                <a type="button" class="{{$class_reg}}" data-toggle="tooltip"
                                                   data-placement="top" onclick="changeDriverRegStatus('{{$driver->id}}','{{$driver->du_is_reg_active}}','{{$company->id}}')">{{$name_reg}}</a>
                                            </td>
                                    @if($driver->DriverProfile !== null)
                                    @if($driver->is_signup_mobile == 1)
                                        @if($driver->is_company_update == 0)
                                            <td colspan="2">
                                                <input type="hidden" name="driver_id" id="driver_id" value="{{$driver->id}}">
                                                <select name="is_company_update" id="is_company_update" class="form-control select2" onchange="updateCompanyStatus(this);">
                                                    <option value="">{{ 'Select One' }}</option>
                                                    {{ \App\Utility\Utility::create_option("companies","id","com_name") }}
                                                </select>
                                            </td>
                                        @else
                                            <td>
                                                @if($driver->du_driver_status == "driver_status_when_block")
                                                    @php
                                                        $class = "badge badge-danger";
                                                        $name = "Block";
                                                    @endphp
                                                @endif
                                                @if($driver->du_driver_status == 'driver_status_when_pending')
                                                    @php
                                                        $class = "badge badge-warning";
                                                        $name = "Pending";
                                                    @endphp
                                                @endif
                                                @if($driver->du_driver_status == 'driver_status_when_approved')
                                                    @php
                                                        $class = "badge badge-success";
                                                        $name = "Approve";
                                                    @endphp
                                                @endif

                                                <a type="button" class="{{$class}}" data-toggle="tooltip"
                                                   data-placement="top">{{$name}}</a>
                                            </td>
                                            <td>
                                                <?php
                                                $select_option = '<select class="form-control" onchange="updateDriverStatus(' . $driver->id . ',' . $company->id . ')" id="driver_status_' . $driver->id . '">';
                                                $select_option .= ($driver->du_driver_status == "driver_status_when_block") ? "<option value='driver_status_when_block' selected>Block</option>" : "<option value='driver_status_when_block'  >Block</option>";
                                                $select_option .= ($driver->du_driver_status == "driver_status_when_pending") ? "<option value='driver_status_when_pending' selected>Pending</option>" : "<option value='driver_status_when_pending' >Pending</option>";
                                                $select_option .= ($driver->du_driver_status == "driver_status_when_approved") ? "<option value='driver_status_when_approved' selected>Approve</option>" : "<option value='driver_status_when_approved' >Approve</option>";
                                                $select_option .= "</select>";
                                                echo $select_option;
                                                ?>
                                            </td>
                                        @endif
                                        @else
                                        <td>
                                            @if($driver->du_driver_status == "driver_status_when_block")
                                                @php
                                                    $class = "badge badge-danger";
                                                    $name = "Block";
                                                @endphp
                                            @endif
                                            @if($driver->du_driver_status == 'driver_status_when_pending')
                                                @php
                                                    $class = "badge badge-warning";
                                                    $name = "Pending";
                                                @endphp
                                            @endif
                                            @if($driver->du_driver_status == 'driver_status_when_approved')
                                                @php
                                                    $class = "badge badge-success";
                                                    $name = "Approve";
                                                @endphp
                                            @endif

                                            <a type="button" class="{{$class}}" data-toggle="tooltip"
                                               data-placement="top">{{$name}}</a>
                                        </td>
                                        <td>
                                            <?php
                                            $select_option = '<select class="form-control" onchange="updateDriverStatus(' . $driver->id . ',' . $company->id . ')" id="driver_status_' . $driver->id . '">';
                                            $select_option .= ($driver->du_driver_status == "driver_status_when_block") ? "<option value='driver_status_when_block' selected>Block</option>" : "<option value='driver_status_when_block'  >Block</option>";
                                            $select_option .= ($driver->du_driver_status == "driver_status_when_pending") ? "<option value='driver_status_when_pending' selected>Pending</option>" : "<option value='driver_status_when_pending' >Pending</option>";
                                            $select_option .= ($driver->du_driver_status == "driver_status_when_approved") ? "<option value='driver_status_when_approved' selected>Approve</option>" : "<option value='driver_status_when_approved' >Approve</option>";
                                            $select_option .= "</select>";
                                            echo $select_option;
                                            ?>
                                        </td>
                                        @endif
                                    @else
                                        <td colspan="2">
                                            <p>Driver Profile Not Completed</p>
                                        </td>
                                    @endif
{{--                                    @else--}}
{{--                                        <td>--}}
{{--                                            <select name="is_company_update" id="is_company_update" class="form-control">--}}
{{--                                                <option value="">{{ 'Select One' }}</option>--}}
{{--                                                {{ \App\Utility\Utility::create_option("companies","id","com_name") }}--}}
{{--                                            </select>--}}
{{--                                        </td>--}}
{{--                                    @endif--}}
                                    <td>
                                        <div class="btn-icon-list">
                                            <a type="button"
                                               class="addOTP btn btn-info btn-icon"
                                               data-target="#modaldemo55" data-toggle="modal"
                                               data-effect="effect-fall"
                                               data-id="{{ $driver->id }}">
                                                <i class="bx bx-plus font-size-16 align-middle"></i>
                                            </a>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-icon-list">
                                            <a href="{{ route('admin::driver.editDriver',[$company->id,$driver->id]) }}"
                                               class="btn btn-info btn-icon"
                                               data-effect="effect-fall"
                                               data-id="{{ $driver->id }}"
                                               data-toggle="tooltip" data-placement="top"
                                               title="Edit">
                                                <i class="bx bx-pencil font-size-16 align-middle"></i>
                                            </a>&nbsp;
                                            <a type="button" data-rideid="{{ $driver->id }}" class="driver-details btn btn-info btn-icon" data-effect="effect-fall" data-placement="top" title="Driver Detail" data-target="#modaldemo3" data-toggle="modal"><i class="fas fa-eye font-size-16 align-middle"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!--/div-->
    </div>
    <!-- /row -->
    </div>
    <!-- Container closed -->
    </div>


    <div class="modal" id="modaldemo55">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content modal-content-demo">
                <div class="modal-header">
                    <h6 class="modal-title">Add OTP Manually</h6>
                    <button aria-label="Close" class="close" data-dismiss="modal" type="button"><span
                            aria-hidden="true">&times;</span></button>
                </div>
                <form method="POST" data-parsley-validate="" id="addEditFormOTP" role="form">
                    @csrf
                    <div class="modal-body">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <input type="hidden" id="driverId" name="driverId">
                                <input class="form-control" id="manual" name="du_otp_manual"
                                       placeholder="Enter Manual OTP" required oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength); if (this.value.length < this.minlength) this.value = this.value.slice(0, this.minlength);"
                                       type = "number"
                                       minlength = "6"
                                       maxlength = "6">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn ripple btn-secondary" data-dismiss="modal" type="button">Close</button>
                        <button type="submit" class="btn ripple btn-secondary" >Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div class="modal fade" id="modaldemo3" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="globalModalTitle">Modal title</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="globalModalDetails"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">close</button>
                </div>
            </div>
        </div>
    </div>
    <!-- main-content closed -->
@endsection
@section('js')
    <script src="{{URL::asset('assets/plugins/fancybox/jquery.fancybox.js')}}"></script>
    <script src="{{URL::asset('assets/js/custom/CompanyDetail.js')}}"></script>
@endsection
