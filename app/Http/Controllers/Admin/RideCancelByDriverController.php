<?php

namespace App\Http\Controllers\Admin;

use App\Company;
use App\Driver;
use App\DriverCancelRideHistory;
use App\RideIgnoredBy;
use App\Utility\Utility;
use App\PassengerCancelRideHistory;
use App\RideBookingSchedule;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;


class RideCancelByDriverController extends Controller
{
    /**
     * Display a listing of the RideCancelByDriver.
     *
     * @param Request $request
     * @return Application|Factory|View
     * @throws \Exception
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $start_date = (!empty($_GET["start_date"])) ? ($_GET["start_date"]) : ('');
            $end_date = (!empty($_GET["end_date"])) ? ($_GET["end_date"]) : ('');
            $currentDate = date('Y-m-d');

            if ($start_date && $end_date) {
                $start_date = date('Y-m-d', strtotime($start_date));
                $end_date = date('Y-m-d', strtotime($end_date));
                $cancelRideHistory = DriverCancelRideHistory::query()->whereRaw("date(driver_cancel_ride_histories.dcrh_created_at) >= '" . $start_date . "' AND date(driver_cancel_ride_histories.dcrh_created_at) <= '" . $end_date . "'")->with('driver', 'passenger', 'reasonReference')->get();
            } else {
                $cancelRideHistory = DriverCancelRideHistory::whereDate("driver_cancel_ride_histories.dcrh_created_at", $currentDate)->with('driver', 'passenger', 'reasonReference')->get();
            }

            return Datatables::of($cancelRideHistory)
                ->addColumn('passenger_name', function ($cancelRideHistory) {
                    if (!empty($cancelRideHistory->passenger->name)) {
                        return $cancelRideHistory->passenger->name;
                    }
                })
                ->addColumn('driver_name', function ($cancelRideHistory) {
                    if (!empty($cancelRideHistory->driver->du_full_name)) {
                        return $cancelRideHistory->driver->du_full_name;
                    }
                })
                ->addColumn('reason', function ($cancelRideHistory) {
                    if (!empty($cancelRideHistory->reasonReference->name)) {
                        return $cancelRideHistory->reasonReference->name;
                    }
                })
                ->addColumn('dcrh_comments', function ($cancelRideHistory) {
                    if (!empty($cancelRideHistory->dcrh_comments)) {
                        return $cancelRideHistory->dcrh_comments;
                    }
                })
                ->addColumn('cancel_at', function ($cancelRideHistory) {
                    
                return Utility:: convertTimeToUSERzone($cancelRideHistory->dcrh_created_at,Utility::getUserTimeZone(auth()->guard('admin')->user()->time_zone_id));
                
                })

                ->addColumn('totalRideIgnored', function ($cancelRideHistory) {
                    $totalRideIgnored = $this->getTotalIgnoredCount($cancelRideHistory->dcrh_passenger_id);
                    return $totalRideIgnored;
                })
                ->addColumn('action', function ($cancelRideHistory) {
                    $rideDetail = '<a type="button" data-driverid="' . $cancelRideHistory->dcrh_passenger_id . '" data-rideid="' . $cancelRideHistory->dcrh_job_id . '" class=" ride-details btn btn-sm btn-outline-info waves-effect waves-light"  data-placement="top" title="Ride Detail" data-target="#modaldemo3" data-toggle="modal"><i class="fas fa-eye font-size-16 align-middle"></i></a>';
                    return $rideDetail;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('admin.rideCancelByDriver.index');
    }

    /**
     * Method for RideCancelByDriver
     * @param $driver_id
     * @param $ride_id
     * @return Factory|View
     */
    public function getRideCancelByDriver($driver_id, $ride_id)
    {
        $rideBookSchedule = RideBookingSchedule::where('id', $driver_id)->with('driver', 'passenger')->first();

        return view('admin.rideCancelByDriver.viewRideDetailModal', ['rideBookSchedule' => $rideBookSchedule]);
    }

    /**
     * Method for total Ignored Rides
     * @param $driver_id
     * @return
     */
    public function getTotalIgnoredCount($driver_id)
    {

        return $totalCountRide = RideIgnoredBy::where('rib_driver_id', $driver_id)->count();
    }

