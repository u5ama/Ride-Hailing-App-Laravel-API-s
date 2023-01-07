<?php

namespace App\Http\Controllers\Admin;

use App\Country;
use App\CustomerCreditCard;
use App\FarePlanHead;
use App\PromoCode;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use phpDocumentor\Reflection\Types\Null_;
use Yajra\DataTables\Facades\DataTables;


class PromoCodeController extends Controller
{
    /**
     * Display a listing of the PromoCode.
     *
     * @param Request $request
     * @return Factory|View
     * @throws Exception
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $start_date = (!empty($_GET["start_date"])) ? ($_GET["start_date"]) : ('');
            $end_date = (!empty($_GET["end_date"])) ? ($_GET["end_date"]) : ('');

            if ($start_date && $end_date) {
                $start_date = date('Y-m-d', strtotime($start_date));
                $end_date = date('Y-m-d', strtotime($end_date));
                $promoCodes = PromoCode::where('pco_is_active', $_GET["promo_status"])->whereDate("promo_codes.pco_created_at", '>=', $start_date)->whereDate("promo_codes.pco_created_at", "<=", $end_date)->get();
            } elseif ($_GET["promo_status"] == 0) {
                $promoCodes = PromoCode::where('pco_is_active', $_GET["promo_status"])->get();
            } elseif ($_GET["promo_status"] == 1) {
                $promoCodes = PromoCode::where('pco_is_active', $_GET["promo_status"])->get();
            } else {
                $promoCodes = PromoCode::all();
            }
            return Datatables::of($promoCodes)
                ->addColumn('country', function ($promoCodes) {
                    if (!empty($promoCodes->pco_country_id)) {
                        $country_id = $promoCodes->pco_country_id;
                        $country = Country::listsTranslations('name')->where('countries.id', $country_id)->first();
                        return $country->name;
                    }
                })
                ->addColumn('status', function ($promoCodes) {
                    if ($promoCodes->pco_is_active == 0) {
                        $class = "badge badge-warning";
                        $name = "Inactive";
                    }
                    if ($promoCodes->pco_is_active == 1) {
                        $class = "badge badge-success";
                        $name = "Active";
                    }
                    $status_button = '<a type="button" onclick="updateStatus(' . $promoCodes->id . ',' . $promoCodes->pco_is_active . ') " data-id="' . $promoCodes->id . '" class="' . $class . '" data-toggle="tooltip" data-placement="top" title="Change Status">' . $name . '</a>';
                    return $status_button;
                })
                ->addColumn('action', function ($promoCodes) {
                    $promoCode = '<a type="button" data-cardid="' . $promoCodes->id . '" class="delete-single btn btn-sm btn-outline-info waves-effect waves-light"  data-placement="top" ><i class="fas fa-trash font-size-16 align-middle"></i></a>';
                    return $promoCode;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }
        return view('admin.promoCode.index');
    }

    /**
     * Show the form for creating a new PromoCode.
     *
     * @return Factory|View
     */
    public function create()
    {
        $countries = Country::listsTranslations('name')
            ->where('status', 'Active')
            ->get();
        return view('admin.promoCode.create', compact('countries'));
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
                'promo_code' => 'required',
                'promo_country' => 'required',
                'start_date' => 'required',
                'end_date' => 'required',
                'promo_type' => 'required',
                'promo_value' => 'required',
                'promo_value_type' => 'required',
            ];

            $validator = Validator::make($request->all(), $validator_array);
            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
            }
            if (!empty($request->promo_code)) {
                $promo = PromoCode::where(['pco_promo_code' => $request->promo_code, 'pco_is_active' => 1])->first();
                if ($promo != null) {
                    return response()->json(['success' => false, 'message' => 'Promo Code Already exits, Please select the different name of Promo Code']);
                } else {
                    $promoCode = $request->promo_code;
                }
            }
            $start_date = $request->input('start_date');
            $end_date = $request->input('end_date');
            $start_time = $request->input('start_time');
            $end_time = $request->input('end_time');
            if (PromoCode::whereDate('promo_codes.pco_start_date', '<=', $start_date)
                ->whereDate('promo_codes.pco_end_date', '>=', $end_date)
                ->whereTime('promo_codes.pco_start_time', '<', $start_time)
                ->whereTime('promo_codes.pco_end_time', '>', $end_time)
                ->where('pco_is_active', 1)
                ->exists()) {
                return response()->json(['success' => false, 'message' => 'Promo Code Already exits for selected date range, Please select the different range of Promo Code']);
            }else{
                $promo = new PromoCode();

                $promo->pco_country_id = $request->input('promo_country');
                $promo->pco_promo_code = $promoCode;
                $promo->pco_promo_value = $request->input('promo_value');
                $promo->pco_promo_value_type = $request->input('promo_value_type');
                $promo->pco_start_date = $request->input('start_date');
                $promo->pco_end_date = $request->input('end_date');
                $promo->pco_start_time = $start_time;
                $promo->pco_end_time = $end_time;
                $promo->pco_promo_type = $request->input('promo_type');
                $promo->pco_admin_remarks = $request->input('admin_remarks');
                $promo->save();

                return response()->json(['success' => true, 'message' => trans('adminMessages.promoCode_inserted')]);
            }

        } else {
            $start_date = $request->input('start_date');
            $end_date = $request->input('end_date');
            $start_time = $request->input('start_time');
            $end_time = $request->input('end_time');
            if (!empty($request->promo_code)) {
                if (PromoCode::whereDate('promo_codes.pco_start_date', '<=', $start_date)
                    ->whereDate('promo_codes.pco_end_date', '>=', $end_date)
                    ->whereTime('promo_codes.pco_start_time', '<', $start_time)
                    ->whereTime('promo_codes.pco_end_time', '>', $end_time)
                    ->where(['pco_promo_code' => $request->promo_code, 'pco_is_active' => 1])
                    ->exists()) {
                    return response()->json(['success' => false, 'message' => 'Promo Code Already exits for selected date range, Please select the different range of Promo Code']);
                }else {
                    $promoCode = $request->promo_code;

                    $promo = PromoCode::find($id);

                    $promo->pco_country_id = $request->input('promo_country');
                    $promo->pco_promo_code = $promoCode;
                    $promo->pco_promo_value = $request->input('promo_value');
                    $promo->pco_promo_value_type = $request->input('promo_value_type');
                    $promo->pco_start_date = $request->input('start_date');
                    $promo->pco_end_date = $request->input('end_date');
                    $promo->pco_start_time = $start_time;
                    $promo->pco_end_time = $end_time;
                    $promo->pco_promo_type = $request->input('promo_type');
                    $promo->pco_admin_remarks = $request->input('admin_remarks');
                    $promo->save();

                    return response()->json(['success' => true, 'message' => trans('adminMessages.promoCode_inserted')]);
                }
            }
        }
    }


    /**
     * Display the specified PromoCode.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified PromoCode.
     *
     * @param int $id
     * @return void
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
     * Remove the specified PromoCode from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        PromoCode::where('id', $id)->delete();
        return response()->json(['success' => true, 'message' => trans('Status Deleted Successfully')]);
    }

    /**
     * Change the status for PromoCode
     * @param $id
     * @param $status
     * @return JsonResponse
     */
    public function promoCodeStatus($id, $status)
    {
        if ($status == 0) {
            $status = 1;
        } elseif ($status == 1) {
            $status = 0;
        }
        PromoCode::where('id', $id)->update(['pco_is_active' => $status]);
        return response()->json(['success' => true, 'message' => trans('Status Updated Successfully')]);
    }
}
