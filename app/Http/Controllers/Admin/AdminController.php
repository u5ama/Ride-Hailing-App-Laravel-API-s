<?php

namespace App\Http\Controllers\Admin;

use App\AdminUser;
use App\TimeZone;
use App\User;
use App\Driver;
use App\Company;
use App\RideBookingSchedule;
use App\PassengerCancelRideHistory;
use App\RideIgnoredBy;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use DB;
use Session;

class AdminController extends Controller
{
    /** Method Admin Dashboard Stats */
    public function __construct()
    {
        $this->middleware('admin')->except('logout');
    }

    public function index()
    {
        $currentDate = date('Y-m-d');
        $userCount = User::whereDate("created_at", $currentDate)->count();
        $driverCount = Driver::whereDate("du_created_at", $currentDate)->count();
        $companyCount = Company::whereDate("com_created_at", $currentDate)->count();
        $ridesCount = RideBookingSchedule::whereDate("rbs_created_at", $currentDate)->count();
        $ridesCancelByPAssengerCount = PassengerCancelRideHistory::whereDate("pcrh_created_at", $currentDate)->count();
        $rideIgnoredCount = RideIgnoredBy::whereDate("rib_created_at", $currentDate)->count();

        $waitingCount = $this->countRideStatistics('Waiting');
        $acceptedCount = $this->countRideStatistics('Accepted');
        $completedCount = $this->countRideStatistics('Completed');
        $drivingCount = $this->countRideStatistics('Driving');
        $requestedCount = $this->countRideStatistics('Requested');
        $rejectedCount = $this->countRideStatistics('Rejected');

        $bankCom = DB::table("customer_invoices")->whereDate("ci_created_at", $currentDate)->get()->sum("ci_bank_amount");
        $grossValue = DB::table("customer_invoices")->whereDate("ci_created_at", $currentDate)->get()->sum("ci_company_gross_earning");
        $netInvoice = DB::table("customer_invoices")->whereDate("ci_created_at", $currentDate)->get()->sum("ci_net_invoice");
        $whipp = DB::table("customer_invoices")->whereDate("ci_created_at", $currentDate)->get()->sum("ci_whipp_amount");
        $driver = DB::table("customer_invoices")->whereDate("ci_created_at", $currentDate)->get()->sum("ci_driver_amount");

        $driversEarningCount = 100;
        $companyEarningCount = 100;
        $whippEarningCount = 100;

        return view('admin.admin.index', ['userCount' => $userCount, 'driverCount' => $driverCount, 'companyCount' => $companyCount, 'ridesCount' => $ridesCount, 'waitingCount' => $waitingCount, 'acceptedCount' => $acceptedCount, 'completedCount' => $completedCount, 'drivingCount' => $drivingCount, 'grossValue' => number_format($grossValue, 3, ".", ","),
            'requestedCount' => $requestedCount, 'rejectedCount' => $rejectedCount, 'ridesCancelByPAssengerCount' => $ridesCancelByPAssengerCount, 'rideIgnoredCount' => $rideIgnoredCount, 'driversEarningCount' => $driversEarningCount, 'companyEarningCount' => $companyEarningCount, 'whippEarningCount' => $whippEarningCount, 'bankCom' => number_format($bankCom, 3, ".", ","), 'netInvoice' => number_format($netInvoice, 3, ".", ","), 'whipp' => number_format($whipp, 3, ".", ","), 'driver' => number_format($driver, 3, ".", ",")]);
    }

    /** Method Admin Calculate and count Ride Stats
     * @param $status
     * @return
     */

    public function countRideStatistics($status)
    {
        $currentDate = date('Y-m-d');
        return RideBookingSchedule::where(['rbs_ride_status' => $status])->whereDate("rbs_created_at", $currentDate)->count();
    }

    /** Method Admin Dashboard Theme Change
     * @param $id
     * @return RedirectResponse
     */

    public function changeThemes($id)
    {
        AdminUser::where('id', auth()->guard('admin')->user()->id)->update(['panel_mode' => $id]);
        return redirect()->route('admin::admin');
    }

    /** Method Admin Dashboard Change Theme Mode
     * @param $local
     * @return RedirectResponse
     */

    public function changeThemesMode($local)
    {
        Session::put('locale', $local);
        App::setLocale($local);
        AdminUser::where('id', auth()->guard('admin')->user()->id)->update(['locale' => $local]);
        return redirect()->route('admin::admin');
    }

