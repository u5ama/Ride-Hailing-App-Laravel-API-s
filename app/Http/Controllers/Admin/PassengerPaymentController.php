<?php

namespace App\Http\Controllers\Admin;

use App\Currency;
use App\Language;
use App\Country;
use App\FarePlanHead;
use App\PassengerPaymentDetail;
use App\PaymentGatewaySetting;
use App\RideBookingSchedule;
use App\User;
use App\Utility\Utility;
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



class PassengerPaymentController extends Controller
{
    /**
     * Display a listing of the PassengerPayment.
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
                $payments = PassengerPaymentDetail::query()->whereRaw("date(passenger_payment_details.ppd_created_at) >= '" . $start_date . "' AND date(passenger_payment_details.ppd_created_at) <= '" . $end_date . "'")->get();
            } else {
                $payments = PassengerPaymentDetail::all();
            }
            return Datatables::of($payments)
                ->addColumn('action', function ($payments) {
                    $delCurrencies = '<a type="button" data-planid="' . $payments->id . '" class="delete-single btn btn-sm btn-outline-info waves-effect waves-light"  data-toggle="tooltip" data-placement="top"  title="Delete Payment"><i class="fas fa-trash font-size-16 align-middle"></i></a>';
                    return $delCurrencies;
                })
                ->addColumn('ppd_created_at', function ($payments) {
                    return Utility:: convertTimeToUSERzone($payments->ppd_created_at,Utility::getUserTimeZone(auth()->guard('admin')->user()->time_zone_id));
                   
                })
                ->addColumn('passenger_name', function ($payments) {
                    $passenger = User::where('id', $payments->ppd_passenger_id)->first();
                    return $passenger->name;
                })
                ->rawColumns(['passenger_name', 'action'])
                ->make(true);
        }
        return view('admin.passengerPayments.index');
    }

    /**
     * Show the form for creating a new PassengerPayment.
     *
     * @return Factory|View
     */
    public function create()
    {
        return view('admin.passengerPayments.create');
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
            $setting = new PassengerPaymentDetail();
            $setting->pgs_username = $request->input('pgs_username');
            $setting->pgs_merchant_id = $request->input('pgs_merchant_id');
            $setting->pgs_base_url = $request->input('pgs_base_url');
            $setting->pgs_password = $request->input('pgs_password');
            $setting->pgs_api_key = $request->input('pgs_api_key');
            $setting->pgs_payment_gateway = $request->input('pgs_payment_gateway');
            $setting->pgs_whitelabled = $request->input('pgs_whitelabled');
            $setting->pgs_success_url = $request->input('pgs_success_url');
            $setting->pgs_error_url = $request->input('pgs_error_url');
            $setting->pgs_currency_code = $request->input('pgs_currency_code');
            $setting->pgs_gateway_type = $request->input('pgs_gateway_type');
            $setting->pgs_status = 0;
            $setting->pgs_created_at = now();
            $setting->pgs_updated_at = now();
            $setting->save();
        } else {
            $setting = PassengerPaymentDetail::find($id);
            $setting->pgs_username = $request->input('pgs_username');
            $setting->pgs_merchant_id = $request->input('pgs_merchant_id');
            $setting->pgs_base_url = $request->input('pgs_base_url');
            $setting->pgs_password = $request->input('pgs_password');
            $setting->pgs_api_key = $request->input('pgs_api_key');
            $setting->pgs_payment_gateway = $request->input('pgs_payment_gateway');
            $setting->pgs_whitelabled = $request->input('pgs_whitelabled');
            $setting->pgs_success_url = $request->input('pgs_success_url');
            $setting->pgs_error_url = $request->input('pgs_error_url');
            $setting->pgs_currency_code = $request->input('pgs_currency_code');
            $setting->pgs_gateway_type = $request->input('pgs_gateway_type');
            $setting->pgs_status = 0;
            $setting->pgs_created_at = now();
            $setting->pgs_updated_at = now();
            $setting->save();
        }
        return response()->json(['success' => true, 'message' => 'payment Gateway Setting are successfully Saved']);
    }

    /**
     * Display the specified PassengerPayment.
     *
     * @param int $id
     * @return void
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Factory|View
     */
    public function edit($id)
    {
        $payment = PassengerPaymentDetail::find($id);
        if ($payment) {
            return view('admin.passengerPayments.edit', ['payment' => $payment]);
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
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        PassengerPaymentDetail::where('id', $id)->delete();
        return response()->json(['success' => true]);
    }

    /**
     * Change the status for PassengerPayment
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
        DB::table('payment_gateway_settings')->update(['pgs_status' => 0]);
        PassengerPaymentDetail::where('id', $id)->update(['pgs_status' => $status_new]);
        return response()->json(['success' => true, 'message' => 'payment Gateway Settings status is successfully Updated']);
    }
}
