<?php

namespace App\Http\Controllers\Admin;

use App\BaseAppTheme;
use App\BaseAppThemeDesign;
use App\BaseDefaultImage;
use App\LanguageScreen;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;
use Auth;

class BaseAppThemeDesignController extends Controller
{
    /**
     * Display a listing of the BaseAppThemeDesign.
     *
     * @param Request $request
     * @return Factory|View
     * @throws \Exception
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $base_app_theme_design = BaseAppThemeDesign::leftJoin('admin_users', 'base_app_themes_design.batd_created_by', '=', 'admin_users.id')->select('base_app_themes_design.id as id', 'base_app_themes_design.batd_screen_info', 'base_app_themes_design.batd_device_type', 'admin_users.name'
                , 'base_app_themes_design.batd_design_key_field', 'base_app_themes_design.batd_design_value', 'base_app_themes_design.batd_color_code', 'base_app_themes_design.batd_description', 'base_app_themes_design.batd_status')
                ->get();
            return Datatables::of($base_app_theme_design)
                ->addColumn('action', function ($base_app_theme_design) {
                    $edit_button = '<a href="' . route('admin::BaseAppThemeDesign.edit', [$base_app_theme_design->id]) . '" class="btn btn-sm btn-outline-info waves-effect waves-light" data-toggle="tooltip" data-placement="top" title="Edit"><i class="bx bx-pencil font-size-16 align-middle"></i></a>';
                    return $edit_button;
                })
                ->rawColumns(['action'])
                ->addColumn('status', function ($base_app_theme_design) {
                    if ($base_app_theme_design->batd_status == 1) {
                        $class = "badge badge-success";
                        $name = "Active";
                    }
                    if ($base_app_theme_design->batd_status == 0) {
                        $class = "badge badge-warning";
                        $name = "Inactive";
                    }
                    $status_button = '<a type="button" onclick="updatestatus(' . $base_app_theme_design->id . ',' . $base_app_theme_design->batd_status . ')" class="' . $class . '" data-toggle="tooltip" data-placement="top" title="Edit">' . $name . '</a>';
                    return $status_button;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }
        return view('admin.BaseAppThemeDesign.index');
    }

    /**
     * Show the form for creating a new BaseAppThemeDesign.
     *
     * @return Factory|View
     */
    public function create()
    {
        $themes = BaseAppTheme::all();
        $language_screens = LanguageScreen::all();
        return view('admin.BaseAppThemeDesign.create', ['themes' => $themes, 'language_screens' => $language_screens,]);
    }

    /**
     * Store a newly created BaseAppThemeDesign in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $id = $request->input('edit_value');
        if ($id == null) {
            $app_theme_design = new BaseAppThemeDesign;
        } else {

            $app_theme_design = BaseAppThemeDesign::where('id', $id)->first();
        }
        $app_theme_design->batd_theme_ref_id = $request->input('batd_theme_ref_id');
        $app_theme_design->batd_language_screen_ref_id = $request->input('batd_language_screen_ref_id');
        $app_theme_design->batd_screen_info = $request->input('batd_screen_info');
        $app_theme_design->batd_device_type = $request->input('batd_device_type');
        $app_theme_design->batd_design_key_field = $request->input('batd_design_key_field');
        $app_theme_design->batd_design_value = $request->input('batd_design_value');
        $app_theme_design->batd_color_code = $request->input('batd_color_code');
        $app_theme_design->batd_description = $request->input('batd_description');
        $app_theme_design->batd_created_by = \Auth::guard('admin')->user()->id;
        $app_theme_design->batd_created_at = now();
        $app_theme_design->batd_updated_at = now();
        $app_theme_design->save();

        return response()->json(['success' => true, 'message' => 'Theme Design is successfully Uploaded']);
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
     * Show the form for editing the specified BaseAppThemeDesign.
     *
     * @param int $id
     * @return Factory|View
     */
    public function edit($id)
    {
        $themes = BaseAppTheme::all();
        $base_app_theme_design = BaseAppThemeDesign::where('id', $id)->first();
        $language_screens = LanguageScreen::all();
        if ($base_app_theme_design) {
            return view('admin.BaseAppThemeDesign.edit', ['base_app_theme_design' => $base_app_theme_design, 'themes' => $themes, 'language_screens' => $language_screens]);
        } else {
            abort(404);
        }
    }

    /**
     * Update the specified BaseAppThemeDesign in storage.
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

    /**Change the Status for BaseAppThemeDesign
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
        BaseAppThemeDesign::where('id', $id)->update(['batd_status' => $status_new]);
        return response()->json(['success' => true, 'message' => 'Theme Design status is successfully Updated']);
    }
}
