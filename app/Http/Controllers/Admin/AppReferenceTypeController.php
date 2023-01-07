<?php

namespace App\Http\Controllers\Admin;

use App\AppReference;
use App\AppReferenceTranslation;
use App\AppReferenceType;
use App\AppReferenceTypeTranslation;
use App\Language;
use App\ReferenceModule;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class AppReferenceTypeController extends Controller
{
    /**
     * Display a listing of the AppReferenceType.
     *
     * @param Request $request
     * @return Factory|View
     * @throws Exception
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $references_types = AppReferenceType::leftJoin('base_app_references_modules', 'base_app_reference_types.bart_mod_id_ref', '=', 'base_app_references_modules.id')
                ->listsTranslations('name')
                ->select('base_app_reference_types.id', 'base_app_references_modules.barm_name', 'base_app_reference_types.bart_mod_id_ref', 'base_app_reference_types.bart_category_id_ref',
                    'base_app_reference_types.bart_icon', 'base_app_reference_types.bart_image', 'base_app_reference_types.bart_status', 'base_app_reference_types.bart_order_by')
                ->get();
            return Datatables::of($references_types)
                ->addColumn('action', function ($references_types) {
                    $edit_button = '<a href="' . route('admin::referenceType.edit', [$references_types->id]) . '" class="btn btn-sm btn-outline-info waves-effect waves-light" data-toggle="tooltip" data-placement="top" title="Edit"><i class="bx bx-pencil font-size-16 align-middle"></i></a>';
                    $delete_button = '<button data-id="' . $references_types->id . '" class="delete-single btn btn-sm btn-outline-danger waves-effect waves-light" data-toggle="tooltip" data-placement="top" title="Delete"><i class="bx bx-trash font-size-16 align-middle"></i></button>';
                    return $edit_button . " " . $delete_button;
                })->addColumn('status', function ($references_types) {
                    if ($references_types->bart_status == 1) {
                        $class = "badge badge-success";
                        $name = "Active";
                    }
                    if ($references_types->bart_status == 0) {
                        $class = "badge badge-warning";
                        $name = "Inactive";
                    }
                    $status_button = '<a type="button" onclick="updatestatus(' . $references_types->id . ',' . $references_types->bart_status . ')" class="' . $class . '" data-toggle="tooltip" data-placement="top" title="' . $name . '">' . $name . '</a>';
                    return $status_button;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }
        return view('admin.ReferenceType.index');
    }

    /**
     * Show the form for creating a new AppReferenceType.
     *
     * @return Factory|View
     */
    public function create()
    {
        $languages = Language::where('status', 1)->get();
        $ref_modules = ReferenceModule::all();
        $refs = AppReferenceType::all();
        return view('admin.ReferenceType.create', ['languages' => $languages, 'ref_module' => $ref_modules, 'refs' => $refs]);
    }

    /**
     * Store a newly created AppReferenceType in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $id = $request->input('edit_value');
        if ($id == null) {
            $referencetype = new AppReferenceType;
        } else {
            $referencetype = AppReferenceType::where('id', $id)->first();
        }
        if ($request->hasFile('bart_icon')) {
            @unlink(public_path() . '/' . $referencetype->bart_icon);
            $mime = $request->bart_icon->getMimeType();
            $image = $request->file('bart_icon');
            $image_name = preg_replace('/\s+/', '', $image->getClientOriginalName());
            $ImageName = time() . '-' . $image_name;
            $image->move('./assets/ReferenceType/Icons/', $ImageName);
            $referencetype->bart_icon = 'assets/ReferenceType/Icons/' . $ImageName;
        }
        if ($request->hasFile('bart_image')) {
            @unlink(public_path() . '/' . $referencetype->bart_image);
            $mime = $request->bart_image->getMimeType();
            $image = $request->file('bart_image');
            $image_name = preg_replace('/\s+/', '', $image->getClientOriginalName());
            $ImageName = time() . '-' . $image_name;
            $image->move('./assets/ReferenceType/Images/', $ImageName);
            $referencetype->bart_image = 'assets/ReferenceType/Images/' . $ImageName;
        }
        $referencetype->bart_mod_id_ref = $request->input('bart_mod_id_ref');
        $referencetype->bart_order_by = $request->input('bart_order_by');
        $referencetype->bart_created_at = now();
        $referencetype->bart_updated_at = now();

        $referencetype->save();
        $languages = Language::where('status', 1)->get();
        foreach ($languages as $language) {
            AppReferenceTypeTranslation::updateOrCreate([
                'app_reference_type_id' => $referencetype->id,
                'locale' => $language->language_code,
            ],
                [
                    'app_reference_type_id' => $referencetype->id,
                    'locale' => $language->language_code,
                    'name' => $request->input($language->language_code . '_name')
                ]);
        }
        return response()->json(['success' => true, 'message' => 'Language Screen is successfully Uploaded']);
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
     * Show the form for editing the specified AppReferenceType.
     *
     * @param int $id
     * @return Factory|View
     */
    public function edit($id)
    {
        $app_ref = AppReferenceType::find($id);
        $ref_modules = ReferenceModule::all();
        $refs = AppReferenceType::all();
        if ($ref_modules) {
            $languages = Language::where('status', 1)->get();;
            return view('admin.ReferenceType.edit', ['app_ref' => $app_ref, 'languages' => $languages, 'ref_module' => $ref_modules, 'refs' => $refs]);
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

    /**Change the Status for AppReferenceType
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
        AppReferenceType::where('id', $id)->update(['bart_status' => $status_new]);
        return response()->json(['success' => true, 'message' => 'page status is successfully Updated']);
    }
}
