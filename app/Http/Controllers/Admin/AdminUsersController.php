<?php

namespace App\Http\Controllers\Admin;

use App\AdminUser;
use App\Company;
use App\Driver;
use App\Roles;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class AdminUsersController extends Controller
{
    /**
     * Display All Admins Lists in Dashboard
     *
     * @return Application|Factory|View
     * @throws \Exception
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $admin = AdminUser::where('id', '!=', 1)->get();
            return Datatables::of($admin)
                ->addColumn('logo', function ($admin) {
                    $url = asset($admin->profile_pic);
                    if (!empty($admin->profile_pic)) {
                        return '<img   src="' . $url . '" >';
                    }
                })
                ->addColumn('admin_role', function ($admin) {
                    if (!empty($admin->admin_role)) {
                        $role = Roles::where('id', $admin->admin_role)->first();
                        $roleName = $role['role_name'];
                        return $roleName;
                    }
                })
                ->addColumn('action', function ($admin) {
                    $edit_button = '<a type="button" href="' . route('admin::admins.edit', [$admin->id]) . '" class="btn btn-sm btn-outline-info waves-effect waves-light" data-toggle="tooltip" data-placement="top" title="Edit"><i class="bx bx-pencil font-size-16 align-middle"></i></a>';
                    $del_driver_btn = '<a type="button" data-adminid="' . $admin->id . '" class="delete-single btn btn-sm btn-outline-info waves-effect waves-light" data-toggle="tooltip" data-placement="top" title="Delete Admin"><i class="fas fa-trash font-size-16 align-middle"></i></a>';
                    return $edit_button . ' ' . $del_driver_btn;
                })
                ->rawColumns(['admin_role', 'action', 'logo'])
                ->make(true);
        }
        return view('admin.adminUser.index');
    }

    /**
     * Show the form for creating new Admin.
     *
     * @return Factory|View
     */
    public function create()
    {
        return view('admin.adminUser.create');
    }

    /**
     * Store a newly created admin resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $id = $request->input('edit_value');

        if ($id == NULL) {
            $validator_array = [
            ];

            $validator = Validator::make($request->all(), $validator_array);
            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
            }

            $admin = new AdminUser();

            if ($request->hasFile('admin_logo')) {
                $mime = $request->admin_logo->getMimeType();
                $logo = $request->file('admin_logo');
                $logo_name = preg_replace('/\s+/', '', $logo->getClientOriginalName());
                $logoName = time() . '-' . $logo_name;
                $logo->move('./assets/admin/logo/', $logoName);
                $comlogo = 'assets/admin/logo/' . $logoName;
                $admin->profile_pic = $comlogo;
            }

            $admin->name = $request->input('name');
            $admin->mobile_no = $request->input('mobile_no');
            $admin->email = $request->input('email');
            $admin->user_type = 'admin';
            $admin->status = 'Active';
            $admin->password = Hash::make($request->input('password'));

            $admin->admin_role = $request->input('admin_role');

            $admin->save();
            return response()->json(['success' => true, 'message' => trans('adminMessages.company_inserted')]);
        } else {
            $admin = AdminUser::find($id);
            if ($request->hasFile('admin_logo')) {
                $mime = $request->admin_logo->getMimeType();
                $logo = $request->file('admin_logo');
                $logo_name = preg_replace('/\s+/', '', $logo->getClientOriginalName());
                $logoName = time() . '-' . $logo_name;
                $logo->move('./assets/admin/logo/', $logoName);
                $comlogo = 'assets/admin/logo/' . $logoName;
                $admin->profile_pic = $comlogo;
            }
            $admin->name = $request->input('name');
            $admin->mobile_no = $request->input('mobile_no');
            $admin->email = $request->input('email');
            $admin->user_type = 'admin';
            $admin->status = 'Active';
            $admin->admin_role = $request->input('admin_role');

            if (!empty($request->password)) {

                $admin->password = Hash::make($request->input('password'));
            }

            $admin->save();
            return response()->json(['success' => true, 'message' => trans('adminMessages.company_updated')]);
        }
    }


    /**
     * Display the specified Admin resource.
     *
     * @param int $id
     * @return Factory|View
     */
    public function show($id)
    {
        $admin = AdminUser::where('id', $id)->first();

        return view('admin.adminUser.show', ['admin' => $admin]);
    }

    /**
     * Show the form for editing the specified Admin resource.
     *
     * @param int $id
     * @return Application|Factory|View
     */
    public function edit($id)
    {
        $admin = AdminUser::find($id);
        if ($admin) {
            return view('admin.adminUser.edit', ['admin' => $admin]);
        } else {
            abort(404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified Admin resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        AdminUser::where('id', $id)->delete();
        return response()->json(['success' => true, 'message' => trans('adminMessages.country_deleted')]);
    }
}
