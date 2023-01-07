<?php

namespace App\Http\Controllers\Company;


use App\TransportMake;
use App\TransportType;
use App\TransportModel;
use App\TransportModelTranslation;
use App\Language;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class TransportModelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {

        if ($request->ajax()) {
            //   DB::enableQueryLog();
            $models = TransportModel::listsTranslations('name')->with('transportType','transportMake')
                ->select('transport_models.id', 'transport_models.tmo_tt_ref_id','transport_models.tmo_tm_id_ref')
                ->get();
            //    dd(DB::getQueryLog());
            return Datatables::of($models)
                ->addColumn('action', function ($models) {
                    $edit_button = '<a href="' . route('admin::transportModel.edit', [$models->id]) . '" class="btn btn-sm btn-outline-info waves-effect waves-light" data-toggle="tooltip" data-placement="top" title="Edit"><i class="bx bx-pencil font-size-16 align-middle"></i></a>';
                    $delete_button = '<button data-id="' . $models->id . '" class="delete-single btn btn-sm btn-outline-danger waves-effect waves-light" data-toggle="tooltip" data-placement="top" title="Delete"><i class="bx bx-trash font-size-16 align-middle"></i></button>';
                    return $edit_button . ' ' . $delete_button;
                })
                ->addColumn('type', function ($models) {
                    $type = '';
                    if(isset($models->transportType)){
                       $type = $models->transportType->translateOrNew('en')->name;
                    }
                    return $type;
                    
                })
                ->addColumn('make', function ($models) {
                    $make = '';
                    if(isset($models->transportMake)){
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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $languages = Language::all();
        $transportTypes = TransportType::where('tt_status',1)->get();
       // $transportMakes = TransportMake::all();

        return view('admin.transportModel.create', ['languages' => $languages, 'transportTypes' => $transportTypes]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
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
                'tmo_status'=>1,
                'tmo_created_by'=>auth()->guard('admin')->user()->id,
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
                'tmo_updated_by'=>auth()->guard('admin')->user()->id,
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
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $model = TransportModel::find($id);
        $transportTypes = TransportType::where('tt_status',1)->get();
        if ($model) {
            $transportMakes = TransportMake::where('tm_type_ref_id',$model->tmo_tt_ref_id)->get();
            $languages = Language::all();
            return view('admin.transportModel.edit', [
                'transportModel' => $model,
                'transportMakes' => $transportMakes,
                'transportTypes'=>$transportTypes,
                'languages' => $languages
            ]);
        } else {
            abort(404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        TransportModel::where('id', $id)->delete();
        TransportModelTranslation::where('transport_model_id', $id)->delete();

        return response()->json(['success' => true, 'message' => trans('adminMessages.model_deleted')]);
    }

    public function getModel(Request $request)
    {
        $make_id = $request->input('make_id');
        $type_id = $request->input('type_id');

        $make_models = TransportModel::where(['tmo_tm_id_ref'=> $make_id,'tmo_tt_ref_id'=>$type_id])->get();

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
