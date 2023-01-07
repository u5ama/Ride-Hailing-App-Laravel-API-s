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

class AppReferenceController extends Controller
{
    /**
     * Display a listing of the App References.
     *
     * @param Request $request
     * @return Factory|View
     * @throws Exception
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $references = AppReference::leftJoin('base_app_references_modules', 'base_app_references.bar_mod_id_ref', '=', 'base_app_references_modules.id')
                ->listsTranslations('name')
                ->select('base_app_references.id', 'base_app_references_modules.barm_name', 'base_app_references.bar_mod_id_ref', 'base_app_references.bar_ref_type_id',
                    'base_app_references.bar_icon', 'base_app_references.bar_image', 'base_app_references.bar_status', 'base_app_references.bar_order_by')
                ->get();
            foreach ($references as $reference) {
                $ref_type = AppReferenceType::listsTranslations('name')->where('base_app_reference_types.id', $reference->bar_ref_type_id)->first();

                if ($ref_type != null) {
                    $reference['ref_type_name'] = $ref_type->name;
                } else {
                    $reference['ref_type_name'] = "";
                }
            }
            return Datatables::of($references)
                ->addColumn('action', function ($references) {
                    $edit_button = '<a href="' . route('admin::appReference.edit', [$references->id]) . '" class="btn btn-sm btn-outline-info waves-effect waves-light" data-toggle="tooltip" data-placement="top" title="Edit"><i class="bx bx-pencil font-size-16 align-middle"></i></a>';
                    $delete_button = '<button data-id="' . $references->id . '" class="delete-single btn btn-sm btn-outline-danger waves-effect waves-light" data-toggle="tooltip" data-placement="top" title="Delete"><i class="bx bx-trash font-size-16 align-middle"></i></button>';
                    return $edit_button . " " . $delete_button;
                })->addColumn('status', function ($references) {
                    if ($references->bar_status == 1) {
                        $class = "badge badge-success";
                        $name = "Active";
                    }
                    if ($references->bar_status == 0) {
                        $class = "badge badge-warning";
                        $name = "Inactive";
                    }
                    $status_button = '<a type="button" onclick="updatestatus(' . $references->id . ',' . $references->bar_status . ')" class="' . $class . '" data-toggle="tooltip" data-placement="top" title="' . $name . '">' . $name . '</a>';
                    return $status_button;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }
        return view('admin.AppReference.index');
    }

    /**
     * Show the form for creating a new APp Reference.
     *
     * @return Response
     */
    public function create()
    {
        $languages = Language::where('status', 1)->get();
        $ref_modules = ReferenceModule::all();
        $refs = AppReference::all();
        $refsTypes = AppReferenceType::listsTranslations('name')->get();
        return view('admin.AppReference.create', ['languages' => $languages, 'refsTypes' => $refsTypes, 'ref_module' => $ref_modules, 'refs' => $refs]);
    }

    /**
     * Store a newly created App Reference in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $id = $request->input('edit_value');
        if ($id == null) {
            $reference = new AppReference;
        } else {
            $reference = AppReference::where('id', $id)->first();
        }
        if ($request->hasFile('bar_icon')) {
            @unlink(public_path() . '/' . $reference->bar_icon);
            $mime = $request->bar_icon->getMimeType();
            $image = $request->file('bar_icon');
            $image_name = preg_replace('/\s+/', '', $image->getClientOriginalName());
            $ImageName = time() . '-' . $image_name;
            $image->move('./assets/Reference/Icons/', $ImageName);
            $reference->bar_icon = 'assets/Reference/Icons/' . $ImageName;
            $reference->bar_image_unselected = 'assets/Reference/Icons/' . $ImageName;
        }
        if ($request->hasFile('bar_image')) {
            @unlink(public_path() . '/' . $reference->bar_image);
            $mime = $request->bar_image->getMimeType();
            $image = $request->file('bar_image');
            $image_name = preg_replace('/\s+/', '', $image->getClientOriginalName());
            $ImageName = time() . '-' . $image_name;
            $image->move('./assets/Reference/Images/', $ImageName);
            $reference->bar_image = 'assets/Reference/Images/' . $ImageName;
            $reference->bar_icon_unselected = 'assets/Reference/Images/' . $ImageName;
        }
        $reference->bar_mod_id_ref = $request->input('bar_mod_id_ref');
        $reference->bar_ref_type_id = $request->input('bar_ref_type_id');
        $reference->bar_order_by = $request->input('bar_order_by');
        $reference->bar_created_at = now();
        $reference->bar_updated_at = now();

        $reference->save();
        $languages = Language::where('status', 1)->get();
        foreach ($languages as $language) {
            AppReferenceTranslation::updateOrCreate([
                'app_reference_id' => $reference->id,
                'locale' => $language->language_code,
            ],
                [
                    'app_reference_id' => $reference->id,
                    'locale' => $language->language_code,
                    'name' => $request->input($language->language_code . '_name')
                ]);
        }
        return response()->json(['success' => true, 'message' => 'App Reference is successfully Uploaded']);
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
     * Show the form for editing the specified App Reference.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $app_ref = AppReference::find($id);
        $ref_modules = ReferenceModule::all();
        $refs = AppReference::all();

        $refs = AppReference::where(['bar_mod_id_ref'=> $app_ref->bar_mod_id_ref,'bar_ref_type_id'=>$app_ref->bar_ref_type_id])->get();

        


        $refsTypes = AppReferenceType::where('bart_mod_id_ref', $app_ref->bar_mod_id_ref)->get();
        if ($ref_modules) {
            $languages = Language::where('status', 1)->get();;
            return view('admin.AppReference.edit', ['app_ref' => $app_ref, 'languages' => $languages, 'ref_module' => $ref_modules, 'refs' => $refs, 'refsTypes' => $refsTypes]);
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
     * Remove the specified App Reference from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        AppReference::where('id', $id)->delete();
        AppReferenceTranslation::where('app_reference_id', $id)->delete();
        return response()->json(['success' => true, 'message' => 'App Reference is successfully Deleted']);
    }

    /** Method For changing App Reference Status
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
        AppReference::where('id', $id)->update(['bar_status' => $status_new]);
        return response()->json(['success' => true, 'message' => 'App Reference status is successfully Updated']);
    }

    public function getReferenceType(Request $request){

         $module_id = $request->input('module_id');

        $referenceType = AppReferenceType::where('bart_mod_id_ref', $module_id)->get();

        if (count($referenceType) > 0) {
            echo "<option value=''>Please select App Reference Type</option>";
            foreach ($referenceType as $type) {
                echo "<option value='" . $type->id . "'>" . $type->name . "</option>";
            }
        } else {
            echo "<option value=''>No Data Found</option>";
        }
    }

    public function getReferenceOrder(Request $request){

         $module_id = $request->input('module_id');
         $ref_type_id = $request->input('ref_type_id');

        $getmaxid = AppReference::where(['bar_mod_id_ref'=> $module_id,'bar_ref_type_id'=>$ref_type_id])->max('id');
       if($getmaxid){
        $get_order = AppReference::where('id', $getmaxid)->first();

        $orderby = $get_order->bar_order_by+1;
       }else{
        $orderby = 1;
       }
        

        echo "<option value='" . $orderby . "'>" . $orderby . "</option>";
    }


}
