@extends('admin.layouts.master')
@section('css')
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">Email Templates Settings</h4>
                <span class="text-muted mt-1 tx-13 ml-2 mb-0"></span>
            </div>
        </div>

    </div>
    <!-- breadcrumb -->
@endsection
@section('content')
    <!-- row opened -->
    <div class="row row-sm">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table mg-b-0 text-md-nowrap" id="data-table">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Template</th>
                                <th>Preview</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>1</td>
                                <td>Welcome Passenger Email Template</td>
                                <td>
                                    <a class="btn btn-sm btn-outline-info waves-effect waves-light"
                                       href="{{route('admin::welcome_email_settings.show', [1])}}" data-toggle="tooltip"
                                       data-placement="top" title="View"><i
                                            class="fas fa-eye font-size-16 align-middle"></i></a>
                                </td>
                                <td>
                                    <a href="{{route('admin::welcome_email_settings.index')}}"
                                       class="btn btn-sm btn-outline-info waves-effect waves-light"
                                       data-toggle="tooltip" data-placement="top" title="Edit"><i
                                            class="bx bx-pencil font-size-16 align-middle"></i></a>
                                </td>
                            </tr>

                            <tr>
                                <td>2</td>
                                <td>Welcome Driver Email Template</td>
                                <td>
                                    <a class="btn btn-sm btn-outline-info waves-effect waves-light"
                                       href="{{route('admin::welcome_email_driver_settings.show', [7])}}" data-toggle="tooltip"
                                       data-placement="top" title="View"><i
                                            class="fas fa-eye font-size-16 align-middle"></i></a>
                                </td>
                                <td>
                                    <a href="{{route('admin::welcome_email_driver_settings.index')}}"
                                       class="btn btn-sm btn-outline-info waves-effect waves-light"
                                       data-toggle="tooltip" data-placement="top" title="Edit"><i
                                            class="bx bx-pencil font-size-16 align-middle"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td>OTP Email Template</td>
                                <td>
                                    <a class="btn btn-sm btn-outline-info waves-effect waves-light"
                                       href="{{route('admin::otp_email_settings.show', [2])}}" data-toggle="tooltip"
                                       data-placement="top" title="View"><i
                                            class="fas fa-eye font-size-16 align-middle"></i></a>
                                </td>
                                <td>
                                    <a href="{{route('admin::otp_email_settings.index')}}"
                                       class="btn btn-sm btn-outline-info waves-effect waves-light"
                                       data-toggle="tooltip" data-placement="top" title="Edit"><i
                                            class="bx bx-pencil font-size-16 align-middle"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <td>4</td>
                                <td>Cancel Email Template</td>
                                <td>
                                    <a class="btn btn-sm btn-outline-info waves-effect waves-light"
                                       href="{{route('admin::cancel_email_settings.show', [3])}}" data-toggle="tooltip"
                                       data-placement="top" title="View"><i
                                            class="fas fa-eye font-size-16 align-middle"></i></a>
                                </td>
                                <td>
                                    <a href="{{route('admin::cancel_email_settings.index')}}"
                                       class="btn btn-sm btn-outline-info waves-effect waves-light"
                                       data-toggle="tooltip" data-placement="top" title="Edit"><i
                                            class="bx bx-pencil font-size-16 align-middle"></i></a>
                                </td>
                            </tr>

                            <tr>
                                <td>5</td>
                                <td>Receipt Email Template</td>
                                <td>
                                    <a class="btn btn-sm btn-outline-info waves-effect waves-light"
                                       href="{{route('admin::receipt_email_settings.show', [4])}}" data-toggle="tooltip"
                                       data-placement="top" title="View"><i
                                            class="fas fa-eye font-size-16 align-middle"></i></a>
                                </td>
                                <td>
                                    <a href="{{route('admin::receipt_email_settings.index')}}"
                                       class="btn btn-sm btn-outline-info waves-effect waves-light"
                                       data-toggle="tooltip" data-placement="top" title="Edit"><i
                                            class="bx bx-pencil font-size-16 align-middle"></i></a>
                                </td>
                            </tr>

                            <tr>
                                <td>6</td>
                                <td>Welcome Company Email Template</td>
                                <td>
                                    <a class="btn btn-sm btn-outline-info waves-effect waves-light"
                                       href="{{route('admin::welcome_company_email_settings.show', [5])}}" data-toggle="tooltip"
                                       data-placement="top" title="View"><i
                                            class="fas fa-eye font-size-16 align-middle"></i></a>
                                </td>
                                <td>
                                    <a href="{{route('admin::welcome_company_email_settings.index')}}"
                                       class="btn btn-sm btn-outline-info waves-effect waves-light"
                                       data-toggle="tooltip" data-placement="top" title="Edit"><i
                                            class="bx bx-pencil font-size-16 align-middle"></i></a>
                                </td>
                            </tr>

                            <tr>
                                <td>7</td>
                                <td>Company Approval Email Template</td>
                                <td>
                                    <a class="btn btn-sm btn-outline-info waves-effect waves-light"
                                       href="{{route('admin::company_approval_email_settings.show', [6])}}" data-toggle="tooltip"
                                       data-placement="top" title="View"><i
                                            class="fas fa-eye font-size-16 align-middle"></i></a>
                                </td>
                                <td>
                                    <a href="{{route('admin::company_approval_email_settings.index')}}"
                                       class="btn btn-sm btn-outline-info waves-effect waves-light"
                                       data-toggle="tooltip" data-placement="top" title="Edit"><i
                                            class="bx bx-pencil font-size-16 align-middle"></i></a>
                                </td>
                            </tr>

                            <tr>
                                <td>8</td>
                                <td>Driver Approval Email Template</td>
                                <td>
                                    <a class="btn btn-sm btn-outline-info waves-effect waves-light"
                                       href="{{route('admin::driver_approval_email_settings.show', [8])}}" data-toggle="tooltip"
                                       data-placement="top" title="View"><i
                                            class="fas fa-eye font-size-16 align-middle"></i></a>
                                </td>
                                <td>
                                    <a href="{{route('admin::driver_approval_email_settings.index')}}"
                                       class="btn btn-sm btn-outline-info waves-effect waves-light"
                                       data-toggle="tooltip" data-placement="top" title="Edit"><i
                                            class="bx bx-pencil font-size-16 align-middle"></i></a>
                                </td>
                            </tr>

                            <tr>
                                <td>9</td>
                                <td>Driver Verify Email Template</td>
                                <td>
                                    <a class="btn btn-sm btn-outline-info waves-effect waves-light"
                                       href="{{route('admin::driver_verify_email_settings.show', [9])}}" data-toggle="tooltip"
                                       data-placement="top" title="View"><i
                                            class="fas fa-eye font-size-16 align-middle"></i></a>
                                </td>
                                <td>
                                    <a href="{{route('admin::driver_verify_email_settings.index')}}"
                                       class="btn btn-sm btn-outline-info waves-effect waves-light"
                                       data-toggle="tooltip" data-placement="top" title="Edit"><i
                                            class="bx bx-pencil font-size-16 align-middle"></i></a>
                                </td>
                            </tr>
                            <tr>
                                <td>10</td>
                                <td>Passenger Verify Email Template</td>
                                <td>
                                    <a class="btn btn-sm btn-outline-info waves-effect waves-light"
                                       href="{{route('admin::passenger_verify_email_settings.show', [10])}}" data-toggle="tooltip"
                                       data-placement="top" title="View"><i
                                            class="fas fa-eye font-size-16 align-middle"></i></a>
                                </td>
                                <td>
                                    <a href="{{route('admin::passenger_verify_email_settings.index')}}"
                                       class="btn btn-sm btn-outline-info waves-effect waves-light"
                                       data-toggle="tooltip" data-placement="top" title="Edit"><i
                                            class="bx bx-pencil font-size-16 align-middle"></i></a>
                                </td>
                            </tr>
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
    <!-- main-content closed -->

@endsection
@section('js')
    <script src="{{URL::asset('assets/js/custom/EmailSettings.js')}}"></script>
    <script src="{{URL::asset('assets/js/modal.js')}}"></script>
@endsection
