<?php

namespace App\Http\Controllers\Admin;

use App\Language;
use App\LanguageScreen;
use App\AppReference;
use App\LanguageString;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;
use Auth;
use App\LanguageStringTranslation;

class LanguageScreenController extends Controller
{
    /**
     * Display a listing of the LanguageScreen.
     *
     * @param Request $request
     * @return Factory|View
     * @throws Exception
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $language_screen = LanguageScreen::leftJoin('users', 'base_language_screens.blsc_created_by', '=', 'users.id')
                ->select('base_language_screens.id', 'base_language_screens.blsc_screen_family_id', 'base_language_screens.blsc_title',
                    'base_language_screens.blsc_image', 'users.name', 'base_language_screens.blsc_status')
                ->get();
            foreach ($language_screen as $item) {
                $family_name = AppReference::listsTranslations('name')->where('base_app_references.id', $item->blsc_screen_family_id)->first();
                if ($family_name->name != null) {
                    $item['family_name'] = $family_name->name;
                } else {
                    $item['family_name'] = "";
                }
            }
            return Datatables::of($language_screen)
                ->addColumn('action', function ($language_screen) {
                    $edit_button = '<a href="' . route('admin::languageScreen.edit', [$language_screen->id]) . '" class="btn btn-sm btn-outline-info waves-effect waves-light" data-toggle="tooltip" data-placement="top" title="Edit"><i class="bx bx-pencil font-size-16 align-middle"></i></a>';
                    return $edit_button;
                })->addColumn('viewScreen', function ($language_screen) {
                    return '<a  class="btn btn-sm btn-outline-info waves-effect waves-light" href="' . route('admin::languageScreenView', [$language_screen->id]) . '"  data-toggle="tooltip" data-placement="top"  title="Edit"><i class="fas fa-eye font-size-16 align-middle"></i></a>';
                })->addColumn('status', function ($language_screen) {
                    if ($language_screen->blsc_status == 1) {
                        $class = "badge badge-success";
                        $name = "Active";
                    }
                    if ($language_screen->blsc_status == 0) {
                        $class = "badge badge-warning";
                        $name = "Inactive";
                    }
                    $status_button = '<a type="button" onclick="updatestatus(' . $language_screen->id . ',' . $language_screen->blsc_status . ')" class="' . $class . '" data-toggle="tooltip" data-placement="top" title="Edit">' . $name . '</a>';
                    return $status_button;
                })
                ->rawColumns(['viewScreen', 'status', 'action'])
                ->make(true);
        }
        $language_string = Language::where('status', 1)->get();
        return view('admin.languageScreen.index', ['language_string' => $language_string]);
    }

    /**
     * Show the form for creating a new LanguageScreen.
     *
     * @return Factory|View
     */
    public function create()
    {
        $language_screen_families = AppReference::listsTranslations('name')->where(['bar_mod_id_ref' => 1, 'bar_ref_type_id' => 1])->get();
        return view('admin.languageScreen.create', ['language_screen_families' => $language_screen_families]);
    }

    /**
     * Store a newly created LanguageScreen in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $id = $request->input('edit_value');
        if ($id == null) {
            $languageScreen = new LanguageScreen;
        } else {
            $languageScreen = LanguageScreen::where('id', $id)->first();
        }
        if ($request->hasFile('blsc_image')) {
            @unlink(public_path() . '/' . $languageScreen->blsc_image);
            $mime = $request->blsc_image->getMimeType();
            $image = $request->file('blsc_image');
            $image_name = preg_replace('/\s+/', '', $image->getClientOriginalName());
            $ImageName = time() . '-' . $image_name;
            $image->move('./assets/LanguageScreen/', $ImageName);
            $languageScreen->blsc_image = 'assets/LanguageScreen/' . $ImageName;
        }
        $languageScreen->blsc_screen_family_id = $request->input('blsc_screen_family_id');
        $languageScreen->blsc_created_by = Auth::guard('admin')->user()->id;
        $languageScreen->blsc_title = $request->input('blsc_title');
        $languageScreen->blsc_created_at = now();
        $languageScreen->blsc_updated_at = now();
        $languageScreen->save();

        return response()->json(['success' => true, 'message' => 'Language Screen is successfully Uploaded']);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified LanguageScreen.
     *
     * @param int $id
     * @return Factory|View
     */
    public function edit($id)
    {
        $language_screen = LanguageScreen::find($id);
        $language_screen_families = AppReference::listsTranslations('name')->where(['bar_mod_id_ref' => 1, 'bar_ref_type_id' => 1])->get();
        if ($language_screen) {
            return view('admin.languageScreen.edit', ['language_screen' => $language_screen, 'language_screen_families' => $language_screen_families]);
        } else {
            abort(404);
        }
    }

    /**
     * Update the specified LanguageScreen in storage.
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
     * Change the status for LanguageScreen
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
        LanguageScreen::where('id', $id)->update(['blsc_status' => $status_new]);
        return response()->json(['success' => true, 'message' => 'Screen status is successfully Updated']);
    }

    /**
     * View Display for LanguageScreen
     * @param Request $request
     * @param $id
     * @return Factory|View
     * @throws Exception
     */
    public function screenview(Request $request, $id)
    {
        if ($request->ajax()) {
            $language_strings = LanguageString::listsTranslations('name')
                ->select('base_language_strings.id', 'base_language_strings.bls_screen_info', 'base_language_strings.bls_string_type_id', 'base_language_strings.bls_screen_name',
                    'base_language_strings.bls_app_or_panel', 'base_language_strings.bls_name_key', 'base_language_strings.bls_status')
                ->where('base_language_strings.bls_language_screen_ref_id', $id)->get();
            return Datatables::of($language_strings)
                ->addColumn('action', function ($language_strings) {
                    $edit_button = '<a href="' . route('admin::languageString.edit', [$language_strings->id]) . '" class="btn btn-sm btn-outline-info waves-effect waves-light" data-toggle="tooltip" data-placement="top" title="Edit"><i class="bx bx-pencil font-size-16 align-middle"></i></a>';
                    return $edit_button;
                })->addColumn('status', function ($language_strings) {
                    if ($language_strings->bls_status == 1) {
                        $class = "badge badge-success";
                        $name = "Active";
                    }
                    if ($language_strings->bls_status == 0) {
                        $class = "badge badge-warning";
                        $name = "Inactive";
                    }
                    $status_button = '<a type="button" onclick="updatestatuslangstring(' . $language_strings->id . ',' . $language_strings->bls_status . ')" class="' . $class . '" data-toggle="tooltip" data-placement="top" title="Edit">' . $name . '</a>';
                    return $status_button;
                })
                ->addColumn('for', function ($language_strings) {
                    if ($language_strings->bls_app_or_panel == 1) {
                        return 'App';
                    } else {
                        return 'Admin Panel';
                    }
                })
                ->rawColumns(['for', 'status', 'action'])
                ->make(true);
        }
        $screen = LanguageScreen::where('id', $id)->first();
        return view('admin.languageScreen.viewstring', ['screen' => $screen]);
    }
}
