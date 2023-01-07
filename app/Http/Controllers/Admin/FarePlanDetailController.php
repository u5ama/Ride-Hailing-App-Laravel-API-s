<?php

namespace App\Http\Controllers\Admin;

use App\Language;
use App\ExrtaFareCharge;
use App\FarePlanHead;
use App\FarePlanDetail;
use App\TransportType;
use App\Country;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;
use Auth;


class FarePlanDetailController extends Controller
{
    /**
     * Display a listing of the FarePlanDetail.
     *
     * @param Request $request
     * @return Factory|View
     */
    public function index(Request $request)
    {
        return view('admin.farePlanDetail.index');
    }

    /**
     * Show the form for creating a new FarePlanDetail.
     *
     * @param $farPlanHeadId
     * @return Factory|View
     */
    public function create($farPlanHeadId)
    {
        $FarePlanHead = FarePlanHead::where('id', $farPlanHeadId)->with('country')->first();
        $transportTypes = TransportType::listsTranslations('name')->where('tt_status', 1)->get();
        $countries = Country::where('status', 1)->get();
        return view('admin.farePlanDetail.create', ['FarePlanHead' => $FarePlanHead, 'transportTypes' => $transportTypes, 'countries' => $countries]);
    }

    /**
     * Method to show FarePlanDetail
     * @param Request $request
     * @return Factory|View
     */
    public function getFareDetailData(Request $request)
    {
        $FarePlanHead = FarePlanHead::where('id', $request->FarePlanHeadId)->first();
        $farePlanDetail = FarePlanDetail::where(['fpd_head_id_ref' => $request->FarePlanHeadId, 'fpd_transport_type_id' => $request->fpd_transport_type_id, 'fpd_country_id' => $request->fpd_country_id])->get();
        $transportTypes = TransportType::listsTranslations('name')->where('tt_status', 1)->get();
        $countries = Country::where('status', 1)->get();
        return view('admin.farePlanDetail.edit', ['FarePlanHead' => $FarePlanHead, 'transportTypes' => $transportTypes, 'countries' => $countries, 'farePlanDetail' => $farePlanDetail]);
    }

    /**
     * Store a newly created FarePlanDetail in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $FarePlanHeadId = $request->input('FarePlanHeadId');
        $fpd_transport_type_id = $request->input('fpd_transport_type_id');
        $fpd_country_id = $request->input('fpd_country_id');
        $farePlanDetailId = $request->input('farePlanDetailId');

        $start_time = $request->input('fpd_start_time');
        $end_time = $request->input('fpd_end_time');

        if (FarePlanDetail::where(['fpd_head_id_ref' => $FarePlanHeadId, 'fpd_transport_type_id' => $fpd_transport_type_id, 'fpd_country_id' => $fpd_country_id])
            ->whereTime('fare_plan_details.fpd_start_time', '<', $start_time)
            ->whereTime('fare_plan_details.fpd_end_time', '>', $end_time)
            ->exists()) {
            return response()->json(['success' => false, 'message' => 'Plan Time / Date should be unique']);
        } else {
            $fpd_base_fare = $request->input('fpd_base_fare');
            $fpd_minimum_fare = $request->input('fpd_minimum_fare');
            $fpd_cancel_charge = $request->input('fpd_cancel_charge');
            $fpd_cancel_minute = $request->input('fpd_cancel_minute');
            $fpd_min_booking_charges_if_cancel = $request->input('fpd_min_booking_charges_if_cancel');
            $fpd_per_km_fare = $request->input('fpd_per_km_fare');
            $fpd_per_minute_fare = $request->input('fpd_per_minute_fare');
            $fpd_per_km_fare_before_pickup = $request->input('fpd_per_km_fare_before_pickup');
            $fpd_per_minutes_fare_before_pickup = $request->input('fpd_per_minutes_fare_before_pickup');
            $fpd_wait_cost_per_km_fare = $request->input('fpd_wait_cost_per_km_fare');
            $fpd_wait_cost_per_minute_fare = $request->input('fpd_wait_cost_per_minute_fare');
            $fpd_estimate_percentage = $request->input('fpd_estimate_percentage');
            $fpd_start_time = $start_time;
            $fpd_end_time = $end_time;

            for ($i = 0; $i < count($fpd_base_fare); $i++) {
                $farePlaneDeatil = FarePlanDetail::where(['fpd_head_id_ref' => $FarePlanHeadId, 'fpd_transport_type_id' => $fpd_transport_type_id, 'fpd_country_id' => $fpd_country_id, 'id' => $farePlanDetailId[$i]])->get();

                if (isset($farePlaneDeatil) && count($farePlaneDeatil) > 0) {

                    $farePlanHead = FarePlanDetail::where(['fpd_head_id_ref' => $FarePlanHeadId, 'fpd_transport_type_id' => $fpd_transport_type_id, 'fpd_country_id' => $fpd_country_id, 'id' => $farePlanDetailId[$i]])->first();
                    $farePlanHead->fpd_updated_by = auth()->guard('admin')->user()->id;
                } else {
                    $farePlanHead = new FarePlanDetail;
                    $farePlanHead->fpd_created_by = auth()->guard('admin')->user()->id;
                }

                $farePlanHead->fpd_head_id_ref = $FarePlanHeadId;
                $farePlanHead->fpd_city_id = 0;
                $farePlanHead->fpd_base_fare = $fpd_base_fare[$i];
                $farePlanHead->fpd_cancel_charge = $fpd_cancel_charge[$i];
                $farePlanHead->fpd_cancel_minute = $fpd_cancel_minute[$i];
                $farePlanHead->fpd_per_km_fare = $fpd_per_km_fare[$i];
                $farePlanHead->fpd_per_minute_fare = $fpd_per_minute_fare[$i];
                $farePlanHead->fpd_per_km_fare_before_pickup = $fpd_per_km_fare_before_pickup[$i];
                $farePlanHead->fpd_per_minutes_fare_before_pickup = $fpd_per_minutes_fare_before_pickup[$i];
                $farePlanHead->fpd_wait_cost_per_minute_fare = $fpd_wait_cost_per_minute_fare[$i];
                $farePlanHead->fpd_estimate_percentage = $fpd_estimate_percentage[$i];
                $farePlanHead->fpd_start_time = $fpd_start_time[$i];
                $farePlanHead->fpd_end_time = $fpd_end_time[$i];
                $farePlanHead->fpd_transport_type_id = $fpd_transport_type_id;
                $farePlanHead->fpd_country_id = $fpd_country_id;
                $farePlanHead->save();

            }
            return response()->json(['success' => true, 'message' => 'Fare Plan Detail is successfully Saved', 'FarePlanHeadId' => $FarePlanHeadId, 'fpd_country_id' => $fpd_country_id, 'fpd_transport_type_id' => $fpd_transport_type_id]);
        }
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
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {

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
     * Remove the specified FarePlanDetail from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        FarePlanDetail::where('id', $id)->delete();
        return response()->json(['success' => true, 'message' => 'Fare Plan Detail is successfully Deleted']);
    }

    /**
     * Get Details for specified FarePlanDetail
     * @param $id
     * @return JsonResponse
     */
    public function getFarePlanHeadByid($id)
    {
        $farePlanHead = FarePlanHead::where('id', $id)->first();
        return response()->json(['success' => true, 'fph_plan_name' => $farePlanHead->fph_plan_name, 'fph_description' => $farePlanHead->fph_description, 'fph_fare_type' => $farePlanHead->fph_fare_type]);
    }

