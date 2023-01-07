<?php

namespace App\Http\Controllers\Admin;

use App\Company;
use App\Driver;
use App\User;
use App\RideBookingSchedule;
use Exception;
use App\Utility\Utility;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;


class RideStatisticsDriverController extends Controller
{
    /**
     * Display a listing of the RideStatisticsDriver.
     *
     * @param Request $request
     * @return Application|Factory|View
     * @throws Exception
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $rideBookSchedule = RideBookingSchedule::with('driver', 'passenger')->groupBy('rbs_driver_id')->get();
            return Datatables::of($rideBookSchedule)
                ->addColumn('driver_name', function ($rideBookSchedule) {
                    $driver_name = '';
                    if(isset($rideBookSchedule->driver->du_full_name)){
                        $driver_name = $rideBookSchedule->driver->du_full_name;
                    }

                    return $driver_name;
                    
                })
                ->addColumn('total_requested', function ($rideBookSchedule) {
                    $total = $this->countRideStatistics($rideBookSchedule->rbs_driver_id, 'Requested');
                    $rideDetail = '<a href="avascript:void(0)" data-id="' . $rideBookSchedule->rbs_driver_id . '" data-totalcount="' . $total . '" data-status="Requested" class="view-ride-detail"  data-placement="top" title="Requested Ride" data-target="#modaldemo4" data-toggle="modal" style="text-decoration: underline;"> ' . $total . ' </a>';
                    return $rideDetail;
                })
                ->addColumn('total_waiting', function ($rideBookSchedule) {
                    $total = $this->countRideStatistics($rideBookSchedule->rbs_driver_id, 'Waiting');
                    $rideDetail = '<a href="avascript:void(0)"  data-id="' . $rideBookSchedule->rbs_driver_id . '" data-totalcount="' . $total . '" data-status="Waiting" class="view-ride-detail"  data-placement="top" title="Waiting Ride" data-target="#modaldemo4" data-toggle="modal" style="text-decoration: underline;"> ' . $total . ' </a>';
                    return $rideDetail;
                })
                ->addColumn('total_accepted', function ($rideBookSchedule) {
                    $total = $this->countRideStatistics($rideBookSchedule->rbs_driver_id, 'Accepted');
                    $rideDetail = '<a href="avascript:void(0)"  data-id="' . $rideBookSchedule->rbs_driver_id . '" data-totalcount="' . $total . '" data-status="Accepted" class="view-ride-detail"  data-placement="top" title="Accepted Ride" data-target="#modaldemo4" data-toggle="modal" style="text-decoration: underline;"> ' . $total . ' </a>';
                    return $rideDetail;
                })
                ->addColumn('total_rejected', function ($rideBookSchedule) {
                    $total = $this->countRideStatistics($rideBookSchedule->rbs_driver_id, 'Rejected');
                    $rideDetail = '<a href="avascript:void(0)"  data-id="' . $rideBookSchedule->rbs_driver_id . '" data-totalcount="' . $total . '" data-status="Rejected" class="view-ride-detail"  data-placement="top" title="Rejected Ride" data-target="#modaldemo4" data-toggle="modal" style="text-decoration: underline;"> ' . $total . ' </a>';
                    return $rideDetail;
                })
                ->addColumn('total_completed', function ($rideBookSchedule) {
                    $total = $this->countRideStatistics($rideBookSchedule->rbs_driver_id, 'Completed');
                    $rideDetail = '<a href="avascript:void(0)"  data-id="' . $rideBookSchedule->rbs_driver_id . '" data-totalcount="' . $total . '" data-status="Completed" class="view-ride-detail"  data-placement="top" title="Completed Ride" data-target="#modaldemo4" data-toggle="modal" style="text-decoration: underline;"> ' . $total . ' </a>';
                    return $rideDetail;
                })
                ->addColumn('action', function ($rideBookSchedule) {
                    $total = $this->countRideStatistics($rideBookSchedule->rbs_driver_id, 'Driving');
                    $rideDetail = '<a href="avascript:void(0)"  data-id="' . $rideBookSchedule->rbs_driver_id . '" data-totalcount="' . $total . '" data-status="Driving" class="view-ride-detail"  data-placement="top" title="Driving Ride" data-target="#modaldemo4" data-toggle="modal" style="text-decoration: underline;"> ' . $total . ' </a>';
                    return $rideDetail;
                })
                ->rawColumns(['action', 'total_waiting', 'total_requested', 'total_accepted', 'total_rejected', 'total_completed', 'total_driving'])
                ->make(true);
        }
        return view('admin.rideStatisticsDriver.index');
    }

    /**
     * Method for counting stats
     * @param $id
     * @param $status
     * @return
     */
    public function countRideStatistics($id, $status)
    {
        return RideBookingSchedule::where(['rbs_driver_id' => $id, 'rbs_ride_status' => $status])->count();
    }

    /**
     * Method for Ride Details View
     * @param $id
     * @param $status
     * @param $totalCount
     * @return Factory|View
     */
    public function getRideDetailViewModalByDriver($id, $status, $totalCount)
    {
        $rideBookSchedule = RideBookingSchedule::where(['rbs_driver_id' => $id, 'rbs_ride_status' => $status])->with('driver', 'passenger')->get();
        $user = Driver::where(['id' => $id])->first();
        return view('admin.rideStatisticsDriver.viewRideDetailStatisticsModal', ['rideBookSchedule' => $rideBookSchedule, 'status' => $status, 'totalCount' => $totalCount, 'user' => $user]);
    }

    public function getViewMapModal(Request $request)
    {
        return view('admin.rideStatisticsDriver.viewMapModal');
    }

    /**
     * Show the form for creating a new RideStatisticsDriver.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.company.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
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
     * Display the specified resource.
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
     * Show the form for editing the specified resource.
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
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
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
        return response()->json(['success' => true, 'message' => trans('adminMessages.country_deleted')]);
    }

    public function status($id, $status)
    {
        $company = Company::where('id', $id)->update(['com_status' => $status]);
        return response()->json(['success' => true, 'message' => 'Company status is successfully Updated']);
    }

    public function updateDriverStatus($id, $status, $company_id)
    {
        $company = Driver::where('id', $id)->update(['du_driver_status' => $status]);
        return response()->json(['success' => true, 'message' => 'Driver status is successfully Updated']);
    }

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
