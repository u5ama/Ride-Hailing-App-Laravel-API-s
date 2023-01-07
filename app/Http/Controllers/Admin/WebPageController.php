<?php

namespace App\Http\Controllers\Admin;

use App\Language;
use App\Page;
use App\PageTranslation;
use App\WebPage;
use App\WebPageTranslation;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class WebPageController extends Controller
{
    /**
     * Display a listing of the Page.
     *
     * @param Request $request
     * @return Application|Factory|View
     * @throws \Exception
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $bodies = WebPage::listsTranslations('name')
                ->select('web_pages.id', 'web_pages.page_name', 'web_pages.slug', 'web_pages.app_type', 'web_pages.page_status', 'web_pages.is_skip')
                ->get();
            return Datatables::of($bodies)
                ->addColumn('action', function ($bodies) {
                    return '<a href="' . route('admin::webpage.edit', [$bodies->id]) . '" class="btn btn-sm btn-outline-info waves-effect waves-light" data-toggle="tooltip" data-placement="top" title="Edit"><i class="bx bx-pencil font-size-16 align-middle"></i></a>';
                })->addColumn('PageContentEdit', function ($bodies) {
                    return '<a href="' . route('admin::webpage.edit', [$bodies->id]) . '" class="btn btn-sm btn-outline-info waves-effect waves-light" data-toggle="tooltip" data-placement="top" title="Edit"><i class="bx bx-pencil font-size-16 align-middle"></i></a>';
                })->addColumn('PageContentView', function ($bodies) {
                    return '<a href="' . route('admin::webpageView', [$bodies->id]) . '" class="btn btn-sm btn-outline-info waves-effect waves-light" data-toggle="tooltip" data-placement="top" title="Edit"><i class="fas fa-eye font-size-16 align-middle"></i></a>';
                })->addColumn('status', function ($bodies) {
                    if ($bodies->page_status == 1) {
                        $class = "badge badge-success";
                        $name = "Active";
                        $status = 0;
                    }
                    if ($bodies->page_status == 0) {
                        $class = "badge badge-warning";
                        $name = "Inactive";
                        $status = 1;
                    }
                    $status_button = '<a type="button" onclick="updatestatus(' . $bodies->id . ',' . $status . ')" class="' . $class . '" data-toggle="tooltip" data-placement="top" title="Edit">' . $name . '</a>';
                    return $status_button;
                })
                ->addColumn('skip', function ($bodies) {
                    if ($bodies->is_skip == 1) {
                        $class = "badge badge-success";
                        $name = "Active";

                    }
                    if ($bodies->is_skip == 0) {
                        $class = "badge badge-warning";
                        $name = "Inactive";
                    }
                    $skip_button = '<a type="button" onclick="updateSkip(' . $bodies->id . ',' . $bodies->is_skip . ')" class="' . $class . '" data-toggle="tooltip" data-placement="top" title="Edit">' . $name . '</a>';
                    return $skip_button;
                })
                ->rawColumns(['action', 'PageContentEdit', 'PageContentView', 'status','skip'])
                ->make(true);
        }
        return view('admin.web_page.index');
    }

    /**
     * Show the form for creating a new WebPage.
     *
     * @return Factory|View
     */
    public function create()
    {
        $index = WebPage::all();
        $languages = Language::where('status', 1)->get();
        return view('admin.web_page.add', ['indexes' => $index, 'languages' => $languages]);
    }

    /**
     * Store a newly created Page in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $id = $request->input('edit_value');
        $page_name = $request->input('page_name');
        $app_type = $request->input('driver_or_passenger');
        $slug = $request->input('slug');
        $page_order = $request->input('page_order');
        if ($id == null) {
            $page_date = [
                'page_name' => $page_name,
                'slug' => $slug,
                'app_type' => $app_type,
                'page_order' => $page_order
            ];
            $page = WebPage::create($page_date);
            $languages = Language::all();
            foreach ($languages as $language) {
                WebPageTranslation::updateOrCreate([
                    'web_page_id' => $page->id,
                    'locale' => $language->language_code,
                ],
                    [
                        'web_page_id' => $page->id,
                        'locale' => $language->language_code,
                        'name' => $request->input($language->language_code . '_name'),
                        'description' => $request->input($language->language_code . '_description')
                    ]);
            }
        } else {

            $languages = Language::where('status', 1)->get();
            $page_date = [
                'page_name' => $page_name,
                'slug' => $slug,
                'app_type' => $app_type,
                'page_order' => $page_order
            ];
            $page = WebPage::where('id', $id)->update($page_date);
            foreach ($languages as $language) {
                WebPageTranslation::updateOrCreate([
                    'web_page_id' => $id,
                    'locale' => $language->language_code,
                ],
                    [
                        'web_page_id' => $id,
                        'locale' => $language->language_code,
                        'name' => $request->input($language->language_code . '_name'),
                        'description' => $request->input($language->language_code . '_description')
                    ]);
            }
        }
        return response()->json(['success' => true, 'message' => trans('adminMessages.page_updated')]);
    }

    /**
     * Display the specified Page.
     *
     * @param int $id
     * @return void
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified Page.
     *
     * @param int $id
     * @return Application|Factory|View
     */
    public function edit($id)
    {
        $page = WebPage::find($id);
        $indexes = WebPage::all();
        if ($page) {
            $languages = Language::where('status', 1)->get();
            return view('admin.web_page.edit', ['page' => $page, 'indexes' => $indexes, 'languages' => $languages]);
        } else {
            abort(404);
        }
    }

    /**
     * Method to View Page Resource
     * @param $id
     * @return Factory|View
     */
    public function pageView($id)
    {
        $page = WebPage::find($id);
        if ($page) {
            $languages = Language::where('status', 1)->get();
            return view('admin.web_page.view', ['page' => $page, 'languages' => $languages]);
        } else {
            abort(404);
        }
    }

    /**
     * Update the specified Page in storage.
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
     * Remove the specified Page from storage.
     *
     * @param int $id
     * @return void
     */
    public function destroy($id)
    {

    }

    /**
     * Change the status for Page
     * @param $id
     * @param $status
     * @return JsonResponse
     */
    public function status($id, $status)
    {
//        DB::table('web_pages')->update(['page_status' => 0]);
        WebPage::where('id', $id)->update(['page_status' => $status]);
        return response()->json(['success' => true, 'message' => 'page status is successfully Updated']);
    }

    public function Is_Skip($id, $skip)
    {
        if ($skip == 1) {
            $status_new = 0;
        }
        if ($skip == 0) {
            $status_new = 1;
        }
        WebPage::where('id', $id)->update(['is_skip' => $status_new]);
        return response()->json(['success' => true, 'message' => 'page status is successfully Updated']);
    }
}
