<?php

namespace App\Http\Controllers\Admin;

use App\Company;
use App\Country;
use App\Driver;
use App\DriverCurrentLocation;
use App\DriverCurrentLogs;
use App\RideBookingSchedule;
use App\TimeZone;
use App\Utility\Utility;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;


class DriverStatusController extends Controller
{
    /**
     * Display a listing of the DriverStatus.
     *
     * @return Application|Factory|View
     */
    protected $data = [];

    public function index()
    {
        $countries = Country::listsTranslations('name')
            ->where('status', 'Active')
            ->get();
        $drivers = Driver::all();
        $vehicles = Driver::with('driverProf')->get();
        return view('admin.DriverStatus.index', compact('countries','drivers','vehicles'));
    }

    /**
     * Show the form for creating a new DriverStatus.
     *
     * @return void
     */
    public function create()
    {

    }

    /**
     * Driver Data Method to Display
     * @param Request $request
     * @param $country
     * @return JsonResponse
     */
    public function getDriversData(Request $request, $country)
    {
        if ($request->ajax()) {
            $id = (!empty($country)) ? ($country) : ('');
            $filterWithCountry = Country::listsTranslations('name')->where('countries.id', $id)->first();

            $onlineDrivers = DriverCurrentLocation::where(['dcl_country' => $filterWithCountry['name'], 'dcl_app_active' => 1])->count();
            $offlineDrivers = DriverCurrentLocation::where(['dcl_country' => $filterWithCountry['name'], 'dcl_app_active' => 0])->count();
            $busyDriversCount = RideBookingSchedule::leftJoin('driver_current_locations', 'ride_booking_schedules.rbs_driver_id', '=', 'driver_current_locations.dcl_user_id')->whereIn('ride_booking_schedules.rbs_ride_status', ['Requested', 'Waiting', 'Accepted', 'Driving'])->select('driver_current_locations.*')->where(['driver_current_locations.dcl_app_active' => 1, 'driver_current_locations.dcl_country' => $filterWithCountry['name']])->count();
            $busyDrivers_ides = RideBookingSchedule::leftJoin('driver_current_locations', 'ride_booking_schedules.rbs_driver_id', '=', 'driver_current_locations.dcl_user_id')->whereIn('ride_booking_schedules.rbs_ride_status', ['Requested', 'Waiting', 'Accepted', 'Driving'])->pluck('driver_current_locations.dcl_user_id')->toArray();

            $drivers = DriverCurrentLocation::where(['dcl_country' => $filterWithCountry['name']])->whereNotIn('dcl_user_id', $busyDrivers_ides)->get();
            foreach ($drivers as $val) {
                $val['isBusy'] = false;
            }

            $busyDrivers = DriverCurrentLocation::where(['dcl_country' => $filterWithCountry['name']])->whereIn('dcl_user_id', $busyDrivers_ides)->get();
            foreach ($busyDrivers as $items) {
                $items['isBusy'] = true;
            }
            $drivers = $drivers->merge($busyDrivers);

            return response()->json(['success' => true, 'drivers' => $drivers, 'onlineDrivers' => $onlineDrivers, 'offlineDrivers' => $offlineDrivers, 'busyDrivers' => $busyDriversCount]);
        }
    }

