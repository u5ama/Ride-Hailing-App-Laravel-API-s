<?php

namespace App\Http\Controllers\Company;

use App\AdminUser;
use App\Company;
use App\CustomerInvoice;
use App\Driver;
use App\PassengerCancelRideHistory;
use App\RideBookingSchedule;
use App\RideIgnoredBy;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Session;
class CompanyController extends Controller
{
    public function index()
    {

        $driverCount = Driver::where('du_com_id',auth()->guard('company')->user()->id)->count();
        $ridesCount = RideBookingSchedule::leftjoin('drivers', 'ride_booking_schedules.rbs_driver_id', '=', 'drivers.id')->where(['drivers.du_com_id' => auth()->guard('company')->user()->id])->count();
        $ridesCancelByPAssengerCount = PassengerCancelRideHistory::leftjoin('drivers', 'passenger_cancel_ride_histories.pcrh_driver_id', '=', 'drivers.id')->where(['drivers.du_com_id' => auth()->guard('company')->user()->id])->count();
        $rideIgnoredCount = RideIgnoredBy::leftjoin('drivers', 'ride_ignored_bies.rib_driver_id', '=', 'drivers.id')->where(['drivers.du_com_id' => auth()->guard('company')->user()->id])->count();

        $waitingCount =  $this->countRideStatistics('Waiting');
        $acceptedCount =  $this->countRideStatistics('Accepted');
        $completedCount =  $this->countRideStatistics('Completed');
        $drivingCount =  $this->countRideStatistics('Driving');
        $requestedCount =  $this->countRideStatistics('Requested');
        $rejectedCount =  $this->countRideStatistics('Rejected');
        $currentDate = date('Y-m-d');

        $bankCom = DB::table("customer_invoices")->leftjoin('drivers', 'customer_invoices.ci_driver_id', '=', 'drivers.id')->where('drivers.du_com_id', auth()->guard('company')->user()->id)->whereDate("ci_created_at", $currentDate)->get()->sum("ci_bank_amount");
        $netInvoice = DB::table("customer_invoices")->leftjoin('drivers', 'customer_invoices.ci_driver_id', '=', 'drivers.id')->where('drivers.du_com_id', auth()->guard('company')->user()->id)->whereDate("ci_created_at", $currentDate)->get()->sum("ci_net_invoice");
        $driver = DB::table("customer_invoices")->leftjoin('drivers', 'customer_invoices.ci_driver_id', '=', 'drivers.id')->where('drivers.du_com_id', auth()->guard('company')->user()->id)->whereDate("ci_created_at", $currentDate)->get()->sum("ci_driver_amount");

        $driversEarningCount = CustomerInvoice::leftjoin('drivers', 'customer_invoices.ci_driver_id', '=', 'drivers.id')->where('drivers.du_com_id', auth()->guard('company')->user()->id)->whereDate("customer_invoices.ci_created_at", $currentDate)->sum('customer_invoices.ci_driver_amount');
        $companyEarningCount = CustomerInvoice::leftjoin('drivers', 'customer_invoices.ci_driver_id', '=', 'drivers.id')->where('drivers.du_com_id', auth()->guard('company')->user()->id)->whereDate("customer_invoices.ci_created_at", $currentDate)->sum('customer_invoices.ci_whipp_amount');

        return view('company.company.index',['driverCount'=>$driverCount,'ridesCount'=>$ridesCount,'waitingCount'=>$waitingCount,'acceptedCount'=>$acceptedCount,'completedCount'=>$completedCount,'drivingCount'=>$drivingCount,
            'requestedCount'=>$requestedCount,'rejectedCount'=>$rejectedCount,'ridesCancelByPAssengerCount'=>$ridesCancelByPAssengerCount,'rideIgnoredCount'=>$rideIgnoredCount,'driversEarningCount'=>$driversEarningCount,
            'companyEarningCount'=>$companyEarningCount,'bankCom'=> number_format($bankCom,3,".",","), 'netInvoice' => number_format($netInvoice,3,".",","), 'driver' => number_format($driver,3,".",",") ]);
    }


