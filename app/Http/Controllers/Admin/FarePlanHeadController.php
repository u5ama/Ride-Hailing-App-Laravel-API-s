<?php

namespace App\Http\Controllers\Admin;

use App\FarePlanDetail;
use App\Language;
use App\Country;
use App\FarePlanHead;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;
use DB;
use Auth;


class FarePlanHeadController extends Controller
{
    /**
     * Display a listing of the FarePlanHead.
     *
     * @param Request $request
     * @return Factory|View
     * @throws Exception
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $farePlanHead = FarePlanHead::with('country')->get();
            return Datatables::of($farePlanHead)
                ->addColumn('country_name', function ($farePlanHead) {
                    if (isset($farePlanHead->country)) {
                        return $farePlanHead->country->translateOrNew('en')->name;
                    }
                })
                ->addColumn('start_date', function ($farePlanHead) {
                    if ($farePlanHead->fph_is_default !== 'default') {
                        if (!empty($farePlanHead->fph_start_date)) {
                            return $farePlanHead->fph_start_date;
                        }
                    }
                })
                ->addColumn('end_date', function ($farePlanHead) {
                    if ($farePlanHead->fph_is_default !== 'default') {
                        if (!empty($farePlanHead->fph_end_date)) {
                            return $farePlanHead->fph_end_date;
                        }
                    }
                })
                ->addColumn('action', function ($farePlanHead) {
                    $addFarePlanDetail = '<a type="button" data-planId="' . $farePlanHead->id . '" class="plan-details btn btn-sm btn-outline-info waves-effect waves-light" data-placement="top" title="View" data-target="#modaldemo33" data-toggle="modal"><i class="fas fa-eye font-size-16 align-middle"></i></a>';
                    $edit_button = '<a  class="btn btn-sm btn-outline-info waves-effect waves-light" href="' . route('admin::FarePlanDetail.add', [$farePlanHead->id]) . '"  data-toggle="tooltip" data-placement="top"  title="Fare Plan Detail Edit"><i class="bx bx-pencil font-size-16 align-middle"></i></a>';
                    $delFarePlanDetail = '<a type="button" data-planid="' . $farePlanHead->id . '" class="delete-single btn btn-sm btn-outline-info waves-effect waves-light"  data-toggle="tooltip" data-placement="top"  title="Delete Fare Plan"><i class="fas fa-trash font-size-16 align-middle"></i></a>';
                    return $addFarePlanDetail . ' ' . $edit_button . ' ' . $delFarePlanDetail;
                    // return $addFarePlanDetail.' ' .$delFarePlanDetail;
                })->addColumn('status', function ($farePlanHead) {
                    if ($farePlanHead->fph_status == 1) {
                        $class = "badge badge-success";
                        $name = "Active";
                        $status = "Inactive";
                    }
                    if ($farePlanHead->fph_status == 0) {
                        $class = "badge badge-warning";
                        $name = "Inactive";
                        $status = "Active";
                    }
                    $status_button = '<a type="button" onclick="updateStatus(' . $farePlanHead->id . ',' . $farePlanHead->fph_status . ')" class="' . $class . '" data-toggle="tooltip" data-placement="top" title="' . $status . '">' . $name . '</a>';
                    return $status_button;
                })
                ->rawColumns(['status', 'action', 'start_date', 'end_date'])
                ->make(true);
        }
        $countries = Country::where('status', 1)->get();

        return view('admin.farePlanHead.index', ['countries' => $countries]);
    }

    /**
     * Show the form for creating a new FarePlanHead.
     *
     * @return Factory|View
     */
    public function create()
    {
        $countries = Country::where('status', 1)->get();
        return view('admin.farePlanHead.create', ['countries' => $countries]);
    }

