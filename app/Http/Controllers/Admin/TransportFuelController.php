<?php

namespace App\Http\Controllers\Admin;

use App\TransportFuel;
use App\Helpers\ImageUploadHelper;
use App\TransportMakeTranslation;
use App\TransportFuelTranslation;
use App\Language;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class TransportFuelController extends Controller
{
    /**
     * Display a listing of the TransportFuel.
     *
     * @param Request $request
     * @return Application|Factory|View
     * @throws \Exception
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $transportFuel = TransportFuel::listsTranslations('name')
                ->select('transport_fuels.id')
                ->get();
            return Datatables::of($transportFuel)
                ->addColumn('action', function ($transportFuel) {
                    $edit_button = '<a href="' . route('admin::transportFuel.edit', [$transportFuel->id]) . '" class="btn btn-sm btn-outline-info waves-effect waves-light" data-toggle="tooltip" data-placement="top" title="Edit"><i class="bx bx-pencil font-size-16 align-middle"></i></a>';
                    $delete_button = '<button data-id="' . $transportFuel->id . '" class="delete-single btn btn-sm btn-outline-danger waves-effect waves-light" data-toggle="tooltip" data-placement="top" title="Delete"><i class="bx bx-trash font-size-16 align-middle"></i></button>';
                    return $edit_button . ' ' . $delete_button;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('admin.transportFuel.index');
    }

    /**
     * Show the form for creating a new TransportFuel.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        $languages = Language::all();
        return view('admin.transportFuel.create', ['languages' => $languages]);
    }

    /**
     * Store a newly created TransportFuel in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $id = $request->input('edit_value');

        if ($id == NULL) {
            $tt_order = TransportFuel::max('id');
            $insert_id = TransportFuel::create([
                'tf_order' => $tt_order + 1,
                'tf_status' => 1,

                'tf_created_by' => auth()->guard('admin')->user()->id,
            ]);
            $languages = Language::all();
            foreach ($languages as $language) {
                TransportFuelTranslation::create([
                    'name' => $request->input($language->language_code . '_name'),

                    'transport_fuel_id' => $insert_id->id,
                    'locale' => $language->language_code,
                    'tft_created_by' => auth()->guard('admin')->user()->id,
                ]);
            }
            return response()->json(['success' => true, 'message' => trans('adminMessages.transport_fuel_inserted')]);
        } else {
            TransportFuel::where('id', $id)->update([
                'tf_updated_by' => auth()->guard('admin')->user()->id,
            ]);

            $languages = Language::all();
            foreach ($languages as $language) {
                TransportFuelTranslation::updateOrCreate([
                    'transport_fuel_id' => $id,
                    'locale' => $language->language_code,
                ],
                    [
                        'transport_fuel_id' => $id,
                        'locale' => $language->language_code,
                        'name' => $request->input($language->language_code . '_name'),
                        'tft_updated_by' => auth()->guard('admin')->user()->id,
                    ]);
            }
            return response()->json(['success' => true, 'message' => trans('adminMessages.transport_fuel_updated')]);
        }
    }

    /**
     * Display the specified TransportFuel.
     *
     * @param int $id
     * @return void
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified TransportFuel.
     *
     * @param int $id
     * @return Application|Factory|View
     */
    public function edit($id)
    {
        $transportFuel = TransportFuel::find($id);
        if ($transportFuel) {
            $languages = Language::all();
            return view('admin.transportFuel.edit', ['transportFuel' => $transportFuel, 'languages' => $languages]);
        } else {
            abort(404);
        }
    }

    /**
     * Update the specified TransportFuel in storage.
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
     * Remove the specified TransportFuel from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        TransportFuel::where('id', $id)->delete();
        TransportFueltranslation::where('transport_fuel_id', $id)->delete();
        return response()->json(['success' => true, 'message' => trans('adminMessages.transport_fuel_deleted')]);
    }
}