    public function countRideStatistics($status){

        return RideBookingSchedule::leftjoin('drivers', 'ride_booking_schedules.rbs_driver_id', '=', 'drivers.id')->where(['drivers.du_com_id' => auth()->guard('company')->user()->id,'rbs_ride_status'=>$status])->count();
    }


    public function changeThemes($id)
    {
        Company::where('id', auth()->guard('company')->user()->id)->update(['com_panel_mode' => $id]);
        return redirect()->route('company::company');
    }

    public function changeThemesMode($local)
    {
        Session::put('com_locale', $local);
        Company::where('id', auth()->guard('company')->user()->id)->update(['com_locale' => $local]);
        return redirect()->route('company::company');
    }

    public function companyProfile()
    {
        $companyProfile = Company::where('id', auth()->guard('company')->user()->id)->first();

        return view('company.profile.profile',['companyProfile'=>$companyProfile
        ]);
    }

    public function updateProfile(Request $request)
    {
        $companyProfile = Company::where('id', auth()->guard('company')->user()->id)->first();

      if ($request->hasFile('com_logo')){
            $mime= $request->com_logo->getMimeType();
            $logo = $request->file('com_logo');
            $logo_name =  preg_replace('/\s+/', '', $logo->getClientOriginalName());
            $logoName = time() .'-'.$logo_name;
            $logo->move('./assets/company/logo/', $logoName);
            $comlogo = 'assets/company/logo/'.$logoName;
            $companyProfile->com_logo = $comlogo;
        }

        $companyProfile->com_name = $request->input('com_name');
        $companyProfile->com_contact_number = $request->input('com_contact_number');
        $companyProfile->com_full_contact_number = $request->input('com_full_contact_number');
        $companyProfile->com_license_no = $request->input('com_license_no');
        $companyProfile->com_service_type = $request->input('com_service_type');
        $companyProfile->com_time_zone = $request->input('com_time_zone');
        $companyProfile->com_radius = $request->input('com_radius');
        $companyProfile->com_lat = $request->input('com_lat');
        $companyProfile->com_long = $request->input('com_long');
        $companyProfile->com_user_name = $request->input('com_user_name');
        $companyProfile->email = $request->input('email');

        $companyProfile->com_country_code = $request->input('country_code');

        if(!empty($request->password)){

           $companyProfile->password = Hash::make($request->input('password'));
        }

        $companyProfile->save();

        return response()->json(['success' => true, 'message' => 'Company profile is Successfully updated']);
    }

