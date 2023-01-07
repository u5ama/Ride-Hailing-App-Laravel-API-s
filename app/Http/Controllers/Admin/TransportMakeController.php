<?php

namespace App\Http\Controllers\Admin;

use App\TransportMake;
use App\TransportType;
use App\TransportTypeTranslation;
use App\Helpers\ImageUploadHelper;
use App\TransportMakeTranslation;
use App\Language;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class TransportMakeController extends Controller
{
    /**
     * Display a listing of the TransportMake.
     *
     * @param Request $request
     * @return Application|Factory|View
     * @throws \Exception
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $transportmake = TransportMake::listsTranslations('name')->with('transportType')
                ->select('transport_makes.id', 'transport_makes.tm_type_ref_id')
                ->get();
            return Datatables::of($transportmake)
                ->addColumn('action', function ($transportmake) {
                    $edit_button = '<a href="' . route('admin::transportMake.edit', [$transportmake->id]) . '" class="btn btn-sm btn-outline-info waves-effect waves-light" data-toggle="tooltip" data-placement="top" title="Edit"><i class="bx bx-pencil font-size-16 align-middle"></i></a>';
                    $delete_button = '<button data-id="' . $transportmake->id . '" class="delete-single btn btn-sm btn-outline-danger waves-effect waves-light" data-toggle="tooltip" data-placement="top" title="Delete"><i class="bx bx-trash font-size-16 align-middle"></i></button>';
                    return $edit_button . ' ' . $delete_button;
                })
                ->addColumn('type', function ($transportmake) {
                    $type = '';
                    if (isset($transportmake->transportType)) {
                        $type = $transportmake->transportType->translateOrNew('en')->name;
                    }
                    return $type;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('admin.transportMake.index');
    }

    /**
     * Show the form for creating a new TransportMake.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        $transportTypes = TransportType::where('tt_status', 1)->get();
        $languages = Language::all();
        return view('admin.transportMake.create', ['transportTypes' => $transportTypes, 'languages' => $languages]);
    }

    /**
     * Store a newly created TransportMake in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $id = $request->input('edit_value');

        if ($id == NULL) {
            $tm_order = TransportMake::max('id');
            $insert_id = TransportMake::create([
                'tm_order' => $tm_order + 1,
                'tm_status' => 1,
                'tm_type_ref_id' => $request->tm_type_ref_id,
                'tm_created_by' => auth()->guard('admin')->user()->id,
            ]);
            $languages = Language::all();
            foreach ($languages as $language) {
                TransportMakeTranslation::create([
                    'name' => $request->input($language->language_code . '_name'),
                    'transport_make_id' => $insert_id->id,
                    'locale' => $language->language_code,
                    'tmt_created_by' => auth()->guard('admin')->user()->id,
                ]);
            }
            return response()->json(['success' => true, 'message' => trans('adminMessages.transport_make_inserted')]);
        } else {
            TransportMake::where('id', $id)->update([
                'tm_updated_by' => auth()->guard('admin')->user()->id,
                'tm_type_ref_id' => $request->tm_type_ref_id,
            ]);
            $languages = Language::all();
            foreach ($languages as $language) {
                TransportMakeTranslation::updateOrCreate([
                    'transport_make_id' => $id,
                    'locale' => $language->language_code,
                ],
                    [
                        'transport_make_id' => $id,
                        'locale' => $language->language_code,
                        'name' => $request->input($language->language_code . '_name'),
                        'tmt_updated_by' => auth()->guard('admin')->user()->id,
                    ]);
            }
            return response()->json(['success' => true, 'message' => trans('adminMessages.transport_make_updated')]);
        }
    }

    /**
     * Display the specified TransportMake.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified TransportMake.
     *
     * @param int $id
     * @return Application|Factory|View
     */
    public function edit($id)
    {
        $transportMake = TransportMake::find($id);
        $transportTypes = TransportType::where('tt_status', 1)->get();
        if ($transportMake) {
            $languages = Language::all();
            return view('admin.transportMake.edit', ['transportMake' => $transportMake, 'transportTypes' => $transportTypes, 'languages' => $languages]);
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
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        TransportMake::where('id', $id)->delete();
        TransportMaketranslation::where('transport_make_id', $id)->delete();

        return response()->json(['success' => true, 'message' => trans('adminMessages.transport_make_deleted')]);
    }

    /**
     * Method for make model
     * @param Request $request
     */
    public function getMake(Request $request)
    {
        $type_id = $request->input('type_id');
        $makes = TransportMake::where('tm_type_ref_id', $type_id)->get();

        if (count($makes) > 0) {
            echo "<option value=''>Please select transport make</option>";
            foreach ($makes as $make) {
                echo "<option value='" . $make->id . "'>" . $make->name . "</option>";
            }
        } else {
            echo "<option value=''>No Data Found</option>";
        }
    }
}
