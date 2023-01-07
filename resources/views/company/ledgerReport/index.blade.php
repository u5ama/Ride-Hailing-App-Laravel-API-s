@extends('admin.layouts.master')
@section('css')

@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="card mt-3">
        <div class="card-body">
            <div class="row">
                <div class="col-md-2 col-xl-2">
                    <div class="breadcrumb-header justify-content-between" style="text-align: center;display: flex;">
                                <h4 class="content-title mb-0 mt-3">{{ config('languageString.ledger_reports') }}</h4>
                                <span class="text-muted mt-1 tx-13 ml-2 mb-0"></span>
                    </div>
                </div>
                <div class="col-md-10 col-xl-10">
                    <form  action="">
                    <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="filterWithCategory">{{ config('languageString.type_title ') }}</label>
                                    <select class="form-control select2" id="type_filter" name="type_filter" onchange="getPassengerORDriver(this.value)" required="">
                                        <option value="">Select Type</option>
                                        <option value="Passenger" @if(isset($type) && $type == 'Passenger'){{'selected'}}@endif>Passenger</option>
                                        <option value="Driver" @if(isset($type) && $type == 'Driver'){{'selected'}}@endif>Driver</option>
                                    </select>
                                </div>
                        </div>
                        <div class="col-md-3">
                                <div class="form-group">
                                    <label for="cust_id">{{ config('languageString.select_one_title ') }}</label>
                                    <select class="form-control select2" id="cust_id" name="cust_id" required="">
                                   <?php if(isset($passenger_list) && !empty($passenger_list)){ ?>
                                    <option value="">Select Passenger</option>
                                        <?php foreach($passenger_list as $row){ ?>
                                            <option value="{{$row->id}}" @if(isset($cust_id) && $cust_id == $row->id){{'selected'}}@endif>{{$row->name.' - '. $row->country_code. $row->mobile_no}}</option>
                                        <?php  } ?>

                                    <?php } ?>

                                    <?php if(isset($driver_list) && !empty($driver_list)){ ?>
                                    <option value="">Select Driver</option>
                                        <?php foreach($driver_list as $row){ ?>
                                            <option value="{{$row->id}}" @if(isset($cust_id) && $cust_id == $row->id){{'selected'}}@endif>{{$row->du_full_name.' - '. $row->du_country_code. $row->du_mobile_number}}</option>
                                        <?php  } ?>

                                    <?php } ?>
                                    </select>
                                </div>
                        </div>

                        <div class="col-md-3">
                                <div class="form-group">
                                    <label for="filterWithCategory">{{ config('languageString.from_date') }}</label>
                                    <input type="date" name="start_date" id="start_date"
                                           class="form-control datepicker-autoclose" placeholder="Please select start date" required="" value="<?php if(isset($start_date)){ echo $start_date; } ?>">
                                </div>
                        </div>
                        <div class="col-md-3">
                                <div class="form-group">
                                    <label for="filterWithCategory">{{ config('languageString.to_date') }}</label>
                                    <input type="date" name="end_date" id="end_date"
                                           class="form-control datepicker-autoclose" placeholder="Please select end date" required="" value="<?php if(isset($end_date)){ echo $end_date; } ?>">
                                </div>
                        </div>
                        <div class="col-md-1">
                                <button class="btn btn-outline-primary" type="submit"
                                        style="margin-top: 30px;">{{ config('languageString.filter_button') }}
                                </button>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php if(isset($acc_no) && !empty($acc_no)){  ?>
    <div class="row">
        <div class="col-md-2 col-xl-2">
            <div class="breadcrumb-header justify-content-between">
                <div class="my-auto">
                    <div class="d-flex">
                        <h4 class="content-title mb-0 mt-0">&nbsp;</h4>

                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-xl-4" >
            <div class="breadcrumb-header justify-content-between">
                <div class="my-auto">
                    <div class="d-flex">
                        <h4 class="content-title mb-0 mt-0">Account# :</h4>
                        <span class="text-muted mt-1 tx-16 ml-2 mb-0" id="acc_no"><?php if(isset($acc_no)){ echo $acc_no; } ?></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-6">
            <div class="breadcrumb-header justify-content-between">
                <div class="my-auto">
                    <div class="d-flex">
                        <h4 class="content-title mb-0 mt-0">Account Name : </h4>
                        <span class="text-muted mt-1 tx-16 ml-2 mb-0" id="acc_name"><?php if(isset($name)){ echo $name; } ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
@endsection
@section('content')
    <!-- row opened -->
    <div class="row row-sm">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table mg-b-0 text-md-nowrap" >
                            <thead>
                            <tr>
                                <th>{{ config('languageString.date_time_title') }}</th>
                                <th>{{ config('languageString.transaction_title') }}</th>
                                <th>{{ config('languageString.description_title') }}</th>
                                <th>{{ config('languageString.debit_title') }}</th>
                                 <th>{{ config('languageString.credit_ttile') }}</th>
                                <th>{{ config('languageString.balance_title') }}</th>


                            </tr>

                            </thead>
                                 <?php if(isset($cust_id)){ ?>
                                   <tr>
                                    <td>
                                        <?php if(isset($start_date)){ echo date('d-m-Y', strtotime($start_date)); } ?>
                                    </td>
                                    <td>0</td>
                                    <td>Opening</td>
                                    <td></td>
                                    <td></td>
                                    <td>
                                        <?php echo number_format($closing_balance, 3, ".", ","); ?>
                                    </td>
                                </tr>
                            <?php } ?>

                             <?php
                             $i =1;
                             $x =1;
                             $bal = $closing_balance;
                             $cr = 0;
                             $dr = 0;
                             $total_credit = 0;

                             foreach($ledger_credit as $row){
                                       if(isset($type) && $type == 'Passenger'){
                                       $pc_amount  = $row->pc_amount;
                                        if(!empty($pc_amount)){
                                            $cr = $pc_amount;
                                        }

                                         $bal  = $bal + $cr - $dr ;

                                         $total_credit += $cr;


                                         if($row->pc_source_type == 'knet'){
                                            $pc_source_type = 'Knet';
                                         }

                                         if($row->pc_source_type == 'credit_card'){
                                            $pc_source_type = 'Credit Card';
                                         }

                                         if($row->pc_source_type == 'voucher'){
                                            $pc_source_type = 'Voucher';
                                         }
                            ?>

                                <tr>
                                    <td><?php echo $row->ppd_PostDate; ?></td>
                                    <td><?php echo $row->ppd_TranID; ?></td>
                                    <td><?php echo ' Wallet '.'Topup'.' - '.$row->pc_source_type.' ('.$row->ppd_Result.', Payment ID '. $row->ppd_PaymentID .', '. $row->ppd_Auth.')'; ?></td>
                                    <td></td>
                                    <td><?php echo number_format($pc_amount, 3, ".", ","); ?></td>
                                    <td>
                                        <?php echo number_format($bal, 3, ".", ",");?>
                                    </td>
                                </tr>
                                 <?php

                             }  // end passenger

                             if(isset($type) && $type == 'Driver'){

                                        $ci_driver_amount  = $row->ci_driver_amount;
                                        if(!empty($ci_driver_amount)){
                                            $cr = $ci_driver_amount;
                                        }

                                      $bal  = $bal + $cr - $dr ;

                                         $total_credit += $cr;

                              ?>

                               <tr>
                                    <td><?php
                                    $datetime =  Utility:: convertTimeToUSERzone($row->ci_invoice_date,Utility::getUserTimeZone(auth()->guard('company')->user()->com_time_zone));

                                       echo date('d-m-Y H:s', strtotime($datetime));

                                    ?></td>
                                    <td><?php echo $row->ci_Trx_id; ?></td>
                                    <td><?php echo $row->ci_vehicle_category.' - '.$row->rbs_ride_status.' - '. $row->ci_payment_mode .' - '.$row->rbs_destination_distance.'KM - '. round($row->rbs_destination_time) .' Minutes '; ?></td>
                                    <td></td>
                                    <td><?php echo number_format($cr, 3, ".", ","); ?></td>
                                    <td>
                                        <?php echo number_format($bal, 3, ".", ",");?>
                                    </td>
                                </tr>

                             <?php } // end driver credit

                                 ?>


                            <?php
                              }

                             if(!empty($bal) && $bal != 0){
                                $bal = $bal;
                             }else{
                                $bal = 0;
                             }

                             $cr = 0;
                             $dr = 0;
                             $total_debit = 0;

                            foreach($ledger as $row){

                                       if(isset($type) && $type == 'Passenger'){

                                        $custom_invoice_amount  = $row->ci_customer_invoice_amount;
                                        if(!empty($custom_invoice_amount)){
                                            $dr = $custom_invoice_amount;
                                        }


                                         $bal  = $bal + $cr - $dr ;

                                         $total_debit += $dr;
                            ?>

                                <tr>
                                    <td><?php
                                    $datetime =  Utility:: convertTimeToUSERzone($row->ci_invoice_date,Utility::getUserTimeZone(auth()->guard('company')->user()->com_time_zone));

                                       echo date('d-m-Y H:s', strtotime($datetime));

                                    ?></td>
                                    <td><?php echo $row->ci_Trx_id; ?></td>
                                    <td><?php echo $row->ci_vehicle_category.' - '.$row->rbs_ride_status.' - '. $row->ci_payment_mode .' - '.$row->rbs_destination_distance.'KM - '. round($row->rbs_destination_time) .' Minutes '; ?></td>
                                    <td><?php echo number_format($dr, 3, ".", ","); ?></td>
                                    <td></td>
                                    <td>
                                        <?php echo number_format($bal, 3, ".", ",");?>
                                    </td>
                                </tr>

                              <?php } // end passenger debit
                              if(isset($type) && $type == 'Driver'){


                                 $dc_amount  = $row->dc_amount;
                                        if(!empty($dc_amount)){
                                            $dr = $dc_amount;
                                        }


                                         $bal  = $bal + $cr - $dr ;

                                         $total_debit += $dr;
                            ?>

                                <tr>
                                    <td><?php
                                    $datetime =  Utility:: convertTimeToUSERzone($row->ci_invoice_date,Utility::getUserTimeZone(auth()->guard('company')->user()->com_time_zone));

                                       echo date('d-m-Y H:s', strtotime($datetime));

                                    ?></td>
                                    <td><?php echo $row->ci_Trx_id; ?></td>
                                    <td><?php echo $row->dc_source_type; ?></td>
                                    <td><?php echo number_format($dr, 3, ".", ","); ?></td>
                                    <td></td>
                                    <td>
                                        <?php echo number_format($bal, 3, ".", ",");?>
                                    </td>
                                </tr>




                              <?php } // end driver debit

                              ?>

                            <?php  } ?>

                            <tfoot >
                            <tr >
                                <th colspan="3" style="text-align:right"></th>
                                <th>
                                    <?php if(isset($total_debit)) { echo number_format($total_debit, 3, ".", ","); } ?>
                                </th>
                                <th style="text-align:right">
                                    <?php if(isset($total_credit)) { echo  number_format($total_credit, 3, ".", ","); } ?></th>
                                <th><?php echo number_format($bal, 3, ".", ","); ?></th>

                            </tr>
                        </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!--/div-->
    </div>
    <!-- /row -->
@endsection
@section('js')
    <script>
        $(document).ready(function () {
            $('#account_no').hide();

        });
    </script>
    <script src="{{URL::asset('assets/js/custom/ledgerReport.js')}}"></script>
    <script src="{{URL::asset('assets/js/modal.js')}}"></script>

@endsection
