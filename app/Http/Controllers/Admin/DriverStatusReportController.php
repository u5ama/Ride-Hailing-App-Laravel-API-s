<?php

namespace App\Http\Controllers\Admin;

use App\Company;
use App\Country;
use App\Driver;
use App\TimeZone;
use App\TransportType;
use App\DriverCurrentLocation;
use App\DriverCurrentLogs;
use App\RideBookingSchedule;
use App\Utility\Utility;
use Carbon\Carbon;
use DateTimeZone;
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

class DriverStatusReportController extends Controller
{
    /**
     * Display a listing of the DriverStatus.
     *
     * @return Application|Factory|View
     */
    protected $data = [];

    public function index(Request $request)
    {
             $busyDrivers_ides = RideBookingSchedule::leftJoin('driver_current_locations', 'ride_booking_schedules.rbs_driver_id', '=', 'driver_current_locations.dcl_user_id')->whereIn('ride_booking_schedules.rbs_ride_status', ['Requested', 'Waiting', 'Accepted', 'Driving'])->pluck('driver_current_locations.dcl_user_id')->toArray();

               $onlineDrivers = DriverCurrentLocation::where(['dcl_app_active' => 1])->whereNotIn('dcl_user_id', $busyDrivers_ides)->count();
               $offlineDrivers = DriverCurrentLocation::where(['dcl_app_active' => 0])->whereNotIn('dcl_user_id', $busyDrivers_ides)->count();
               $busyDriversCount = DriverCurrentLocation::whereIn('dcl_user_id', $busyDrivers_ides)->count();

        if ($request->ajax()) {
            $country_id = (!empty($_GET["filterWithCountry"])) ? ($_GET["filterWithCountry"]) : ('');
            $filterByCompany = (!empty($_GET["filterByCompany"])) ? ($_GET["filterByCompany"]) : ('');

            $number = (!empty($_GET["driverNumber"])) ? ($_GET["driverNumber"]) : ('');
            $vehicle = (!empty($_GET["driverVehicle"])) ? ($_GET["driverVehicle"]) : ('');



            $busyDrivers_ides = RideBookingSchedule::leftJoin('driver_current_locations', 'ride_booking_schedules.rbs_driver_id', '=', 'driver_current_locations.dcl_user_id')->whereIn('ride_booking_schedules.rbs_ride_status', ['Requested', 'Waiting', 'Accepted', 'Driving'])->pluck('driver_current_locations.dcl_user_id')->toArray();

            // company and country id
        if(!empty($country_id) && !empty($filterByCompany)){
                $filterWithCountry = Country::listsTranslations('name')->where('countries.id', $country_id)->first();


            $busyDrivers_ides = RideBookingSchedule::leftJoin('driver_current_locations', 'ride_booking_schedules.rbs_driver_id', '=', 'driver_current_locations.dcl_user_id')->whereIn('ride_booking_schedules.rbs_ride_status', ['Requested', 'Waiting', 'Accepted', 'Driving'])->pluck('driver_current_locations.dcl_user_id')->toArray();

            if (!empty($filterByCompany)){
                $ondrivers = Driver::Join('driver_current_locations', 'drivers.id', '=', 'driver_current_locations.dcl_user_id')->leftJoin('driver_profiles', 'drivers.id', '=', 'driver_profiles.dp_user_id')->where(['drivers.du_com_id' => $filterByCompany,'driver_current_locations.dcl_app_active'=>1,'dcl_country' => $filterWithCountry['name']]);
                $ofdrivers = Driver::Join('driver_current_locations', 'drivers.id', '=', 'driver_current_locations.dcl_user_id')->leftJoin('driver_profiles', 'drivers.id', '=', 'driver_profiles.dp_user_id')->where(['drivers.du_com_id' => $filterByCompany,'driver_current_locations.dcl_app_active'=>0,'dcl_country' => $filterWithCountry['name']]);

            }else{
                $ondrivers = Driver::Join('driver_current_locations', 'drivers.id', '=', 'driver_current_locations.dcl_user_id')->leftJoin('driver_profiles', 'drivers.id', '=', 'driver_profiles.dp_user_id')->where(['driver_current_locations.dcl_app_active'=>1,'dcl_country' => $filterWithCountry['name']]);
                $ofdrivers = Driver::Join('driver_current_locations', 'drivers.id', '=', 'driver_current_locations.dcl_user_id')->leftJoin('driver_profiles', 'drivers.id', '=', 'driver_profiles.dp_user_id')->where(['driver_current_locations.dcl_app_active'=>0,'dcl_country' => $filterWithCountry['name']]);
            }


            if (isset($number) && !empty($number)&& count($number) > 0) {

                $ondrivers = $ondrivers->whereIn('drivers.du_mobile_number', $number);
                $ofdrivers = $ofdrivers->whereIn('drivers.du_mobile_number', $number);
            }
            if (isset($vehicle) && !empty($vehicle) && count($vehicle) > 0) {

                $ondrivers = $ondrivers->whereIn('driver_profiles.car_registration', $vehicle);
                $ofdrivers = $ofdrivers->whereIn('driver_profiles.car_registration', $vehicle);
            }

            $ondrivers = $ondrivers->whereNotIn('driver_current_locations.dcl_user_id', $busyDrivers_ides)->select('driver_current_locations.*')->orderBy('id','desc')->get();
            $ofdrivers = $ofdrivers->whereNotIn('driver_current_locations.dcl_user_id', $busyDrivers_ides)->select('driver_current_locations.*')->orderBy('id','desc')->get();
            $drivers  = $ondrivers->merge($ofdrivers);
            foreach ($drivers as $val) {
                $val['isBusy'] = false;
            }

            if(!empty($filterByCompany)){
                $busyDrivers = Driver::leftJoin('driver_current_locations', 'drivers.id', '=', 'driver_current_locations.dcl_user_id')->leftJoin('driver_profiles', 'drivers.id', '=', 'driver_profiles.dp_user_id');
            }
            if (isset($number) && !empty($number)&& count($number) > 0) {

                $busyDrivers = $busyDrivers->whereIn('drivers.du_mobile_number', $number);
            }
            if (isset($vehicle) && !empty($vehicle) && count($vehicle) > 0) {

               $busyDrivers =  $busyDrivers->whereIn('driver_profiles.car_registration', $vehicle);
            }
            $busyDrivers = $busyDrivers->whereIn('dcl_user_id', $busyDrivers_ides)->select('driver_current_locations.*')->orderBy('id','desc')->get();
            foreach ($busyDrivers as $items) {
                $items['isBusy'] = true;
            }




            }elseif(!empty($country_id) && empty($filterByCompany)){
                $filterWithCountry = Country::listsTranslations('name')->where('countries.id', $country_id)->first();

                //$drivers = DriverCurrentLocation::where(['dcl_country' => $filterWithCountry['name']])->whereNotIn('dcl_user_id', $busyDrivers_ides)->get();


                $ondrivers = DriverCurrentLocation::where('dcl_app_active',1)->where(['dcl_country' => $filterWithCountry['name']])->whereNotIn('dcl_user_id', $busyDrivers_ides)->orderBy('id','desc')->get();

                 $ofdrivers = DriverCurrentLocation::where('dcl_app_active',0)->where(['dcl_country' => $filterWithCountry['name']])->whereNotIn('dcl_user_id', $busyDrivers_ides)->orderBy('id','desc')->get();

                 $drivers  = $ondrivers->merge($ofdrivers);

            foreach ($drivers as $val) {
                $val['isBusy'] = false;
            }



            $busyDrivers = DriverCurrentLocation::where(['dcl_country' => $filterWithCountry['name']])->whereIn('dcl_user_id', $busyDrivers_ides)->orderBy('id','desc')->get();
            foreach ($busyDrivers as $items) {
                $items['isBusy'] = true;
            }

            }
            else{



                 $ondrivers = DriverCurrentLocation::where('dcl_app_active',1)->whereNotIn('dcl_user_id', $busyDrivers_ides)->orderBy('id','desc')->get();
                 $ofdrivers = DriverCurrentLocation::where('dcl_app_active',0)->whereNotIn('dcl_user_id', $busyDrivers_ides)->orderBy('id','desc')->get();

                 $drivers  = $ondrivers->merge($ofdrivers);

                  foreach ($drivers as $val) {
                        $val['isBusy'] = false;
                    }


                    $busyDrivers = DriverCurrentLocation::whereIn('dcl_user_id', $busyDrivers_ides)->orderBy('id','desc')->get();
                    foreach ($busyDrivers as $items) {
                        $items['isBusy'] = true;
                    }
            }

            $drivers  = $ondrivers->merge($busyDrivers);
            $drivers = $drivers->merge($ofdrivers);


            return Datatables::of($drivers)
             ->addColumn('vehicle_type', function ($drivers) {
                $vehicle_type = '';
                $driverInfo = $this->getDriverDetailById($drivers->dcl_user_id);
                 if(isset($driverInfo->driverProf->dp_transport_type_id_ref)){
                   $vehicle_type = $this->getVehicleType($driverInfo->driverProf->dp_transport_type_id_ref);
                }

                return $vehicle_type;

                })
            ->addColumn('vehicle_no', function ($drivers) {
                $car_registration = '';
                $driverInfo = $this->getDriverDetailById($drivers->dcl_user_id);
                if(isset($driverInfo->driverProf->car_registration)){
                   $car_registration =  $driverInfo->driverProf->car_registration;
                }
                return $car_registration;

                })
               ->addColumn('license_no', function ($drivers) {
                $license_no = '';
                $driverInfo = $this->getDriverDetailById($drivers->dcl_user_id);
                if(isset($driverInfo->driverProf->dp_license_number)){
                   $license_no =  $driverInfo->driverProf->dp_license_number;
                }
                return $license_no;

                })

               ->addColumn('mobile_no', function ($drivers) {
                $mobile_no = '';
                $driverInfo = $this->getDriverDetailById($drivers->dcl_user_id);
                if(isset($driverInfo->du_full_mobile_number)){
                   $mobile_no =  $driverInfo->du_full_mobile_number;
                }
                return $mobile_no;

                })
               ->addColumn('captain_name', function ($drivers) {
                $du_full_name = '';
                $driverInfo = $this->getDriverDetailById($drivers->dcl_user_id);
                if(isset($driverInfo->du_full_name)){
                   $du_full_name =  $driverInfo->du_full_name;
                }
                return $du_full_name;

                })

               ->addColumn('company_name', function ($drivers) {
                $company_name = '';
                $driverInfo = $this->getDriverDetailById($drivers->dcl_user_id);
                if(isset($driverInfo->company->com_name)){
                   $company_name =  $driverInfo->company->com_name;
                }
                return $company_name;

                })

             ->addColumn('company_name', function ($drivers) {
                $company_name = '';
                $driverInfo = $this->getDriverDetailById($drivers->dcl_user_id);
                if(isset($driverInfo->company->com_name)){
                   $company_name =  $driverInfo->company->com_name;
                }
                return $company_name;

                })

             ->addColumn('driver_status', function ($drivers) {
                if($drivers->isBusy == true){
                  $driver_status = 'busy';
                   $class = "badge badge-warning";
                }else{

                   if($drivers->dcl_app_active == 1){

                    $driver_status = $this->getDriverStatusAndTimeAgo($drivers->dcl_user_id);
                     $class = "badge badge-success";

                  }
                  if($drivers->dcl_app_active == 0){
                     $driver_status = $this->getDriverStatusAndTimeAgo($drivers->dcl_user_id);
                     $class = "badge badge-danger";

                 }

                }

                return $status_button = '<span  class="' . $class . '" data-toggle="tooltip" data-placement="top">' . $driver_status . '</span>';


                })

                ->addColumn('country', function ($drivers) {

                  return  $drivers->dcl_country;

                })

              ->addColumn('city', function ($drivers) {
                 return  $drivers->dcl_city;

                })


                ->addColumn('action', function ($drivers) {
                    $voucherCodes = '<a type="button" data-cardid="' . $drivers->id . '" class="delete-single btn btn-sm btn-outline-info waves-effect waves-light"  data-placement="top" ><i class="fas fa-trash font-size-16 align-middle"></i></a>';
                    return $voucherCodes;
                })
                ->rawColumns(['action','driver_status'])
                ->make(true);
        }

        $countries = Country::listsTranslations('name')
            ->where('status', 'Active')
            ->get();
        $drivers = Driver::all();
        $vehicles = Driver::with('driverProf')->get();
        return view('admin.DriverStatusReport.index', compact('countries','onlineDrivers','offlineDrivers','busyDriversCount','drivers', 'vehicles'));
    }

