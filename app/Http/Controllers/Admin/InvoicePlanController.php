<?php

namespace App\Http\Controllers\Admin;

use App\Country;
use App\FarePlanHead;
use App\InvoicePlan;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\In;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class InvoicePlanController extends Controller
{
    /**
     * Display a listing of the InvoicePlan.
     *
     * @param Request $request
     * @return Factory|View
     * @throws Exception
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $invoicePlan = InvoicePlan::all();
            return Datatables::of($invoicePlan)
                ->addColumn('action', function ($invoicePlan) {
                    $edit_button = '<a  class="btn btn-sm btn-outline-info waves-effect waves-light" href="' . route('admin::InvoicePlan.edit', [$invoicePlan->id]) . '"  data-toggle="tooltip" data-placement="top"  title="Fare Plan Detail Edit"><i class="bx bx-pencil font-size-16 align-middle"></i></a>';
//                    $delFarePlanDetail = '<a type="button" data-planid="' . $invoicePlan->id . '" class="delete-single btn btn-sm btn-outline-info waves-effect waves-light"  data-toggle="tooltip" data-placement="top"  title="Delete Fare Plan"><i class="fas fa-trash font-size-16 align-middle"></i></a>';
                    return $edit_button;
                })->addColumn('status', function ($invoicePlan) {
                    if ($invoicePlan->ip_status == 1) {
                        $class = "badge badge-success";
                        $name = "Active";
                        $status = "Inactive";
                    }
                    if ($invoicePlan->ip_status == 0) {
                        $class = "badge badge-warning";
                        $name = "Inactive";
                        $status = "Active";
                    }
                    $status_button = '<a type="button" onclick="updateStatus(' . $invoicePlan->id . ',' . $invoicePlan->ip_status . ')" class="' . $class . '" data-toggle="tooltip" data-placement="top" title="' . $status . '">' . $name . '</a>';
                    return $status_button;
                })->addColumn('is_default', function ($invoicePlan) {
                    if ($invoicePlan->ip_is_default == 1) {
                        $status = "Yes";
                    }
                    if ($invoicePlan->ip_is_default == 0) {
                        $status = "No";
                    }
                    return $status;
                })
                ->rawColumns(['status', 'action', 'is_default'])
                ->make(true);
        }

        return view('admin.invoicePlan.index');
    }

    /**
     * Show the form for creating a new InvoicePlan.
     *
     * @return Factory|View
     */
    public function create()
    {
        return view('admin.invoicePlan.create');
    }

    /**
     * Store a newly created InvoicePlan in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $id = $request->input('edit_value');
        if ($id == null) {
            $invoicePlan = new InvoicePlan;
        } else {
            $invoicePlan = InvoicePlan::where('id', $id)->first();
        }

        $invoicePlan->ip_bank_commesion = $request->ip_bank_commesion;
        $invoicePlan->ip_bank_fixed_commesion = $request->ip_bank_fixed_commesion;
        $invoicePlan->ip_bank_extra_charges = $request->ip_bank_extra_charges;
        $invoicePlan->ip_is_default = $request->ip_is_default;
        $invoicePlan->ip_payment_type = $request->ip_payment_type;
        $invoicePlan->ip_start_date = date('Y-m-d', strtotime($request->ip_start_date));
        $invoicePlan->ip_end_date = date('Y-m-d', strtotime($request->ip_end_date));
        $invoicePlan->save();

        return response()->json(['success' => true, 'message' => 'Invoice Plan is successfully Uploaded']);
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
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Factory|View
     */
    public function edit($id)
    {
        $invoice = InvoicePlan::where('id', $id)->first();
        if ($invoice) {
            return view('admin.invoicePlan.edit', ['invoice' => $invoice]);
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
     * @return Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Changes the Status for InvoicePlan
     * @param $id
     * @param $status
     * @return JsonResponse
     */
    public function status($id, $status)
    {

        $invoice = InvoicePlan::where('id', $id)->first();
        if ($invoice->ip_payment_type == 'knet'){
            DB::table('invoice_plans')->where(['ip_payment_type'=>'knet'])->update(['ip_status' => 0]);
            InvoicePlan::where('id', $id)->update(['ip_status' => 1, 'ip_payment_type' => 'knet']);
        }else{
            DB::table('invoice_plans')->where(['ip_payment_type'=>'creditcard'])->update(['ip_status' => 0]);
            InvoicePlan::where('id', $id)->update(['ip_status' => 1, 'ip_payment_type' => 'creditcard']);
        }

        return response()->json(['success' => true, 'message' => 'Invoice Plan status is successfully Updated']);
    }
}
