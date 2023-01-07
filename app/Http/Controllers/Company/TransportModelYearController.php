<?php

namespace App\Http\Controllers\Company;


use App\TransportModelYear;
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

class TransportModelYearController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            //DB::enableQueryLog();
            $model_year = TransportModelYear::with('transportType','transportMake','transportModel','transportModelColor')
            ->select('transport_model_years.id','transport_model_years.tmy_name', 'transport_model_years.tmy_tt_ref_id','transport_model_years.tmy_tm_ref_id','transport_model_years.tmy_tmo_ref_id','transport_model_years.tmc_tmo_id_ref')
            ->get();
            // dd(DB::getQueryLog());
            return Datatables::of($model_year)
                ->addColumn('action', function ($model_year) {
                    $edit_button = '<a href="' . route('admin::transportModelYear.edit', [$model_year->id]) . '" class="btn btn-sm btn-outline-info waves-effect waves-light" data-toggle="tooltip" data-placement="top" title="Edit"><i class="bx bx-pencil font-size-16 align-middle"></i></a>';
                    $delete_button = '<button data-id="' . $model_year->id . '" class="delete-single btn btn-sm btn-outline-danger waves-effect waves-light" data-toggle="tooltip" data-placement="top" title="Delete"><i class="bx bx-trash font-size-16 align-middle"></i></button>';
                    return $edit_button . ' ' . $delete_button;
                })
                ->addColumn('type', function ($model_year) {
                    $type = '';
                    if(isset($model_year->transportType)){
                       $type =$model_year->transportType->translateOrNew('en')->name;
                    }
                    return $type;
                    
                })
                ->addColumn('make', function ($model_year) {
                    $make = '';
                    if(isset($model_year->transportMake)){

                        $make = $model_year->transportMake->translateOrNew('en')->name;
                    }
                    return $make;
                    
                })
                ->addColumn('model', function ($model_year) {
                    $model  = '';
                    if(isset($model_year->transportModel)){
                       $model = $model_year->transportModel->translateOrNew('en')->name; 
                    }
                    return $model ; 
                })
                ->addColumn('model_color', function ($model_year) {
                    $model_color  = '';
                    if(isset($model_year->transportModelColor)){
                       $model_color = $model_year->transportModelColor->translateOrNew('en')->name; 
                    }
                    return $model_color ; 
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('admin.transportModelYear.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
          $transportTypes = TransportType::where('tt_status',1)->get();
        return view('admin.transportModelYear.create', ['transportTypes' => $transportTypes]);
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
            $tmy_order = TransportModelYear::max('id');
            $insert_id = TransportModelYear::create([
                'tmy_tt_ref_id' => $request->input('type_id'),
                'tmy_tm_ref_id' => $request->input('make_id'),
                'tmy_tmo_ref_id' => $request->input('model_id'),
                'tmc_tmo_id_ref' => $request->input('model_color_id'),
                'tmy_name' => $request->input('name'),
                'tmy_order' => $tmy_order  + 1,
                'tmy_status'=>1,
                'tmy_created_by'=>auth()->guard('admin')->user()->id,
            ]);
            
            return response()->json(['success' => true, 'message' => trans('adminMessages.model_year_inserted')]);
        } else {
            
            TransportModelYear::where('id', $id)->update([
                'tmy_tt_ref_id' => $request->input('type_id'),
                'tmy_tm_ref_id' => $request->input('make_id'),
                'tmy_tmo_ref_id' => $request->input('model_id'),
                'tmc_tmo_id_ref' => $request->input('model_color_id'),
                'tmy_name' => $request->input('name'),
                'tmy_updated_by'=>auth()->guard('admin')->user()->id,
            ]);

            
            return response()->json(['success' => true, 'message' => trans('adminMessages.model_year_updated')]);
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
        $modelYear = TransportModelYear::find($id);
        $transportTypes = TransportType::where('tt_status',1)->get();
        
        if ($modelYear) {
            $transportMakes = TransportMake::where('tm_type_ref_id',$modelYear->tmy_tt_ref_id)->get();
            $transportModels = TransportModel::where(['tmo_tm_id_ref'=>$modelYear->tmy_tm_ref_id,'tmo_tt_ref_id'=>$modelYear->tmy_tt_ref_id])->get();

            $transportModelColors = TransportModelColor::where(['tmc_tm_ref_id'=>$modelYear->tmy_tm_ref_id,'tmc_tt_ref_id'=>$modelYear->tmy_tt_ref_id,'tmc_tmo_id_ref'=>$modelYear->tmy_tmo_ref_id])->get();

            $languages = Language::all();
            return view('admin.transportModelYear.edit', [
                'transportModelYear' => $modelYear,
                'transportTypes'=>$transportTypes,
                'transportMakes' => $transportMakes,
                'transportModels'=>$transportModels,
                'transportModelColors'=>$transportModelColors,
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
        TransportModelYear::where('id', $id)->delete();

        return response()->json(['success' => true, 'message' => trans('adminMessages.model_year_deleted')]);
    }


     public function getModelYear(Request $request)
    {
        $type_id = $request->input('type_id');
        $make_id = $request->input('make_id');
        $model_id = $request->input('model_id');
        $model_color_id = $request->input('model_color_id');

        $modelYears = TransportModelYear::where(['tmy_tt_ref_id'=>$type_id,'tmy_tm_ref_id'=>$make_id,'tmy_tmo_ref_id'=>$model_id,'tmc_tmo_id_ref'=>$model_color_id])->get();

        if (count($modelYears) > 0) {
            echo "<option value=''>Please Select transport model Year</option>";
            foreach ($modelYears as $modelYear) {
                echo "<option value='" . $modelYear->id . "'>" . $modelYear->tmy_name . "</option>";
            }
        } else {
            echo "<option value=''>No Data Found</option>";
        }
    }
}
