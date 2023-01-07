<?php

namespace App\Http\Controllers\Company;


use App\TransportMake;
use App\TransportType;
use App\TransportModel;
use App\TransportModelColor;
use App\TransportModelColorTranslation;
use App\Language;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class TransportModelColorController extends Controller
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
            $colors = TransportModelColor::listsTranslations('name')->with('transportType','transportMake','transportModel')
                ->select('transport_model_colors.id', 'transport_model_colors.tmc_tmo_id_ref', 'transport_model_colors.tmc_tt_ref_id','transport_model_colors.tmc_tm_ref_id')
                ->get();
            //    dd(DB::getQueryLog());
            return Datatables::of($colors)
                ->addColumn('action', function ($colors) {
                    $edit_button = '<a href="' . route('admin::transportModelColor.edit', [$colors->id]) . '" class="btn btn-sm btn-outline-info waves-effect waves-light" data-toggle="tooltip" data-placement="top" title="Edit"><i class="bx bx-pencil font-size-16 align-middle"></i></a>';
                    $delete_button = '<button data-id="' . $colors->id . '" class="delete-single btn btn-sm btn-outline-danger waves-effect waves-light" data-toggle="tooltip" data-placement="top" title="Delete"><i class="bx bx-trash font-size-16 align-middle"></i></button>';
                    return $edit_button . ' ' . $delete_button;
                })
                 ->addColumn('type', function ($colors) {
                    $type = '';
                    if(isset($colors->transportType)){
                       $type =$colors->transportType->translateOrNew('en')->name;
                    }
                    return $type;
                    
                })
                ->addColumn('make', function ($colors) {
                    $make = '';
                    if(isset($colors->transportMake)){

                        $make = $colors->transportMake->translateOrNew('en')->name;
                    }
                    return $make;
                    
                })
                ->addColumn('model', function ($colors) {
                    return $colors->transportModel->translateOrNew('en')->name;
                })
               
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('admin.transportModelColor.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $languages = Language::all();
        //$transportMakes = TransportMake::all();
        $transportTypes = TransportType::where('tt_status',1)->get();

        return view('admin.transportModelColor.create', ['languages' => $languages, 'transportTypes' => $transportTypes]);
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
                'model_id' => 'required|integer',
            ];
        } else {
            $validator_array = [
                 'type_id' => 'required|integer',
                 'make_id' => 'required|integer',
                'model_id' => 'required|integer'];
        }
        $validator = Validator::make($request->all(), $validator_array);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

       

        if ($id == NULL) {
            $tmc_order = TransportModelColor::max('id');
            $insert_id = TransportModelColor::create([
                'tmc_tt_ref_id' => $request->input('type_id'),
                'tmc_tm_ref_id' => $request->input('make_id'),
                'tmc_tmo_id_ref' => $request->input('model_id'),
                'tmc_order' => $tmc_order  + 1,
                'tmc_status'=>1,
                'tmc_created_by'=>auth()->guard('admin')->user()->id,
            ]);
            $languages = Language::all();
            foreach ($languages as $language) {
                TransportModelColorTranslation::create([
                    'name' => $request->input($language->language_code . '_name'),
                    'transport_model_color_id' => $insert_id->id,
                    'locale' => $language->language_code,
                ]);
            }
            return response()->json(['success' => true, 'message' => trans('adminMessages.model_inserted')]);
        } else {
            $languages = Language::all();
            TransportModelColor::where('id', $id)->update([
                'tmc_tt_ref_id' => $request->input('type_id'),
                'tmc_tm_ref_id' => $request->input('make_id'),
                'tmc_tmo_id_ref' => $request->input('model_id'),
                'tmc_updated_by'=>auth()->guard('admin')->user()->id,
            ]);

            foreach ($languages as $language) {
                TransportModelColorTranslation::updateOrCreate([
                    'transport_model_color_id' => $id,
                    'locale' => $language->language_code,
                ],
                    [
                        'transport_model_color_id' => $id,
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
        $modelColor = TransportModelColor::find($id);
        $transportTypes = TransportType::where('tt_status',1)->get();
        
        if ($modelColor) {
            $transportMakes = TransportMake::where('tm_type_ref_id',$modelColor->tmc_tt_ref_id)->get();
            $transportModels = TransportModel::where(['tmo_tm_id_ref'=>$modelColor->tmc_tm_ref_id,'tmo_tt_ref_id'=>$modelColor->tmc_tt_ref_id])->get();
            $languages = Language::all();
            return view('admin.transportModelColor.edit', [
                'transportModelColor' => $modelColor,
                'transportTypes'=>$transportTypes,
                'transportMakes' => $transportMakes,
                'transportModels'=>$transportModels,
                'languages' => $languages,
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
        TransportModelColor::where('id', $id)->delete();
        TransportModelColorTranslation::where('transport_model_color_id', $id)->delete();

        return response()->json(['success' => true, 'message' => trans('adminMessages.color_deleted')]);
    }

    public function getModel(Request $request)
    {
        $tmo_tm_id_ref = $request->input('tmo_tm_id_ref');

        $make_models = TransportModel::where('tmo_tm_id_ref', $tmo_tm_id_ref)->get();

        if (count($make_models) > 0) {
            echo "<option value=''>Please Select transport model</option>";
            foreach ($make_models as $make_model) {
                echo "<option value='" . $make_model->id . "'>" . $make_model->name . "</option>";
            }
        } else {
            echo "<option value=''>No Data Found</option>";
        }
    }
    public function getModelColor(Request $request)
    {
        $type_id = $request->input('type_id');
        $make_id = $request->input('make_id');
        $model_id = $request->input('model_id');

        $modelColors = TransportModelColor::where(['tmc_tt_ref_id'=>$type_id,'tmc_tm_ref_id'=>$make_id,'tmc_tmo_id_ref'=>$model_id])->get();

        if (count($modelColors) > 0) {
            echo "<option value=''>Please Select transport model Color</option>";
            foreach ($modelColors as $modelColor) {
                echo "<option value='" . $modelColor->id . "'>" . $modelColor->name . "</option>";
            }
        } else {
            echo "<option value=''>No Data Found</option>";
        }
    }
}
