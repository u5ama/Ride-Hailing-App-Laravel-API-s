<?php

namespace App\Http\Controllers\Admin;

use App\BaseMedia;
use App\Company;
use App\CustomerInvoice;
use App\Driver;
use App\DriverAccount;
use App\DriverCurrentLocation;
use App\DriverCurrentLogs;
use App\DriverProfile;
use App\PassengerAccount;
use App\PassengerCancelRideHistory;
use App\PassengerCurrentLocation;
use App\PassengerPaymentDetail;
use App\RideBookingSchedule;
use App\TransportFuel;
use App\TransportMake;
use App\TransportModel;
use App\TransportModelColor;
use App\TransportModelYear;
use App\TransportType;
use App\User;
use App\Utility\Utility;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class DriverListController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Application|Factory|View
     * @throws Exception
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $driver = Driver::with('DriverRating','DriverProfile')->get();
            return Datatables::of($driver)
                ->addColumn('logo', function ($driver) {
                    $url = asset($driver->du_profile_pic);
                    if (!empty($driver->du_profile_pic)) {
                        return '<img   src="' . $url . '" style="height:60px;">';
                    }
                })
                ->addColumn('d_vehicle_type', function ($driver) {
                    if (!empty($driver->DriverProfile) && $driver->DriverProfile != null) {
                        if (isset($driver->driverProf->dp_transport_type_id_ref) && $driver->driverProf->dp_transport_type_id_ref != null) {
                            $type_id = $driver->DriverProfile->dp_transport_type_id_ref;
                            $t_type = TransportType::listsTranslations('name')->where('transport_types.id', $type_id)->first();
                            return $t_type['name'];
                        }else{
                            return $t_type['name'] = null;
                        }
                    }
                })
                ->addColumn('d_vehicle', function ($driver) {
                    if (!empty($driver->DriverProfile) && $driver->DriverProfile != null) {
                        return $driver->DriverProfile->car_registration;
                    }
                })
                ->addColumn('d_license', function ($driver) {
                    if (!empty($driver->DriverProfile) && $driver->DriverProfile != null) {
                        return $driver->DriverProfile->dp_license_number;
                    }
                })
                ->addColumn('on_boarding', function ($driver) {
                    if (!empty($driver->du_created_at)) {
                        return $driver->du_created_at;
                    }
                })
                ->addColumn('d_name', function ($driver) {
                    if (!empty($driver->du_full_name)) {
                        return $driver->du_full_name;
                    }
                })
                ->addColumn('d_mobile', function ($driver) {
                    if (!empty($driver->du_full_mobile_number)) {
                        return $driver->du_full_mobile_number;
                    }
                })
                ->addColumn('d_email', function ($driver) {
                    if (!empty($driver->email)) {
                        return $driver->email;
                    }
                })

                ->addColumn('d_success_rides', function ($driver) {
                    if (!empty($driver->id)) {
                        $ridesCount = RideBookingSchedule::where(['rbs_driver_id'=>$driver->id, 'rbs_ride_status' => 'Completed'])->count();
                        $ridesTotal = RideBookingSchedule::leftJoin('customer_invoices', 'ride_booking_schedules.id', '=', 'customer_invoices.ci_ride_id')->where(['ride_booking_schedules.rbs_driver_id'=>$driver->id, 'ride_booking_schedules.rbs_ride_status' => 'Completed'])->get()->sum('ci_customer_invoice_amount');
                        $ridesTotal = number_format($ridesTotal, 3, ".", ",");
                        return $ridesCount .' '.$ridesTotal ;
                    }
                })

                ->addColumn('d_cancel_rides', function ($driver) {
                    if (!empty($driver->id)) {
                        $ridesCount = RideBookingSchedule::where(['rbs_driver_id'=>$driver->id, 'rbs_ride_status' => 'Cancelled'])->count();
                        $ridesTotal = RideBookingSchedule::leftJoin('customer_invoices', 'ride_booking_schedules.id', '=', 'customer_invoices.ci_ride_id')->where(['ride_booking_schedules.rbs_driver_id'=>$driver->id, 'ride_booking_schedules.rbs_ride_status' => 'Cancelled'])->get()->sum('ci_customer_invoice_amount');
                        $ridesTotal = number_format($ridesTotal, 3, ".", ",");
                        return $ridesCount .' '.$ridesTotal ;
                    }
                })

                ->addColumn('d_wallet', function ($driver) {
                    if (!empty($driver->id)) {
                        $wallet = DriverAccount::where(['dc_target_id'=>$driver->id, 'dc_operation_type' => 'ride'])->get()->last();
                        if (isset($wallet)){
                            $walletAmount = $wallet->dc_balance;
                        }else{
                            $walletAmount = 0;
                        }

                        $walletAmount = number_format($walletAmount, 3, ".", ",");
                        return $walletAmount;
                    }
                })

                ->addColumn('d_rating', function ($driver) {
                    if (!empty($driver->id)) {
                        $crRating = $driver->DriverRating->sum('dr_rating');
                        $total = $driver->DriverRating->count();

                        $rating = (isset($crRating) && $crRating != null) ? number_format((float)$crRating/$total , 2, '.', '') : '0.00';

                        return $rating;
                    }
                })

                ->addColumn('d_last_ride', function ($driver) {
                    if (!empty($driver->id)) {
                        $currentDateTime = date('Y-m-d H:s:i');
                        $from = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $currentDateTime);

                        $rides = RideBookingSchedule::where(['rbs_driver_id'=>$driver->id])->get()->last();

                        if (isset($rides)){
                            $last = $rides->rbs_created_at;

//                        $to = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $last);
                            $to = Utility:: convertTimeToUSERzone($last,Utility::getUserTimeZone(auth()->guard('admin')->user()->time_zone_id));

                            $format = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $to);
                            $diff_in_minutes = $format->diffInMinutes($from);

                            $hrs_format = intdiv($diff_in_minutes, 60);

                            if($hrs_format !== 0){
                                $diff_in_minutes = $hrs_format.'hr '. ($diff_in_minutes % 60).'mins ago';
                            }else{
                                $diff_in_minutes = ($diff_in_minutes % 60).'mins ago';
                            }
                        }else{
                            $diff_in_minutes = 'No Ride';
                        }

                        return $diff_in_minutes;
                    }
                })

                ->addColumn('d_last_online', function ($driver) {
                    if (!empty($driver->id)) {
                        $driverTime = DriverCurrentLogs::where(['dcl_user_id' => $driver->id, 'action' => 'update'])->orderBy('timestamp', 'DESC')->first();

                        $currentDateTime = date('Y-m-d H:s:i');
                        $from = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $currentDateTime);

                        if (!empty($driverTime)) {
                            if ($driverTime->dcl_app_active == 0) {
                                $to = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $driverTime->timestamp);
                                $to = Utility:: convertTimeToUSERzone($to, Utility::getUserTimeZone(auth()->guard('admin')->user()->time_zone_id));
                                $format = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $to);
                                $diff_in_minutes = $format->diffInMinutes($from);
                                $hrs_format = intdiv($diff_in_minutes, 60);
                                if ($hrs_format !== 0) {
                                    $diff_in_minutes = 'offline'.$hrs_format . 'hr ' . ($diff_in_minutes % 60).'mins ago';
                                } else {
                                    $diff_in_minutes = 'offline'.($diff_in_minutes % 60).'mins ago';
                                }
                            } else {
                                $to = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $driverTime->timestamp);
                                $to = Utility:: convertTimeToUSERzone($to, Utility::getUserTimeZone(auth()->guard('admin')->user()->time_zone_id));
                                $format = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $to);
                                $diff_in_minutes = $format->diffInMinutes($from);
                                $hrs_format = intdiv($diff_in_minutes, 60);

                                if ($hrs_format !== 0) {
                                    $diff_in_minutes = 'online'.$hrs_format . 'hr ' . ($diff_in_minutes % 60).'mins ago';
                                } else {
                                    $diff_in_minutes = 'online'.($diff_in_minutes % 60).'mins ago';
                                }
                            }
                        } else {
                            $diff_in_minutes = '0';
                        }
                        return $diff_in_minutes;
                    }
                })

                ->addColumn('d_last_location', function ($driver) {
                    if (!empty($driver->id)) {
                        $location = DriverCurrentLocation::where('dcl_user_id', $driver->id)->first();
                        if (isset($location)){
                            $city = $location->dcl_country;
                        }else{
                            $city = 'none';
                        }
                        return $city;
                    }
                })

                ->addColumn('d_company', function ($driver) {
                    if (!empty($driver->du_com_id)) {
                        $company = Company::where('id',$driver->du_com_id)->first();
                        $com_name = $company->com_name;
                        return $com_name;
                    }
                })

                ->addColumn('action', function ($driver) {
                    $view_driver_btn = '<a type="button" data-rideid="' . $driver->id . '" class="driver-details btn btn-sm btn-outline-info waves-effect waves-light"  data-placement="top" title="Passenger Detail" data-target="#modaldemo3" data-toggle="modal"><i class="fas fa-eye font-size-16 align-middle"></i></a>';
                    return $view_driver_btn;
                })
                ->rawColumns(['action', 'logo','d_company','d_last_location','d_last_online','d_last_ride','d_rating','d_wallet','d_cancel_rides','d_success_rides','d_email','d_mobile','d_name','on_boarding'])
                ->make(true);
        }
        return view('admin.DriverList.index');
    }

    /**
     * Show the form for creating a new Passenger.
     *
     * @return Factory|View
     */
    public function create()
    {
        return view('admin.DriverList.create');
    }

    /**
     * Store a newly created Passenger in storage.
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request)
    {
        //
    }


    /**
     * Display the specified Passenger.
     *
     * @param int $id
     * @return Factory|View
     */
    public function showPassenger($id)
    {
        $dirver_list = Driver::where('id',$id)->first();

        $driverProfile = DriverProfile::where('dp_user_id',$id)->first();
        if(isset($driverProfile)){
            $driverLicenseFiles = BaseMedia::where(['bm_user_id'=>$id,'bm_activity_category'=>'LicenseScreen','bm_mediable_type'=>'App/Driver'])->get();
            $driverPersonalIdFiles = BaseMedia::where(['bm_user_id'=>$id,'bm_activity_category'=>'IdScreen','bm_mediable_type'=>'App/Driver'])->get();
            $carRegistrationFiles = BaseMedia::where(['bm_user_id'=>$id,'bm_activity_category'=>'CarScreen','bm_mediable_type'=>'App/Driver'])->get();

            $carMultiImages = BaseMedia::where(['bm_user_id'=>$id,'bm_activity_category'=>'CarMultiImages','bm_mediable_type'=>'App/Driver'])->get();

            $transportTypes = TransportType::listsTranslations('name')->select('transport_types.id', 'transport_type_translations.name')->where('transport_types.id',$driverProfile->dp_transport_type_id_ref)->first();
            $transportMakes = TransportMake::listsTranslations('name')->select('transport_makes.id', 'transport_make_translations.name')->where('transport_makes.id',$driverProfile->dp_transport_make_id)->first();

            $transportModels = TransportModel::where(['tmo_tm_id_ref'=>$driverProfile->dp_transport_make_id,'tmo_tt_ref_id'=>$driverProfile->dp_transport_type_id_ref])->first();

            $transportFuels = TransportFuel::where(['id'=>$driverProfile->dp_fuel_id_ref])->first();

            $transportModelColors = TransportModelColor::where(['tmc_tm_ref_id'=>$driverProfile->dp_transport_make_id,'tmc_tt_ref_id'=>$driverProfile->dp_transport_type_id_ref,'tmc_tmo_id_ref'=>$driverProfile->dp_transport_model_id])->first();

            $transportModelYears = TransportModelYear::where(['tmy_tt_ref_id'=>$driverProfile->dp_transport_type_id_ref,'tmy_tm_ref_id'=>$driverProfile->dp_transport_make_id,'tmy_tmo_ref_id'=>$driverProfile->dp_transport_model_id,'tmc_tmo_id_ref'=>$driverProfile->dp_transport_color_id])->first();
        }


        $array['globalModalTitle'] = 'Driver : ' . $dirver_list->du_full_name . ' | ' . $dirver_list->du_country_code . ' | ' . $dirver_list->du_mobile_number.' | '.$dirver_list->du_full_mobile_number.'| Manual Otp :'.$dirver_list->du_otp_manual ;

        $array['globalModalDetails'] = '<table class="table table-bordered">';
        $array['globalModalDetails'] .= '<thead class="thead-light"><tr><th colspan="6" class="text-center"> Driver Profile</th></tr></thead>';
        if(isset($driverProfile->dp_license_number)){
            $array['globalModalDetails'] .= '<table class="table table-bordered">';
            $array['globalModalDetails'] .= '<thead class="thead-light"><tr><th colspan="6" class="text-center"> Driver License Number : '.$driverProfile->dp_license_number.' </th></tr></thead>';

            $array['globalModalDetails'] .= '<thead class="thead-dark"><tr><th>Front Image</th><th>Back Image</th></tr></thead>';
            $i = 1;
            $array['globalModalDetails'] .= '<tr>';
            foreach($driverLicenseFiles as $drlicense){
                $url = asset($drlicense->bm_file_path);

                if($i == 1){
                    $array['globalModalDetails'] .= '<td> ' . "<img src='" . $url . "' width='200' />" . '</td>';
                }
                if($i == 2){
                    $array['globalModalDetails'] .= '<td> ' . "<img src='" . $url . "' width='200'  />" . '</td>';
                }


            }
            $array['globalModalDetails'] .= '</tr>';
            $array['globalModalDetails'] .= '</table>';
        }


        if(isset($driverProfile->dp_personal_id)){
            $array['globalModalDetails'] .= '<table class="table table-bordered">';
            $array['globalModalDetails'] .= '<thead class="thead-light"><tr><th colspan="6" class="text-center"> Driver Personal ID Card Number : '.$driverProfile->dp_personal_id.' </th></tr></thead>';

            $array['globalModalDetails'] .= '<thead class="thead-dark"><tr><th>Front Image</th><th>Back Image</th></tr></thead>';
            $i = 1;
            $array['globalModalDetails'] .= '<tr>';
            foreach($driverPersonalIdFiles as $driverPId){
                $url = asset($driverPId->bm_file_path);

                if($i == 1){
                    $array['globalModalDetails'] .= '<td> ' . "<img src='" . $url . "' width='200' />" . '</td>';
                }
                if($i == 2){
                    $array['globalModalDetails'] .= '<td> ' . "<img src='" . $url . "' width='200'  />" . '</td>';
                }


            }
            $array['globalModalDetails'] .= '</tr>';
            $array['globalModalDetails'] .= '</table>';
        }


        if(isset($transportTypes)){
            $array['globalModalDetails'] .= '<table class="table table-bordered">';
            $array['globalModalDetails'] .= '<thead class="thead-light"><tr><th colspan="8" class="text-center"> Car Registration Transport : Registration No.'.$driverProfile->car_registration.'</th></tr></thead>';

            $array['globalModalDetails'] .= '<thead class="thead-dark"><tr><th>Type</th><th>Make</th><th>Model</th><th>Model Color</th><th>Model Year</th><th>Fuel Type</th><th>Manufacture Date</th><th>Registration Date</th></tr></thead>';
            $i = 1;
            $array['globalModalDetails'] .= '<tr>';

            $array['globalModalDetails'] .= '<td> ' . $transportTypes->name . '</td>';

            if(isset($transportMakes)) {
                $array['globalModalDetails'] .= '<td> ' . $transportMakes->name . '</td>';
            }

            if(isset($transportModels)) {
                $array['globalModalDetails'] .= '<td> ' . $transportModels->name . '</td>';
            }

            if(isset($transportModelColors)) {
                $array['globalModalDetails'] .= '<td> ' . $transportModelColors->name . '</td>';
            }

            if(isset($transportModelYears)) {
                $array['globalModalDetails'] .= '<td> ' . $transportModelYears->tmy_name . '</td>';
            }

            if(isset($transportFuels->name) && !empty($transportFuels->name)){
                $array['globalModalDetails'] .= '<td> ' .  $transportFuels->name . '</td>';
            }else{
                $array['globalModalDetails'] .= '<td></td>';
            }

            $array['globalModalDetails'] .= '<td> ' . date('Y-m-d',strtotime($driverProfile->dp_date_manufacture)) . '</td>';
            $array['globalModalDetails'] .= '<td> ' . date('Y-m-d',strtotime($driverProfile->dp_date_registration)) . '</td>';

            $array['globalModalDetails'] .= '</tr>';



            $array['globalModalDetails'] .= '</table>';
        }

        if(isset($carRegistrationFiles)){
            $array['globalModalDetails'] .= '<table class="table table-bordered">';
            $array['globalModalDetails'] .= '<thead class="thead-light"><tr><th colspan="8" class="text-center"> Car Registration : '.' </th></tr></thead>';

            $array['globalModalDetails'] .= '<thead class="thead-dark"><tr><th>Main Image</th><th>Front Image</th><th>Back Image</th></tr></thead>';
            $i = 1;
            $array['globalModalDetails'] .= '<tr>';
            foreach($carRegistrationFiles as $carReg){
                $url = asset($carReg->bm_file_path);

                if($carReg->bm_section_order == 0){
                    $array['globalModalDetails'] .= '<td> ' . "<img src='" . $url . "' width='200' />" . '</td>';
                }
                if($carReg->bm_section_order == 1){
                    $array['globalModalDetails'] .= '<td> ' . "<img src='" . $url . "' width='200' />" . '</td>';
                }
                if($carReg->bm_section_order == 2){
                    $array['globalModalDetails'] .= '<td> ' . "<img src='" . $url . "' width='200'  />" . '</td>';
                }


            }
            $array['globalModalDetails'] .= '</tr>';
            $array['globalModalDetails'] .= '</table>';
        }

        if(isset($carMultiImages)){
            $array['globalModalDetails'] .= '<table class="table table-bordered">';
            $array['globalModalDetails'] .= '<thead class="thead-light"><tr><th colspan="8" class="text-center"> Car Multiple Images : '.' </th></tr></thead>';


            //for($i=1;$i<=count($carMultiImages)/3;$i++){
            $array['globalModalDetails'] .= '<tr>';

            foreach($carMultiImages as $key => $carReg){

                $url = asset($carReg->bm_file_path);

                $array['globalModalDetails'] .= '<td> ' . "<img src='" . $url . "' width='200' />" . '</td>';


            }

            $array['globalModalDetails'] .= '</tr>';
            //}

            $array['globalModalDetails'] .= '</table>';
        }






        $array['globalModalDetails'] .= '</table>';


        return response()->json(['success' => true, 'data' => $array]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return void
     */
    public function edit($id)
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
     * Remove the specified Passenger from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        return response()->json(['success' => true, 'message' => trans('adminMessages.country_deleted')]);
    }

}
