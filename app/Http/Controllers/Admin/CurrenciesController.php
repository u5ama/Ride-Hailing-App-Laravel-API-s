<?php

namespace App\Http\Controllers\Admin;

use App\Currency;
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


class CurrenciesController extends Controller
{
    /**
     * Display a listing of the Currencies.
     *
     * @param Request $request
     * @return Factory|View
     * @throws Exception
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $currencies = Currency::all();
            return Datatables::of($currencies)
                ->addColumn('action', function ($currencies) {
                    $addCurrencies = '<a  class="btn btn-sm btn-outline-info waves-effect waves-light" href="' . route('admin::FarePlanDetail.add', [$currencies->id]) . '"  data-toggle="tooltip" data-placement="top"  title="Fare Plan Detail"><i class="fas fa-eye font-size-16 align-middle"></i></a>';
                    $delCurrencies = '<a type="button" data-planid="' . $currencies->id . '" class="delete-single btn btn-sm btn-outline-info waves-effect waves-light"  data-toggle="tooltip" data-placement="top"  title="Delete Fare Plan"><i class="fas fa-trash font-size-16 align-middle"></i></a>';
                    return $addCurrencies . ' ' . $delCurrencies;
                })->addColumn('status', function ($currencies) {
                    if ($currencies->cu_status == 1) {
                        $class = "badge badge-success";
                        $name = "Active";
                        $status = "Inactive";
                    }
                    if ($currencies->cu_status == 0) {
                        $class = "badge badge-warning";
                        $name = "Inactive";
                        $status = "Active";
                    }
                    $status_button = '<a type="button" onclick="updateStatus(' . $currencies->id . ',' . $currencies->cu_status . ')" class="' . $class . '" data-toggle="tooltip" data-placement="top" title="' . $status . '">' . $name . '</a>';
                    return $status_button;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('admin.Currencies.index');
    }

    /**
     * Show the form for creating a new Currencies.
     *
     * @return Factory|View
     */
    public function create()
    {
        return view('admin.Currencies.create');
    }

    /**
     * Store a newly created Currencies in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $id = $request->input('edit_value');

        $currency = new Currency();
        $currency->cu_title = $request->input('cu_title');
        $currency->cu_code = $request->input('cu_code');
        $currency->cu_symbol_left = $request->input('cu_symbol_left');
        $currency->cu_symbol_right = $request->input('cu_symbol_right');
        $currency->cu_decimal_places = $request->input('cu_decimal_places');
        $currency->cu_value = $request->input('cu_value');
        $currency->cu_status = 1;
        $currency->cu_created_at = now();
        $currency->cu_updated_at = now();
        $currency->save();

        return response()->json(['success' => true, 'message' => 'Currency is successfully Saved']);
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
     * Show the form for editing the specified Currencies.
     *
     * @param int $id
     * @return Factory|View
     */
    public function edit($id)
    {
        $currency = Currency::find($id);
        if ($currency) {
            return view('admin.Currencies.edit', ['currency' => $currency]);
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
     * Remove the specified Currencies from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        Currency::where('id', $id)->delete();
        return response()->json(['success' => true]);
    }

    /**
     * Change the status for Currencies
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
        Currency::where('id', $id)->update(['cu_status' => $status_new]);
        return response()->json(['success' => true, 'message' => 'Currencies status is successfully Updated']);
    }
}
