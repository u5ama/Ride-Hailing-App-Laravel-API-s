<?php

namespace App\Http\Controllers\Admin;


use App\Company;
use App\Http\Controllers\Controller;
use App\Permission;
use App\Roles;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class RolesController extends Controller
{
    /**
     * Display a listing of the Roles.
     *
     * @param Request $request
     * @return Factory|View
     * @throws Exception
     */
    public function index(Request $request)
    {
        $roles = Roles::all()->sortByDesc("id");
        if ($request->ajax()) {
            $role = Roles::all();
            return Datatables::of($role)
                ->addColumn('role_name', function ($role) {
                    return $role->role_name;
                })
                ->addColumn('role_note', function ($role) {
                    if (!empty($role->note)) {
                        return $role->note;
                    }
                })
                ->addColumn('action', function ($role) {
                    $edit_button = '<a type="button" href="' . route('admin::roles.edit', [$role->id]) . '" class="btn btn-sm btn-outline-info waves-effect waves-light"  data-placement="top" title="Passenger Detail"><i class="fas fa-edit font-size-16 align-middle"></i></a>';
                    $view_btn = '<a type="button" data-roleid="' . $role->id . '" class="passenger-details btn btn-sm btn-outline-info waves-effect waves-light"  data-placement="top" title="Passenger Detail" data-target="#modaldemo3" data-toggle="modal"><i class="fas fa-eye font-size-16 align-middle"></i></a>';
                    $del_btn = '<a type="button" data-roleid="' . $role->id . '" class="delete-single btn btn-sm btn-outline-info waves-effect waves-light"  data-placement="top" title="Passenger Detail"><i class="fas fa-trash font-size-16 align-middle"></i></a>';
                    return $edit_button . ' ' . $view_btn . ' ' . $del_btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('admin.roles.index', compact('roles'));
    }


    /**
     * Show the form for creating a new Roles.
     *
     * @param Request $request
     * @return Factory|View
     */
    public function create(Request $request)
    {
        if (!$request->ajax()) {
            return view('admin.roles.create');
        } else {
            return view('admin.roles.modal.create');
        }
    }

    /**
     * Store a newly created Roles in storage.
     *
     * @param Request $request
     * @return RedirectResponse|Redirector
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role_name' => 'required|max:191',
            'note' => ''
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect('admin/roles/create')
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        $role = new Roles();
        $role->role_name = $request->input('role_name');
        $role->note = $request->input('note');
        $role->save();
        if (!$request->ajax()) {
            return redirect('admin/roles/create')->with('success', 'Information has been added successfully');
        } else {
            return response()->json(['result' => 'success', 'action' => 'store', 'message' => 'Information has been added successfully', 'data' => $role]);
        }
    }

    /**
     * Display the specified Roles.
     *
     * @param int $id
     * @return Factory|View
     */
    public function show($id)
    {
        $role = Roles::find($id);
        return view('admin.roles.view', ['role' => $role]);
    }

    /**
     * Show the form for editing the specified Roles.
     *
     * @param Request $request
     * @param int $id
     * @return Factory|View
     */
    public function edit(Request $request, $id)
    {
        $role = Roles::find($id);
        if (!$request->ajax()) {
            return view('admin.roles.edit', compact('role', 'id'));
        } else {
            return view('admin.roles.modal.edit', compact('role', 'id'));
        }

    }

    /**
     * Update the specified Roles in storage.
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'role_name' => 'required|max:191',
            'note' => ''
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect()->route('roles.edit', $id)
                    ->withErrors($validator)
                    ->withInput();
            }
        }
        $role = Roles::find($id);
        $role->role_name = $request->input('role_name');
        $role->note = $request->input('note');

        $role->save();

        if (!$request->ajax()) {
            return redirect('admin/roles')->with('success', 'Information has been updated successfully');
        } else {
            return response()->json(['result' => 'success', 'action' => 'update', 'message' => 'Information has been updated successfully', 'data' => $role]);
        }
    }

    /**
     * Remove the specified Roles from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        $role = Roles::find($id);
        $role->delete();
        return response()->json(['success' => true, 'message' => trans('Role deleted')]);
    }
}