    public function getCompanyDataFilterDashboard(Request $request){

        $from_date =  $request->input('from_date');
        $to_date =  $request->input('to_date');
        $today =  $request->input('today');
        $this_week =  $request->input('this_week');
        $this_month =  $request->input('this_month');
        $this_year =  $request->input('this_year');
        $userwhere = '';
        if(isset($today) && !empty($today)){
            $currentDate = date('Y-m-d');
//            $userCount = User::whereDate("created_at", $currentDate)->count();

            $driverCount = Driver::where('du_com_id',auth()->guard('company')->user()->id)->whereDate("du_created_at", $currentDate)->count();
            $ridesCount = RideBookingSchedule::leftjoin('drivers', 'ride_booking_schedules.rbs_driver_id', '=', 'drivers.id')->where(['drivers.du_com_id' => auth()->guard('company')->user()->id])->whereDate("rbs_created_at", $currentDate)->count();
            $ridesCancelByPAssengerCount = PassengerCancelRideHistory::leftjoin('drivers', 'passenger_cancel_ride_histories.pcrh_driver_id', '=', 'drivers.id')->where(['drivers.du_com_id' => auth()->guard('company')->user()->id])->whereDate("pcrh_created_at", $currentDate)->count();
            $rideIgnoredCount = RideIgnoredBy::leftjoin('drivers', 'ride_ignored_bies.rib_driver_id', '=', 'drivers.id')->where(['drivers.du_com_id' => auth()->guard('company')->user()->id])->whereDate("rib_created_at", $currentDate)->count();

            $bankCom = DB::table("customer_invoices")->leftjoin('drivers', 'customer_invoices.ci_driver_id', '=', 'drivers.id')->where('drivers.du_com_id', auth()->guard('company')->user()->id)->whereDate("ci_created_at", $currentDate)->get()->sum("ci_bank_amount");
            $netInvoice = DB::table("customer_invoices")->leftjoin('drivers', 'customer_invoices.ci_driver_id', '=', 'drivers.id')->where('drivers.du_com_id', auth()->guard('company')->user()->id)->whereDate("ci_created_at", $currentDate)->get()->sum("ci_net_invoice");
            $driver = DB::table("customer_invoices")->leftjoin('drivers', 'customer_invoices.ci_driver_id', '=', 'drivers.id')->where('drivers.du_com_id', auth()->guard('company')->user()->id)->whereDate("ci_created_at", $currentDate)->get()->sum("ci_driver_amount");

        }

        if(isset($from_date) && !empty($from_date) && isset($to_date) && !empty($to_date)){

            $from_date = date('Y-m-d', strtotime($from_date));
            $to_date = date('Y-m-d', strtotime($to_date));

//            $userCount =  User::whereDate('created_at', '>=', $from_date)->whereDate('created_at', '<=', $to_date)->count();

            $driverCount = Driver::where('du_com_id',auth()->guard('company')->user()->id)->whereDate('du_created_at', '>=', $from_date)->whereDate('du_created_at', '<=', $to_date)->count();

            $ridesCount = RideBookingSchedule::leftjoin('drivers', 'ride_booking_schedules.rbs_driver_id', '=', 'drivers.id')->where(['drivers.du_com_id' => auth()->guard('company')->user()->id])->whereDate('rbs_created_at', '>=', $from_date)->whereDate('rbs_created_at', '<=', $to_date)->count();
            $ridesCancelByPAssengerCount = PassengerCancelRideHistory::leftjoin('drivers', 'passenger_cancel_ride_histories.pcrh_driver_id', '=', 'drivers.id')->where(['drivers.du_com_id' => auth()->guard('company')->user()->id])->whereDate('pcrh_created_at', '>=', $from_date)->whereDate('pcrh_created_at', '<=', $to_date)->count();
            $rideIgnoredCount = RideIgnoredBy::leftjoin('drivers', 'ride_ignored_bies.rib_driver_id', '=', 'drivers.id')->where(['drivers.du_com_id' => auth()->guard('company')->user()->id])->whereDate('rib_created_at', '>=', $from_date)->whereDate('rib_created_at', '<=', $to_date)->count();

            $bankCom = DB::table("customer_invoices")->leftjoin('drivers', 'customer_invoices.ci_driver_id', '=', 'drivers.id')->where('drivers.du_com_id', auth()->guard('company')->user()->id)->whereDate("ci_created_at", '>=', $from_date)->whereDate('ci_created_at', '<=', $to_date)->get()->sum("ci_bank_amount");
            $netInvoice = DB::table("customer_invoices")->leftjoin('drivers', 'customer_invoices.ci_driver_id', '=', 'drivers.id')->where('drivers.du_com_id', auth()->guard('company')->user()->id)->whereDate("ci_created_at", '>=', $from_date)->whereDate('ci_created_at', '<=', $to_date)->get()->sum("ci_net_invoice");
            $driver = DB::table("customer_invoices")->leftjoin('drivers', 'customer_invoices.ci_driver_id', '=', 'drivers.id')->where('drivers.du_com_id', auth()->guard('company')->user()->id)->whereDate("ci_created_at", '>=', $from_date)->whereDate('ci_created_at', '<=', $to_date)->get()->sum("ci_driver_amount");

        }

        if(isset($from_date) && !empty($from_date) && empty($to_date)){

            $from_date = date('Y-m-d', strtotime($from_date));

//            $userCount =  User::whereDate('created_at', '>=', $from_date)->whereDate('created_at', '<=', $from_date)->count();

            $driverCount = Driver::where('du_com_id',auth()->guard('company')->user()->id)->whereDate('du_created_at', '>=', $from_date)->whereDate('du_created_at', '<=', $from_date)->count();

            $ridesCount = RideBookingSchedule::leftjoin('drivers', 'ride_booking_schedules.rbs_driver_id', '=', 'drivers.id')->where(['drivers.du_com_id' => auth()->guard('company')->user()->id])->whereDate('rbs_created_at', '>=', $from_date)->whereDate('rbs_created_at', '<=', $from_date)->count();
            $ridesCancelByPAssengerCount = PassengerCancelRideHistory::leftjoin('drivers', 'passenger_cancel_ride_histories.pcrh_driver_id', '=', 'drivers.id')->where(['drivers.du_com_id' => auth()->guard('company')->user()->id])->whereDate('pcrh_created_at', '>=', $from_date)->whereDate('pcrh_created_at', '<=', $from_date)->count();
            $rideIgnoredCount = RideIgnoredBy::leftjoin('drivers', 'ride_ignored_bies.rib_driver_id', '=', 'drivers.id')->where(['drivers.du_com_id' => auth()->guard('company')->user()->id])->whereDate('rib_created_at', '>=', $from_date)->whereDate('rib_created_at', '<=', $from_date)->count();

            $bankCom = DB::table("customer_invoices")->leftjoin('drivers', 'customer_invoices.ci_driver_id', '=', 'drivers.id')->where('drivers.du_com_id', auth()->guard('company')->user()->id)->whereDate("ci_created_at", '>=', $from_date)->whereDate('ci_created_at', '<=', $from_date)->get()->sum("ci_bank_amount");
            $netInvoice = DB::table("customer_invoices")->leftjoin('drivers', 'customer_invoices.ci_driver_id', '=', 'drivers.id')->where('drivers.du_com_id', auth()->guard('company')->user()->id)->whereDate("ci_created_at", '>=', $from_date)->whereDate('ci_created_at', '<=', $from_date)->get()->sum("ci_net_invoice");
            $driver = DB::table("customer_invoices")->leftjoin('drivers', 'customer_invoices.ci_driver_id', '=', 'drivers.id')->where('drivers.du_com_id', auth()->guard('company')->user()->id)->whereDate("ci_created_at", '>=', $from_date)->whereDate('ci_created_at', '<=', $from_date)->get()->sum("ci_driver_amount");

        }

        if(isset($to_date) && !empty($to_date) && empty($from_date)){

            $to_date = date('Y-m-d', strtotime($to_date));

//            $userCount =  User::whereDate('created_at', '>=', $to_date)->whereDate('created_at', '<=', $to_date)->count();

            $driverCount = Driver::where('du_com_id',auth()->guard('company')->user()->id)->whereDate('du_created_at', '>=', $to_date)->whereDate('du_created_at', '<=', $to_date)->count();

            $ridesCount = RideBookingSchedule::leftjoin('drivers', 'ride_booking_schedules.rbs_driver_id', '=', 'drivers.id')->where(['drivers.du_com_id' => auth()->guard('company')->user()->id])->whereDate('rbs_created_at', '>=', $to_date)->whereDate('rbs_created_at', '<=', $to_date)->count();
            $ridesCancelByPAssengerCount = PassengerCancelRideHistory::leftjoin('drivers', 'passenger_cancel_ride_histories.pcrh_driver_id', '=', 'drivers.id')->where(['drivers.du_com_id' => auth()->guard('company')->user()->id])->whereDate('pcrh_created_at', '>=', $to_date)->whereDate('pcrh_created_at', '<=', $to_date)->count();
            $rideIgnoredCount = RideIgnoredBy::leftjoin('drivers', 'ride_ignored_bies.rib_driver_id', '=', 'drivers.id')->where(['drivers.du_com_id' => auth()->guard('company')->user()->id])->whereDate('rib_created_at', '>=', $to_date)->whereDate('rib_created_at', '<=', $to_date)->count();

            $bankCom = DB::table("customer_invoices")->leftjoin('drivers', 'customer_invoices.ci_driver_id', '=', 'drivers.id')->where('drivers.du_com_id', auth()->guard('company')->user()->id)->whereDate("ci_created_at", '>=', $to_date)->whereDate('ci_created_at', '<=', $to_date)->get()->sum("ci_bank_amount");
            $netInvoice = DB::table("customer_invoices")->leftjoin('drivers', 'customer_invoices.ci_driver_id', '=', 'drivers.id')->where('drivers.du_com_id', auth()->guard('company')->user()->id)->whereDate("ci_created_at", '>=', $to_date)->whereDate('ci_created_at', '<=', $to_date)->get()->sum("ci_net_invoice");
            $driver = DB::table("customer_invoices")->leftjoin('drivers', 'customer_invoices.ci_driver_id', '=', 'drivers.id')->where('drivers.du_com_id', auth()->guard('company')->user()->id)->whereDate("ci_created_at", '>=', $to_date)->whereDate('ci_created_at', '<=', $to_date)->get()->sum("ci_driver_amount");

        }

        if(isset($this_week) && !empty($this_week)){

            $wdate1 = date('Y-m-d', strtotime('-7 days'));
            $wdate2 = date('Y-m-d');

//            $userCount =  User::whereDate('created_at', '>=', $wdate1)->whereDate('created_at', '<=', $wdate2)->count();

            $driverCount = Driver::where('du_com_id',auth()->guard('company')->user()->id)->whereDate('du_created_at', '>=', $wdate1)->whereDate('du_created_at', '<=', $wdate2)->count();

            $ridesCount = RideBookingSchedule::leftjoin('drivers', 'ride_booking_schedules.rbs_driver_id', '=', 'drivers.id')->where(['drivers.du_com_id' => auth()->guard('company')->user()->id])->whereDate('rbs_created_at', '>=', $wdate1)->whereDate('rbs_created_at', '<=', $wdate2)->count();
            $ridesCancelByPAssengerCount = PassengerCancelRideHistory::leftjoin('drivers', 'passenger_cancel_ride_histories.pcrh_driver_id', '=', 'drivers.id')->where(['drivers.du_com_id' => auth()->guard('company')->user()->id])->whereDate('pcrh_created_at', '>=', $wdate1)->whereDate('pcrh_created_at', '<=', $wdate2)->count();
            $rideIgnoredCount = RideIgnoredBy::leftjoin('drivers', 'ride_ignored_bies.rib_driver_id', '=', 'drivers.id')->where(['drivers.du_com_id' => auth()->guard('company')->user()->id])->whereDate('rib_created_at', '>=', $wdate1)->whereDate('rib_created_at', '<=', $wdate2)->count();

            $bankCom = DB::table("customer_invoices")->leftjoin('drivers', 'customer_invoices.ci_driver_id', '=', 'drivers.id')->where('drivers.du_com_id', auth()->guard('company')->user()->id)->whereDate("ci_created_at", '>=', $wdate1)->whereDate('ci_created_at', '<=', $wdate2)->get()->sum("ci_bank_amount");
            $netInvoice = DB::table("customer_invoices")->leftjoin('drivers', 'customer_invoices.ci_driver_id', '=', 'drivers.id')->where('drivers.du_com_id', auth()->guard('company')->user()->id)->whereDate("ci_created_at", '>=', $wdate1)->whereDate('ci_created_at', '<=', $wdate2)->get()->sum("ci_net_invoice");
            $driver = DB::table("customer_invoices")->leftjoin('drivers', 'customer_invoices.ci_driver_id', '=', 'drivers.id')->where('drivers.du_com_id', auth()->guard('company')->user()->id)->whereDate("ci_created_at", '>=', $wdate1)->whereDate('ci_created_at', '<=', $wdate2)->get()->sum("ci_driver_amount");
        }

        if(isset($this_month) && !empty($this_month)){

//            $userCount =  User::whereMonth('created_at', date('m'))->count();

            $driverCount = Driver::where('du_com_id',auth()->guard('company')->user()->id)->whereMonth('du_created_at', date('m'))->count();

            $ridesCount = RideBookingSchedule::leftjoin('drivers', 'ride_booking_schedules.rbs_driver_id', '=', 'drivers.id')->where(['drivers.du_com_id' => auth()->guard('company')->user()->id])->whereMonth('rbs_created_at', date('m'))->count();
            $ridesCancelByPAssengerCount = PassengerCancelRideHistory::leftjoin('drivers', 'passenger_cancel_ride_histories.pcrh_driver_id', '=', 'drivers.id')->where(['drivers.du_com_id' => auth()->guard('company')->user()->id])->whereMonth('pcrh_created_at', date('m'))->count();
            $rideIgnoredCount = RideIgnoredBy::leftjoin('drivers', 'ride_ignored_bies.rib_driver_id', '=', 'drivers.id')->where(['drivers.du_com_id' => auth()->guard('company')->user()->id])->whereMonth('rib_created_at', date('m'))->count();

            $bankCom = DB::table("customer_invoices")->leftjoin('drivers', 'customer_invoices.ci_driver_id', '=', 'drivers.id')->where('drivers.du_com_id', auth()->guard('company')->user()->id)->whereMonth('ci_created_at', date('m'))->get()->sum("ci_bank_amount");
            $netInvoice = DB::table("customer_invoices")->leftjoin('drivers', 'customer_invoices.ci_driver_id', '=', 'drivers.id')->where('drivers.du_com_id', auth()->guard('company')->user()->id)->whereMonth('ci_created_at', date('m'))->get()->sum("ci_net_invoice");
            $driver = DB::table("customer_invoices")->leftjoin('drivers', 'customer_invoices.ci_driver_id', '=', 'drivers.id')->where('drivers.du_com_id', auth()->guard('company')->user()->id)->whereMonth('ci_created_at', date('m'))->get()->sum("ci_driver_amount");

        }

        if(isset($this_year) && !empty($this_year)){

//            $userCount =  User::whereYear('created_at', date('Y'))->count();

            $driverCount = Driver::where('du_com_id',auth()->guard('company')->user()->id)->whereYear('du_created_at', date('Y'))->count();

            $ridesCount = RideBookingSchedule::leftjoin('drivers', 'ride_booking_schedules.rbs_driver_id', '=', 'drivers.id')->where(['drivers.du_com_id' => auth()->guard('company')->user()->id])->whereYear('rbs_created_at', date('Y'))->count();
            $ridesCancelByPAssengerCount = PassengerCancelRideHistory::leftjoin('drivers', 'passenger_cancel_ride_histories.pcrh_driver_id', '=', 'drivers.id')->where(['drivers.du_com_id' => auth()->guard('company')->user()->id])->whereYear('pcrh_created_at', date('Y'))->count();
            $rideIgnoredCount = RideIgnoredBy::leftjoin('drivers', 'ride_ignored_bies.rib_driver_id', '=', 'drivers.id')->where(['drivers.du_com_id' => auth()->guard('company')->user()->id])->whereYear('rib_created_at', date('Y'))->count();

            $bankCom = DB::table("customer_invoices")->leftjoin('drivers', 'customer_invoices.ci_driver_id', '=', 'drivers.id')->where('drivers.du_com_id', auth()->guard('company')->user()->id)->whereYear('ci_created_at', date('Y'))->get()->sum("ci_bank_amount");
            $netInvoice = DB::table("customer_invoices")->leftjoin('drivers', 'customer_invoices.ci_driver_id', '=', 'drivers.id')->where('drivers.du_com_id', auth()->guard('company')->user()->id)->whereYear('ci_created_at', date('Y'))->get()->sum("ci_net_invoice");
            $driver = DB::table("customer_invoices")->leftjoin('drivers', 'customer_invoices.ci_driver_id', '=', 'drivers.id')->where('drivers.du_com_id', auth()->guard('company')->user()->id)->whereYear('ci_created_at', date('Y'))->get()->sum("ci_driver_amount");


        }
//        return response()->json(['userCount'=>$userCount,'driverCount'=>$driverCount,'companyCount'=>$companyCount,'ridesCount' =>$ridesCount,'ridesCancelByPAssengerCount'=>$ridesCancelByPAssengerCount,'rideIgnoredCount'=>$rideIgnoredCount]);
        return response()->json(['driverCount'=>$driverCount,'ridesCount' =>$ridesCount,'ridesCancelByPAssengerCount'=>$ridesCancelByPAssengerCount,'rideIgnoredCount'=>$rideIgnoredCount,
            'bankCom'=>number_format($bankCom, 3, ".", ","),'netInvoice'=>number_format($netInvoice, 3, ".", ","),'driver'=>number_format($driver, 3, ".", ",")]);
    }


    public function TimeZoneSettings(Request $request){
    Company::where('id', auth()->guard('company')->user()->id)->update(['com_time_zone' => $request->userTimeZone]);
    return response()->json(['success' => true, 'message' => 'Timezone Settings is updated successfully']);
    }

}
