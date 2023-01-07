<?php

namespace App\Http\Controllers\Company;

use App\Company;
use App\Driver;
use App\RideBookingSchedule;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use App\Utility\Utility;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;


class RideBookingScheduleController extends Controller
{
    /**
     * Display a listing of the RideBookingSchedule.
     *
     * @param Request $request
     * @return Application|Factory|View
     * @throws Exception
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $company_id = auth()->guard('company')->user()->id;
            $start_date = (!empty($_GET["start_date"])) ? ($_GET["start_date"]) : ('');
            $end_date = (!empty($_GET["end_date"])) ? ($_GET["end_date"]) : ('');

            if ($start_date && $end_date) {
                $start_date = date('Y-m-d', strtotime($start_date));
                $end_date = date('Y-m-d', strtotime($end_date));
                $rideBookSchedule = RideBookingSchedule::leftJoin('drivers', 'ride_booking_schedules.rbs_driver_id', '=', 'drivers.id')->where(['drivers.du_com_id'=>$company_id])->whereRaw("date(ride_booking_schedules.rbs_created_at) >= '" . $start_date . "' AND date(ride_booking_schedules.rbs_created_at) <= '" . $end_date . "'")->with('driver', 'passenger')->get();
            } else {
                $currentDate = date('Y-m-d');
                $rideBookSchedule = RideBookingSchedule::leftJoin('drivers', 'ride_booking_schedules.rbs_driver_id', '=', 'drivers.id')->where(['drivers.du_com_id'=>$company_id])->whereDate("ride_booking_schedules.rbs_created_at", $currentDate)->with('driver', 'passenger')->get();
            }

            return Datatables::of($rideBookSchedule)
                ->addColumn('driver_name', function ($rideBookSchedule) {

                    return $rideBookSchedule->driver->du_full_name;
                })
                ->addColumn('passenger_name', function ($rideBookSchedule) {

                    return $rideBookSchedule->passenger->name;
                })
                ->addColumn('driving_total_time', function ($rideBookSchedule) {
                    $drivnig_total_time = '';
                    if (!empty($rideBookSchedule->rbs_driving_start_time) && !empty($rideBookSchedule->rbs_driving_end_time)) {
                        $to_time = strtotime($rideBookSchedule->rbs_driving_start_time);
                        $from_time = strtotime($rideBookSchedule->rbs_driving_end_time);
                        $drivnig_total_time = round(abs($to_time - $from_time) / 60) . " minute";
                    }
                    return $drivnig_total_time;
                })
                ->addColumn('rbs_driving_start_time', function ($rideBookSchedule) {
                    return Utility:: convertTimeToUSERzone($rideBookSchedule->rbs_driving_start_time,Utility::getUserTimeZone(auth()->guard('company')->user()->com_time_zone));
                   
                })
                ->addColumn('rbs_driving_end_time', function ($rideBookSchedule) {
                    return Utility:: convertTimeToUSERzone($rideBookSchedule->rbs_driving_end_time,Utility::getUserTimeZone(auth()->guard('company')->user()->com_time_zone));
                   
                })
                ->addColumn('rbs_driving_wait_start_time', function ($rideBookSchedule) {
                    return Utility:: convertTimeToUSERzone($rideBookSchedule->rbs_driving_wait_start_time,Utility::getUserTimeZone(auth()->guard('company')->user()->com_time_zone));
                   
                })
                ->addColumn('rbs_driving_wait_end_time', function ($rideBookSchedule) {
                    return Utility:: convertTimeToUSERzone($rideBookSchedule->rbs_driving_wait_end_time,Utility::getUserTimeZone(auth()->guard('company')->user()->com_time_zone));
                   
                })
                ->addColumn('status', function ($rideBookSchedule) {
                    $class = '';
                    $name = '';
                    if ($rideBookSchedule->rbs_ride_status == 'Requested') {
                        $class = "badge badge-warning";
                        $name = "Inactive";
                    }
                    if ($rideBookSchedule->rbs_ride_status == 'Waiting') {
                        $class = "badge badge-info";
                        $name = "Waiting";
                    }
                    if ($rideBookSchedule->rbs_ride_status == 'Accept') {
                        $class = "badge badge-success";
                        $name = "Accept";
                    }
                    if ($rideBookSchedule->rbs_ride_status == 'Rejected') {
                        $class = "badge badge-warning";
                        $name = "Rejected";
                    }
                    if ($rideBookSchedule->rbs_ride_status == 'Completed') {
                        $class = "badge badge-primary";
                        $name = "Completed";
                    }
                    if ($rideBookSchedule->rbs_ride_status == 'Driving') {
                        $class = "badge badge-info";
                        $name = "Driving";
                    }

                    $status_button = '<a type="button"  class="' . $class . '" data-toggle="tooltip" data-placement="top" >' . $name . '</a>';
                    return $status_button;
                })
                ->addColumn('action', function ($rideBookSchedule) {
                    $view_map_btn = '<a type="button" data-lat="' . $rideBookSchedule->rbs_driver_lat . '" data-lng="' . $rideBookSchedule->rbs_driver_long . '" class="btn btn-sm btn-outline-info waves-effect waves-light"  data-placement="top" title="View Map" data-target="#modaldemo3" data-toggle="modal"><i class="fas fa-eye font-size-16 align-middle"></i></a>';
                    return $view_map_btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }
        return view('company.rideBookingSchedule.index');
    }

    /**
     * Show map view for RideBookingSchedule
     * @return Factory|View
     */
    public function getViewMapModal()
    {
        return view('company.rideBookingSchedule.viewMapModal');
    }

    /**
     * Show the form for creating a new RideBookingSchedule.
     *
     * @return Factory|View
     */
    public function create()
    {
        return view('admin.company.create');
    }

    /**
     * Store a newly created RideBookingSchedule in storage.
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
     * Display the specified RideBookingSchedule.
     *
     * @param int $id
     * @return Factory|View
     */
    public function show($id)
    {
        $company = Company::where('id', $id)->first();
        $drivers = Driver::where('du_com_id', $id)->get();
        return view('company.company.show', ['company' => $company, 'drivers' => $drivers]);
    }

    /**
     * Show the form for editing the specified RideBookingSchedule.
     *
     * @param int $id
     * @return Application|Factory|View
     */
    public function edit($id)
    {
        $company = Company::find($id);
        if ($company) {
            return view('company.company.edit', ['company' => $company]);
        } else {
            abort(404);
        }
    }

    /**
     * Update the specified RideBookingSchedule in storage.
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
     * Remove the specified RideBookingSchedule from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        return response()->json(['success' => true, 'message' => trans('adminMessages.country_deleted')]);
    }

    /**
     * Change the Status for Company
     * @param $id
     * @param $status
     * @return JsonResponse
     */
    public function status($id, $status)
    {
        Company::where('id', $id)->update(['com_status' => $status]);
        return response()->json(['success' => true, 'message' => 'Company status is successfully Updated']);
    }

    /**
     * Change the Status for Driver
     * @param $id
     * @param $status
     * @return JsonResponse
     */
    public function updateDriverStatus($id, $status)
    {
        Driver::where('id', $id)->update(['du_driver_status' => $status]);
        return response()->json(['success' => true, 'message' => 'Driver status is successfully Updated']);
    }

    /**
     * Show the Status for Company
     * @param $id
     * @param $status
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
}