    /**
     * Store a newly created FarePlanHead in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $start_date = date('Y-m-d', strtotime($request->start_date));
        $end_date = date('Y-m-d', strtotime($request->end_date));

        $id = $request->input('edit_value');
        if ($id == null) {
            if ($request->input('fph_is_default') == 'default'){
                if (FarePlanHead::where(['fph_country_id' => $request->input('fph_country_id'), 'fph_is_default' => 'default'])->exists()){
                    return response()->json(['success' => false, 'message' => 'Default Fare Plan Already exits for selected country']);
                }
            }
             else{
                $farePlanHead = new FarePlanHead;
                $farePlanHead->fph_created_by = auth()->guard('admin')->user()->id;
                if (FarePlanHead::whereDate('fare_plan_head.fph_start_date', '<=', $start_date)
                    ->whereDate('fare_plan_head.fph_end_date', '>=', $end_date)
                    ->where('fph_status', 1)
                    ->exists()) {
                    return response()->json(['success' => false, 'message' => 'Fare Plan Already exits for selected date range, Please select the different range of fare plan']);
                } else {
                    $farePlanHead->fph_plan_name = $request->input('fph_plan_name');
                    $farePlanHead->fph_description = $request->input('fph_description');
                    $farePlanHead->fph_fare_type = $request->input('fph_fare_type');
                    $farePlanHead->fph_country_id = $request->input('fph_country_id');
                    $farePlanHead->fph_vat_per = $request->input('fph_vat_per');
                    $farePlanHead->fph_tax_per = $request->input('fph_tax_per');
                    $farePlanHead->fph_start_date = $start_date;
                    $farePlanHead->fph_end_date = $end_date;
                    $farePlanHead->fph_is_default = $request->input('fph_is_default');
                    $farePlanHead->fph_status = 1;
                    $farePlanHead->fph_created_at = now();
                    $farePlanHead->fph_updated_at = now();
                    $farePlanHead->save();

                    return response()->json(['success' => true, 'message' => 'Fare Plan Head is successfully Saved']);
                }
            }
        } else {
            $farePlanHead = FarePlanHead::where('id', $id)->first();
            $farePlanHead->fph_updated_by = auth()->guard('admin')->user()->id;

            $farePlanHead->fph_plan_name = $request->input('fph_plan_name');
            $farePlanHead->fph_description = $request->input('fph_description');
            $farePlanHead->fph_fare_type = $request->input('fph_fare_type');
            $farePlanHead->fph_country_id = $request->input('fph_country_id');
            $farePlanHead->fph_vat_per = $request->input('fph_vat_per');
            $farePlanHead->fph_tax_per = $request->input('fph_tax_per');
            $farePlanHead->fph_start_date = $start_date;
            $farePlanHead->fph_end_date = $end_date;
            $farePlanHead->fph_is_default = $request->input('fph_is_default');
            $farePlanHead->fph_status = 1;
            $farePlanHead->fph_created_at = now();
            $farePlanHead->fph_updated_at = now();
            $farePlanHead->save();
            return response()->json(['success' => true, 'message' => 'Fare Plan Head is successfully Saved']);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return void
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified FarePlanHead.
     *
     * @param int $id
     * @return Factory|View
     */
    public function edit($id)
    {
        $fare_plan = FarePlanHead::find($id);
        if ($fare_plan) {
            return view('admin.farePlanHead.edit', ['fare_plan' => $fare_plan]);
        } else {
            abort(404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified FarePlanHead from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        $plan = FarePlanHead::where('id', $id)->first();
        if ($plan->fph_is_default == 'default'){
            return response()->json(['success' => false, 'message' => 'Default Fare Plan cannot be deleted for selected country']);
        }else{
            FarePlanHead::where('id', $id)->delete();
            return response()->json(['success' => true]);
        }
    }

    /**
     * Get specified FarePlanHead
     * @param $id
     * @return JsonResponse
     */
    public function getFarePlanHeadByid($id)
    {
        $farePlanHead = FarePlanHead::where('id', $id)->first();
        return response()->json(['success' => true, 'fph_plan_name' => $farePlanHead->fph_plan_name, 'fph_description' => $farePlanHead->fph_description, 'fph_fare_type' => $farePlanHead->fph_fare_type, 'fph_country_id' => $farePlanHead->fph_country_id, 'fph_vat_per' => $farePlanHead->fph_vat_per, 'fph_tax_per' => $farePlanHead->fph_tax_per, 'start_date' => $farePlanHead->fph_start_date, 'end_date' => $farePlanHead->fph_end_date]);
    }

    /**
     * Change the status for FarePlanHead
     * @param $id
     * @param $status
     * @return JsonResponse
     */
    public function status($id, $status)
    {
        if ($status == 1) {
            $status_new = 0;
        }
        if ($status == 0) {
            $status_new = 1;
        }
        $plan = FarePlanHead::where('id', $id)->first();
        if ($plan->fph_is_default == 'default') {
            return response()->json(['success' => false, 'message' => 'Default Fare Plan status cannot be changed for selected country']);
        }else{
            FarePlanHead::where('id', $id)->update(['fph_status' => $status_new]);
            return response()->json(['success' => true, 'message' => 'Fare Plan Head status is successfully Updated']);
        }
    }


    /**
     * Show the detailed view for FarePlanHead
     * @param $planId
     * @return Factory|View
     */
    public function getDetailedView($planId)
    {
        $detailsPlan = FarePlanDetail::where(['fare_plan_details.fpd_head_id_ref' => $planId])->get();
        return view('admin.farePlanHead.fareplanModal', ['detailsPlan' => $detailsPlan]);
    }
}
