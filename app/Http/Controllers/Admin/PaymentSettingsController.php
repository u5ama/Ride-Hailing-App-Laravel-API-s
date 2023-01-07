<?php

namespace App\Http\Controllers\Admin;

use App\Currency;
use App\Language;
use App\Country;
use App\FarePlanHead;
use App\PaymentGatewaySetting;
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


class PaymentSettingsController extends Controller
{
    /**
     * Display a listing of the PaymentSettings.
     *
     * @param Request $request
     * @return Factory|View
     * @throws Exception
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $settings = PaymentGatewaySetting::all();
            return Datatables::of($settings)
                ->addColumn('action', function ($settings) {
                    $edit_button = '<a href="' . route('admin::paymentSettings.edit', [$settings->id]) . '" class="btn btn-sm btn-outline-info waves-effect waves-light" data-toggle="tooltip" data-placement="top" title="Edit"><i class="bx bx-pencil font-size-16 align-middle"></i></a>';
                    $delCurrencies = '<a type="button" data-planid="' . $settings->id . '" class="delete-single btn btn-sm btn-outline-info waves-effect waves-light"  data-toggle="tooltip" data-placement="top"  title="Delete Fare Plan"><i class="fas fa-trash font-size-16 align-middle"></i></a>';
                    return $edit_button . ' ' . $delCurrencies;
                })->addColumn('status', function ($settings) {
                    if ($settings->pgs_status == 1) {
                        $class = "badge badge-success";
                        $name = "Active";
                        $status = "Inactive";
                    }
                    if ($settings->pgs_status == 0) {
                        $class = "badge badge-warning";
                        $name = "Inactive";
                        $status = "Active";
                    }
                    $status_button = '<a type="button" onclick="updateStatus(' . $settings->id . ',' . $settings->pgs_status . ')" class="' . $class . '" data-toggle="tooltip" data-placement="top" title="' . $status . '">' . $name . '</a>';
                    return $status_button;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }
        return view('admin.paymentGatewaySettings.index');
    }

    /**
     * Show the form for creating a new PaymentSettings.
     *
     * @return Factory|View
     */
    public function create()
    {
        return view('admin.paymentGatewaySettings.create');
    }

    /**
     * Store a newly created PaymentSettings in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $id = $request->input('edit_value');

        if ($id == NULL) {
            $setting = new PaymentGatewaySetting();
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
            $setting = PaymentGatewaySetting::find($id);
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
     * Display the specified PaymentSettings.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified PaymentSettings.
     *
     * @param int $id
     * @return Factory|View
     */
    public function edit($id)
    {
        $payment = PaymentGatewaySetting::find($id);
        if ($payment) {
            return view('admin.paymentGatewaySettings.edit', ['payment' => $payment]);
        } else {
            abort(404);
        }
    }

    /**
     * Update the specified PaymentSettings in storage.
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
     * Remove the specified PaymentSettings from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        PaymentGatewaySetting::where('id', $id)->delete();
        return response()->json(['success' => true]);
    }

    /**
     * Change the Status For PaymentSettings
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

        PaymentGatewaySetting::where('id', $id)->update(['pgs_status' => $status_new]);
        return response()->json(['success' => true, 'message' => 'payment Gateway Settings status is successfully Updated']);
    }
}
