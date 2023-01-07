<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\User;
use App\VoucherCode;
use App\Utility\Utility;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;



class VoucherCodeController extends Controller
{
    /**
     * Display a listing of the VoucherCode.
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
            if ($_GET["voucher_status"] == 0) {
                $voucher_status = 0;
            } else {
                $voucher_status = 1;
            }
            if ($start_date && $end_date) {
                $start_date = date('Y-m-d', strtotime($start_date));
                $end_date = date('Y-m-d', strtotime($end_date));
                $voucherCode = VoucherCode::where('vc_status', $voucher_status)->whereDate("voucher_codes.vc_created_at", '>=', $start_date)->whereDate("voucher_codes.vc_created_at", "<=", $end_date)->get();
            } elseif ($voucher_status == 0) {
                $voucherCode = VoucherCode::where('vc_status', $voucher_status)->get();
            } elseif ($voucher_status == 1) {
                $voucherCode = VoucherCode::where('vc_status', $voucher_status)->get();
            } else {
                $voucherCode = VoucherCode::all();
            }

            return Datatables::of($voucherCode)
                ->addColumn('user', function ($voucherCode) {
                    if (!empty($voucherCode->vc_user_id)) {
                        $user_id = $voucherCode->vc_user_id;
                        $user = User::where('id', $user_id)->first();
                        return $user->name;
                    } else {
                        $msg = 'Not Redeemed';
                        return $msg;
                    }
                })

                ->addColumn('vc_issue_date', function ($voucherCode) {
                    return Utility:: convertTimeToUSERzone($voucherCode->vc_issue_date,Utility::getUserTimeZone(auth()->guard('admin')->user()->time_zone_id));

                })
                  ->addColumn('vc_expiry_date', function ($voucherCode) {

                    return Utility:: convertTimeToUSERzone($voucherCode->vc_expiry_date,Utility::getUserTimeZone(auth()->guard('admin')->user()->time_zone_id));

                })
                  ->addColumn('vc_redeemed_at', function ($voucherCode) {

                    return Utility:: convertTimeToUSERzone($voucherCode->vc_redeemed_at,Utility::getUserTimeZone(auth()->guard('admin')->user()->time_zone_id));

                })

                ->addColumn('status', function ($voucherCode) {

                    if ($voucherCode->vc_status == 0) {
                        $class = "badge badge-warning";
                        $name = "Inactive";
                    }
                    if ($voucherCode->vc_status == 1) {
                        $class = "badge badge-success";
                        $name = "Active";
                    }
                    $status_button = '<a type="button" onclick="updateStatus(' . $voucherCode->id . ',' . $voucherCode->vc_status . ') " data-id="' . $voucherCode->id . '" class="' . $class . '" data-toggle="tooltip" data-placement="top" title="Change Status">' . $name . '</a>';
                    return $status_button;
                })
                ->addColumn('action', function ($voucherCode) {
                    $voucherCodes = '<a type="button" data-cardid="' . $voucherCode->id . '" class="delete-single btn btn-sm btn-outline-info waves-effect waves-light"  data-placement="top" ><i class="fas fa-trash font-size-16 align-middle"></i></a>';
                    return $voucherCodes;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }
        return view('admin.voucherCode.index');
    }

    /**
     * Show the form for creating a new VoucherCode.
     *
     * @return Factory|View
     */
    public function create()
    {
        return view('admin.voucherCode.create');
    }

    /**
     * Store a newly created VoucherCode in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $id = $request->input('edit_value');

        if ($id == NULL) {
            $validator_array = [
                'voucher_code' => 'required',
                'vc_amount' => 'required',
                'issue_date' => 'required',
                'expiry_date' => 'required',
                'voucher_status' => 'required',
            ];

            $validator = Validator::make($request->all(), $validator_array);
            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
            }

            if (!empty($request->voucher_code)) {
                $voucher = VoucherCode::where(['vc_voucher_code' => $request->voucher_code, 'vc_status' => 1])->first();
                if ($voucher != null) {
                    return response()->json(['success' => false, 'message' => 'Voucher Code Already exits, Please select the different name of Voucher Code']);
                } else {
                    $voucherCode = $request->voucher_code;
                }
            }
            $start_date = $request->input('issue_date');
            $end_date = $request->input('expiry_date');
            $start_time = $request->input('issue_time');
            $end_time = $request->input('expiry_time');
            if (VoucherCode::whereDate('voucher_codes.vc_issue_date', '<=', $start_date)
                ->whereDate('voucher_codes.vc_expiry_date', '>=', $end_date)
                ->whereTime('voucher_codes.vc_issue_time', '<', $start_time)
                ->whereTime('voucher_codes.vc_expiry_time', '>', $end_time)
                ->where('vc_status', 1)
                ->exists()) {
                return response()->json(['success' => false, 'message' => 'Voucher Code Already exits for selected date range, Please select the different range of Voucher Code']);
            }else{
                $voucher = new VoucherCode();
                $voucher->vc_voucher_code = $voucherCode;
                $voucher->vc_amount = $request->input('vc_amount');
                $voucher->vc_issue_date = $request->input('issue_date');
                $voucher->vc_expiry_date = $request->input('expiry_date');
                $voucher->vc_issue_time = $request->input('issue_time');
                $voucher->vc_expiry_time = $request->input('expiry_time');
                $voucher->vc_status = $request->input('voucher_status');
                $voucher->save();

                return response()->json(['success' => true, 'message' => trans('adminMessages.voucherCode_inserted')]);
            }

        } else {
            $start_date = $request->input('issue_date');
            $end_date = $request->input('expiry_date');
            $start_time = $request->input('issue_time');
            $end_time = $request->input('expiry_time');
            if (!empty($request->voucher_code)) {
                if (VoucherCode::whereDate('voucher_codes.vc_issue_date', '<=', $start_date)
                    ->whereDate('voucher_codes.vc_expiry_date', '>=', $end_date)
                    ->whereTime('voucher_codes.vc_issue_time', '<', $start_time)
                    ->whereTime('voucher_codes.vc_expiry_time', '>', $end_time)
                    ->where('vc_status', 1)
                    ->exists()) {
                    return response()->json(['success' => false, 'message' => 'Voucher Code Already exits for selected date range, Please select the different range of Voucher Code']);
                } else {
                    $voucherCode = $request->voucher_code;
                    $voucher = VoucherCode::find($id);

                    $voucher->vc_voucher_code = $voucherCode;
                    $voucher->vc_issue_date = $request->input('issue_date');
                    $voucher->vc_expiry_date = $request->input('expiry_date');
                    $voucher->vc_issue_time = $request->input('issue_time');
                    $voucher->vc_expiry_time = $request->input('expiry_time');
                    $voucher->vc_status = $request->input('voucher_status');
                    $voucher->save();
                    return response()->json(['success' => true, 'message' => trans('adminMessages.voucherCode_inserted')]);
                }
            }
        }
    }


    /**
     * Display the specified VoucherCode.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified VoucherCode.
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
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        VoucherCode::where('id', $id)->delete();
        return response()->json(['success' => true, 'message' => trans('Status Deleted Successfully')]);
    }

    /**
     * Change the status for VoucherCode
     * @param $id
     * @param $status
     * @return JsonResponse
     */
    public function voucherCodeStatus($id, $status)
    {
        if ($status == 0) {
            $status = 1;
        } elseif ($status == 1) {
            $status = 0;
        }
        VoucherCode::where('id', $id)->update(['vc_status' => $status]);
        return response()->json(['success' => true, 'message' => trans('Status Updated Successfully')]);
    }
}
