<?php

namespace App\Http\Controllers\Company;

use App\TransportType;
use App\Helpers\ImageUploadHelper;
use App\TransportMakeTranslation;
use App\TransportTypeTranslation;
use App\Language;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class TransportTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            //DB::enableQueryLog();
            $transportType = TransportType::listsTranslations('name','ttt_description')
                ->select('transport_types.id')
                ->get();
            // dd(DB::getQueryLog());
            return Datatables::of($transportType)
                ->addColumn('action', function ($transportType) {
                    $edit_button = '<a href="' . route('admin::transportType.edit', [$transportType->id]) . '" class="btn btn-sm btn-outline-info waves-effect waves-light" data-toggle="tooltip" data-placement="top" title="Edit"><i class="bx bx-pencil font-size-16 align-middle"></i></a>';
                    $delete_button = '<button data-id="' . $transportType->id . '" class="delete-single btn btn-sm btn-outline-danger waves-effect waves-light" data-toggle="tooltip" data-placement="top" title="Deletw"><i class="bx bx-trash font-size-16 align-middle"></i></button>';
                    return $edit_button . ' ' . $delete_button;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('admin.transportType.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $languages = Language::all();
        return view('admin.transportType.create', ['languages' => $languages]);
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
           $tt_image = NULL;
            if ($request->hasFile('type_image')){
            $image = $request->file('type_image');
            $image_name =  preg_replace('/\s+/', '', $image->getClientOriginalName());
            $ImageName = time() .'-'.$image_name;
            $image->move('./assets/transport_type_image/', $ImageName);
            $tt_image = 'assets/transport_type_image/'.$ImageName;
        }
            
            $tt_order = TransportType::max('id');
            $insert_id = TransportType::create([
                'tt_order' => $tt_order + 1,
                'tt_status' => 1,
                'tt_image' => $tt_image,
                'tt_created_by' => auth()->guard('admin')->user()->id,
            ]);
            $languages = Language::all();
            foreach ($languages as $language) {
                TransportTypeTranslation::create([
                    'name' => $request->input($language->language_code . '_name'),
                    'ttt_description' => $request->input($language->language_code . '_ttt_description'),
                    'transport_type_id' => $insert_id->id,
                    'locale' => $language->language_code,
                    'ttt_created_by'=> auth()->guard('admin')->user()->id,
                ]);
            }
            return response()->json(['success' => true, 'message' => trans('adminMessages.transport_make_inserted')]);
        } else {
         

         if ($request->hasFile('type_image')){
            $transportType = TransportType::where('id', $id)->first();
            @unlink(public_path() .'/'. $transportType->tt_image);
            $image = $request->file('type_image');
            $image_name =  preg_replace('/\s+/', '', $image->getClientOriginalName());
            $ImageName = time() .'-'.$image_name;
            $image->move('./assets/transport_type_image/', $ImageName);
            $tt_image = 'assets/transport_type_image/'.$ImageName;

            TransportType::where('id', $id)->update([
                'tt_image' => $tt_image,
            ]);
        }
           TransportType::where('id', $id)->update([
                'tt_updated_by' => auth()->guard('admin')->user()->id,
            ]);

            $languages = Language::all();
            foreach ($languages as $language) {
                TransportTypeTranslation::updateOrCreate([
                    'transport_type_id' => $id,
                    'locale' => $language->language_code,
                    
                ],
                    [
                        'transport_type_id' => $id,
                        'locale' => $language->language_code,
                        'name' => $request->input($language->language_code . '_name'),
                        'ttt_description' => $request->input($language->language_code . '_ttt_description'),
                        'ttt_updated_by'=> auth()->guard('admin')->user()->id,
                    ]);

            }
            return response()->json(['success' => true, 'message' => trans('adminMessages.transport_type_updated')]);
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
        $transportType = TransportType::find($id);
        if ($transportType) {
            $languages = Language::all();
            return view('admin.transportType.edit', ['transportType' => $transportType, 'languages' => $languages]);
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
        TransportType::where('id', $id)->delete();
        TransportTypetranslation::where('transport_type_id', $id)->delete();

        return response()->json(['success' => true, 'message' => trans('adminMessages.transport_Type_deleted')]);
    }
}