    /** Method Admin Dashboard Stats Filters
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function getDataFilterDashboard(Request $request)
    {

        $from_date = $request->input('from_date');
        $to_date = $request->input('to_date');
        $today = $request->input('today');
        $this_week = $request->input('this_week');
        $this_month = $request->input('this_month');
        $this_year = $request->input('this_year');
        $userwhere = '';
        if (isset($today) && !empty($today)) {
            $currentDate = date('Y-m-d');
            $userCount = User::whereDate("created_at", $currentDate)->count();

            $driverCount = Driver::whereDate("du_created_at", $currentDate)->count();
            $companyCount = Company::whereDate("com_created_at", $currentDate)->count();
            $ridesCount = RideBookingSchedule::whereDate("rbs_created_at", $currentDate)->count();
            $ridesCancelByPAssengerCount = PassengerCancelRideHistory::whereDate("pcrh_created_at", $currentDate)->count();
            $rideIgnoredCount = RideIgnoredBy::whereDate("rib_created_at", $currentDate)->count();

            $bankCom = DB::table("customer_invoices")->whereDate("ci_created_at", $currentDate)->get()->sum("ci_bank_amount");
            $grossValue = DB::table("customer_invoices")->whereDate("ci_created_at", $currentDate)->get()->sum("ci_company_gross_earning");
            $netInvoice = DB::table("customer_invoices")->whereDate("ci_created_at", $currentDate)->get()->sum("ci_net_invoice");
            $whipp = DB::table("customer_invoices")->whereDate("ci_created_at", $currentDate)->get()->sum("ci_whipp_amount");
            $driver = DB::table("customer_invoices")->whereDate("ci_created_at", $currentDate)->get()->sum("ci_driver_amount");
        }

        if (isset($from_date) && !empty($from_date) && isset($to_date) && !empty($to_date)) {

            $from_date = date('Y-m-d', strtotime($from_date));
            $to_date = date('Y-m-d', strtotime($to_date));

            $userCount = User::whereDate('created_at', '>=', $from_date)->whereDate('created_at', '<=', $to_date)->count();

            $driverCount = Driver::whereDate('du_created_at', '>=', $from_date)->whereDate('du_created_at', '<=', $to_date)->count();

            $companyCount = Company::whereDate('com_created_at', '>=', $from_date)->whereDate('com_created_at', '<=', $to_date)->count();
            $ridesCount = RideBookingSchedule::whereDate('rbs_created_at', '>=', $from_date)->whereDate('rbs_created_at', '<=', $to_date)->count();
            $ridesCancelByPAssengerCount = PassengerCancelRideHistory::whereDate('pcrh_created_at', '>=', $from_date)->whereDate('pcrh_created_at', '<=', $to_date)->count();
            $rideIgnoredCount = RideIgnoredBy::whereDate('rib_created_at', '>=', $from_date)->whereDate('rib_created_at', '<=', $to_date)->count();

            $bankCom = DB::table("customer_invoices")->whereDate('ci_created_at', '>=', $from_date)->whereDate('ci_created_at', '<=', $to_date)->get()->sum("ci_bank_amount");
            $grossValue = DB::table("customer_invoices")->whereDate('ci_created_at', '>=', $from_date)->whereDate('ci_created_at', '<=', $to_date)->get()->sum("ci_company_gross_earning");
            $netInvoice = DB::table("customer_invoices")->whereDate('ci_created_at', '>=', $from_date)->whereDate('ci_created_at', '<=', $to_date)->get()->sum("ci_net_invoice");
            $whipp = DB::table("customer_invoices")->whereDate('ci_created_at', '>=', $from_date)->whereDate('ci_created_at', '<=', $to_date)->get()->sum("ci_whipp_amount");
            $driver = DB::table("customer_invoices")->whereDate('ci_created_at', '>=', $from_date)->whereDate('ci_created_at', '<=', $to_date)->get()->sum("ci_driver_amount");

        }

        if (isset($from_date) && !empty($from_date) && empty($to_date)) {

            $from_date = date('Y-m-d', strtotime($from_date));

            $userCount = User::whereDate('created_at', '>=', $from_date)->whereDate('created_at', '<=', $from_date)->count();

            $driverCount = Driver::whereDate('du_created_at', '>=', $from_date)->whereDate('du_created_at', '<=', $from_date)->count();

            $companyCount = Company::whereDate('com_created_at', '>=', $from_date)->whereDate('com_created_at', '<=', $from_date)->count();
            $ridesCount = RideBookingSchedule::whereDate('rbs_created_at', '>=', $from_date)->whereDate('rbs_created_at', '<=', $from_date)->count();
            $ridesCancelByPAssengerCount = PassengerCancelRideHistory::whereDate('pcrh_created_at', '>=', $from_date)->whereDate('pcrh_created_at', '<=', $from_date)->count();
            $rideIgnoredCount = RideIgnoredBy::whereDate('rib_created_at', '>=', $from_date)->whereDate('rib_created_at', '<=', $from_date)->count();

            $bankCom = DB::table("customer_invoices")->whereDate('ci_created_at', '>=', $from_date)->whereDate('ci_created_at', '<=', $from_date)->get()->sum("ci_bank_amount");
            $grossValue = DB::table("customer_invoices")->whereDate('ci_created_at', '>=', $from_date)->whereDate('ci_created_at', '<=', $from_date)->get()->sum("ci_company_gross_earning");
            $netInvoice = DB::table("customer_invoices")->whereDate('ci_created_at', '>=', $from_date)->whereDate('ci_created_at', '<=', $from_date)->get()->sum("ci_net_invoice");
            $whipp = DB::table("customer_invoices")->whereDate('ci_created_at', '>=', $from_date)->whereDate('ci_created_at', '<=', $from_date)->get()->sum("ci_whipp_amount");
            $driver = DB::table("customer_invoices")->whereDate('ci_created_at', '>=', $from_date)->whereDate('ci_created_at', '<=', $from_date)->get()->sum("ci_driver_amount");

        }

        if (isset($to_date) && !empty($to_date) && empty($from_date)) {

            $to_date = date('Y-m-d', strtotime($to_date));

            $userCount = User::whereDate('created_at', '>=', $to_date)->whereDate('created_at', '<=', $to_date)->count();

            $driverCount = Driver::whereDate('du_created_at', '>=', $to_date)->whereDate('du_created_at', '<=', $to_date)->count();

            $companyCount = Company::whereDate('com_created_at', '>=', $to_date)->whereDate('com_created_at', '<=', $to_date)->count();
            $ridesCount = RideBookingSchedule::whereDate('rbs_created_at', '>=', $to_date)->whereDate('rbs_created_at', '<=', $to_date)->count();
            $ridesCancelByPAssengerCount = PassengerCancelRideHistory::whereDate('pcrh_created_at', '>=', $to_date)->whereDate('pcrh_created_at', '<=', $to_date)->count();
            $rideIgnoredCount = RideIgnoredBy::whereDate('rib_created_at', '>=', $to_date)->whereDate('rib_created_at', '<=', $to_date)->count();

            $bankCom = DB::table("customer_invoices")->whereDate('ci_created_at', '>=', $to_date)->whereDate('ci_created_at', '<=', $to_date)->get()->sum("ci_bank_amount");
            $grossValue = DB::table("customer_invoices")->whereDate('ci_created_at', '>=', $to_date)->whereDate('ci_created_at', '<=', $to_date)->get()->sum("ci_company_gross_earning");
            $netInvoice = DB::table("customer_invoices")->whereDate('ci_created_at', '>=', $to_date)->whereDate('ci_created_at', '<=', $to_date)->get()->sum("ci_net_invoice");
            $whipp = DB::table("customer_invoices")->whereDate('ci_created_at', '>=', $to_date)->whereDate('ci_created_at', '<=', $to_date)->get()->sum("ci_whipp_amount");
            $driver = DB::table("customer_invoices")->whereDate('ci_created_at', '>=', $to_date)->whereDate('ci_created_at', '<=', $to_date)->get()->sum("ci_driver_amount");
        }

        if (isset($this_week) && !empty($this_week)) {

            $wdate1 = date('Y-m-d', strtotime('-7 days'));
            $wdate2 = date('Y-m-d');

            $userCount = User::whereDate('created_at', '>=', $wdate1)->whereDate('created_at', '<=', $wdate2)->count();

            $driverCount = Driver::whereDate('du_created_at', '>=', $wdate1)->whereDate('du_created_at', '<=', $wdate2)->count();

            $companyCount = Company::whereDate('com_created_at', '>=', $wdate1)->whereDate('com_created_at', '<=', $wdate2)->count();
            $ridesCount = RideBookingSchedule::whereDate('rbs_created_at', '>=', $wdate1)->whereDate('rbs_created_at', '<=', $wdate2)->count();
            $ridesCancelByPAssengerCount = PassengerCancelRideHistory::whereDate('pcrh_created_at', '>=', $wdate1)->whereDate('pcrh_created_at', '<=', $wdate2)->count();
            $rideIgnoredCount = RideIgnoredBy::whereDate('rib_created_at', '>=', $wdate1)->whereDate('rib_created_at', '<=', $wdate2)->count();

            $bankCom = DB::table("customer_invoices")->whereDate('ci_created_at', '>=', $wdate1)->whereDate('ci_created_at', '<=', $wdate2)->get()->sum("ci_bank_amount");
            $grossValue = DB::table("customer_invoices")->whereDate('ci_created_at', '>=', $wdate1)->whereDate('ci_created_at', '<=', $wdate2)->get()->sum("ci_company_gross_earning");
            $netInvoice = DB::table("customer_invoices")->whereDate('ci_created_at', '>=', $wdate1)->whereDate('ci_created_at', '<=', $wdate2)->get()->sum("ci_net_invoice");
            $whipp = DB::table("customer_invoices")->whereDate('ci_created_at', '>=', $wdate1)->whereDate('ci_created_at', '<=', $wdate2)->get()->sum("ci_whipp_amount");
            $driver = DB::table("customer_invoices")->whereDate('ci_created_at', '>=', $wdate1)->whereDate('ci_created_at', '<=', $wdate2)->get()->sum("ci_driver_amount");
        }

        if (isset($this_month) && !empty($this_month)) {

            $userCount = User::whereMonth('created_at', date('m'))->count();

            $driverCount = Driver::whereMonth('du_created_at', date('m'))->count();

            $companyCount = Company::whereMonth('com_created_at', date('m'))->count();
            $ridesCount = RideBookingSchedule::whereMonth('rbs_created_at', date('m'))->count();
            $ridesCancelByPAssengerCount = PassengerCancelRideHistory::whereMonth('pcrh_created_at', date('m'))->count();
            $rideIgnoredCount = RideIgnoredBy::whereMonth('rib_created_at', date('m'))->count();

            $bankCom = DB::table("customer_invoices")->whereMonth('ci_created_at', date('m'))->get()->sum("ci_bank_amount");
            $grossValue = DB::table("customer_invoices")->whereMonth('ci_created_at', date('m'))->get()->sum("ci_company_gross_earning");
            $netInvoice = DB::table("customer_invoices")->whereMonth('ci_created_at', date('m'))->get()->sum("ci_net_invoice");
            $whipp = DB::table("customer_invoices")->whereMonth('ci_created_at', date('m'))->get()->sum("ci_whipp_amount");
            $driver = DB::table("customer_invoices")->whereMonth('ci_created_at', date('m'))->get()->sum("ci_driver_amount");
        }

        if (isset($this_year) && !empty($this_year)) {

            $userCount = User::whereYear('created_at', date('Y'))->count();

            $driverCount = Driver::whereYear('du_created_at', date('Y'))->count();

            $companyCount = Company::whereYear('com_created_at', date('Y'))->count();
            $ridesCount = RideBookingSchedule::whereYear('rbs_created_at', date('Y'))->count();
            $ridesCancelByPAssengerCount = PassengerCancelRideHistory::whereYear('pcrh_created_at', date('Y'))->count();
            $rideIgnoredCount = RideIgnoredBy::whereYear('rib_created_at', date('Y'))->count();

            $bankCom = DB::table("customer_invoices")->whereYear('ci_created_at', date('Y'))->get()->sum("ci_bank_amount");
            $grossValue = DB::table("customer_invoices")->whereYear('ci_created_at', date('Y'))->get()->sum("ci_company_gross_earning");
            $netInvoice = DB::table("customer_invoices")->whereYear('ci_created_at', date('Y'))->get()->sum("ci_net_invoice");
            $whipp = DB::table("customer_invoices")->whereYear('ci_created_at', date('Y'))->get()->sum("ci_whipp_amount");
            $driver = DB::table("customer_invoices")->whereYear('ci_created_at', date('Y'))->get()->sum("ci_driver_amount");
        }
        return response()->json(['userCount' => $userCount, 'driverCount' => $driverCount, 'companyCount' => $companyCount,
            'ridesCount' => $ridesCount, 'ridesCancelByPAssengerCount' => $ridesCancelByPAssengerCount, 'rideIgnoredCount' => $rideIgnoredCount,
            'bankCom'=>number_format($bankCom, 3, ".", ","),
            'netInvoice'=>number_format($netInvoice, 3, ".", ","),
            'driver'=>number_format($driver, 3, ".", ","),
            'grossValue'=>number_format($grossValue, 3, ".", ","),
            'whipp'=>number_format($whipp, 3, ".", ",")]);
    }


    public function getTimeZone(Request $request)
    {
       $timeZones = TimeZone::all();
        if (count($timeZones) > 0) {
            echo "<option value=''>Please Select Time Zone</option>";
            foreach ($timeZones as $timeZone) {
                echo "<option value='" . $timeZone->id . "'>" . $timeZone->time_zone . "</option>";
            }
        } else {
            echo "<option value=''>No Data Found</option>";
        }
    }

    public function TimeZoneSettings(Request $request){

    AdminUser::where('id', auth()->guard('admin')->user()->id)->update(['time_zone_id' => $request->userTimeZone]);
    return response()->json(['success' => true, 'message' => 'Timezone Settings is updated successfully']);

    }




}