    /**
     * Driver Data Filter Method
     * @param Request $request
     * @return JsonResponse
     */
    public function getDriversDataByFilter(Request $request)
    {
        if ($request->ajax()) {
            $country = $request->country_id;
            $company = $request->company_id;
            $number = $request->driverNumber;
            $vehicle = $request->driverVehicle;

            $id = (!empty($country)) ? ($country) : ('');
            $filterWithCountry = Country::listsTranslations('name')->where('countries.id', $id)->first();
            $filterWithCompany = (!empty($company)) ? ($company) : ('');

            if (!empty($filterWithCompany)) {
                $onlineDrivers = Driver::leftJoin('driver_current_locations', 'drivers.id', '=', 'driver_current_locations.dcl_user_id')->leftJoin('driver_profiles', 'drivers.id', '=', 'driver_profiles.dp_user_id')->where(['drivers.du_com_id' => $filterWithCompany]);
            }else{
                $onlineDrivers = Driver::leftJoin('driver_current_locations', 'drivers.id', '=', 'driver_current_locations.dcl_user_id')->leftJoin('driver_profiles', 'drivers.id', '=', 'driver_profiles.dp_user_id');
            }
            if (isset($number) && count($number) > 0) {

                $onlineDrivers->whereIn('drivers.du_mobile_number', $number);
            }
            if (isset($vehicle) && count($vehicle) > 0) {

                $onlineDrivers->whereIn('driver_profiles.car_registration', $vehicle);
            }
            $onlineDrivers = $onlineDrivers->select('driver_current_locations.*')->where(['dcl_country' => $filterWithCountry['name'], 'dcl_app_active' => 1])->count();

            if (!empty($filterWithCompany)){
                $offlineDrivers = Driver::leftJoin('driver_current_locations', 'drivers.id', '=', 'driver_current_locations.dcl_user_id')->where(['drivers.du_com_id' => $filterWithCompany]);
            }
           else{
               $offlineDrivers = Driver::leftJoin('driver_current_locations', 'drivers.id', '=', 'driver_current_locations.dcl_user_id');
           }

            if (isset($number) && count($number) > 0) {

                $offlineDrivers->whereIn('drivers.du_mobile_number', $number);
            }
            if (isset($vehicle) && count($vehicle) > 0) {

                $offlineDrivers->leftJoin('driver_profiles', 'drivers.id', '=', 'driver_profiles.dp_user_id')->whereIn('driver_profiles.car_registration', $vehicle);
            }
            $offlineDrivers = $offlineDrivers->select('driver_current_locations.*')->where(['dcl_country' => $filterWithCountry['name'], 'dcl_app_active' => 0])->count();

            if (!empty($filterWithCompany)){
                $busyDriversCount = RideBookingSchedule::leftJoin('driver_current_locations', 'ride_booking_schedules.rbs_driver_id', '=', 'driver_current_locations.dcl_user_id')->leftJoin('drivers', 'ride_booking_schedules.rbs_driver_id', '=', 'drivers.id')->where('drivers.du_com_id', $filterWithCompany)->whereIn('ride_booking_schedules.rbs_ride_status', ['Requested', 'Waiting', 'Accepted', 'Driving'])->select('driver_current_locations.*')->where('dcl_country', $filterWithCountry['name'])->count();
            }else{
                $busyDriversCount = RideBookingSchedule::leftJoin('driver_current_locations', 'ride_booking_schedules.rbs_driver_id', '=', 'driver_current_locations.dcl_user_id')->leftJoin('drivers', 'ride_booking_schedules.rbs_driver_id', '=', 'drivers.id')->whereIn('ride_booking_schedules.rbs_ride_status', ['Requested', 'Waiting', 'Accepted', 'Driving'])->select('driver_current_locations.*')->where('dcl_country', $filterWithCountry['name'])->count();
            }
            $busyDrivers_ides = RideBookingSchedule::leftJoin('driver_current_locations', 'ride_booking_schedules.rbs_driver_id', '=', 'driver_current_locations.dcl_user_id')->whereIn('ride_booking_schedules.rbs_ride_status', ['Requested', 'Waiting', 'Accepted', 'Driving'])->pluck('driver_current_locations.dcl_user_id')->toArray();

            if (!empty($filterWithCompany)) {
                $drivers = Driver::leftJoin('driver_current_locations', 'drivers.id', '=', 'driver_current_locations.dcl_user_id')->leftJoin('driver_profiles', 'drivers.id', '=', 'driver_profiles.dp_user_id')->where(['drivers.du_com_id' => $filterWithCompany]);
            }
            else{
                $drivers = Driver::leftJoin('driver_current_locations', 'drivers.id', '=', 'driver_current_locations.dcl_user_id')->leftJoin('driver_profiles', 'drivers.id', '=', 'driver_profiles.dp_user_id');
            }
            if (isset($number) && count($number) > 0) {

                $drivers->whereIn('drivers.du_mobile_number', $number);
            }
            if (isset($vehicle) && count($vehicle) > 0) {

                $drivers->whereIn('driver_profiles.car_registration', $vehicle);
            }
            $drivers = $drivers->whereNotIn('dcl_user_id', $busyDrivers_ides)->select('driver_current_locations.*')->get();
            foreach ($drivers as $val) {
                $val['isBusy'] = false;
            }

            if (!empty($filterWithCompany)) {
                $busyDrivers = Driver::leftJoin('driver_current_locations', 'drivers.id', '=', 'driver_current_locations.dcl_user_id')->leftJoin('driver_profiles', 'drivers.id', '=', 'driver_profiles.dp_user_id')->where(['drivers.du_com_id' => $filterWithCompany]);
            }else{
                $busyDrivers = Driver::leftJoin('driver_current_locations', 'drivers.id', '=', 'driver_current_locations.dcl_user_id')->leftJoin('driver_profiles', 'drivers.id', '=', 'driver_profiles.dp_user_id');
            }
            if (isset($number) && count($number) > 0) {

                $busyDrivers->whereIn('drivers.du_mobile_number', $number);
            }
            if (isset($vehicle) && count($vehicle) > 0) {

                $busyDrivers->whereIn('driver_profiles.car_registration', $vehicle);
            }
            $busyDrivers = $busyDrivers->whereIn('dcl_user_id', $busyDrivers_ides)->select('driver_current_locations.*')->get();
            foreach ($busyDrivers as $items) {
                $items['isBusy'] = true;
            }

            $drivers = $drivers->merge($busyDrivers);
            return response()->json(['success' => true, 'drivers' => $drivers, 'onlineDrivers' => $onlineDrivers, 'offlineDrivers' => $offlineDrivers, 'busyDrivers' => $busyDriversCount]);
        }
    }

