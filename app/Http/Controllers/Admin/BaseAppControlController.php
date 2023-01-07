<?php

namespace App\Http\Controllers\Admin;

use App\BaseAppControl;
use App\BaseAppTheme;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class BaseAppControlController extends Controller
{
    /**
     * Display a listing of the BaseAppControls.
     *
     * @param Request $request
     * @return Factory|View
     * @throws Exception
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $base_app_control = BaseAppControl::select('base_app_controls.id as id', 'base_app_controls.bac_meta_key', 'base_app_controls.bac_meta_value', 'base_app_controls.bac_control_error_message'
                , 'base_app_controls.bac_status')
                ->get();

            return Datatables::of($base_app_control)
                ->addColumn('action', function ($base_app_control) {
                    $edit_button = '<a href="' . route('admin::BaseAppControl.edit', [$base_app_control->id]) . '" class="btn btn-sm btn-outline-info waves-effect waves-light" data-toggle="tooltip" data-placement="top" title="Edit"><i class="bx bx-pencil font-size-16 align-middle"></i></a>';
                    return $edit_button;
                })
                ->rawColumns(['action'])
                ->addColumn('status', function ($base_app_control) {
                    if ($base_app_control->bac_status == 1) {
                        $class = "badge badge-success";
                        $name = "Active";
                    }
                    if ($base_app_control->bac_status == 0) {
                        $class = "badge badge-warning";
                        $name = "Inactive";
                    }
                    $status_button = '<a type="button" onclick="updatestatus(' . $base_app_control->id . ',' . $base_app_control->bac_status . ')" class="' . $class . '" data-toggle="tooltip" data-placement="top" title="Edit">' . $name . '</a>';
                    return $status_button;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }
        return view('admin.BaseAppControl.index');
    }

    /**
     * Show the form for creating a new BaseAppControl.
     *
     * @return Factory|View
     */
    public function create()
    {
        return view('admin.BaseAppControl.create');
    }

    /**
     * Store a newly created BaseAppControls in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $id = $request->input('edit_value');
        if ($id == null) {
            $app_control = new BaseAppControl;
        } else {

            $app_control = BaseAppControl::where('id', $id)->first();
        }

        $app_control->bac_meta_key = $request->input('bac_meta_key');
        $app_control->bac_meta_value = $request->input('bac_meta_value');
        $app_control->bac_control_error_message = $request->input('bac_control_error_message');
        $app_control->bac_created_at = now();
        $app_control->bac_updated_at = now();
        $app_control->save();

        return response()->json(['success' => true, 'message' => 'App Control is Successfully Created']);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return void
     */
    public function show($id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Factory|View
     */
    public function edit($id)
    {
        $base_app_Control = BaseAppControl::where('id', $id)->first();
        if ($base_app_Control) {
            return view('admin.BaseAppControl.edit', ['base_app_Control' => $base_app_Control]);
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
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }

    /**Change for the Status For BaseAppControls
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
        BaseAppControl::where('id', $id)->update(['bac_status' => $status_new]);
        return response()->json(['success' => true, 'message' => 'Image status is successfully Updated']);
    }
}
