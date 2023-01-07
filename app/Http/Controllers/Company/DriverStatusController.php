<?php

namespace App\Http\Controllers\Company;

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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;


class DriverStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    protected $data = [];

    public function index()
    {
        $company_id = auth()->guard('company')->user()->id;
        $drivers = Driver::with('DriverProfile')->where('du_com_id',$company_id)->get();

        return view('company.DriverStatus.index', compact('drivers','company_id'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {

    }

    public function getDriversData(Request $request)
    {
        if ($request->ajax())
        {
            $filterWithCompany = auth()->guard('company')->user()->id;
            $company_row = Company::where('id', $filterWithCompany)->first();
            if (!empty($company_row)){
                $filterWithCountry = Country::listsTranslations('name')->where('countries.id', $company_row->com_country_id)->first();
            }else{
                $filterWithCountry = Country::listsTranslations('name')->where('countries.id', 75)->first();
            }


                $onlineDrivers = Driver::leftJoin('driver_current_locations', 'drivers.id', '=', 'driver_current_locations.dcl_user_id')->where(['drivers.du_com_id' => $filterWithCompany,'driver_current_locations.dcl_country' => $filterWithCountry['name']])->select('driver_current_locations.*')->where(['dcl_app_active'=> 1])->count();
                $offlineDrivers = Driver::leftJoin('driver_current_locations', 'drivers.id', '=', 'driver_current_locations.dcl_user_id')->where(['drivers.du_com_id' => $filterWithCompany,'driver_current_locations.dcl_country' => $filterWithCountry['name']])->select('driver_current_locations.*')->where(['dcl_app_active'=> 0])->count();

                $busyDriversCount = RideBookingSchedule::leftJoin('driver_current_locations', 'ride_booking_schedules.rbs_driver_id', '=', 'driver_current_locations.dcl_user_id')->leftJoin('drivers', 'ride_booking_schedules.rbs_driver_id', '=', 'drivers.id')->where(['drivers.du_com_id'=>$filterWithCompany,'driver_current_locations.dcl_country' => $filterWithCountry['name']])->whereIn('ride_booking_schedules.rbs_ride_status', ['Requested','Waiting','Accepted','Driving'])->select('driver_current_locations.*')->count();
                $busyDrivers_ides = RideBookingSchedule::leftJoin('driver_current_locations', 'ride_booking_schedules.rbs_driver_id', '=', 'driver_current_locations.dcl_user_id')->leftJoin('drivers', 'ride_booking_schedules.rbs_driver_id', '=', 'drivers.id')->where(['drivers.du_com_id'=>$filterWithCompany,'driver_current_locations.dcl_country' => $filterWithCountry['name']])->whereIn('ride_booking_schedules.rbs_ride_status', ['Requested','Waiting','Accepted','Driving'])->pluck('driver_current_locations.dcl_user_id')->toArray();


                if(count($busyDrivers_ides) > 0) {
                    $drivers = Driver::leftJoin('driver_current_locations', 'drivers.id', '=', 'driver_current_locations.dcl_user_id')->whereNotIn('driver_current_locations.dcl_user_id', $busyDrivers_ides)->where(['drivers.du_driver_status' => 'driver_status_when_approved', 'drivers.du_com_id' => $filterWithCompany])->select('driver_current_locations.*')->get();
                }else{
                    $drivers = Driver::leftJoin('driver_current_locations', 'drivers.id', '=', 'driver_current_locations.dcl_user_id')->where(['drivers.du_driver_status' => 'driver_status_when_approved', 'drivers.du_com_id' => $filterWithCompany])->select('driver_current_locations.*')->get();
                }

                foreach ($drivers as $val){
                    $val['isBusy'] = false;
                }

                $busyDrivers = Driver::leftJoin('driver_current_locations', 'drivers.id', '=', 'driver_current_locations.dcl_user_id')->where(['drivers.du_driver_status'=>'driver_status_when_approved','drivers.du_com_id' => $filterWithCompany])->select('driver_current_locations.*')->whereIn('dcl_user_id',$busyDrivers_ides)->get();
                foreach ($busyDrivers as $items){
                    $items['isBusy'] = true;
                }

                $drivers = $drivers->merge($busyDrivers);

            return response()->json(['success' => true, 'drivers' => $drivers, 'onlineDrivers' => $onlineDrivers, 'offlineDrivers' => $offlineDrivers, 'busyDrivers' => $busyDriversCount]);
        }
    }

    public function getDriversDataByFilter(Request $request)
    {
        if ($request->ajax())
        {
            /*$this->validate($request, [
                'driverNumber' => 'required',
                'driverVehicle' => 'required',
            ]);*/

            $number = $request->driverNumber;
            $vehicle = $request->driverVehicle;

            $filterWithCompany = auth()->guard('company')->user()->id;

            $company_row = Company::where('id', $filterWithCompany)->first();
            $filterWithCountry = Country::listsTranslations('name')->where('countries.id', $company_row->com_country_id)->first();

            $onlineDrivers = Driver::leftJoin('driver_current_locations', 'drivers.id', '=', 'driver_current_locations.dcl_user_id')->leftJoin('driver_profiles', 'drivers.id', '=', 'driver_profiles.dp_user_id')->where(['drivers.du_com_id' => $filterWithCompany,'driver_current_locations.dcl_country' => $filterWithCountry['name']]);
            if(isset($number) && count($number) >0){

                $onlineDrivers->whereIn('drivers.du_mobile_number', $number);
            } if(isset($vehicle) && count($vehicle) >0){

            $onlineDrivers->whereIn('driver_profiles.car_registration', $vehicle);
        }
            $onlineDrivers =   $onlineDrivers->select('driver_current_locations.*')->where(['dcl_app_active'=> 1])->count();
            $offlineDrivers = Driver::leftJoin('driver_current_locations', 'drivers.id', '=', 'driver_current_locations.dcl_user_id')->leftJoin('driver_profiles', 'drivers.id', '=', 'driver_profiles.dp_user_id')->where(['drivers.du_com_id' => $filterWithCompany,'driver_current_locations.dcl_country' => $filterWithCountry['name']]);
            if(isset($number) && count($number) >0){

                $offlineDrivers->whereIn('drivers.du_mobile_number' ,$number);
            }
            if(isset($vehicle) && count($vehicle) >0){

                $offlineDrivers->whereIn('driver_profiles.car_registration', $vehicle);
            }
            $offlineDrivers =  $offlineDrivers->select('driver_current_locations.*')->where(['dcl_app_active'=> 0])->count();

            $busyDriversCount = RideBookingSchedule::leftJoin('driver_current_locations', 'ride_booking_schedules.rbs_driver_id', '=', 'driver_current_locations.dcl_user_id')->leftJoin('drivers', 'ride_booking_schedules.rbs_driver_id', '=', 'drivers.id')->where(['drivers.du_com_id'=>$filterWithCompany,'driver_current_locations.dcl_country' => $filterWithCountry['name']])->whereIn('ride_booking_schedules.rbs_ride_status', ['Requested','Waiting','Accepted','Driving'])->select('driver_current_locations.*')->count();

            $busyDrivers_ides = RideBookingSchedule::leftJoin('driver_current_locations', 'ride_booking_schedules.rbs_driver_id', '=', 'driver_current_locations.dcl_user_id')->whereIn('ride_booking_schedules.rbs_ride_status', ['Requested','Waiting','Accepted','Driving'])->pluck('driver_current_locations.dcl_user_id')->toArray();

            $drivers = Driver::leftJoin('driver_current_locations', 'drivers.id', '=', 'driver_current_locations.dcl_user_id')->leftJoin('driver_profiles', 'drivers.id', '=', 'driver_profiles.dp_user_id')->where(['drivers.du_com_id' => $filterWithCompany,'driver_current_locations.dcl_country' => $filterWithCountry['name']]);
            if(isset($number) && count($number) >0){

                $drivers->whereIn('drivers.du_mobile_number' ,$number);
            }
            if(isset($vehicle) && count($vehicle) >0){

                $drivers->whereIn('driver_profiles.car_registration', $vehicle);
            }
            $drivers = $drivers->whereNotIn('dcl_user_id',$busyDrivers_ides)->select('driver_current_locations.*')->get();
            foreach ($drivers as $val){
                $val['isBusy'] = false;
            }

            $busyDrivers = Driver::leftJoin('driver_current_locations', 'drivers.id', '=', 'driver_current_locations.dcl_user_id')->leftJoin('driver_profiles', 'drivers.id', '=', 'driver_profiles.dp_user_id')->where(['drivers.du_com_id' => $filterWithCompany,'driver_current_locations.dcl_country' => $filterWithCountry['name']]);
            if(isset($number) && count($number) >0){

                $busyDrivers->whereIn('drivers.du_mobile_number' ,$number);
            }
            if(isset($vehicle) && count($vehicle) >0){

                $busyDrivers->whereIn('driver_profiles.car_registration', $vehicle);
            }
            $busyDrivers = $busyDrivers->whereIn('dcl_user_id',$busyDrivers_ides)->select('driver_current_locations.*')->get();
            foreach ($busyDrivers as $items){
                $items['isBusy'] = true;
            }

            $drivers = $drivers->merge($busyDrivers);


            return response()->json(['success' => true, 'drivers' => $drivers, 'onlineDrivers' => $onlineDrivers, 'offlineDrivers' => $offlineDrivers, 'busyDrivers' => $busyDriversCount]);
        }
    }

    public function driverDetails($id)
    {
        $filterWithCompany = auth()->guard('company')->user()->id;
        $driver = Driver::with('company','driverProf')->where(['id'=> $id, 'du_com_id' => $filterWithCompany])->first();
        $driverTime = DriverCurrentLogs::where(['dcl_user_id' => $id, 'action' => 'update'])->orderBy('timestamp', 'DESC')->first();

        $currentDateTime = date('Y-m-d H:s:i');
        $timezone = TimeZone::where('id',auth()->guard('company')->user()->com_time_zone)->first();

        $currentDateTime = Utility:: convertTimeToUSERzone($currentDateTime,$timezone->time_zone);

        $from = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $currentDateTime);
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
     * @return JsonResponse
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
    public function edit($company_id,$id)
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