    /**
     * extra fare charge for FarePlanDetail
     * @param Request $request
     * @return JsonResponse
     */

    public function fareExtraCharge(Request $request)
    {
        $efc_plan_detail_id = $request->input('efc_plan_detail_id');
        $fareExtraId = $request->input('fareExtraId');
        $efc_plan_head_id = $request->input('efc_plan_head_id');
        $efc_key = $request->input('efc_key');
        $efc_info = $request->input('efc_info');
        $efc_charge = $request->input('efc_charge');

        for ($i = 0; $i < count($efc_key); $i++) {
            $farePlaneExtra = ExrtaFareCharge::where(['efc_plan_head_id' => $efc_plan_head_id, 'efc_plan_detail_id' => $efc_plan_detail_id, 'id' => $fareExtraId[$i]])->get();

            if (isset($farePlaneExtra) && count($farePlaneExtra) > 0) {
                $exrtaFareCharge = ExrtaFareCharge::where(['efc_plan_head_id' => $efc_plan_head_id, 'efc_plan_detail_id' => $efc_plan_detail_id, 'id' => $fareExtraId[$i]])->first();

            } else {
                $exrtaFareCharge = new ExrtaFareCharge;
            }

            $exrtaFareCharge->efc_plan_detail_id = $efc_plan_detail_id;
            $exrtaFareCharge->efc_plan_head_id = $efc_plan_head_id;
            $exrtaFareCharge->efc_key = $efc_key[$i];
            $exrtaFareCharge->efc_info = $efc_info[$i];
            $exrtaFareCharge->efc_charge = $efc_charge[$i];
            $exrtaFareCharge->efc_status = '1';
            $exrtaFareCharge->save();
        }
        return response()->json(['success' => true, 'message' => 'Extra Fare Charge is successfully Saved', 'planDetailId' => $efc_plan_detail_id, 'FarePlanHeadId' => $efc_plan_head_id]);

    }

    /**
     * Display extra fare charge for model
     * @param Request $request
     * @return Factory|View
     */
    public function getFareExtraModalData(Request $request)
    {
        $extraFareCharges = ExrtaFareCharge::where(['efc_plan_head_id' => $request->efc_plan_head_id, 'efc_plan_detail_id' => $request->efc_plan_detail_id])->get();
        return view('admin.farePlanDetail.extraFareModal', ['extraFareCharges' => $extraFareCharges]);
    }

    /**
     * Method to check if extra fare charge exsits
     * @param $efc_plan_detail_id
     * @param $efc_plan_head_id
     * @return JsonResponse
     */
    public function checkExistExtraCharges($efc_plan_detail_id, $efc_plan_head_id)
    {
        $check = ExrtaFareCharge::where(['efc_plan_detail_id' => $efc_plan_detail_id, 'efc_plan_head_id' => $efc_plan_head_id])->get();
        if (count($check) > 0) {
            return response()->json(['error' => false, 'message' => 'This plan detail can not delete due to having Extra Fare Charges']);
        } else {
            return response()->json(['success' => true, 'message' => 'yes']);
        }

    }

    /**
     * Method to remove extra fare charge
     * @param $id
     * @return JsonResponse
     */
    public function deleteExtraFareCharge($id)
    {
        ExrtaFareCharge::where('id', $id)->delete();
        return response()->json(['success' => true, 'message' => 'Extra Fare Charge is successfully Deleted']);
    }
    //end extra fare charge----------------------------------------
}
