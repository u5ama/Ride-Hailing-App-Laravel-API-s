<?php

namespace App\Http\Controllers\Admin;


use App\Country;
use App\CountryTranslation;
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

class CountryController extends Controller
{
    /**
     * Display a listing of the Country.
     *
     * @param Request $request
     * @return Application|Factory|View
     * @throws Exception
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $countries = Country::listsTranslations('name')
                ->select('countries.id', 'countries.code', 'countries.country_code')
                ->get();
            return Datatables::of($countries)
                ->addColumn('action', function ($countries) {
                    $edit_button = '<a href="' . route('admin::country.edit', [$countries->id]) . '" class="btn btn-sm btn-outline-info waves-effect waves-light" data-toggle="tooltip" data-placement="top" title="Edit"><i class="bx bx-pencil font-size-16 align-middle"></i></a>';
                    $delete_button = '<button data-id="' . $countries->id . '" class="delete-single btn btn-sm btn-outline-danger waves-effect waves-light" data-toggle="tooltip" data-placement="top" title="Delete"><i class="bx bx-trash font-size-16 align-middle"></i></button>';
                    return $edit_button . ' ' . $delete_button;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('admin.country.index');
    }

    /**
     * Show the form for creating a new Country.
     *
     * @return Factory|View
     */
    public function create()
    {
        $languages = Language::all();
        return view('admin.country.create', ['languages' => $languages]);
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
                'country_code' => 'required|max:5',
                'code' => 'required|max:5',
            ];
            $validator = Validator::make($request->all(), $validator_array);
            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
            }
        }
        if ($id == NULL) {
            $country_order = Country::max('id');
            $insert_id = Country::create([
                'country_code' => $request->input('country_code'),
                'code' => $request->input('code'),
                'country_order' => $country_order + 1,
            ]);
            $languages = Language::all();
            foreach ($languages as $language) {
                CountryTranslation::create([
                    'name' => $request->input($language->language_code . '_name'),
                    'country_id' => $insert_id->id,
                    'locale' => $language->language_code,
                ]);
            }
            return response()->json(['success' => true, 'message' => trans('adminMessages.country_inserted')]);
        } else {

            Country::where('id', $id)->update([
                'country_code' => $request->input('country_code'),
                'code' => $request->input('code'),
            ]);

            $languages = Language::all();
            foreach ($languages as $language) {
                CountryTranslation::updateOrCreate([
                    'country_id' => $id,
                    'locale' => $language->language_code,
                ],
                    [
                        'country_id' => $id,
                        'locale' => $language->language_code,
                        'name' => $request->input($language->language_code . '_name')
                    ]);
            }
            return response()->json(['success' => true, 'message' => trans('adminMessages.country_updated')]);
        }
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
     * Show the form for editing the specified Country.
     *
     * @param int $id
     * @return Application|Factory|View
     */
    public function edit($id)
    {
        $country = Country::find($id);
        if ($country) {
            $languages = Language::all();
            return view('admin.country.edit', ['country' => $country, 'languages' => $languages]);
        } else {
            abort(404);
        }
    }

    /**
     * Update the specified resource in storage.
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
     * Remove the specified Country from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        Country::where('id', $id)->delete();
        return response()->json(['success' => true, 'message' => trans('adminMessages.country_deleted')]);
    }
}
