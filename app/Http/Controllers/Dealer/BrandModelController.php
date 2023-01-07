<?php

namespace App\Http\Controllers\Dealer;


use App\Brand;
use App\BrandModel;
use App\BrandModelTranslation;
use App\Language;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class BrandModelController extends Controller
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
            $models = BrandModel::listsTranslations('name')
                ->with('brand')
                ->select('brand_models.id', 'brand_models.brand_id', 'brand_models.image')
                ->get();
            //    dd(DB::getQueryLog());
            return Datatables::of($models)
                ->addColumn('action', function ($models) {
                    $edit_button = '<a href="' . route('admin::model.edit', [$models->id]) . '" class="btn btn-sm btn-outline-info waves-effect waves-light" data-toggle="tooltip" data-placement="top" title="Edit"><i class="bx bx-pencil font-size-16 align-middle"></i></a>';
                    $delete_button = '<button data-id="' . $models->id . '" class="delete-single btn btn-sm btn-outline-danger waves-effect waves-light" data-toggle="tooltip" data-placement="top" title="Delete"><i class="bx bx-trash font-size-16 align-middle"></i></button>';
                    return $edit_button . ' ' . $delete_button;
                })
                ->addColumn('brand', function ($models) {
                    return $models->brand->translateOrNew('en')->name;
                })
                ->addColumn('image', function ($models) {
                    $url = asset($models->image);
                    return "<img src='".$url."' style='width:100px' />";
                })
                ->rawColumns(['action', 'brand', 'image'])
                ->make(true);
        }
        return view('admin.model.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $languages = Language::all();
        $brands = Brand::all();

        return view('admin.model.create', ['languages' => $languages, 'brands' => $brands]);
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
                'brand_id' => 'required|integer',
            ];
        } else {
            $validator_array = ['brand_id' => 'required|integer'];
        }
        $validator = Validator::make($request->all(), $validator_array);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
        }

        $image_1_path = $file_1_name =  '';
        if ($request->hasFile('image')) {
            $image_1_path = 'uploads/' . date('Y') . '/' . date('m');
            $files = $request->file('image');

            if (!File::exists(public_path() . "/" . $image_1_path)) {
                File::makeDirectory(public_path() . "/" . $image_1_path, 0777, true);
            }

            $extension = $files->getClientOriginalExtension();
            $destination_path = public_path() . '/' . $image_1_path;
            $file_1_name = uniqid() . '.' . $extension;
            $files->move($destination_path, $file_1_name);

            if ($id != NULL) {
                BrandModel::where('id', $id)->update([
                    'image' => $image_1_path . '/' . $file_1_name,
                ]);
            }
        }

        if ($id == NULL) {
            $model_order = BrandModel::max('id');
            $insert_id = BrandModel::create([
                'brand_id' => $request->input('brand_id'),
                'model_order' => $model_order + 1,
                'image' => $image_1_path . '/' . $file_1_name,
            ]);
            $languages = Language::all();
            foreach ($languages as $language) {
                BrandModelTranslation::create([
                    'name' => $request->input($language->language_code . '_name'),
                    'brand_model_id' => $insert_id->id,
                    'locale' => $language->language_code,
                ]);
            }
            return response()->json(['success' => true, 'message' => trans('adminMessages.model_inserted')]);
        } else {
            $languages = Language::all();
            BrandModel::where('id', $id)->update([
                'brand_id' => $request->input('brand_id'),
            ]);

            foreach ($languages as $language) {
                BrandModelTranslation::updateOrCreate([
                    'brand_model_id' => $id,
                    'locale' => $language->language_code,
                ],
                    [
                        'brand_model_id' => $id,
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
        $model = BrandModel::find($id);
        if ($model) {
            $brands = Brand::all();
            $languages = Language::all();
            return view('admin.model.edit', [
                'model' => $model,
                'brands' => $brands,
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
        BrandModel::where('id', $id)->delete();

        return response()->json(['success' => true, 'message' => trans('adminMessages.model_deleted')]);
    }

    public function getModel(Request $request)
    {
        $brand_id = $request->input('brand_id');

        $brand_models = BrandModel::where('brand_id', $brand_id)->get();

        if (count($brand_models) > 0) {
            echo "<option value=''>Please Select model</option>";
            foreach ($brand_models as $brand_model) {
                echo "<option value='" . $brand_model->id . "'>" . $brand_model->name . "</option>";
            }
        } else {
            echo "<option value=''>No Data Found</option>";
        }
    }
}
