<?php

namespace App\Http\Controllers\Admin;

use App\Company;
use App\CustomerInvoice;
use App\RideBookingSchedule;
use App\PassengerPaymentDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use App\Driver;
use App\User;
use App\DriverAccount;
use App\Utility\Utility;

class LedgerReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function index(Request $request)
    {
            $companies = Company::all();
            $start_date = $request->start_date;
            $end_date = $request->end_date;

             $type = $request->type_filter;

             $cust_id = $request->cust_id;

             $company_filter = $request->company_filter;

            $ledger_credit = [];
            $ledger =  [];
            $passenger_list = [];
            $driver_list = [];
            $name = '';
            $acc_no = '';
            $closing_balance = 0;
            
           if($type == 'Passenger'){

                $start_date = date('Y-m-d', strtotime($start_date));
                $end_date = date('Y-m-d', strtotime($end_date));
                
                $ledger_credit = PassengerPaymentDetail::leftJoin('passenger_accounts', 'passenger_payment_details.id', '=', 'passenger_accounts.pc_payment_detail_id')
                 ->where(['passenger_payment_details.ppd_passenger_id'=>$cust_id,'passenger_accounts.pc_target_id'=>$cust_id,'passenger_accounts.pc_operation_type'=>'top_up'])
                 ->whereRaw("passenger_payment_details.ppd_PostDate >= '" . $start_date . "' AND passenger_payment_details.ppd_PostDate <= '" . $end_date . "'")->get();


                $ledger = CustomerInvoice::whereRaw("date(customer_invoices.ci_invoice_date) >= '" . $start_date . "' AND date(customer_invoices.ci_invoice_date) <= '" . $end_date . "'")->
                leftJoin('ride_booking_schedules', 'customer_invoices.ci_ride_id', '=', 'ride_booking_schedules.id')->with('passenger', 'driver')->where(['customer_invoices.ci_company_id'=>$company_filter,'customer_invoices.ci_passenger_id'=>$cust_id])->get();

                   $debit_sum = CustomerInvoice::whereRaw("date(customer_invoices.ci_invoice_date) < '" . $start_date . "'")->
                leftJoin('ride_booking_schedules', 'customer_invoices.ci_ride_id', '=', 'ride_booking_schedules.id')->with('passenger', 'driver')->where(['customer_invoices.ci_company_id'=>$company_filter,'customer_invoices.ci_passenger_id'=>$cust_id])
                ->sum('customer_invoices.ci_customer_invoice_amount');

            

                $credit_sum = PassengerPaymentDetail::leftJoin('passenger_accounts', 'passenger_payment_details.id', '=', 'passenger_accounts.pc_payment_detail_id')
                 ->where(['passenger_payment_details.ppd_passenger_id'=>$cust_id,'passenger_accounts.pc_target_id'=>$cust_id,'passenger_accounts.pc_operation_type'=>'top_up'])
                 ->whereRaw("passenger_payment_details.ppd_PostDate < '" . $start_date . "'")
                 ->sum('passenger_accounts.pc_amount');

                 $closing_balance = $credit_sum - $debit_sum;

                   
                 if(!empty($cust_id)){
                 $results = User::where('id', $cust_id)->first();
                 $name = $results->name;
                 $acc_no = $results->mobile_no;
                 }

                if(isset($company_filter)){

                   $driverIds = Driver::where(['du_com_id'=>$company_filter])->pluck('id')->toArray();

                   $passengerIds =RideBookingSchedule::whereIn('rbs_driver_id', $driverIds)->pluck('rbs_passenger_id')->toArray();

                   $passenger_list = User::whereIn('id', $passengerIds)->get(); 
                }
                 
                 


                } 
                if($type == 'Driver'){


                $start_date = date('Y-m-d', strtotime($start_date));
                $end_date = date('Y-m-d', strtotime($end_date));
                
                

                 // for credit
                $ledger_credit = CustomerInvoice::whereRaw("date(customer_invoices.ci_invoice_date) >= '" . $start_date . "' AND date(customer_invoices.ci_invoice_date) <= '" . $end_date . "'")->
                leftJoin('ride_booking_schedules', 'customer_invoices.ci_ride_id', '=', 'ride_booking_schedules.id')->with('passenger', 'driver')->where(['customer_invoices.ci_company_id'=>$company_filter,'customer_invoices.ci_driver_id'=>$cust_id])->get();
                // for debit
                $ledger = DriverAccount::leftJoin('customer_invoices', 'driver_accounts.dc_ride_id', '=', 'customer_invoices.ci_ride_id')
                 ->where(['customer_invoices.ci_driver_id'=>$cust_id,'driver_accounts.dc_target_id'=>$cust_id,'driver_accounts.dc_operation_type'=>'ride'])
                 ->whereRaw("customer_invoices.ci_invoice_date >= '" . $start_date . "' AND customer_invoices.ci_invoice_date <= '" . $end_date . "'")->get();

                   $credit_sum  = CustomerInvoice::whereRaw("date(customer_invoices.ci_invoice_date) < '" . $start_date . "'")->
                leftJoin('ride_booking_schedules', 'customer_invoices.ci_ride_id', '=', 'ride_booking_schedules.id')->with('passenger', 'driver')->where(['customer_invoices.ci_company_id'=>$company_filter,'customer_invoices.ci_driver_id'=>$cust_id])
                ->sum('customer_invoices.ci_customer_invoice_amount');
                  // get total debit
               

                 $debit_sum = PassengerPaymentDetail::leftJoin('passenger_accounts', 'passenger_payment_details.id', '=', 'passenger_accounts.pc_payment_detail_id')
                 ->where(['passenger_payment_details.ppd_passenger_id'=>$cust_id,'passenger_accounts.pc_target_id'=>$cust_id,'passenger_accounts.pc_operation_type'=>'top_up'])
                 ->whereRaw("passenger_payment_details.ppd_PostDate < '" . $start_date . "'")
                 ->sum('passenger_accounts.pc_amount');

                 $closing_balance = $credit_sum - $debit_sum;

                   if(isset($cust_id) && !empty($cust_id)){

                    $results = Driver::where('id', $cust_id)->first();
                   $name = $results->du_full_name;
                   $acc_no = $results->du_mobile_number;

                   }

                   if(isset($company_filter)){

                    $driver_list = Driver::where(['du_com_id'=>$company_filter])->get();
                }
            }
        return view('admin.ledgerReport.index',compact('companies','ledger','ledger_credit','closing_balance','name','acc_no','start_date','end_date','type','company_filter','cust_id','passenger_list','driver_list'));
    }

    public function getDebitAmount($passenger_id,$id,$status,$start_date,$end_date){
     if(!empty($start_date) && !empty($end_date)){
           $start_date = date('Y-m-d', strtotime($start_date));
                $end_date = date('Y-m-d', strtotime($end_date));
                $ledger = CustomerInvoice::query()->whereRaw("date(customer_invoices.ci_created_at) >= '" . $start_date . "' AND date(customer_invoices.ci_created_at) <= '" . $end_date . "'")->orderBy('customer_invoices.ci_created_at','DESC')->with('passenger', 'driver')->where(['ci_transaction_status'=>$status,'ci_passenger_id'=>$passenger_id,'id'=>$id])->first();
        }else{
            $currentDate = date('Y-m-d');
             $ledger = CustomerInvoice::with('passenger', 'driver')->whereDate("customer_invoices.ci_created_at", $currentDate)->where(['ci_transaction_status'=>$status,'ci_passenger_id'=>$passenger_id,'id'=>$id])->orderBy('customer_invoices.ci_created_at','DESC')->first();
        }
       $debit = '';
        if(isset($ledger->ci_customer_invoice_amount) && !empty($ledger->ci_customer_invoice_amount)){
            $debit = number_format($ledger->ci_customer_invoice_amount,3,".",",");
        }
       return $debit;

    }

    public function getCreditAmount($passenger_id,$id,$status,$start_date,$end_date){
        if(!empty($start_date) && !empty($end_date)){
           $start_date = date('Y-m-d', strtotime($start_date));
                $end_date = date('Y-m-d', strtotime($end_date));
                $ledger = CustomerInvoice::query()->whereRaw("date(customer_invoices.ci_created_at) >= '" . $start_date . "' AND date(customer_invoices.ci_created_at) <= '" . $end_date . "'")->orderBy('customer_invoices.ci_created_at','DESC')->with('passenger', 'driver')->where(['ci_transaction_status'=>$status,'ci_passenger_id'=>$passenger_id,'id'=>$id])->first();
        }else{
            $currentDate = date('Y-m-d');
             $ledger = CustomerInvoice::with('passenger', 'driver')->whereDate("customer_invoices.ci_created_at", $currentDate)->where(['ci_transaction_status'=>$status,'ci_passenger_id'=>$passenger_id,'id'=>$id])->orderBy('customer_invoices.ci_created_at','DESC')->first();
        }
      
      
       $credit = '';
        if(isset($ledger->ci_customer_invoice_amount) && !empty($ledger->ci_customer_invoice_amount)){
            $credit = number_format($ledger->ci_customer_invoice_amount,3,".",",");
        }
       return $credit;

    }


     public function getbalanceAmount($passenger_id,$id,$status,$start_date,$end_date,$debit,$total){
        if(!empty($start_date) && !empty($end_date)){
           $start_date = date('Y-m-d', strtotime($start_date));
                $end_date = date('Y-m-d', strtotime($end_date));
                $ledger = CustomerInvoice::query()->whereRaw("date(customer_invoices.ci_created_at) >= '" . $start_date . "' AND date(customer_invoices.ci_created_at) <= '" . $end_date . "'")->orderBy('customer_invoices.ci_created_at','DESC')->with('passenger', 'driver')->where(['ci_transaction_status'=>$status,'ci_passenger_id'=>$passenger_id,'id'=>$id])->first();
        }else{
            $currentDate = date('Y-m-d');
             $ledger = CustomerInvoice::with('passenger', 'driver')->whereDate("customer_invoices.ci_created_at", $currentDate)->where(['ci_transaction_status'=>$status,'ci_passenger_id'=>$passenger_id,'id'=>$id])->orderBy('customer_invoices.ci_created_at','DESC')->first();
        }
      
      
      $balance = $total-$debit;
       return $balance;

    }


    public function getLedgerReport(Request $request){

        $start_date = date('Y-m-d', strtotime($request->from_date));
        $end_date = date('Y-m-d', strtotime($request->to_date));
        $type_filter = $request->type_filter;
        $cust_id = $request->cust_id;
        if($type_filter == 'Passenger'){

            $results = User::where('id', $cust_id)->first();
            $name = $results->name;
            $acc_no = $results->mobile_no;

        }else{
           $results = Driver::where('id', $cust_id)->first();
            $name = $results->du_full_name;
            $acc_no = $results->du_mobile_number;
        }

        return response()->json(['success' => true, 'acc_name' => $name, 'acc_no' => $acc_no]);

    }


    public function getPassengerORDriver(Request $request)
    {
        $type =  $request->type;
        $company_filter =  $request->company_filter;
        if($type == 'Passenger'){
          
        $driverIds = Driver::where(['du_com_id'=>$company_filter])->pluck('id')->toArray();

        $passengerIds =RideBookingSchedule::whereIn('rbs_driver_id', $driverIds)->pluck('rbs_passenger_id')->toArray();

        $results = User::whereIn('id', $passengerIds)->get();

        $output = '';
        $output .= '<option value="">' . 'Select Passenger' . '</option>';

        foreach ($results as $data) {
            $ref_type_obj = $data->name;
            $output .= '<option value="' . $data->id . '">' . $ref_type_obj.' - '. $data->country_code. $data->mobile_no . '</option>';
        }

        }else{


        $results = Driver::where(['du_com_id'=>$company_filter])->get();
        $output = '';
        $output .= '<option value="">' . 'Select Driver' . '</option>';

        foreach ($results as $data) {
            $ref_type_obj = $data->du_full_name;
            $output .= '<option value="' . $data->id . '">' . $ref_type_obj .'-'. $data->du_country_code. $data->du_mobile_number . '</option>';
        }

        }
        
        return $output;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function store(Request $request)
    {
        
    }


    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {

    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        CustomerInvoice::where('id', $id)->delete();
        return response()->json(['success' => true, 'message' => 'Record deleted successfully']);
    }

 public function time_ago($created_at)
 {
        $time_ago = strtotime($created_at);
    $cur_time   = time();
    $time_elapsed   = $cur_time - $time_ago;
    $seconds    = $time_elapsed ;
    $minutes    = round($time_elapsed / 60 );
    $hours      = round($time_elapsed / 3600);
    $days       = round($time_elapsed / 86400 );
    $weeks      = round($time_elapsed / 604800);
    $months     = round($time_elapsed / 2600640 );
    $years      = round($time_elapsed / 31207680 );
    // Seconds
    if($seconds <= 60){
        $timeago = "just now";
    }
    //Minutes
    else if($minutes <=60){
        if($minutes==1){
            $timeago = "one minute ago";
        }
        else{
            $timeago = "$minutes minutes ago";
        }
    }
    //Hours
    else if($hours <=24){
        if($hours==1){
            $timeago = "an hour ago";
        }else{
            $timeago = "$hours hrs ago";
        }
    }
    //Days
    else if($days <= 7){
        if($days==1){
            $timeago = "yesterday";
        }else{
            $timeago = "$days days ago";
        }
    }
    //Weeks
    else if($weeks <= 4.3){
        if($weeks==1){
            $timeago = "a week ago";
        }else{
            $timeago = "$weeks weeks ago";
        }
    }
    //Months
    else if($months <=12){
        if($months==1){
            $timeago = "a month ago";
        }else{
            $timeago = "$months months ago";
        }
    }
    //Years
    else{
        if($years==1){
            $timeago = "one year ago";
        }else{
            $timeago = "$years years ago";
        }
    }

   return $timeago;
}

}
