<?php

namespace App\Http\Controllers\Admin;

use App\BaseAppTheme;
use App\BaseDefaultImage;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;
use Auth;

class BaseAppThemeController extends Controller
{
    /**
     * Display a listing of the BaseAppTheme.
     *
     * @param Request $request
     * @return Factory|View
     * @throws Exception
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $base_app_theme = BaseAppTheme::leftJoin('admin_users', 'base_app_themes.bat_created_by', '=', 'admin_users.id')->select('base_app_themes.id as id', 'base_app_themes.bat_theme_name', 'base_app_themes.bat_theme_description', 'admin_users.name'
                , 'base_app_themes.bat_status')
                ->get();

        return Datatables::of($base_app_theme)
            ->addColumn('action', function ($base_app_theme) {
                $edit_button = '<a href="' . route('admin::BaseAppTheme.edit', [$base_app_theme->id]) . '" class="btn btn-sm btn-outline-info waves-effect waves-light" data-toggle="tooltip" data-placement="top" title="Edit"><i class="bx bx-pencil font-size-16 align-middle"></i></a>';
                return $edit_button;
            })->rawColumns(['action'])
            ->addColumn('status', function ($base_app_theme) {
                if ($base_app_theme->bat_status == 1) {
                    $class = "badge badge-success";
                    $name = "Active";
                }
                if ($base_app_theme->bat_status == 0) {
                    $class = "badge badge-warning";
                    $name = "Inactive";
                }
                $status_button = '<a type="button" onclick="updatestatus(' . $base_app_theme->id . ',' . $base_app_theme->bat_status . ')" class="' . $class . '" data-toggle="tooltip" data-placement="top" title="Edit">' . $name . '</a>';
                return $status_button;
            })->rawColumns(['action', 'status'])
            ->make(true);
        }
        return view('admin.BaseAppTheme.index');
    }

    /**
     * Show the form for creating a new BaseAppTheme.
     *
     * @return Factory|View
     */
    public function create()
    {
        return view('admin.BaseAppTheme.create');
    }

    /**
     * Store a newly created BaseAppTheme in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $id = $request->input('edit_value');
        if ($id == null) {
            $app_theme = new BaseAppTheme;
        } else {

            $app_theme = BaseAppTheme::where('id', $id)->first();
        }
        $app_theme->bat_theme_name = $request->input('bat_theme_name');
        $app_theme->bat_created_by = Auth::user()->id;
        $app_theme->bat_theme_description = $request->input('bat_theme_description');
        $app_theme->save();

        return response()->json(['success' => true, 'message' => 'Theme is Successfully Created']);
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
     * Show the form for editing the specified BaseAppTheme.
     *
     * @param int $id
     * @return Factory|View
     */
    public function edit($id)
    {
        $base_app_theme = BaseAppTheme::where('id', $id)->first();
        if ($base_app_theme) {
            return view('admin.BaseAppTheme.edit', ['base_app_theme' => $base_app_theme]);
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

    /**Change the Status for BaseAppTheme
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
        BaseAppTheme::where('id', $id)->update(['bat_status' => $status_new]);
        return response()->json(['success' => true, 'message' => 'Theme status is successfully Updated']);
    }
}
