<?php

namespace App\Http\Controllers\Admin;

use App\BaseAppSocialLinks;
use App\ReferenceModule;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class ReferenceModuleController extends Controller
{
    /**
     * Display a listing of the ReferenceModule.
     *
     * @param Request $request
     * @return Factory|View
     * @throws \Exception
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $references_modules = ReferenceModule::all();
            return Datatables::of($references_modules)
                ->addColumn('action', function ($references_modules) {
                    $edit_button = '<a href="' . route('admin::referenceModule.edit', [$references_modules->id]) . '" class="btn btn-sm btn-outline-info waves-effect waves-light" data-toggle="tooltip" data-placement="top" title="Edit"><i class="bx bx-pencil font-size-16 align-middle"></i></a>';
                    $delete_button = '<button data-id="' . $references_modules->id . '" class="delete-single btn btn-sm btn-outline-danger waves-effect waves-light" data-toggle="tooltip" data-placement="top" title="Delete"><i class="bx bx-trash font-size-16 align-middle"></i></button>';
                    return $edit_button . " " . $delete_button;
                })->addColumn('status', function ($references_modules) {
                    if ($references_modules->barm_status == 1) {
                        $class = "badge badge-success";
                        $name = "Active";
                    }
                    if ($references_modules->barm_status == 0) {
                        $class = "badge badge-warning";
                        $name = "Inactive";
                    }
                    $status_button = '<a type="button" onclick="updatestatus(' . $references_modules->id . ',' . $references_modules->barm_status . ')" class="' . $class . '" data-toggle="tooltip" data-placement="top" title="' . $name . '">' . $name . '</a>';
                    return $status_button;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }
        return view('admin.ReferenceModule.index');
    }

    /**
     * Show the form for creating a new ReferenceModule.
     *
     * @return Factory|View
     */
    public function create()
    {
        return view('admin.ReferenceModule.create');
    }

    /**
     * Store a newly created ReferenceModule in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $id = $request->input('edit_value');
        if ($id == null) {
            $app_reference_module = new ReferenceModule;
        } else {

            $app_reference_module = ReferenceModule::where('id', $id)->first();
        }
        $app_reference_module->barm_name = $request->input('barm_name');
        $app_reference_module->barm_created_at = now();
        $app_reference_module->barm_updated_at = now();
        $app_reference_module->save();

        return response()->json(['success' => true, 'message' => 'Module is successfully Uploaded']);
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
     * Show the form for editing the specified ReferenceModule.
     *
     * @param int $id
     * @return Factory|View
     */
    public function edit($id)
    {
        $reference_module = ReferenceModule::where('id', $id)->first();
        if ($reference_module) {
            return view('admin.ReferenceModule.edit', ['reference_module' => $reference_module]);
        } else {
            abort(404);
        }
    }

    /**
     * Update the specified ReferenceModule in storage.
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
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return void
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Change the status for ReferenceModule
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
        ReferenceModule::where('id', $id)->update(['barm_status' => $status_new]);
        return response()->json(['success' => true, 'message' => 'Social Link status is successfully Updated']);
    }
}