    /**
     * Show the form for creating a new RideCancelByDriver.
     *
     * @return Factory|View
     */
    public function create()
    {
        return view('admin.company.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $id = $request->input('edit_value');
        if ($id == NULL) {
            $validator_array = [
                'bls_name_key' => 'required|max:255|unique:base_company,bls_name_key',
            ];

            $validator = Validator::make($request->all(), $validator_array);
            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
            }
            $company = new Company();

            if ($request->hasFile('com_logo')) {
                $mime = $request->com_logo->getMimeType();
                $logo = $request->file('com_logo');
                $logo_name = preg_replace('/\s+/', '', $logo->getClientOriginalName());
                $logoName = time() . '-' . $logo_name;
                $logo->move('./assets/company/logo/', $logoName);
                $comlogo = 'assets/company/logo/' . $logoName;
                $company->com_logo = $comlogo;
            }
            $company->com_name = $request->input('com_name');
            $company->com_contact_number = $request->input('com_contact_number');
            $company->com_full_contact_number = $request->input('com_full_contact_number');
            $company->com_license_no = $request->input('com_license_no');
            $company->com_service_type = $request->input('com_service_type');
            $company->com_time_zone = $request->input('com_time_zone');
            $company->com_radius = $request->input('com_radius');
            $company->com_lat = $request->input('com_lat');
            $company->com_long = $request->input('com_long');
            $company->com_user_name = $request->input('com_user_name');
            $company->email = $request->input('email');
            $company->password = Hash::make($request->input('password'));

            $company->save();
            return response()->json(['success' => true, 'message' => trans('adminMessages.company_inserted')]);
        } else {
            $company = Company::find($id);
            if ($request->hasFile('com_logo')) {
                $mime = $request->com_logo->getMimeType();
                $logo = $request->file('com_logo');
                $logo_name = preg_replace('/\s+/', '', $logo->getClientOriginalName());
                $logoName = time() . '-' . $logo_name;
                $logo->move('./assets/company/logo/', $logoName);
                $comlogo = 'assets/company/logo/' . $logoName;
                $company->com_logo = $comlogo;
            }

            $company->com_name = $request->input('com_name');
            $company->com_contact_number = $request->input('com_contact_number');
            $company->com_full_contact_number = $request->input('com_full_contact_number');
            $company->com_license_no = $request->input('com_license_no');
            $company->com_service_type = $request->input('com_service_type');
            $company->com_time_zone = $request->input('com_time_zone');
            $company->com_radius = $request->input('com_radius');
            $company->com_lat = $request->input('com_lat');
            $company->com_long = $request->input('com_long');
            $company->com_user_name = $request->input('com_user_name');
            $company->email = $request->input('email');

            if (!empty($request->password)) {
                $company->password = Hash::make($request->input('password'));
            }
            $company->save();
            return response()->json(['success' => true, 'message' => trans('adminMessages.company_updated')]);
        }
    }


    /**
     * Display the specified Company and Driver.
     *
     * @param int $id
     * @return Factory|View
     */
    public function show($id)
    {
        $company = Company::where('id', $id)->first();
        $drivers = Driver::where('du_com_id', $id)->get();
        return view('admin.company.show', ['company' => $company, 'drivers' => $drivers]);
    }

    /**
     * Show the form for editing the specified RideCancelByDriver.
     *
     * @param int $id
     * @return Application|Factory|View
     */
    public function edit($id)
    {
        $company = Company::find($id);
        if ($company) {
            return view('admin.company.edit', ['company' => $company]);
        } else {
            abort(404);
        }
    }

    /**
     * Update the specified RideCancelByDriver in storage.
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
     * Remove the specified RideCancelByDriver from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        return response()->json(['success' => true, 'message' => trans('adminMessages.country_deleted')]);
    }

    /**
     * Change status for Company
     * @param $id
     * @param $status
     * @return JsonResponse
     */
    public function status($id, $status)
    {
        $company = Company::where('id', $id)->update(['com_status' => $status]);
        return response()->json(['success' => true, 'message' => 'Company status is successfully Updated']);
    }

    /**
     * Change status for Driver
     * @param $id
     * @param $status
     * @return JsonResponse
     */
    public function updateDriverStatus($id, $status)
    {
        $company = Driver::where('id', $id)->update(['du_driver_status' => $status]);
        return response()->json(['success' => true, 'message' => 'Driver status is successfully Updated']);
    }

    /**
     * Show status for Company
     * @param $id
     * @return JsonResponse
     */
    public function getCompanyStatus($id)
    {
        $array['globalModalTitle'] = 'Change Status';
        $array['globalModalDetails'] = '<form method="POST" data-parsley-validate="" id="changeStatusForm" role="form">';
        $array['globalModalDetails'] .= '<select class="form-control" id="com_status" name="com_status">';
        $array['globalModalDetails'] .= '<option value="0">Inactive</option>';
        $array['globalModalDetails'] .= '<option value="1">Temporary Inactive</option>';
        $array['globalModalDetails'] .= '<option value="2">Temporary Block</option>';
        $array['globalModalDetails'] .= '<option value="3">Permanenet Block</option>';
        $array['globalModalDetails'] .= '<option value="4">Pending for Approval</option>';
        $array['globalModalDetails'] .= '<option value="5">Verified Approved</option>';
        $array['globalModalDetails'] .= '</select>';
        $array['globalModalDetails'] .= '<button type="submit" class="btn btn-primary">Submit</button>';
        $array['globalModalDetails'] .= '</form>';

        return response()->json(['success' => true, 'data' => $array]);
    }

    /**
     * Show Driver graph data
     * @param $type
     * @return JsonResponse
     */
    public function getGraphRecordDriver($type)
    {
        if ($type == 'monthly') {
            $users = DriverCancelRideHistory::select('dcrh_driver_id', 'dcrh_created_at')
                ->get()
                ->groupBy(function ($date) {
                    return Carbon::parse($date->dcrh_created_at)->format('m'); // grouping by months
                });

            $usermcount = [];
            $userArr = [];

            foreach ($users as $key => $value) {
                $usermcount[(int)$key] = count($value);
            }

            for ($i = 1; $i <= 12; $i++) {
                if (!empty($usermcount[$i])) {
                    $userArr[] = $usermcount[$i] * 1;
                } else {
                    $userArr[] = 0.0;
                }
            }
        }
        if ($type == 'weekly') {
            $users = DriverCancelRideHistory::select('dcrh_driver_id', 'dcrh_created_at')
                ->get()
                ->groupBy(DB::raw('WEEK(dcrh_created_at)'));

            $usermcount = [];
            $userArr = [];

            foreach ($users as $key => $value) {
                $usermcount[(int)$key] = count($value);
            }
            for ($i = 1; $i <= 7; $i++) {
                if (!empty($usermcount[$i])) {
                    $userArr[] = $usermcount[$i] * 1;
                } else {
                    $userArr[] = 0.0;
                }
            }
        }
        return response()->json(['success' => true, 'record' => $userArr]);
    }
}
