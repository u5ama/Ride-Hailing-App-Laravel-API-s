<?php

namespace App\Http\Controllers\Admin;

use App\Company;
use App\Driver;
use App\RideIgnoredBy;
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


class RideIngnoredbyController extends Controller
{
    /**
     * Display a listing of the RideIngnored.
     *
     * @param Request $request
     * @return Application|Factory|View
     * @throws Exception
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
                $rideIgnoredBy = RideIgnoredBy::query()->whereRaw("date(ride_ignored_bies.rib_created_at) >= '" . $start_date . "' AND date(ride_ignored_bies.rib_created_at) <= '" . $end_date . "'")->with('driver')->groupBy('rib_driver_id')->get();
            } else {
                $rideIgnoredBy = RideIgnoredBy::whereDate("ride_ignored_bies.rib_created_at", $currentDate)->with('driver')->groupBy('rib_driver_id')->get();
            }
            return Datatables::of($rideIgnoredBy)
                ->addColumn('driver_name', function ($rideIgnoredBy) {

                    return $rideIgnoredBy->driver->du_full_name;
                })
                ->addColumn('cancel_at', function ($rideIgnoredBy) {

                    return $rideIgnoredBy->rib_created_at;
                })
                ->addColumn('totalRideIgnored', function ($rideIgnoredBy) {
                    $totalRideIgnored = $this->getTotalIgnoredCount($rideIgnoredBy->rib_driver_id);
                    return $totalRideIgnored;
                })
                ->addColumn('action', function ($rideIgnoredBy) {
                    $rideDetail = '<a type="button" data-driverid="' . $rideIgnoredBy->rib_driver_id . '" data-rideid="' . $rideIgnoredBy->rib_ride_id . '" class=" ride-details btn btn-sm btn-outline-info waves-effect waves-light"  data-placement="top" title="Ride Detail" data-target="#modaldemo3" data-toggle="modal"><i class="fas fa-eye font-size-16 align-middle"></i></a>';
                    return $rideDetail;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('admin.rideIgnoredBy.index');
    }

    /**
     * Method for total Ride view
     * @param $driver_id
     * @return Factory|View
     */
    public function getTotalRideViewModal($driver_id)
    {
        $ride_ids = RideIgnoredBy::where('rib_driver_id', $driver_id)->pluck('rib_ride_id')->toArray();
        $rideBookSchedule = RideBookingSchedule::wherein('id', $ride_ids)->with('driver', 'passenger')->get();
        return view('admin.rideIgnoredBy.viewRideDetailModal', ['rideBookSchedule' => $rideBookSchedule]);
    }

    /**
     * Method for total Ignored Count
     * @param $driver_id
     * @return
     */
    public function getTotalIgnoredCount($driver_id)
    {
        return $totalCountRide = RideIgnoredBy::where('rib_driver_id', $driver_id)->count();
    }

    /**
     * Show the form for creating a new RideIngnored.
     *
     * @return Factory|View
     */
    public function create()
    {
        return view('admin.company.create');
    }

    /**
     * Store a newly created RideIngnored in storage.
     *
     * @param Request $request
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
     * Display the specified RideIngnored.
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
     * Show the form for editing the specified RideIngnored.
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
