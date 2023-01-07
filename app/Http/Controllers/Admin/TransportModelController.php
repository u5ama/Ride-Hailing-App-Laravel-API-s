<?php

namespace App\Http\Controllers\Admin;


use App\TransportMake;
use App\TransportType;
use App\TransportModel;
use App\TransportModelTranslation;
use App\Language;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class TransportModelController extends Controller
{
    /**
     * Display a listing of the TransportModel.
     *
     * @param Request $request
     * @return Application|Factory|View
     * @throws Exception
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $models = TransportModel::listsTranslations('name')->with('transportType', 'transportMake')
                ->select('transport_models.id', 'transport_models.tmo_tt_ref_id', 'transport_models.tmo_tm_id_ref')
                ->get();
            return Datatables::of($models)
                ->addColumn('action', function ($models) {
                    $edit_button = '<a href="' . route('admin::transportModel.edit', [$models->id]) . '" class="btn btn-sm btn-outline-info waves-effect waves-light" data-toggle="tooltip" data-placement="top" title="Edit"><i class="bx bx-pencil font-size-16 align-middle"></i></a>';
                    $delete_button = '<button data-id="' . $models->id . '" class="delete-single btn btn-sm btn-outline-danger waves-effect waves-light" data-toggle="tooltip" data-placement="top" title="Delete"><i class="bx bx-trash font-size-16 align-middle"></i></button>';
                    return $edit_button . ' ' . $delete_button;
                })
                ->addColumn('type', function ($models) {
                    $type = '';
                    if (isset($models->transportType)) {
                        $type = $models->transportType->translateOrNew('en')->name;
                    }
                    return $type;

                })
                ->addColumn('make', function ($models) {
                    $make = '';
                    if (isset($models->transportMake)) {
                        $make = $models->transportMake->translateOrNew('en')->name;
                    }
                    return $make;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('admin.transportModel.index');
    }

    /**
     * Show the form for creating a new TransportModel.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        $languages = Language::all();
        $transportTypes = TransportType::where('tt_status', 1)->get();
        return view('admin.transportModel.create', ['languages' => $languages, 'transportTypes' => $transportTypes]);
    }

    /**
     * Store a newly created TransportModel in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $id = $request->input('edit_value');

        if ($id == NULL) {
            $validator_array = [

                'type_id' => 'required|integer',
                'make_id' => 'required|integer',
            ];
        } else {
            $validator_array = [
                'type_id' => 'required|integer',
                'make_id' => 'required|integer'];
        }
        $validator = Validator::make($request->all(), $validator_array);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }
        if ($id == NULL) {
            $model_order = TransportModel::max('id');
            $insert_id = TransportModel::create([
                'tmo_tm_id_ref' => $request->input('make_id'),
                'tmo_tt_ref_id' => $request->input('type_id'),
                'tmo_order' => $model_order + 1,
                'tmo_status' => 1,
                'tmo_created_by' => auth()->guard('admin')->user()->id,
            ]);
            $languages = Language::all();
            foreach ($languages as $language) {
                TransportModelTranslation::create([
                    'name' => $request->input($language->language_code . '_name'),
                    'transport_model_id' => $insert_id->id,
                    'locale' => $language->language_code,
                ]);
            }
            return response()->json(['success' => true, 'message' => trans('adminMessages.model_inserted')]);
        } else {
            $languages = Language::all();
            TransportModel::where('id', $id)->update([
                'tmo_tm_id_ref' => $request->input('make_id'),
                'tmo_tt_ref_id' => $request->input('type_id'),
                'tmo_updated_by' => auth()->guard('admin')->user()->id,
            ]);

            foreach ($languages as $language) {
                TransportModelTranslation::updateOrCreate([
                    'transport_model_id' => $id,
                    'locale' => $language->language_code,
                ],
                    [
                        'transport_model_id' => $id,
                        'locale' => $language->language_code,
                        'name' => $request->input($language->language_code . '_name')
                    ]);
            }
            return response()->json(['success' => true, 'message' => trans('adminMessages.model_updated')]);
        }
    }

    /**
     * Display the specified TransportModel.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified TransportModel.
     *
     * @param int $id
     * @return Application|Factory|View
     */
    public function edit($id)
    {
        $model = TransportModel::find($id);
        $transportTypes = TransportType::where('tt_status', 1)->get();
        if ($model) {
            $transportMakes = TransportMake::where('tm_type_ref_id', $model->tmo_tt_ref_id)->get();
            $languages = Language::all();
            return view('admin.transportModel.edit', [
                'transportModel' => $model,
                'transportMakes' => $transportMakes,
                'transportTypes' => $transportTypes,
                'languages' => $languages
            ]);
        } else {
            abort(404);
        }
    }

    /**
     * Update the specified TransportModel in storage.
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
     * Remove the specified TransportModel from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        TransportModel::where('id', $id)->delete();
        TransportModelTranslation::where('transport_model_id', $id)->delete();

        return response()->json(['success' => true, 'message' => trans('adminMessages.model_deleted')]);
    }

    /**
     * Method for showing models
     * @param Request $request
     */
    public function getModel(Request $request)
    {
        $make_id = $request->input('make_id');
        $type_id = $request->input('type_id');
        $make_models = TransportModel::where(['tmo_tm_id_ref' => $make_id, 'tmo_tt_ref_id' => $type_id])->get();
        if (count($make_models) > 0) {
            echo "<option value=''>Please Select model</option>";
            foreach ($make_models as $make_model) {
                echo "<option value='" . $make_model->id . "'>" . $make_model->name . "</option>";
            }
        } else {
            echo "<option value=''>No Data Found</option>";
        }
    }
}