    public function getDriverDetailById($id){

         $driver = Driver::with('company', 'driverProf')->where('id', $id)->first();
         return $driver;

    }

    public function getVehicleType($id){
         $trans_type = '';
         $transportType = TransportType::where('id', $id)->first();
         if(isset($transportType) && !empty($transportType)){
            $trans_type =  $transportType->name;

         }

         return $trans_type;


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
    public function getDriverStatusFilter(Request $request)
    {
        if ($request->ajax()) {
            $country = $request->country_id;
            $company = $request->company_id;
            $number = $request->driverNumber;
            $vehicle = $request->driverVehicle;

            $id = (!empty($country)) ? ($country) : ('');
            $filterWithCountry = Country::listsTranslations('name')->where('countries.id', $id)->first();
            $filterWithCompany = (!empty($company)) ? ($company) : ('');

            $busyDrivers_ides = RideBookingSchedule::leftJoin('driver_current_locations', 'ride_booking_schedules.rbs_driver_id', '=', 'driver_current_locations.dcl_user_id')->whereIn('ride_booking_schedules.rbs_ride_status', ['Requested', 'Waiting', 'Accepted', 'Driving'])->pluck('driver_current_locations.dcl_user_id')->toArray();

            if(!empty($filterWithCountry) && !empty($filterWithCompany)){

            $filterWithCountry = Country::listsTranslations('name')->where('countries.id', $id)->first();

            $onlineDrivers = DriverCurrentLocation::where(['dcl_country' => $filterWithCountry['name'], 'dcl_app_active' => 1])->whereNotIn('driver_current_locations.dcl_user_id', $busyDrivers_ides)->count();
            $offlineDrivers = DriverCurrentLocation::where(['dcl_country' => $filterWithCountry['name'], 'dcl_app_active' => 0])->whereNotIn('driver_current_locations.dcl_user_id', $busyDrivers_ides)->count();
            $busyDriversCount = DriverCurrentLocation::where(['dcl_country' => $filterWithCountry['name']])->whereIn('driver_current_locations.dcl_user_id', $busyDrivers_ides)->count();
            // $busyDriversCount = RideBookingSchedule::leftJoin('driver_current_locations', 'ride_booking_schedules.rbs_driver_id', '=', 'driver_current_locations.dcl_user_id')->whereIn('ride_booking_schedules.rbs_ride_status', ['Requested', 'Waiting', 'Accepted', 'Driving'])->select('driver_current_locations.*')->where(['driver_current_locations.dcl_app_active' => 1, 'driver_current_locations.dcl_country' => $filterWithCountry['name']])->count();

            $drivers = DriverCurrentLocation::where(['dcl_country' => $filterWithCountry['name']])->whereNotIn('dcl_user_id', $busyDrivers_ides)->get();
            foreach ($drivers as $val) {
                $val['isBusy'] = false;
            }

            $busyDrivers = DriverCurrentLocation::where(['dcl_country' => $filterWithCountry['name']])->whereIn('dcl_user_id', $busyDrivers_ides)->get();
            foreach ($busyDrivers as $items) {
                $items['isBusy'] = true;
            }

            }else{

              // company and country wise

                if (!empty($filterWithCompany)){
                    $onlineDrivers = Driver::Join('driver_current_locations', 'drivers.id', '=', 'driver_current_locations.dcl_user_id')->where(['drivers.du_com_id' => $filterWithCompany]);
                }else{
                    $onlineDrivers = Driver::Join('driver_current_locations', 'drivers.id', '=', 'driver_current_locations.dcl_user_id');
                }

                if (isset($number) && count($number) > 0) {

                    $onlineDrivers->whereIn('drivers.du_mobile_number', $number);
                }
                if (isset($vehicle) && count($vehicle) > 0) {

                    $onlineDrivers = $onlineDrivers->leftJoin('driver_profiles', 'drivers.id', '=', 'driver_profiles.dp_user_id')->whereIn('driver_profiles.car_registration', $vehicle);
                }

                $onlineDrivers = $onlineDrivers->where(['dcl_country' => $filterWithCountry['name'], 'dcl_app_active' => 1])->count();


                if (!empty($filterWithCompany)) {
                    $offlineDrivers = Driver::Join('driver_current_locations', 'drivers.id', '=', 'driver_current_locations.dcl_user_id')->where(['drivers.du_com_id' => $filterWithCompany]);
                }else{
                    $offlineDrivers = Driver::Join('driver_current_locations', 'drivers.id', '=', 'driver_current_locations.dcl_user_id');
                }
                if (isset($number) && count($number) > 0) {

                    $offlineDrivers->whereIn('drivers.du_mobile_number', $number);
                }
                if (isset($vehicle) && count($vehicle) > 0) {

                    $offlineDrivers = $offlineDrivers->leftJoin('driver_profiles', 'drivers.id', '=', 'driver_profiles.dp_user_id')->whereIn('driver_profiles.car_registration', $vehicle);
                }
                $offlineDrivers = $offlineDrivers->select('driver_current_locations.*')->where(['dcl_country' => $filterWithCountry['name'], 'dcl_app_active' => 0])->count();

                if (!empty($filterWithCompany)) {
                    $busyDriversCount = RideBookingSchedule::leftJoin('driver_current_locations', 'ride_booking_schedules.rbs_driver_id', '=', 'driver_current_locations.dcl_user_id')->leftJoin('drivers', 'ride_booking_schedules.rbs_driver_id', '=', 'drivers.id')->where('drivers.du_com_id', $filterWithCompany)->whereIn('ride_booking_schedules.rbs_ride_status', ['Requested', 'Waiting', 'Accepted', 'Driving'])->select('driver_current_locations.*')->where('dcl_country', $filterWithCountry['name'])->count();
                }
                else{
                    $busyDriversCount = RideBookingSchedule::leftJoin('driver_current_locations', 'ride_booking_schedules.rbs_driver_id', '=', 'driver_current_locations.dcl_user_id')->leftJoin('drivers', 'ride_booking_schedules.rbs_driver_id', '=', 'drivers.id')->whereIn('ride_booking_schedules.rbs_ride_status', ['Requested', 'Waiting', 'Accepted', 'Driving'])->select('driver_current_locations.*')->where('dcl_country', $filterWithCountry['name'])->count();
                }

                $busyDrivers_ides = RideBookingSchedule::leftJoin('driver_current_locations', 'ride_booking_schedules.rbs_driver_id', '=', 'driver_current_locations.dcl_user_id')->whereIn('ride_booking_schedules.rbs_ride_status', ['Requested', 'Waiting', 'Accepted', 'Driving'])->pluck('driver_current_locations.dcl_user_id')->toArray();

                if (!empty($filterWithCompany)) {
                    $drivers = Driver::leftJoin('driver_current_locations', 'drivers.id', '=', 'driver_current_locations.dcl_user_id')->leftJoin('driver_profiles', 'drivers.id', '=', 'driver_profiles.dp_user_id')->where(['drivers.du_com_id' => $filterWithCompany]);
                }else{
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
    public function getDriverStatusAndTimeAgo($id)
    {
        $driver = Driver::with('company', 'driverProf')->where('id', $id)->first();
        $driverTime = DriverCurrentLogs::where(['dcl_user_id' => $id, 'action' => 'update'])->orderBy('timestamp', 'DESC')->first();

        $currentDateTime = date('Y-m-d H:s:i');
        $timezone = TimeZone::where('id',auth()->guard('admin')->user()->time_zone_id)->first();

        $currentDateTime = Utility:: convertTimeToUSERzone($currentDateTime,$timezone->time_zone);

        $from = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $currentDateTime);

        if (!empty($driverTime)) {

            if ($driverTime->dcl_app_active == 0) {
                $to = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $driverTime->timestamp);
                $format = 'Y-m-d H:s:i';
                if(empty($to)){

                }

//                $timezone = TimeZone::where('id',auth()->guard('admin')->user()->time_zone_id)->first();

//                $new_str = new \DateTime($to, new DateTimeZone($timezone->time_zone));
//                $new_str->setTimeZone(new DateTimeZone( 'UTC' ));
//                $to = $new_str->format($format);

                $to = Utility:: convertTimeToUSERzone($to,$timezone->time_zone);

                $format = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $to);
                $diff_in_minutes = $format->diffInMinutes($from, true);

                $hrs_format = intdiv($diff_in_minutes, 60);

                if($hrs_format !== 0){
                    $diff_in_minutes = $hrs_format.'hr '. ($diff_in_minutes % 60);
                }else{
                    $diff_in_minutes = ($diff_in_minutes % 60);
                }

                //$diff_in_minutes = $hrs_format.'hr '. ($diff_in_minutes % 60);
                $status = 'offline';
            } else {
                $to = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $driverTime->timestamp);
                $format = 'Y-m-d H:s:i';
                if(empty($to)){

                }

//                $timezone = TimeZone::where('id',auth()->guard('admin')->user()->time_zone_id)->first();

//                $new_str = new \DateTime($to, new DateTimeZone($timezone->time_zone));
//                $new_str->setTimeZone(new DateTimeZone( 'UTC' ));
//                $to = $new_str->format($format);

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

        return $status.' '. $diff_in_minutes.' mins ago';





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