    /**
     * Get all companies data
     * @param Request $request
     * @return string
     */
    public function getCompanies(Request $request)
    {
        $results = Company::where('com_country_id', $request->com_country_id)->get();
        $app_ref_type = '';
        $app_ref_type .= '<option value="">' . 'Company' . '</option>';

        foreach ($results as $data) {
            $ref_type_obj = $data->com_name;
            $app_ref_type .= '<option value="' . $data->id . '">' . $ref_type_obj . '</option>';
        }
        return $app_ref_type;
    }

    /**
     * Get all companies Driver Numbers
     * @param Request $request
     * @return string
     */
    public function getCompanyDriversNumbers(Request $request)
    {
        $results = Driver::where('du_com_id', $request->com_id)->get();
        $app_ref_type = '';
        $app_ref_type .= '<option value="">' . 'Mobile#' . '</option>';

        foreach ($results as $data) {
            $ref_type_obj = $data->du_mobile_number;
            $app_ref_type .= '<option value="' . $data->du_mobile_number . '">' . $ref_type_obj . '</option>';
        }
        return $app_ref_type;
    }

    /**
     * Get all Company Drivers vehicles Data
     * @param Request $request
     * @return string
     */
    public function getCompanyDriversVehicles(Request $request)
    {
        $results = Driver::with('driverProf')->where('du_com_id', $request->com_id)->get();
        $app_ref_type = '';
        $app_ref_type .= '<option value="">' . 'Vehicle##' . '</option>';

        foreach ($results as $data) {
            if ($data->driverProf !== null) {
                $ref_type_obj = $data->driverProf->car_registration;
                $app_ref_type .= '<option value="' . $data->driverProf->car_registration . '">' . $ref_type_obj . '</option>';
            }
        }
        return $app_ref_type;
    }

    /**
     * Get a single Driver Detail
     * @param $id
     * @return JsonResponse
     */
    public function driverDetails($id)
    {
        $driver = Driver::with('company', 'driverProf')->where('id', $id)->first();
        $driverTime = DriverCurrentLogs::where(['dcl_user_id' => $id, 'action' => 'update'])->orderBy('timestamp', 'DESC')->first();

        $currentDateTime = date('Y-m-d H:s:i');
        $timezone = TimeZone::where('id',auth()->guard('admin')->user()->time_zone_id)->first();

        $currentDateTime = Utility:: convertTimeToUSERzone($currentDateTime,$timezone->time_zone);

        $from = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $currentDateTime);

//        $pickup_location = app('geocoder')->reverse($latitude,$longitude)->get()->first();
        $driverCity = $driverTime->dcl_city;
        if (!empty($driverTime)) {
            if ($driverTime->dcl_app_active == 0) {
                $to = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $driverTime->timestamp);
                if(empty($to)){

                }
                $to = Utility:: convertTimeToUSERzone($to,$timezone->time_zone);

                $format = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $to);
                $diff_in_minutes = $format->diffInMinutes($from, true);

                $hrs_format = intdiv($diff_in_minutes, 60);
                if($hrs_format !== 0){
                    $diff_in_minutes = $hrs_format.'hr '. ($diff_in_minutes % 60);
                }else{
                    $diff_in_minutes = ($diff_in_minutes % 60);
                }
                $status = 'offline';
            } else {
                $to = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $driverTime->timestamp);
                if(empty($to)){

                }
                $to = Utility:: convertTimeToUSERzone($to,$timezone->time_zone);

                $format = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $to);

                $diff_in_minutes = $format->diffInMinutes($from, true);
                $hrs_format = intdiv($diff_in_minutes, 60);

                if($hrs_format !== 0){
                    $diff_in_minutes = $hrs_format.'hr '. ($diff_in_minutes % 60);
                }else{
                    $diff_in_minutes = ($diff_in_minutes % 60);
                }
                $status = 'online';
            }
        } else {
            $status = 'unavailable';
            $diff_in_minutes = '0';
        }

        return response()->json(['success' => true, 'driver' => $driver, 'status' => $status, 'time' => $diff_in_minutes, 'driverCity' => $driverCity]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request)
    {
        //
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
     * @param $company_id
     * @param int $id
     * @return void
     */
    public function edit($company_id, $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return void
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        return response()->json(['success' => true, 'message' => trans('adminMessages.country_deleted')]);
    }

}
