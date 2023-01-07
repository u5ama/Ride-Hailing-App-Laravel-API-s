@extends('admin.layouts.master')
@section('css')
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
                <h4 class="content-title mb-0 my-auto">Company Commission</h4>
                <span class="text-muted mt-1 tx-13 ml-2 mb-0"></span>
            </div>
        </div>
    </div>
    <!-- breadcrumb -->
@endsection
@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" integrity="sha512-mSYUmp1HYZDFaVKK//63EcZq4iFWFjxSL+Z3T/aCt4IO9Cejm03q3NKKYN6pFQzY0SBOr8h+eCIAZHPXcpZaNw==" crossorigin="anonymous" />
    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" data-parsley-validate="" id="addEditCommission" role="form">
                        @csrf
                        <input type="hidden" id="companyId" name="companyId" value="{{$company_id}}">
                        <input type="hidden" id="form-method" value="add">
                        <div class="row row-sm">

                            <div class="col-12">

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="col-12 border-bottom border-top mb-3  p-3 bg-light">
                                            <div class="main-content-label mb-0">Commision
                                                <b>Details</b></div>
                                        </div>
                                    </div>
<!--                                    <input type="hidden" id="fpd_country_id" name="fpd_country_id"
                                           value="{{$company_id}}">-->
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

                                            <th>Whipp Commission </th>
                                            <th>Company Commission</th>
                                            <th>Driver Commission </th>
                                            <th>start.date</th>
                                            <th>end.date</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                        </thead>
                                        <tbody id="commissionDetail">

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-group mb-0 mt-3 justify-content-end">
                                <div>
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </div>
                        </div>
                </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('js')

<script src="{{URL::asset('assets/js/custom/company.js')}}?v={{ time() }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js" integrity="sha512-T/tUfKSV1bihCnd+MxKD0Hm1uBBroVYBOYSk1knyvQ9VyZJpc/ALb4P0r6ubwVPSGB2GvjeoMAJJImBG12TiaQ==" crossorigin="anonymous"></script>
<script>
    $(function() {
        $('#start_date').datepicker({
            startDate: '-0d',
            format: 'yyyy-mm-dd',
            setDate: new Date()
        });

        $('#end_date').datepicker({
            startDate: '-0d',
            format: 'yyyy-mm-dd',
            setDate: new Date()
        });
    });
    function deleteRecordCommission(value_id) {

        $.ajax({
            type: 'GET',
            url: APP_URL + '/deleteCommission' + '/' + value_id,
            success: function (data) {
                successToast(data.message, 'success');
                table.draw()
                loaderHide();
            }, error: function (data) {
                console.log('Error:', data)
            }
        })
    }
    function updateCommissionStatus(value_id,status) {

        $.ajax({
            type: 'GET',
            url: APP_URL + '/updateStatusCommission' + '/' + value_id + '/' + status,
            success: function (data) {
                successToast(data.message, 'success');
                table.draw()
                loaderHide();
            }, error: function (data) {
                console.log('Error:', data)
            }
        })
    }
</script>
@endsection
