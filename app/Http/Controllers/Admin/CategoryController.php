<?php

namespace App\Http\Controllers\Admin;


use App\Category;
use App\CategoryTranslation;
use App\Language;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Config;

class CategoryController extends Controller
{
    /**
     * Display a listing of the Category.
     *
     * @param Request $request
     * @return Application|Factory|View
     * @throws Exception
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $categories = Category::listsTranslations('name')
                ->select('categories.id', 'categories.image')
                ->get();
            return Datatables::of($categories)
                ->addColumn('action', function ($categories) {
                    $edit_button = '<a href="' . route('admin::category.edit', [$categories->id]) . '" class="btn btn-sm btn-outline-info waves-effect waves-light" data-toggle="tooltip" data-placement="top" title="Edit"><i class="bx bx-pencil font-size-16 align-middle"></i></a>';
                    $delete_button = '<button data-id="' . $categories->id . '" class="delete-single btn btn-sm btn-outline-danger waves-effect waves-light" data-toggle="tooltip" data-placement="top" title="Delete"><i class="bx bx-trash font-size-16 align-middle"></i></button>';
                    return $edit_button . ' ' . $delete_button;
                })
                ->addColumn('image', function ($categories) {
                    $url = asset($categories->image);
                    return "<img src='" . $url . "' style='width:100px' />";
                })
                ->rawColumns(['action', 'image'])
                ->make(true);
        }
        return view('admin.category.index');
    }

    /**
     * Show the form for creating a new Category.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        $languages = Language::all();
        return view('admin.category.create', ['languages' => $languages]);
    }

    /**
     * Store a newly created Category in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $id = $request->input('edit_value');
        if ($id == NULL) {
            $validator_array = [
                'image' => 'required|image|mimes:jpeg,png,jpg',
            ];
            $validator = Validator::make($request->all(), $validator_array);
            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
            }
        }

        $image_path = $file_name = '';
        if ($request->hasFile('image')) {
            $image_path = 'uploads/' . date('Y') . '/' . date('m');
            $files = $request->file('image');

            if (!File::exists(public_path() . "/" . $image_path)) {
                File::makeDirectory(public_path() . "/" . $image_path, 0777, true);
            }

            $extension = $files->getClientOriginalExtension();
            $destination_path = public_path() . '/' . $image_path;
            $file_name = uniqid() . '.' . $extension;
            $files->move($destination_path, $file_name);

            if ($id != NULL) {
                Category::where('id', $id)->update([
                    'image' => $image_path . '/' . $file_name,
                ]);
            }
        }

        if ($id == NULL) {
            $category_order = Category::max('id');
            $insert_id = Category::create([
                'image' => $image_path . '/' . $file_name,
                'category_order' => $category_order + 1,
            ]);
            $languages = Language::all();
            foreach ($languages as $language) {
                CategoryTranslation::create([
                    'name' => $request->input($language->language_code . '_name'),
                    'category_id' => $insert_id->id,
                    'locale' => $language->language_code,
                ]);
            }
            return response()->json(['success' => true, 'message' => trans('adminMessages.category_inserted')]);
        } else {
            $languages = Language::all();
            foreach ($languages as $language) {
                CategoryTranslation::updateOrCreate([
                    'category_id' => $id,
                    'locale' => $language->language_code,
                ],
                    [
                        'category_id' => $id,
                        'locale' => $language->language_code,
                        'name' => $request->input($language->language_code . '_name')
                    ]);
            }
            return response()->json(['success' => true, 'message' => trans('adminMessages.category_updated')]);
        }
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
     * Show the form for editing the specified Category.
     *
     * @param int $id
     * @return Application|Factory|View
     */
    public function edit($id)
    {
        $category = Category::find($id);
        if ($category) {
            $languages = Language::all();
            return view('admin.category.edit', ['category' => $category, 'languages' => $languages]);
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
     * Remove the specified Category from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        Category::where('id', $id)->delete();
        return response()->json(['success' => true, 'message' => trans('adminMessages.category_deleted')]);
    }
}
