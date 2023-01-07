<?php

namespace App\Http\Controllers\Admin;

use App\AppReference;
use App\LanguageScreen;
use App\LanguageString;
use App\LanguageStringTranslation;
use App\Language;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class LanguageStringController extends Controller
{
    /**
     * Display a listing of the LanguageString.
     *
     * @param Request $request
     * @return Application|Factory|View
     * @throws Exception
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $language_strings = LanguageString::listsTranslations('name')
             ->select('base_language_strings.id', 'base_language_strings.bls_language_screen_ref_id', 'base_language_strings.bls_string_type_id',
                    'base_language_strings.bls_updated_by', 'base_language_strings.bls_screen_family_id', 'base_language_strings.bls_status', 'base_language_strings.bls_screen_info'
                    , 'base_language_strings.bls_app_or_panel', 'base_language_strings.bls_screen_name', 'base_language_strings.bls_name_key');
            if($request['panel'] != null) {
                $language_strings->where('bls_app_or_panel', $request['panel']);
            }
            $language_strings=$language_strings->get();

            return Datatables::of($language_strings)
                ->addColumn('action', function ($language_strings) {
                    $edit_button = '<a href="' . route('admin::languageString.edit', [$language_strings->id]) . '" class="btn btn-sm btn-outline-info waves-effect waves-light" data-toggle="tooltip" data-placement="top" title="Edit"><i class="bx bx-pencil font-size-16 align-middle"></i></a>';
                    $delete_button = '<button data-id="' . $language_strings->id . '" class="delete-single btn btn-sm btn-outline-danger waves-effect waves-light" data-toggle="tooltip" data-placement="top" title="Delete"><i class="bx bx-trash font-size-16 align-middle"></i></button>';
                    return $edit_button . " " . $delete_button;
                })->addColumn('viewScreen', function ($language_strings) {
                    return '<a  class="btn btn-sm btn-outline-info waves-effect waves-light" href="' . route('admin::languageScreenView', [$language_strings->bls_language_screen_ref_id]) . '"  data-toggle="tooltip" data-placement="top"  title="Edit"><i class="fas fa-eye font-size-16 align-middle"></i></a>';
                })->addColumn('status', function ($language_strings) {
                    if ($language_strings->bls_status == 1) {
                        $class = "badge badge-success";
                        $name = "Active";
                    }
                    if ($language_strings->bls_status == 0) {
                        $class = "badge badge-warning";
                        $name = "Inactive";
                    }
                    $status_button = '<a type="button" onclick="updatestatus(' . $language_strings->id . ',' . $language_strings->bls_status . ')" class="' . $class . '" data-toggle="tooltip" data-placement="top" title="Edit">' . $name . '</a>';
                    return $status_button;
                })
                ->addColumn('for', function ($language_strings) {
                    if ($language_strings->bls_app_or_panel == 1) {
                        return 'App';
                    }elseif ($language_strings->bls_app_or_panel == 2) {
                        return 'Admin Panel';
                    } else {
                        return 'Company Panel';
                    }
                })
                ->rawColumns(['for', 'viewScreen', 'status', 'action'])
                ->make(true);
        }
        return view('admin.languageString.index');
    }

    /**
     * Show the form for creating a new LanguageString.
     *
     * @return Factory|View
     */
    public function create()
    {
        $languages = Language::where('status', 1)->get();
        $language_screens = LanguageScreen::all();
        $language_screen_families = AppReference::listsTranslations('name')->where(['bar_mod_id_ref' => 1, 'bar_ref_type_id' => 1])->get();
        return view('admin.languageString.create', ['languages' => $languages, 'language_screens' => $language_screens, 'language_screen_families' => $language_screen_families]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $id = $request->input('edit_value');
        if ($id == NULL) {
            $validator_array = [
                'bls_name_key' => 'required|max:255|unique:base_language_strings,bls_name_key',
            ];

            $validator = Validator::make($request->all(), $validator_array);
            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
            }
            $screen_family = $request->input('bls_language_screen_ref_id');
            $language_screen_name = LanguageScreen::where(['base_language_screens.id' => $screen_family])->first()->blsc_title;

            $insert_id = LanguageString::create([
                'bls_app_or_panel' => $request->input('bls_app_or_panel'),
                'bls_driver_or_passenger' => $request->input('bls_driver_or_passenger'),
                'bls_screen_family_id' => $request->input('bls_screen_family_id'),
                'bls_language_screen_ref_id' => $request->input('bls_language_screen_ref_id'),
                'bls_string_type_id' => $request->input('bls_string_type_id'),
                'bls_screen_info' => $request->input('bls_screen_info'),
                'bls_name_key' => $request->input('bls_name_key'),
                'bls_screen_name' => $language_screen_name
            ]);
            $languages = Language::where('status', 1)->get();;
            foreach ($languages as $language) {
                LanguageStringTranslation::create([
                    'name' => $request->input($language->language_code . '_name'),
                    'language_string_id' => $insert_id->id,
                    'locale' => $language->language_code,
                ]);
            }
            return response()->json(['success' => true, 'message' => trans('adminMessages.language_string_inserted')]);
        } else {
            $screen_family = $request->input('bls_language_screen_ref_id');
            $language_screen_name = LanguageScreen::where(['base_language_screens.id' => $screen_family])->first()->blsc_title;
            $insert_id = LanguageString::where('id', $id)->update([
                'bls_app_or_panel' => $request->input('bls_app_or_panel'),
                'bls_driver_or_passenger' => $request->input('bls_driver_or_passenger'),
                'bls_screen_family_id' => $request->input('bls_screen_family_id'),
                'bls_language_screen_ref_id' => $request->input('bls_language_screen_ref_id'),
                'bls_string_type_id' => $request->input('bls_string_type_id'),
                'bls_screen_info' => $request->input('bls_screen_info'),
                'bls_screen_name' => $language_screen_name
            ]);
            $languages = Language::where('status', 1)->get();
            foreach ($languages as $language) {
                LanguageStringTranslation::updateOrCreate([
                    'language_string_id' => $id,
                    'locale' => $language->language_code,
                ],
                    [
                        'language_string_id' => $id,
                        'locale' => $language->language_code,
                        'name' => $request->input($language->language_code . '_name')
                    ]);
            }
            return response()->json(['success' => true, 'message' => trans('adminMessages.language_string_updated')]);
        }
    }


    /**
     * Display the specified LanguageString.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified LanguageString.
     *
     * @param int $id
     * @return Application|Factory|View
     */
    public function edit($id)
    {
        $language_string = LanguageString::find($id);
        $language_screens = LanguageScreen::all();
        $language_screen_families = AppReference::listsTranslations('name')->where(['bar_mod_id_ref' => 1, 'bar_ref_type_id' => 1])->get();
        if ($language_string) {
            $languages = Language::where('status', 1)->get();;
            return view('admin.languageString.edit', ['language_string' => $language_string, 'language_screen_families' => $language_screen_families, 'language_screens' => $language_screens, 'languages' => $languages]);
        } else {
            abort(404);
        }
    }

    /**
     * Update the specified LanguageString in storage.
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
     * Remove the specified LanguageString from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        LanguageString::where('id', $id)->delete();
        LanguageStringTranslation::where('language_string_id', $id)->delete();
        return response()->json(['success' => true, 'message' => trans('adminMessages.country_deleted')]);
    }

    /**
     * Change the status for LanguageString
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
        LanguageString::where('id', $id)->update(['bls_status' => $status_new]);
        return response()->json(['success' => true, 'message' => 'page status is successfully Updated']);
    }
}
