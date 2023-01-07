<?php

namespace App\Http\Controllers\Admin;

use App\AppSmtpSetting;
use App\FCMSetting;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class FCMSettingController extends Controller
{
    /**
     * Display a listing of the FCMSetting.
     *
     * @param Request $request
     * @return Factory|View
     * @throws Exception
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $app_FCM = FCMSetting::select('f_c_m_settings.id as id', 'f_c_m_settings.project_name', 'f_c_m_settings.FCM_SERVER_KEY', 'f_c_m_settings.FCM_SENDER_ID'
                , 'f_c_m_settings.FCM_status')
                ->get();
            return Datatables::of($app_FCM)
                ->addColumn('action', function ($app_FCM) {
                    $edit_button = '<a href="' . route('admin::FCMSetting.edit', [$app_FCM->id]) . '" class="btn btn-sm btn-outline-info waves-effect waves-light" data-toggle="tooltip" data-placement="top" title="Edit"><i class="bx bx-pencil font-size-16 align-middle"></i></a>';
                    $delete_button = '<button data-id="' . $app_FCM->id . '" class="delete-single btn btn-sm btn-outline-danger waves-effect waves-light" data-toggle="tooltip" data-placement="top" title="Delete"><i class="bx bx-trash font-size-16 align-middle"></i></button>';
                    return $edit_button . " " . $delete_button;
                })->rawColumns(['action'])
                ->addColumn('status', function ($app_FCM) {
                    if ($app_FCM->FCM_status == 1) {
                        $class = "badge badge-success";
                        $name = "Active";
                    }
                    if ($app_FCM->FCM_status == 0) {
                        $class = "badge badge-warning";
                        $name = "Inactive";
                    }
                    $status_button = '<a type="button" onclick="updatestatus(' . $app_FCM->id . ',' . $app_FCM->FCM_status . ')" class="' . $class . '" data-toggle="tooltip" data-placement="top" title="Edit">' . $name . '</a>';
                    return $status_button;
                })->rawColumns(['action', 'status'])
                ->make(true);
        }
        return view('admin.FCMSetting.index');
    }

    /**
     * Show the form for creating a new FCMSetting.
     *
     * @return Factory|View
     */
    public function create()
    {
        return view('admin.FCMSetting.create');
    }

    /**
     * Store a newly created FCMSetting in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $id = $request->input('edit_value');
        if ($id == null) {
            $app_FCM = new FCMSetting();
        } else {

            $app_FCM = FCMSetting::where('id', $id)->first();
        }

        $app_FCM->project_name = $request->input('project_name');
        $app_FCM->FCM_SERVER_KEY = $request->input('FCM_SERVER_KEY');
        $app_FCM->FCM_SENDER_ID = $request->input('FCM_SENDER_ID');
        $app_FCM->created_at = now();
        $app_FCM->updated_at = now();
        $app_FCM->save();

        return response()->json(['success' => true, 'message' => 'FCM Credential is Successfully Created']);
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
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Factory|View
     */
    public function edit($id)
    {
        $base_app_FCM = FCMSetting::where('id', $id)->first();
        if ($base_app_FCM) {
            return view('admin.FCMSetting.edit', ['base_app_FCM' => $base_app_FCM]);
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
     * @return void
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Change the status for FCMSetting
     * @param $id
     * @param $status
     * @return JsonResponse
     */
    public function status($id, $status)
    {
        $base_image1 = FCMSetting::where('id', '>', 0)->update(['FCM_status' => 0]);
        if ($status == 1) {
            $status_new = 0;
        }
        if ($status == 0) {
            $status_new = 1;
        }
        FCMSetting::where('id', $id)->update(['FCM_status' => $status_new]);
        return response()->json(['success' => true, 'message' => 'FCM status is successfully Updated']);
    }
}
