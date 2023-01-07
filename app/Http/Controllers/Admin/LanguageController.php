<?php

namespace App\Http\Controllers\Admin;


use App\Language;
use App\LanguageString;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Config;

class LanguageController extends Controller
{
    /**
     * Display a listing of the Language.
     *
     * @param Request $request
     * @return Application|Factory|View
     * @throws Exception
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $languages = Language::all();
            return Datatables::of($languages)
                ->addColumn('action', function ($languages) {
                    $edit_button = '<a href="' . route('admin::languages.edit', [$languages->id]) . '" class="btn btn-sm btn-outline-info waves-effect waves-light" data-toggle="tooltip" data-placement="top" title="Edit"><i class="bx bx-pencil font-size-16 align-middle"></i></a>';
                    return $edit_button;
                })->addColumn('status', function ($languages) {
                    if ($languages->status == 1) {
                        $class = "badge badge-success";
                        $name = "Active";
                    }
                    if ($languages->status == 0) {
                        $class = "badge badge-warning";
                        $name = "Inactive";
                    }
                    $status_button = '<a type="button" onclick="updatestatuslang(' . $languages->id . ',' . $languages->status . ')" class="' . $class . '" data-toggle="tooltip" data-placement="top" title="Edit">' . $name . '</a>';
                    return $status_button;
                })->addColumn('is_rtl', function ($languages) {
                    if ($languages->is_rtl == 1) {

                        $name = "Yes";
                    }
                    if ($languages->is_rtl == 0) {

                        $name = "No";
                    }
                    return $name;
                })
                ->rawColumns(['is_rtl', 'status', 'action'])
                ->make(true);
        }
        return view('admin.languages.index');
    }

    /**
     * Show the form for creating a new Language.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        return view('admin.languages.create');
    }

    /**
     * Store a newly created Language in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $id = $request->input('edit_value');
        if ($id == null) {
            $language = new Language;
        } else {
            $language = Language::where('id', $id)->first();
        }
        $language->name = $request->input('name');
        $language->is_rtl = $request->input('is_rtl');
        $language->language_code = $request->input('language_code');
        $language->created_at = now();
        $language->updated_at = now();
        $language->save();

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
     * Show the form for editing the specified Language.
     *
     * @param int $id
     * @return Application|Factory|View
     */
    public function edit($id)
    {
        $language = Language::find($id);
        if ($language) {
            return view('admin.languages.edit', ['language' => $language]);
        } else {
            abort(404);
        }
    }

    /**
     * Update the specified Language in storage.
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
     * Remove the specified Language from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Change the Status for Language
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
        Language::where('id', $id)->update(['status' => $status_new]);
        return response()->json(['success' => true, 'message' => 'Language status is successfully Updated']);
    }
}
