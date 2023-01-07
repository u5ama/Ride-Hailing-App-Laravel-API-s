<?php

namespace App\Http\Controllers\Dealer;

use App\CompanyAddress;
use App\Language;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class BranchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            //DB::enableQueryLog();
            $company_addresses = CompanyAddress::get();
            // dd(DB::getQueryLog());
            return Datatables::of($company_addresses)
                ->addColumn('action', function ($company_addresses) {
                    $edit_button = '<a href="' . route('dealer::branch.edit', [$company_addresses->id]) . '" class="btn btn-sm btn-outline-info waves-effect waves-light" data-toggle="tooltip" data-placement="top" title="Edit"><i class="bx bx-pencil font-size-16 align-middle"></i></a>';
                    $delete_button = '<button data-id="' . $company_addresses->id . '" class="delete-single btn btn-sm btn-outline-danger waves-effect waves-light" data-toggle="tooltip" data-placement="top" title="Delete"><i class="bx bx-trash font-size-16 align-middle"></i></button>';
                    return $edit_button . ' ' . $delete_button;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('dealer.branch.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $languages = Language::all();
        return view('admin.color.create', ['languages' => $languages]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $id = $request->input('edit_value');


        if ($id == NULL) {
            $color_order = Color::max('id');

            $insert_id = Color::create([
                'color_order' => $color_order + 1,
            ]);
            $languages = Language::all();
            foreach ($languages as $language) {
                ColorTranslation::create([
                    'name' => $request->input($language->language_code . '_name'),
                    'color_id' => $insert_id->id,
                    'locale' => $language->language_code,
                ]);
            }
            return response()->json(['success' => true, 'message' => trans('adminMessages.color_inserted')]);
        } else {
            $languages = Language::all();
            foreach ($languages as $language) {
                ColorTranslation::updateOrCreate([
                    'color_id' => $id,
                    'locale' => $language->language_code,
                ],
                    [
                        'color_id' => $id,
                        'locale' => $language->language_code,
                        'name' => $request->input($language->language_code . '_name')
                    ]);

            }
            return response()->json(['success' => true, 'message' => trans('adminMessages.color_updated')]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $color = Color::find($id);
        if ($color) {
            $languages = Language::all();
            return view('admin.color.edit', ['color' => $color, 'languages' => $languages]);
        } else {
            abort(404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        Color::where('id', $id)->delete();

        return response()->json(['success' => true, 'message' => trans('adminMessages.color_deleted')]);
    }
}
