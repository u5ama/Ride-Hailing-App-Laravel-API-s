<?php

namespace App\Http\Controllers\Api\V1;

use App\BaseAppSocialLinks;
use App\CustomerCreditCard;
use App\CustomerInvoice;
use App\Driver;
use App\DriverAccount;
use App\EmailBodyTranslation;
use App\EmailFooterTranslation;
use App\EmailHeader;
use App\EmailHeaderTranslation;
use App\Events\NewInvoiceHasCreatedEvent;
use App\Http\Resources\GetMyCreditCardsResource;
use App\InvoicePlan;
use App\LanguageString;
use App\Mail\CancelRecieptEmail;
use App\Mail\RecieptEmail;
use App\Mail\RecieptEmailDetail;
use App\Mail\WelcomeEmail;
use App\PassengerAccount;
use App\PassengerPaymentDetail;
use App\PaymentGatewaySetting;
use App\RideBookingSchedule;
use App\TransactionId;
use App\User;
use App\Utility\Utility;
use Carbon\Carbon;
use DateTimeZone;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class  InvoiceController extends Controller
{
    /**
     *  Create Invoice
     * @param Request $request,$rate,$basefare,$Passenger,$trx_id, $finalRate_before,$initial_wait_rate,$vatPlan,$taxPlan
     * @return Response
     * @throws Exception
     */

    public static function createInvoice($rate,$basefare,$Passenger,$trx_id, $finalRate_before,$initial_wait_rate,$vatPlan,$taxPlan,$rate_data_Email){

//        $invoicePlan = InvoicePlan::where(['ip_status'=>1,'ip_is_default'=>1])->orderBy('id','desc')->first();
        $paymentGateWayMethod = $Passenger->rbs_payment_method;
//        if($paymentGateWayMethod == 'cash'){
//            $bank_amount = 0;
//            $net_invoice = $rate - $bank_amount;
//        }if($paymentGateWayMethod == 'wallet'){
//            $bank_amount = ($rate*$invoicePlan->ip_bank_commesion/100)+$invoicePlan->ip_bank_extra_charges+$invoicePlan->ip_bank_fixed_commesion;
//            $net_invoice = $rate - $bank_amount;
//        }
//        if($paymentGateWayMethod == 'creditcard'){
//            $bank_amount = ($rate*$invoicePlan->ip_bank_commesion/100)+$invoicePlan->ip_bank_extra_charges+$invoicePlan->ip_bank_fixed_commesion;
//            $net_invoice = $rate - $bank_amount;
//        }

        //Whipp Gross earning amount
//        $whipp_amount = $net_invoice*$invoicePlan->ip_whipp_commesion/100;

        //Agent Commission
//        $company_gross_earning = $whipp_amount*$invoicePlan->ip_company_commesion/100;

        //Driver Amount
//        $driver_amount = $net_invoice*$invoicePlan->ip_driver_commesion/100;

        //Whipp Net Earning
//        $company_net_earning = $whipp_amount - $company_gross_earning;

        $driver = Driver::find($Passenger->rbs_driver_id);

        $tzlist = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
        $transID = Utility::InvoicetransID($user = []);


//        $invoice_data = [
//            'ci_passenger_id'=>$Passenger->rbs_passenger_id,
//            'ci_driver_id'=>$Passenger->rbs_driver_id,
//            'ci_ride_id'=>$Passenger->id,
//            'ci_invoice_date'=>now(),
//            'ci_Trx_id'=>$trx_id->trx_ID,
//            'ci_invoice_id'=>$transID,
//            'ci_vehicle_id'=>$Passenger->rbs_transport_id,
//            'ci_vehicle_category'=>$Passenger->rbs_transport_type,
//            'ci_ride_detail'=>$trx_id->trx_ID,
//            'ci_payment_mode'=>$paymentGateWayMethod,
//            'ci_customer_invoice_amount'=>$rate,
//            'ci_bank_amount'=>$bank_amount,
//            'ci_net_invoice'=>$net_invoice,
//            'ci_driver_amount'=>$driver_amount,
//            'ci_company_id'=>$driver->du_com_id,
//            'ci_company_gross_earning'=>$company_gross_earning,
//            'ci_company_net_earning'=>$company_net_earning,
//            'ci_whipp_amount'=>$whipp_amount,
//            'ci_transaction_status'=>3,
//            'ci_base_fare'=>$basefare,
//            'ci_initial_waiting_fare'=>$initial_wait_rate,
//            'ci_before_pickup_charges'=>$finalRate_before,
//            'ci_plan_vat'=>$vatPlan,
//            'ci_tax_vat'=>$taxPlan,
//            'ci_created_at'=>now(),
//            'ci_updated_at'=>now()
//        ];

        $invoice_data = [
            'ci_passenger_id'=>$Passenger->rbs_passenger_id,
            'ci_driver_id'=>$Passenger->rbs_driver_id,
            'ci_ride_id'=>$Passenger->id,
            'ci_invoice_date'=>now(),
            'ci_Trx_id'=>$trx_id->trx_ID,
            'ci_invoice_id'=>$transID,
            'ci_vehicle_id'=>$Passenger->rbs_transport_id,
            'ci_vehicle_category'=>$Passenger->rbs_transport_type,
            'ci_fare_head_id'=>$Passenger->rbs_fare_plan_head_id,
            'ci_fare_detail_id'=>$Passenger->rbs_fare_plan_detail_id,
            'ci_ride_detail'=>$trx_id->trx_ID,
            'ci_payment_mode'=>$paymentGateWayMethod,
            'ci_customer_invoice_amount'=>$rate,
            'ci_bank_amount'=>0,
            'ci_net_invoice'=>0,
            'ci_driver_amount'=>0,
            'ci_company_id'=>$driver->du_com_id,
            'ci_company_gross_earning'=>0,
            'ci_company_net_earning'=>0,
            'ci_whipp_amount'=>0,
            'ci_transaction_status'=>3,
            'ci_base_fare'=>$basefare,
            'ci_initial_waiting_fare'=>$initial_wait_rate,
            'ci_before_pickup_charges'=>$finalRate_before,
            'ci_plan_vat'=>$vatPlan,
            'ci_tax_vat'=>$taxPlan,
            'ci_created_at'=>now(),
            'ci_updated_at'=>now()
        ];
        if($paymentGateWayMethod == 'cash'){
            $source_type = 3;
            }else{
            $source_type = 3;
        }if($paymentGateWayMethod == 'wallet'){
            $source_type = 1;
            }
        if($paymentGateWayMethod == 'creditcard'){
            $source_type = 2;
            }
        $passenger_account = PassengerAccount::where(['pc_target_id'=>$Passenger->rbs_passenger_id,'pc_target_type'=>'passenger'])->orderBy('id','desc')->first();
        if(isset($passenger_account->pc_balance) && $passenger_account->pc_balance != null){
            $bal = $passenger_account->pc_balance;
        }else{
            $bal = 0;
        }
        $balance =  $bal - $rate;
        $ratedata = [
            'pc_operation_type'=>2,
            'pc_source_type'=>$source_type,
            'pc_source_id'=>$Passenger->rbs_driver_id,
            'pc_target_id'=>$Passenger->rbs_passenger_id,
            'pc_amount'=>$rate,
            'pc_balance'=>$balance
        ];
        $passenger_account = PassengerAccount::create($ratedata);
        $invoice = CustomerInvoice::create($invoice_data);
        $user = User::getuser($Passenger->rbs_passenger_id);
        $beforePickUp = $finalRate_before;

            $ride_id =  $invoice->ci_ride_id;
            $rideData = RideBookingSchedule::where('id', $ride_id)->first();
           event(new NewInvoiceHasCreatedEvent($rideData,$user,$basefare,$rate,$beforePickUp,$trx_id,$initial_wait_rate,$vatPlan,$taxPlan));
        Mail::to($user->email)->send(new RecieptEmailDetail($user->name,$user->id,$basefare,$rate,$beforePickUp,$trx_id->trx_ID,$initial_wait_rate,$vatPlan,$taxPlan,"","","","","","","",$rate_data_Email));

        return $invoice;
    }

    public static function createInvoice1($rate,$basefare,$Passenger,$trx_id, $finalRate_before,$initial_wait_rate,$vatPlan,$taxPlan){

//        $invoicePlan = InvoicePlan::where(['ip_status'=>1,'ip_is_default'=>1])->orderBy('id','desc')->first();
        $paymentGateWayMethod = $Passenger->rbs_payment_method;
//        if($paymentGateWayMethod == 'cash'){
//            $bank_amount = 0;
//            $net_invoice = $rate - $bank_amount;
//        }if($paymentGateWayMethod == 'wallet'){
//            $bank_amount = ($rate*$invoicePlan->ip_bank_commesion/100)+$invoicePlan->ip_bank_extra_charges+$invoicePlan->ip_bank_fixed_commesion;
//            $net_invoice = $rate - $bank_amount;
//        }
//        if($paymentGateWayMethod == 'creditcard'){
//            $bank_amount = ($rate*$invoicePlan->ip_bank_commesion/100)+$invoicePlan->ip_bank_extra_charges+$invoicePlan->ip_bank_fixed_commesion;
//            $net_invoice = $rate - $bank_amount;
//        }

        //Whipp Gross earning amount
//        $whipp_amount = $net_invoice*$invoicePlan->ip_whipp_commesion/100;

        //Agent Commission
//        $company_gross_earning = $whipp_amount*$invoicePlan->ip_company_commesion/100;

        //Driver Amount
//        $driver_amount = $net_invoice*$invoicePlan->ip_driver_commesion/100;

        //Whipp Net Earning
//        $company_net_earning = $whipp_amount - $company_gross_earning;

        $driver = Driver::find($Passenger->rbs_driver_id);

        $tzlist = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
        $transID = Utility::InvoicetransID($user = []);


//        $invoice_data = [
//            'ci_passenger_id'=>$Passenger->rbs_passenger_id,
//            'ci_driver_id'=>$Passenger->rbs_driver_id,
//            'ci_ride_id'=>$Passenger->id,
//            'ci_invoice_date'=>now(),
//            'ci_Trx_id'=>$trx_id->trx_ID,
//            'ci_invoice_id'=>$transID,
//            'ci_vehicle_id'=>$Passenger->rbs_transport_id,
//            'ci_vehicle_category'=>$Passenger->rbs_transport_type,
//            'ci_ride_detail'=>$trx_id->trx_ID,
//            'ci_payment_mode'=>$paymentGateWayMethod,
//            'ci_customer_invoice_amount'=>$rate,
//            'ci_bank_amount'=>$bank_amount,
//            'ci_net_invoice'=>$net_invoice,
//            'ci_driver_amount'=>$driver_amount,
//            'ci_company_id'=>$driver->du_com_id,
//            'ci_company_gross_earning'=>$company_gross_earning,
//            'ci_company_net_earning'=>$company_net_earning,
//            'ci_whipp_amount'=>$whipp_amount,
//            'ci_transaction_status'=>3,
//            'ci_base_fare'=>$basefare,
//            'ci_initial_waiting_fare'=>$initial_wait_rate,
//            'ci_before_pickup_charges'=>$finalRate_before,
//            'ci_plan_vat'=>$vatPlan,
//            'ci_tax_vat'=>$taxPlan,
//            'ci_created_at'=>now(),
//            'ci_updated_at'=>now()
//        ];

        $invoice_data = [
            'ci_passenger_id'=>$Passenger->rbs_passenger_id,
            'ci_driver_id'=>$Passenger->rbs_driver_id,
            'ci_ride_id'=>$Passenger->id,
            'ci_invoice_date'=>now(),
            'ci_Trx_id'=>$trx_id->trx_ID,
            'ci_invoice_id'=>$transID,
            'ci_vehicle_id'=>$Passenger->rbs_transport_id,
            'ci_vehicle_category'=>$Passenger->rbs_transport_type,
            'ci_ride_detail'=>$trx_id->trx_ID,
            'ci_payment_mode'=>$paymentGateWayMethod,
            'ci_customer_invoice_amount'=>$rate,
            'ci_bank_amount'=>0,
            'ci_net_invoice'=>0,
            'ci_driver_amount'=>0,
            'ci_company_id'=>$driver->du_com_id,
            'ci_company_gross_earning'=>0,
            'ci_company_net_earning'=>0,
            'ci_whipp_amount'=>0,
            'ci_transaction_status'=>3,
            'ci_base_fare'=>$basefare,
            'ci_initial_waiting_fare'=>$initial_wait_rate,
            'ci_before_pickup_charges'=>$finalRate_before,
            'ci_plan_vat'=>$vatPlan,
            'ci_tax_vat'=>$taxPlan,
            'ci_created_at'=>now(),
            'ci_updated_at'=>now()
        ];
        if($paymentGateWayMethod == 'cash'){
            $source_type = 3;
            }else{
            $source_type = 3;
        }if($paymentGateWayMethod == 'wallet'){
            $source_type = 1;
            }
        if($paymentGateWayMethod == 'creditcard'){
            $source_type = 2;
            }
        $passenger_account = PassengerAccount::where(['pc_target_id'=>$Passenger->rbs_passenger_id,'pc_target_type'=>'passenger'])->orderBy('id','desc')->first();
        if(isset($passenger_account->pc_balance) && $passenger_account->pc_balance != null){
            $bal = $passenger_account->pc_balance;
        }else{
            $bal = 0;
        }
        $balance =  $bal - $rate;
        $ratedata = [
            'pc_operation_type'=>2,
            'pc_source_type'=>$source_type,
            'pc_source_id'=>$Passenger->rbs_driver_id,
            'pc_target_id'=>$Passenger->rbs_passenger_id,
            'pc_amount'=>$rate,
            'pc_balance'=>$balance
        ];
        $passenger_account = PassengerAccount::create($ratedata);
        $invoice = CustomerInvoice::create($invoice_data);
        $user = User::getuser($Passenger->rbs_passenger_id);
        $beforePickUp = $finalRate_before;

            $ride_id =  $invoice->ci_ride_id;
            $rideData = RideBookingSchedule::where('id', $ride_id)->first();
            if (!empty($rideData)){
                $sourceLat = floatval($rideData->rbs_source_lat);
                $sourceLong = floatval($rideData->rbs_source_long);
                $desLat = floatval($rideData->rbs_destination_lat);
                $desLong = floatval($rideData->rbs_destination_long);
                $pickup_location = app('geocoder')->reverse($sourceLat,$sourceLong)->get()->first();
                $drop_off = app('geocoder')->reverse($desLat,$desLong)->get()->first();
                $address = '<div class="box-style"><p style="text-align: center">Ride Details: </p><div class="locBox"><i class="fas fa-map-marker-alt"></i>&nbsp;<span>Pickup Location: </span><br><span>'.$pickup_location->getFormattedAddress().'</span>'.'</div><br><div class="locBox"><i class="fas fa-map-pin"></i>&nbsp;<span>Dropoff Location: </span><br><span>'.$drop_off->getFormattedAddress().'</span></div></div>';
                $socialLinks = BaseAppSocialLinks::all();
                $header = EmailHeader::where('id',1)->first();
                $headerTrans = EmailHeaderTranslation::where(['email_header_id' => 1, 'locale' => $user->locale])->first();

                $bodyTrans = EmailBodyTranslation::where(['email_body_id'=> 1, 'locale' => $user->locale])->first();

                $footerTrans = EmailFooterTranslation::where(['email_footer_id'=> 1,'locale' => $user->locale])->first();
                $langtxt = $user->locale;
            }else{
                $address = 'No Address';
                $socialLinks = BaseAppSocialLinks::all();
                $header = EmailHeader::where('id',1)->first();
                $headerTrans = EmailHeaderTranslation::where(['email_header_id' => 1, 'locale' => $user->locale])->first();

                $bodyTrans = EmailBodyTranslation::where(['email_body_id'=> 1, 'locale' => $user->locale])->first();

                $footerTrans = EmailFooterTranslation::where(['email_footer_id'=> 1,'locale' => $user->locale])->first();
                $langtxt = $user->locale;
            }

        Mail::to($user->email)->send(new RecieptEmail($user->name,$user->id,$basefare,$rate,$beforePickUp,$trx_id->trx_ID,$initial_wait_rate,$vatPlan,$taxPlan,$address,$socialLinks,$header,$headerTrans,$bodyTrans,$footerTrans,$langtxt));

        return $invoice;
    }

    /**
     *  Create Invoice  Cancel
     * @param Request $request,$rate,$basefare,$Passenger,$trx_id, $finalRate_before,$initial_wait_rate,$vatPlan,$taxPlan
     * send email
     * @return Response
     * @throws Exception
     */

    public static function createInvoiceCancel($rate,$basefare,$Passenger,$trx_id, $finalRate_before,$initial_wait_rate,$vatPlan,$taxPlan){

        $invoicePlan = InvoicePlan::where(['ip_status'=>1,'ip_is_default'=>1])->orderBy('id','desc')->first();
        $paymentGateWayMethod = $Passenger->rbs_payment_method;
        if($paymentGateWayMethod == 'cash'){
            $bank_amount = 0;
            $net_invoice = $rate - $bank_amount;
        }if($paymentGateWayMethod == 'wallet'){
            $bank_amount = ($rate*$invoicePlan->ip_bank_commesion/100)+$invoicePlan->ip_bank_extra_charges+$invoicePlan->ip_bank_fixed_commesion;
            $net_invoice = $rate - $bank_amount;
        }
        if($paymentGateWayMethod == 'creditcard'){
            $bank_amount = ($rate*$invoicePlan->ip_bank_commesion/100)+$invoicePlan->ip_bank_extra_charges+$invoicePlan->ip_bank_fixed_commesion;
            $net_invoice = $rate - $bank_amount;
        }
//        $whipp_amount = $net_invoice*$invoicePlan->ip_whipp_commesion/100;
//        $company_gross_earning = $net_invoice-$whipp_amount;
//        $driver_amount = $company_gross_earning*$invoicePlan->ip_driver_commesion/100;
//        $company_net_earning = $company_gross_earning*$invoicePlan->ip_company_commesion/100;

        //Whipp Gross earning amount
        $whipp_amount = $net_invoice*$invoicePlan->ip_whipp_commesion/100;

        //Agent Commission
        $company_gross_earning = $whipp_amount*$invoicePlan->ip_company_commesion/100;

        //Driver Amount
        $driver_amount = $net_invoice*$invoicePlan->ip_driver_commesion/100;

        //Whipp Net Earning
        $company_net_earning = $whipp_amount - $company_gross_earning;


        $tzlist = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
        $transID = Utility::InvoicetransID($user = []);

        $driver = Driver::find($Passenger->rbs_driver_id);
        $invoice_data = [
            'ci_passenger_id'=>$Passenger->rbs_passenger_id,
            'ci_driver_id'=>$Passenger->rbs_driver_id,
            'ci_ride_id'=>$Passenger->id,
            'ci_invoice_date'=>now(),
            'ci_Trx_id'=>$trx_id->trx_ID,
            'ci_invoice_id'=>$transID,
            'ci_vehicle_id'=>$Passenger->rbs_transport_id,
            'ci_vehicle_category'=>$Passenger->rbs_transport_type,
            'ci_ride_detail'=>$trx_id->trx_ID,
            'ci_payment_mode'=>$paymentGateWayMethod,
            'ci_customer_invoice_amount'=>$rate,
            'ci_bank_amount'=>$bank_amount,
            'ci_net_invoice'=>$net_invoice,
            'ci_driver_amount'=>$driver_amount,
            'ci_company_id'=>$driver->du_com_id,
            'ci_company_gross_earning'=>$company_gross_earning,
            'ci_company_net_earning'=>$company_net_earning,
            'ci_whipp_amount'=>$whipp_amount,
            'ci_transaction_status'=>2,
            'ci_base_fare'=>$basefare,
            'ci_initial_waiting_fare'=>$initial_wait_rate,
            'ci_before_pickup_charges'=>$finalRate_before,
            'ci_plan_vat'=>$vatPlan,
            'ci_tax_vat'=>$taxPlan,
            'ci_created_at'=>now(),
            'ci_updated_at'=>now()
        ];
        if($paymentGateWayMethod == 'cash'){
            $source_type = 3;
            }else{
            $source_type = 3;
        }if($paymentGateWayMethod == 'wallet'){
            $source_type = 1;
            }if($paymentGateWayMethod == 'creditcard'){
            $source_type = 2;
            }
        $passenger_account = PassengerAccount::where(['pc_target_id'=>$Passenger->rbs_passenger_id,'pc_target_type'=>'passenger'])->orderBy('id','desc')->first();
        if(isset($passenger_account->pc_balance) && $passenger_account->pc_balance != null){
            $bal = $passenger_account->pc_balance;
        }else{

            $bal = 0;
        }
        $balance =  $bal - $rate;
        $ratedata = [
            'pc_operation_type'=>2,
            'pc_source_type'=>$source_type,
            'pc_source_id'=>$Passenger->rbs_driver_id,
            'pc_target_id'=>$Passenger->rbs_passenger_id,
            'pc_amount'=>$rate,
            'pc_balance'=>$balance
        ];
        $passenger_account = PassengerAccount::create($ratedata);
        $invoice = CustomerInvoice::create($invoice_data);
        $user = User::getuser($Passenger->rbs_passenger_id);
        $beforePickUp = $finalRate_before;

            $ride_id =  $invoice->ci_ride_id;
            $rideData = RideBookingSchedule::where('id', $ride_id)->first();
            if (!empty($rideData)){
                $sourceLat = floatval($rideData->rbs_source_lat);
                $sourceLong = floatval($rideData->rbs_source_long);
                $desLat = floatval($rideData->rbs_destination_lat);
                $desLong = floatval($rideData->rbs_destination_long);
                $pickup_location = app('geocoder')->reverse($sourceLat,$sourceLong)->get()->first();
                $drop_off = app('geocoder')->reverse($desLat,$desLong)->get()->first();
                $address = '<b>Pickup: </b><span>'.$pickup_location->getFormattedAddress().'</span>'.'<br><b>Dropoff: </b><span>'.$drop_off->getFormattedAddress().'</span>';
                $socialLinks = BaseAppSocialLinks::all();

                $header = EmailHeader::where('id',3)->first();
                $headerTrans = EmailHeaderTranslation::where(['email_header_id' => 3, 'locale' => $user->locale])->first();

                $bodyTrans = EmailBodyTranslation::where(['email_body_id'=> 3, 'locale' => $user->locale])->first();

                $footerTrans = EmailFooterTranslation::where(['email_footer_id'=> 3,'locale' => $user->locale])->first();
                $langtxt = $user->locale;
            }else{
                $address = 'No Address';
                $socialLinks = BaseAppSocialLinks::all();
                $header = EmailHeader::where('id',3)->first();
                $headerTrans = EmailHeaderTranslation::where(['email_header_id' => 3, 'locale' => $user->locale])->first();

                $bodyTrans = EmailBodyTranslation::where(['email_body_id'=> 3, 'locale' => $user->locale])->first();

                $footerTrans = EmailFooterTranslation::where(['email_footer_id'=> 3,'locale' => $user->locale])->first();
                $langtxt = $user->locale;
            }

        Mail::to($user->email)->send(new CancelRecieptEmail($user->name,$user->id,$basefare,$rate,$beforePickUp,$trx_id->trx_ID,$initial_wait_rate,$vatPlan,$taxPlan,$address,$socialLinks,$header,$headerTrans,$bodyTrans,$footerTrans,$langtxt));

        return $invoice;
    }

    /**
     *  Create Canceled Invoice by Driver
     * @param Request $request,$rate,$basefare,$Passenger,$trx_id, $finalRate_before,$initial_wait_rate,$vatPlan,$taxPlan
     * send email
     * @return Response
     * @throws Exception
     */

    public static function createInvoiceCancelDriver($rate,$basefare,$Passenger,$trx_id, $finalRate_before,$initial_wait_rate,$vatPlan,$taxPlan){

        $invoicePlan = InvoicePlan::where(['ip_status'=>1,'ip_is_default'=>1])->orderBy('id','desc')->first();
        $paymentGateWayMethod = $Passenger->rbs_payment_method;
        if($paymentGateWayMethod == 'cash'){
            $bank_amount = 0;
            $net_invoice = $rate - $bank_amount;
        }if($paymentGateWayMethod == 'wallet'){
            $bank_amount = ($rate*$invoicePlan->ip_bank_commesion/100)+$invoicePlan->ip_bank_extra_charges+$invoicePlan->ip_bank_fixed_commesion;
            $net_invoice = $rate - $bank_amount;
        }
        if($paymentGateWayMethod == 'creditcard'){
            $bank_amount = ($rate*$invoicePlan->ip_bank_commesion/100)+$invoicePlan->ip_bank_extra_charges+$invoicePlan->ip_bank_fixed_commesion;
            $net_invoice = $rate - $bank_amount;
        }
//        $whipp_amount = $net_invoice*$invoicePlan->ip_whipp_commesion/100;
//        $company_gross_earning = $net_invoice-$whipp_amount;
//        $driver_amount = $company_gross_earning*$invoicePlan->ip_driver_commesion/100;
//        $company_net_earning = $company_gross_earning*$invoicePlan->ip_company_commesion/100;

        //Whipp Gross earning amount
        $whipp_amount = $net_invoice*$invoicePlan->ip_whipp_commesion/100;

        //Agent Commission
        $company_gross_earning = $whipp_amount*$invoicePlan->ip_company_commesion/100;

        //Driver Amount
        $driver_amount = $net_invoice*$invoicePlan->ip_driver_commesion/100;

//        Whipp Net Earning
        $company_net_earning = $whipp_amount - $company_gross_earning;


        $tzlist = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
        $transID = Utility::InvoicetransID($user = []);

        $driver = Driver::find($Passenger->rbs_driver_id);
        $invoice_data = [
            'ci_passenger_id'=>$Passenger->rbs_passenger_id,
            'ci_driver_id'=>$Passenger->rbs_driver_id,
            'ci_ride_id'=>$Passenger->id,
            'ci_invoice_date'=>now(),
            'ci_Trx_id'=>$trx_id->trx_ID,
            'ci_invoice_id'=>$transID,
            'ci_vehicle_id'=>$Passenger->rbs_transport_id,
            'ci_vehicle_category'=>$Passenger->rbs_transport_type,
            'ci_ride_detail'=>$trx_id->trx_ID,
            'ci_payment_mode'=>$paymentGateWayMethod,
            'ci_customer_invoice_amount'=>$rate,
            'ci_bank_amount'=>$bank_amount,
            'ci_net_invoice'=>$net_invoice,
            'ci_driver_amount'=>$driver_amount,
            'ci_company_id'=>$driver->du_com_id,
            'ci_company_gross_earning'=>$company_gross_earning,
            'ci_company_net_earning'=>$company_net_earning,
            'ci_whipp_amount'=>$whipp_amount,
            'ci_transaction_status'=>2,
            'ci_base_fare'=>$basefare,
            'ci_initial_waiting_fare'=>$initial_wait_rate,
            'ci_before_pickup_charges'=>$finalRate_before,
            'ci_plan_vat'=>$vatPlan,
            'ci_tax_vat'=>$taxPlan,
            'ci_created_at'=>now(),
            'ci_updated_at'=>now()
        ];
        if($paymentGateWayMethod == 'cash'){
            $source_type = 3;
            }else{
            $source_type = 3;
        }if($paymentGateWayMethod == 'wallet'){
            $source_type = 1;
            }if($paymentGateWayMethod == 'creditcard'){
            $source_type = 2;
            }

        $driver_account = DriverAccount::where(['dc_target_id'=>$driver->id,'dc_target_type'=>'driver'])->orderBy('id','desc')->first();
        if(isset($driver_account->dc_balance) && $driver_account->dc_balance != null){
            $bal = $driver_account->dc_balance;
        }else{
            $bal = 0;
        }
        $balance =  $bal - $rate;
        $ratedata1 = [
            'dc_operation_type'=>2,
            'dc_source_type'=>$source_type,
            'dc_ride_id'=>$Passenger->id,
            'dc_source_id'=>$Passenger->rbs_passenger_id,
            'dc_target_id'=>$Passenger->rbs_driver_id,
            'dc_amount'=>$rate,
            'dc_balance'=>$balance
        ];

        $driver_account = DriverAccount::create($ratedata1);

        $invoice = CustomerInvoice::create($invoice_data);
        $user = User::getuser($Passenger->rbs_passenger_id);
        $beforePickUp = $finalRate_before;

            $ride_id =  $invoice->ci_ride_id;
            $rideData = RideBookingSchedule::where('id', $ride_id)->first();
            if (!empty($rideData)){
                $sourceLat = floatval($rideData->rbs_source_lat);
                $sourceLong = floatval($rideData->rbs_source_long);
                $desLat = floatval($rideData->rbs_destination_lat);
                $desLong = floatval($rideData->rbs_destination_long);
                $pickup_location = app('geocoder')->reverse($sourceLat,$sourceLong)->get()->first();
                $drop_off = app('geocoder')->reverse($desLat,$desLong)->get()->first();
                $address = '<b>Pickup: </b><span>'.$pickup_location->getFormattedAddress().'</span>'.'<br><b>Dropoff: </b><span>'.$drop_off->getFormattedAddress().'</span>';
                $socialLinks = BaseAppSocialLinks::all();
                $header = EmailHeader::where('id',3)->first();
                $headerTrans = EmailHeaderTranslation::where(['email_header_id' => 3, 'locale' => $user->locale])->first();
                $bodyTrans = EmailBodyTranslation::where(['email_body_id'=> 3, 'locale' => $user->locale])->first();
                $footerTrans = EmailFooterTranslation::where(['email_footer_id'=> 3,'locale' => $user->locale])->first();
                $langtxt = $user->locale;
            }else{
                $address = 'No Address';
                $socialLinks = BaseAppSocialLinks::all();
                $header = EmailHeader::where('id',3)->first();
                $headerTrans = EmailHeaderTranslation::where(['email_header_id' => 3, 'locale' => $user->locale])->first();
                $bodyTrans = EmailBodyTranslation::where(['email_body_id'=> 3, 'locale' => $user->locale])->first();
                $footerTrans = EmailFooterTranslation::where(['email_footer_id'=> 3,'locale' => $user->locale])->first();
                $langtxt = $user->locale;
            }

        Mail::to($user->email)->send(new CancelRecieptEmail($driver->du_full_name,$driver->id,$basefare,$rate,$beforePickUp,$trx_id->trx_ID,$initial_wait_rate,$vatPlan,$taxPlan,$address,$socialLinks,$header,$headerTrans,$bodyTrans,$footerTrans,$langtxt));

        return $invoice;
    }

    /**
     *  Pay Bill
     * @param Request $request,$driver,$user,$rate,$invoice,$request
     * send email
     * @return Response
     * @throws Exception
     */

    public static function payBill($driver,$user,$rate,$invoice,$request){
        try{

            Log::info('app.requests', ['request' => $request->all(),'URL'=>$request->url(),'trxID'=>$user->TransactionId->last()]);

            $bill_paid1 = Utility::paymentGateWay($request,$invoice,$user,$rate);
            Log::info('app.response', ['response' => $bill_paid1,'statusCode'=>200,'URL'=>$request->url(),'trxID'=>$user->TransactionId->last()]);
            if(count($bill_paid1) > 0){
                $bill_paid = $bill_paid1;
            }else{
               $bill_paid = [];
            }
            return $bill_paid;
        } catch(\Exception $e){
            $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
            $error = ['field'=>'language_strings','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }
    }

    /**
     * Create Payment invoice
     * @param Request $request
     * send email
     * @return Response
     * @throws Exception
     */

    public function cashPayment(Request $request){
        try{


            $messages = [
                'required' => 'the_field_is_required',
                'string' => 'the_string_field_is_required',
                'max' => 'the_field_is_out_from_max',
                'min' => 'the_field_is_low_from_min',
                'unique' => 'the_field_should_unique',
                'confirmed' => 'the_field_should_confirmed',
                'email' => 'the_field_should_email',
                'exists' => 'the_field_should_exists',
            ];
            $validator = Validator::make($request->all(), [
                'invoice_id' => 'required',
                'bill_amount' => 'required',
            ], $messages);
            if ($validator->fails()) {

                $errors = [];
                foreach ($validator->errors()->messages() as $field => $message) {
                    $messageval = LanguageString::translated()->where('bls_name_key', $message[0] )->first()->name;
                    $field_msg = LanguageString::translated()->where('bls_name_key', $field )->first()->name;
                    $errors[] = [
                        'field' => $field,
                        'message' => strtolower($field_msg) . ' ' . strtolower($messageval),
                    ];
                }
                return response()->json(compact('errors'), 401);
            }




            $invoice = CustomerInvoice::where('id',$request->invoice_id)->first();
            $token = JWTAuth::getToken();
            $user = User::find($invoice->ci_passenger_id);
            $driver = \Auth::guard('driver')->user();
            $ride = RideBookingSchedule::where('id',$invoice->ci_ride_id)->update(['rbs_payment_method'=>"cash"]);
            $Passenger = RideBookingSchedule::where('id',$invoice->ci_ride_id)->first();
            $paymentGateWayMethod = $Passenger->rbs_payment_method;
        $rate1 = $request->bill_amount;
        $source_type = 3;
        if($paymentGateWayMethod == 'cash'){
            $source_type = 3;
        }if($paymentGateWayMethod == 'wallet'){
            $source_type = 1;
        }if($paymentGateWayMethod == 'creditcard'){
            $source_type = 2;
        }
        $passenger_account = PassengerAccount::where(['pc_target_id'=>$Passenger->rbs_passenger_id,'pc_target_type'=>'passenger'])->orderBy('id','desc')->first();
        if(isset($passenger_account->pc_balance) && $passenger_account->pc_balance != null){
            $bal = $passenger_account->pc_balance;
        }else{

            $bal = 0;
        }
        $balance =  $bal + $rate1;
        $ratedata = [
            'pc_operation_type'=>2,
            'pc_source_type'=>$source_type,
            'pc_source_id'=>$Passenger->rbs_driver_id,
            'pc_target_id'=>$Passenger->rbs_passenger_id,
            'pc_amount'=>$rate1,
            'pc_balance'=>$balance
        ];
        $driver_account = PassengerAccount::create($ratedata);

            // driver account


        if($paymentGateWayMethod == 'cash'){
            $source_type = 3;
        }if($paymentGateWayMethod == 'wallet'){
            $source_type = 1;
        }if($paymentGateWayMethod == 'creditcard'){
            $source_type = 2;
        }
        $driver_account = DriverAccount::where(['dc_target_id'=>$driver->id,'dc_target_type'=>'driver'])->orderBy('id','desc')->first();
        if(isset($driver_account->dc_balance) && $driver_account->dc_balance != null){
            $bal = $driver_account->dc_balance;
        }else{
            $bal = 0;
        }
        $balance =  $bal - $rate1;
        $ratedata1 = [
            'dc_operation_type'=>2,
            'dc_source_type'=>$source_type,
            'dc_ride_id'=>$Passenger->id,
            'dc_source_id'=>$Passenger->rbs_passenger_id,
            'dc_target_id'=>$Passenger->rbs_driver_id,
            'dc_amount'=>$rate1,
            'dc_balance'=>$balance
        ];

        $driver_account = DriverAccount::create($ratedata1);



            Log::info('app.requests', ['request' => $request->all(),'URL'=>$request->url(),'trxID'=>$user->TransactionId->last()]);
            $payment_success = CustomerInvoice::where('id',$request->invoice_id)->update(['ci_transaction_status'=>"success"]);
            Log::info('app.response', ['response' => $invoice,'statusCode'=>200,'URL'=>$request->url(),'trxID'=>$user->TransactionId->last()]);
            if($invoice != null){
                $rate = number_format($invoice->ci_customer_invoice_amount, 2). " KWD";
               $user = User::getuser($user->id);
                return response()->json(['success'=>true,'user'=>$user,'bill_amount'=>$rate,'invoice_id'=>$invoice->id,'date'=>date('d-m-Y',strtotime($invoice->ci_created_at)),'time'=>date('H:i',strtotime($invoice->ci_created_at)),'payment_method'=>'cash'], 200);

            }else{
                $message = LanguageString::translated()->where('bls_name_key','no_invoice')->first()->name;
                $error = ['field'=>'language_strings','message'=>$message];
                $errors =[$error];
                return response()->json(['errors' => $errors], 401);
            }

        } catch(\Exception $e){
            $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
            $error = ['field'=>'language_strings','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }
    }

    /**
     *  Create Top Up Wallet
     * @param Request $request
     * @return Response
     * @throws Exception
     */

    public function topUpWallet(Request $request){

         $token = JWTAuth::getToken();
         $user = JWTAuth::toUser($token);
        $messages = [
            'required' => 'the_field_is_required',
            'string' => 'the_string_field_is_required',
            'max' => 'the_field_is_out_from_max',
            'min' => 'the_field_is_low_from_min',
            'unique' => 'the_field_should_unique',
            'confirmed' => 'the_field_should_confirmed',
            'email' => 'the_field_should_email',
            'exists' => 'the_field_should_exists',
        ];
        $validator = Validator::make($request->all(), [
            'amount' => 'required',

        ], $messages);
        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors()->messages() as $field => $message) {
                Log::error('app.error',['field' => $field,'message'=>$message,'errorCode'=>401,'URL'=>$request->url(),'passenger' => $user,'token'=>$request->header('Authorization')]);
                $messageval = LanguageString::translated()->where('bls_name_key', $message[0] )->first()->name;
                $field_msg = LanguageString::translated()->where('bls_name_key', $field )->first()->name;
                $errors[] = [
                    'field' => $field,
                    'message' => strtolower($field_msg) . ' ' . strtolower($messageval),
                ];
            }
            return response()->json(compact('errors'), 401);
        }

        $amount = $request->amount;
        $pc_source_type = $request->source_type;


        $paymentGatWay = PaymentGatewaySetting::where(['pgs_status'=>1])->orderBy('id','desc')->first();
        if($paymentGatWay->pgs_gateway_type == 'production'){
            if ($pc_source_type == 'knet') {
                $source_t = $pc_source_type;
            }
            if ($pc_source_type == 'credit_card') {
                $source_t = 'cc';
            }
            $paymentGatWay['pgs_api_key'] = '$2y$12$mwP5EXlJrbh9oJLHN3S5T.dH9gnzScdXSGC/tUNzLb9SpmY3v4yZG';
            $testmode = "0";
            $payment = Utility::paymentGateWay($request, $user, $user, $amount, $source_t,$paymentGatWay,$testmode);
            Log::info('app.requests', ['request' => $request->all(), 'URL' => $request->url(), 'trxID' => $user->TransactionId->last()]);
            Log::info('app.response', ['response' => $user, 'statusCode' => 200, 'URL' => $request->url(), 'trxID' => $user->TransactionId->last()]);
            if ($payment['status'] == "success") {
                $user = User::getuser($user->id);
                return response()->json(['success' => true, 'url' => $payment['paymentURL'], 'source_type' => $pc_source_type, 'user' => $user], 200);
            }else {
                $user = User::getuser($user->id);
                return response()->json(['success' => false, 'url' => null, 'source_type' => $pc_source_type, 'user' => $user], 200);
            }
            }
            if($paymentGatWay->pgs_gateway_type == 'sandbox') {
                if ($pc_source_type == 'knet') {
                    $source_t = $pc_source_type;
                }
                if ($pc_source_type == 'credit_card') {
                    $source_t = 'cc';
                }

                $testmode = "1";
                $payment = Utility::paymentGateWay($request, $user, $user, $amount, $source_t,$paymentGatWay,$testmode);
                Log::info('app.requests', ['request' => $request->all(), 'URL' => $request->url(), 'trxID' => $user->TransactionId->last()]);
                Log::info('app.response', ['response' => $user, 'statusCode' => 200, 'URL' => $request->url(), 'trxID' => $user->TransactionId->last()]);
                if ($payment['status'] == "success") {
                    $user = User::getuser($user->id);
                    return response()->json(['success' => true, 'url' => $payment['paymentURL'], 'source_type' => $pc_source_type, 'user' => $user], 200);
                } else {
                    $user = User::getuser($user->id);
                    return response()->json(['success' => false, 'url' => null, 'source_type' => $pc_source_type, 'user' => $user], 200);
                }
            }

            if($paymentGatWay->pgs_gateway_type == 'test_env'){

                $passenger_account = PassengerAccount::where(['pc_target_id'=>$user->id,'pc_target_type'=>'passenger'])->orderBy('id','desc')->first();
                if(isset($passenger_account->pc_balance) && $passenger_account->pc_balance != null){
                    $bal = $passenger_account->pc_balance;
                }else{
                    $bal = 0;
                }
                $balance =  $bal + $amount;
                $ratedata = [
                    'pc_operation_type'=>1,
                    'pc_source_type'=>1,
                    'pc_source_id'=>$user->id,
                    'pc_target_id'=>$user->id,
                    'pc_amount'=>$amount,
                    'pc_balance'=>$balance
                ];
                $driver_account = PassengerAccount::create($ratedata);

                Log::info('app.requests', ['request' => $request->all(),'URL'=>$request->url(),'trxID'=>$user->TransactionId->last()]);
                $payment_success = CustomerInvoice::where('id',$request->invoice_id)->update(['ci_transaction_status'=>2]);
                Log::info('app.response', ['response' => $user,'statusCode'=>200,'URL'=>$request->url(),'trxID'=>$user->TransactionId->last()]);
                if($user != null){

                    $user = User::getuser($user->id);
                    return response()->json($user, 200);
            }


            }

                $message = "no method";
                $error = ['field'=>'language_strings','message'=>$message];
                $errors =[$error];
                return response()->json(['errors' => $errors], 401);



    }

    /**
     *  Top Up Wallet Success
     * @param Request $request
     * @return Response
     * @throws Exception
     */


    public function topUpWalletSuccess(Request $request){
        try{
         $token = JWTAuth::getToken();
         $user = JWTAuth::toUser($token);
        $messages = [
            'required' => 'the_field_is_required',
            'string' => 'the_string_field_is_required',
            'max' => 'the_field_is_out_from_max',
            'min' => 'the_field_is_low_from_min',
            'unique' => 'the_field_should_unique',
            'confirmed' => 'the_field_should_confirmed',
            'email' => 'the_field_should_email',
            'exists' => 'the_field_should_exists',
        ];
        $validator = Validator::make($request->all(), [
            'amount' => 'required',
            'source_type' => 'required',
            "PaymentID"=>"required",
            "Result"=>"required",
            "PostDate"=>"required",
            "TranID"=>"required",
            "Ref"=>"required",
            "TrackID"=>"required",
            "Auth"=>"required",
            "OrderID"=>"required"
        ], $messages);
        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors()->messages() as $field => $message) {
                Log::error('app.error',['field' => $field,'message'=>$message,'errorCode'=>401,'URL'=>$request->url(),'passenger' => $user,'token'=>$request->header('Authorization')]);
                $messageval = LanguageString::translated()->where('bls_name_key', $message[0] )->first()->name;
                $field_msg = LanguageString::translated()->where('bls_name_key', $field )->first()->name;
                $errors[] = [
                    'field' => $field,
                    'message' => strtolower($field_msg) . ' ' . strtolower($messageval),
                ];
            }
            return response()->json(compact('errors'), 401);
        }

        $amount = $request->amount;
        $pc_source_type = $request->source_type;
        $PaymentID = $request->PaymentID;
        $Result = $request->Result;
        $PostDate = $request->PostDate;
        $TranID = $request->TranID;
        $Ref = $request->Ref;
        $TrackID = $request->TrackID;
        $Auth = $request->Auth;

        $payment_detail_data = [
          'ppd_passenger_id'=>$user->id,
          'ppd_PaymentID'=>$PaymentID,
          'ppd_Result'=>$Result,
          'ppd_PostDate'=>date('Y-m-d',strtotime($PostDate)),
          'ppd_TranID'=>$TranID,
          'ppd_Ref'=>$Ref,
          'ppd_TrackID'=>$TrackID,
          'ppd_Auth'=>$Auth,
          'ppd_amount_charged'=>$amount
        ];
               $payment_detail = PassengerPaymentDetail::create($payment_detail_data);

               if($payment_detail->ppd_Result == "CAPTURED") {
                   $paymentGatWay = PaymentGatewaySetting::where(['pgs_status' => 1])->orderBy('id', 'desc')->first();
                   $passenger_account = PassengerAccount::where(['pc_target_id' => $user->id, 'pc_target_type' => 'passenger'])->orderBy('id', 'desc')->first();
                   if (isset($passenger_account->pc_balance) && $passenger_account->pc_balance != null) {
                       $bal = $passenger_account->pc_balance;
                   } else {
                       $bal = 0;
                   }
                   $balance = $bal + $amount;
                   $ratedata = [
                       'pc_operation_type' => 1,
                       'pc_source_type' => $pc_source_type,
                       'pc_source_id' => $user->id,
                       'pc_target_id' => $user->id,
                       'pc_payment_detail_id' => $payment_detail->id,
                       'pc_amount' => $amount,
                       'pc_balance' => $balance
                   ];
                   $driver_account = PassengerAccount::create($ratedata);

                   Log::info('app.requests', ['request' => $request->all(), 'URL' => $request->url(), 'trxID' => $user->TransactionId->last()]);
                   $payment_success = CustomerInvoice::where('id', $request->invoice_id)->update(['ci_transaction_status' => 2]);
                   Log::info('app.response', ['response' => $user, 'statusCode' => 200, 'URL' => $request->url(), 'trxID' => $user->TransactionId->last()]);
                   if ($user != null) {
                       $user = User::getuser($user->id);
                       return response()->json($user, 200);

                   } else {
                       $message = LanguageString::translated()->where('bls_name_key', 'no_invoice')->first()->name;
                       $error = ['field' => 'language_strings', 'message' => $message];
                       $errors = [$error];
                       return response()->json(['errors' => $errors], 401);
                   }
               }else{

                   $message = LanguageString::translated()->where('bls_name_key', 'no_invoice')->first()->name;
                   $error = ['field' => 'language_strings', 'message' => $message];
                   $errors = [$error];
                   return response()->json(['errors' => $errors], 401);

               }
                } catch(\Exception $e){
                        $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
                        $error = ['field'=>'language_strings','message'=>$message];
                        $errors =[$error];
                        return response()->json(['errors' => $errors], 500);
                    }
    }



}
