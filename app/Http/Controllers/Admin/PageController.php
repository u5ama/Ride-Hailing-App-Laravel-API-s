<?php

namespace App\Http\Controllers\Admin;

use App\Language;
use App\Page;
use App\PageTranslation;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class PageController extends Controller
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
            $bodies = Page::listsTranslations('name')
                ->select('pages.id', 'pages.page_name', 'pages.slug', 'pages.app_type', 'pages.page_status')
                ->get();
            return Datatables::of($bodies)
                ->addColumn('action', function ($bodies) {
                    return '<a href="' . route('admin::page.edit', [$bodies->id]) . '" class="btn btn-sm btn-outline-info waves-effect waves-light" data-toggle="tooltip" data-placement="top" title="Edit"><i class="bx bx-pencil font-size-16 align-middle"></i></a>';
                })->addColumn('PageContentEdit', function ($bodies) {
                    return '<a href="' . route('admin::page.edit', [$bodies->id]) . '" class="btn btn-sm btn-outline-info waves-effect waves-light" data-toggle="tooltip" data-placement="top" title="Edit"><i class="bx bx-pencil font-size-16 align-middle"></i></a>';
                })->addColumn('PageContentView', function ($bodies) {
                    return '<a href="' . route('admin::pageView', [$bodies->id]) . '" class="btn btn-sm btn-outline-info waves-effect waves-light" data-toggle="tooltip" data-placement="top" title="Edit"><i class="fas fa-eye font-size-16 align-middle"></i></a>';
                })->addColumn('status', function ($bodies) {
                    if ($bodies->page_status == 1) {
                        $class = "badge badge-success";
                        $name = "Active";
                    }
                    if ($bodies->page_status == 0) {
                        $class = "badge badge-warning";
                        $name = "Inactive";
                    }
                    $status_button = '<a type="button" onclick="updatestatus(' . $bodies->id . ',' . $bodies->page_status . ')" class="' . $class . '" data-toggle="tooltip" data-placement="top" title="Edit">' . $name . '</a>';
                    return $status_button;
                })
                ->rawColumns(['action', 'PageContentEdit', 'PageContentView', 'status'])
                ->make(true);
        }
        return view('admin.page.index');
    }

    /**
     * Show the form for creating a new Page.
     *
     * @return Factory|View
     */
    public function create()
    {
        $index = Page::all();
        $languages = Language::where('status', 1)->get();
        return view('admin.page.add', ['indexes' => $index, 'languages' => $languages]);
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
                'page_order' => $page_order,
            ];
            $page = Page::create($page_date);
            $languages = Language::all();
            foreach ($languages as $language) {
                PageTranslation::updateOrCreate([
                    'page_id' => $page->id,
                    'locale' => $language->language_code,
                ],
                    [
                        'page_id' => $page->id,
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
                'page_order' => $page_order,
            ];
            $page = Page::where('id', $id)->update($page_date);
            foreach ($languages as $language) {
                PageTranslation::updateOrCreate([
                    'page_id' => $id,
                    'locale' => $language->language_code,
                ],
                    [
                        'page_id' => $id,
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
        $page = Page::find($id);
        $indexes = Page::all();
        if ($page) {
            $languages = Language::where('status', 1)->get();
            return view('admin.page.edit', ['page' => $page, 'indexes' => $indexes, 'languages' => $languages]);
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
        $page = Page::find($id);
        if ($page) {
            $languages = Language::where('status', 1)->get();
            return view('admin.page.view', ['page' => $page, 'languages' => $languages]);
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
        if ($status == 1) {
            $status_new = 0;
        }
        if ($status == 0) {
            $status_new = 1;
        }
        Page::where('id', $id)->update(['page_status' => $status_new]);
        return response()->json(['success' => true, 'message' => 'page status is successfully Updated']);
    }
}
