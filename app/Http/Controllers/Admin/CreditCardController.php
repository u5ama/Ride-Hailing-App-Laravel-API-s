<?php

namespace App\Http\Controllers\Admin;

use App\CustomerCreditCard;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;


class CreditCardController extends Controller
{
    /**
     * Display a listing of the CreditCard.
     *
     * @param Request $request
     * @return Factory|View
     * @throws \Exception
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $creditCards = CustomerCreditCard::with('passenger')->get();
            return Datatables::of($creditCards)
                ->addColumn('passenger_name', function ($creditCards) {
                    if (!empty($creditCards->passenger->name)) {
                        return $creditCards->passenger->name;
                    }
                })
                ->addColumn('card_holder_name', function ($creditCards) {
                    if (!empty($creditCards->ccc_card_holder_name)) {
                        return $creditCards->ccc_card_holder_name;
                    }
                })
                ->addColumn('card_expire', function ($creditCards) {
                    if (!empty($creditCards->ccc_expire_year)) {
                        return $creditCards->ccc_expire_year;
                    }
                })
                ->addColumn('card_number', function ($creditCards) {
                    if (!empty($creditCards->ccc_card_number)) {
                        $card_number = Crypt::decryptString($creditCards->ccc_card_number);
                        $card_number = substr_replace($card_number, '********', 4, 8);
                        return $card_number;
                    }
                })
                ->addColumn('action', function ($creditCards) {
                    $rideDetail = '<a type="button" data-cardid="' . $creditCards->id . '" class="delete-single btn btn-sm btn-outline-info waves-effect waves-light"  data-placement="top" ><i class="fas fa-trash font-size-16 align-middle"></i></a>';
                    return $rideDetail;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('admin.CreditCards.index');
    }

    /**
     * Show the form for creating a new CreditCard.
     *
     * @return Factory|View
     */
    public function create()
    {
        return view('admin.CreditCards.create');
    }

    /**
     * Store a newly created CreditCard in storage.
     *
     * @param Request $request
     * @return Factory|View
     */
    public function store(Request $request)
    {
        return view('admin.CreditCards.show');
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
     * Show the form for editing the specified CreditCard.
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
     * Remove the specified CreditCard from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        CustomerCreditCard::where("id", $id)->delete();
        return response()->json(['success' => true, 'message' => trans('adminMessages.credit Card Deleted')]);
    }

}
