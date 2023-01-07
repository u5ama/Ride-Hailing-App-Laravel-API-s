<?php

namespace App\Http\Controllers\Admin;

use App\BaseAppTheme;
use App\BaseDefaultImage;
use App\Language;
use App\LanguageString;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\View\View;
use phpDocumentor\Reflection\Types\Null_;
use Yajra\DataTables\Facades\DataTables;

class BaseDefaultImagesController extends Controller
{
    /**
     * Display a listing of the BaseDefaultImages.
     *
     * @param Request $request
     * @return Factory|View
     * @throws Exception
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $base_default_image = BaseDefaultImage::select('base_default_images.id as id', 'base_default_images.bdi_screen_info', 'base_default_images.bdi_description'
                , 'base_default_images.bdi_key', 'base_default_images.bdi_image', 'base_default_images.bdi_file_name', 'base_default_images.bdi_device_type', 'base_default_images.bdi_status')
                ->get();
            return Datatables::of($base_default_image)
                ->addColumn('action', function ($base_default_image) {
                    $edit_button = '<a href="' . route('admin::BaseDefaultImage.edit', [$base_default_image->id]) . '" class="btn btn-sm btn-outline-info waves-effect waves-light" data-toggle="tooltip" data-placement="top" title="Edit"><i class="bx bx-pencil font-size-16 align-middle"></i></a>';
                    return $edit_button;
                })
                ->rawColumns(['action'])
                ->addColumn('status', function ($base_default_image) {
                    if ($base_default_image->bdi_status == 1) {
                        $class = "badge badge-success";
                        $name = "Active";
                    }
                    if ($base_default_image->bdi_status == 0) {
                        $class = "badge badge-warning";
                        $name = "Inactive";
                    }
                    $status_button = '<a type="button" onclick="updatestatus(' . $base_default_image->id . ',' . $base_default_image->bdi_status . ')" class="' . $class . '" data-toggle="tooltip" data-placement="top" title="Edit">' . $name . '</a>';
                    return $status_button;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }
        return view('admin.BaseDefaultImage.index');
    }

    /**
     * Show the form for creating a new BaseDefaultImages.
     *
     * @return Factory|View
     */
    public function create()
    {
        $themes = BaseAppTheme::all();
        return view('admin.BaseDefaultImage.create', ['themes' => $themes]);
    }

    /**
     * Store a newly created BaseDefaultImages in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $id = $request->input('edit_value');
        if ($id == null) {
            $appdefaultimage = new BaseDefaultImage;
        } else {
            $appdefaultimage = BaseDefaultImage::where('id', $id)->first();
        }

        if ($request->hasFile('bdi_image')) {
            @unlink(public_path() . '/' . $appdefaultimage->adi_image);
            $mime = $request->bdi_image->getMimeType();
            $image = $request->file('bdi_image');
            $image_name = preg_replace('/\s+/', '', $image->getClientOriginalName());
            $ImageName = time() . '-' . $image_name;
            $image->move('./assets/app_images/', $ImageName);
            $appdefaultimage->bdi_image = 'assets/app_images/' . $ImageName;
            $appdefaultimage->bdi_mime_type = $mime;
            $appdefaultimage->bdi_file_name = $ImageName;
        }

        $appdefaultimage->bdi_theme_ref_id = $request->input('bdi_theme_ref_id');
        $appdefaultimage->bdi_language_screen_ref_id = $request->input('bdi_language_screen_ref_id');
        $appdefaultimage->bdi_screen_info = $request->input('bdi_screen_info');
        $appdefaultimage->bdi_description = $request->input('bdi_description');
        $appdefaultimage->bdi_key = $request->input('bdi_key');
        $appdefaultimage->bdi_device_type = $request->input('bdi_device_type');
        $appdefaultimage->bdi_created_at = now();
        $appdefaultimage->bdi_updated_at = now();
        $appdefaultimage->save();

        return response()->json(['success' => true, 'message' => 'Image is successfully Uploaded']);
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
     * Show the form for editing the specified BaseDefaultImages.
     *
     * @param int $id
     * @return Factory|View
     */
    public function edit($id)
    {
        $themes = BaseAppTheme::all();
        $base_default_image = BaseDefaultImage::where('id', $id)->first();
        if ($base_default_image) {
            return view('admin.BaseDefaultImage.edit', ['base_default_image' => $base_default_image, 'themes' => $themes]);
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
     * @return void
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Change the Status for BaseDefaultImages
     *
     * @param int $id $status
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
        BaseDefaultImage::where('id', $id)->update(['bdi_status' => $status_new]);
        return response()->json(['success' => true, 'message' => 'Image status is successfully Updated']);
    }
}
