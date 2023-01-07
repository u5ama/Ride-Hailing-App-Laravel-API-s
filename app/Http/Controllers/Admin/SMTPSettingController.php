<?php

namespace App\Http\Controllers\Admin;

use App\AppReference;
use App\AppSmtpSetting;
use App\BaseAppControl;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class SMTPSettingController extends Controller
{
    /**
     * Display a listing of the SMTPSetting.
     *
     * @param Request $request
     * @return Factory|View
     * @throws Exception
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $app_smtps = AppSmtpSetting::select('app_smtp_settings.id as id', 'app_smtp_settings.MAIL_DRIVER', 'app_smtp_settings.MAIL_HOST', 'app_smtp_settings.MAIL_PORT'
                , 'app_smtp_settings.MAIL_USERNAME', 'app_smtp_settings.MAIL_PASSWORD', 'app_smtp_settings.MAIL_ENCRYPTION', 'app_smtp_settings.MAIL_FROM_ADDRESS', 'app_smtp_settings.smtp_status')
                ->get();

            return Datatables::of($app_smtps)
                ->addColumn('action', function ($app_smtps) {
                    $edit_button = '<a href="' . route('admin::SMTPSetting.edit', [$app_smtps->id]) . '" class="btn btn-sm btn-outline-info waves-effect waves-light" data-toggle="tooltip" data-placement="top" title="Edit"><i class="bx bx-pencil font-size-16 align-middle"></i></a>';
                    $delete_button = '<button data-id="' . $app_smtps->id . '" class="delete-single btn btn-sm btn-outline-danger waves-effect waves-light" data-toggle="tooltip" data-placement="top" title="Delete"><i class="bx bx-trash font-size-16 align-middle"></i></button>';
                    return $edit_button . " " . $delete_button;
                })->rawColumns(['action'])
                ->addColumn('status', function ($app_smtps) {
                    if ($app_smtps->smtp_status == 1) {
                        $class = "badge badge-success";
                        $name = "Active";
                    }
                    if ($app_smtps->smtp_status == 0) {
                        $class = "badge badge-warning";
                        $name = "Inactive";
                    }
                    $status_button = '<a type="button" onclick="updatestatus(' . $app_smtps->id . ',' . $app_smtps->smtp_status . ')" class="' . $class . '" data-toggle="tooltip" data-placement="top" title="Edit">' . $name . '</a>';
                    return $status_button;
                })->rawColumns(['action', 'status'])
                ->make(true);
        }
        return view('admin.SMTPSetting.index');
    }

    /**
     * Show the form for creating a new SMTPSetting.
     *
     * @return Factory|View
     */
    public function create()
    {
        return view('admin.SMTPSetting.create');
    }

    /**
     * Store a newly created SMTPSetting in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $id = $request->input('edit_value');
        if ($id == null) {
            $app_SMTP = new AppSmtpSetting;
        } else {

            $app_SMTP = AppSmtpSetting::where('id', $id)->first();
        }
        $app_SMTP->MAIL_DRIVER = $request->input('MAIL_DRIVER');
        $app_SMTP->MAIL_HOST = $request->input('MAIL_HOST');
        $app_SMTP->MAIL_USERNAME = $request->input('MAIL_USERNAME');
        $app_SMTP->MAIL_PORT = $request->input('MAIL_PORT');
        $app_SMTP->MAIL_PASSWORD = $request->input('MAIL_PASSWORD');
        $app_SMTP->MAIL_ENCRYPTION = $request->input('MAIL_ENCRYPTION');
        $app_SMTP->MAIL_FROM_ADDRESS = $request->input('MAIL_FROM_ADDRESS');
        $app_SMTP->created_at = now();
        $app_SMTP->updated_at = now();
        $app_SMTP->save();

        return response()->json(['success' => true, 'message' => 'SMTP Credential is Successfully Created']);
    }

    /**
     * Display the specified SMTPSetting.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified SMTPSetting.
     *
     * @param int $id
     * @return Factory|View
     */
    public function edit($id)
    {
        $base_app_SMTP = AppSmtpSetting::where('id', $id)->first();
        if ($base_app_SMTP) {
            return view('admin.SMTPSetting.edit', ['base_app_SMTP' => $base_app_SMTP]);
        } else {
            abort(404);
        }
    }

    /**
     * Update the specified SMTPSetting in storage.
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
     * Remove the specified SMTPSetting from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Change the status for SMTPSetting
     * @param $id
     * @param $status
     * @return \Illuminate\Http\JsonResponse
     */
    public function status($id, $status)
    {
        $base_image1 = AppSmtpSetting::where('id', '>', 0)->update(['smtp_status' => 0]);
        if ($status == 1) {
            $status_new = 0;
        }
        if ($status == 0) {
            $status_new = 1;
        }
        AppSmtpSetting::where('id', $id)->update(['smtp_status' => $status_new]);
        return response()->json(['success' => true, 'message' => 'SMTP status is successfully Updated']);
    }
}
