<?php

namespace App\Http\Controllers\Admin;

use App\BaseAppSocialLinks;
use App\Language;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class BaseAppSocialLinksController extends Controller
{
    /**
     * Display a listing of the BaseAppSocialLinks.
     *
     * @param Request $request
     * @return Factory|View
     * @throws \Exception
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $socialLinks = BaseAppSocialLinks::all();
            return Datatables::of($socialLinks)
                ->addColumn('action', function ($socialLinks) {
                    $edit_button = '<a href="' . route('admin::BaseAppSocialLink.edit', [$socialLinks->id]) . '" class="btn btn-sm btn-outline-info waves-effect waves-light" data-toggle="tooltip" data-placement="top" title="Edit"><i class="bx bx-pencil font-size-16 align-middle"></i></a>';
                    $delete_button = '<button data-id="' . $socialLinks->id . '" class="delete-single btn btn-sm btn-outline-danger waves-effect waves-light" data-toggle="tooltip" data-placement="top" title="Delete"><i class="bx bx-trash font-size-16 align-middle"></i></button>';
                    return $edit_button . " " . $delete_button;
                })->addColumn('status', function ($socialLinks) {
                    if ($socialLinks->basl_status == 1) {
                        $class = "badge badge-success";
                        $name = "Active";
                    }
                    if ($socialLinks->basl_status == 0) {
                        $class = "badge badge-warning";
                        $name = "Inactive";
                    }
                    $status_button = '<a type="button" onclick="updatestatus(' . $socialLinks->id . ',' . $socialLinks->basl_status . ')" class="' . $class . '" data-toggle="tooltip" data-placement="top" title="Edit">' . $name . '</a>';
                    return $status_button;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }
        return view('admin.BaseAppSocialLinks.index');
    }

    /**
     * Show the form for creating a new BaseAppSocialLinks.
     *
     * @return Factory|View
     */
    public function create()
    {
        $socialLinks = BaseAppSocialLinks::all();
        return view('admin.BaseAppSocialLinks.create', ['socialLinks' => $socialLinks]);
    }

    /**
     * Store a newly created BaseAppSocialLinks in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $id = $request->input('edit_value');
        if ($id == null) {
            $app_social_link = new BaseAppSocialLinks;
        } else {

            $app_social_link = BaseAppSocialLinks::where('id', $id)->first();
        }
        if ($request->hasFile('basl_image')) {
            @unlink(public_path() . '/' . $app_social_link->basl_image);
            $image = $request->file('basl_image');
            $image_name = preg_replace('/\s+/', '', $image->getClientOriginalName());
            $ImageName = time() . '-' . $image_name;
            $image->move('./assets/app_links_images/', $ImageName);
            $app_social_link->basl_image = 'assets/app_links_images/' . $ImageName;
        }
        $app_social_link->basl_title = $request->input('basl_title');
        $app_social_link->basl_description = $request->input('basl_description');
        $app_social_link->basl_url = $request->input('basl_url');
        $app_social_link->basl_order_by = $request->input('basl_order_by');
        $app_social_link->basl_created_at = now();
        $app_social_link->basl_updated_at = now();
        $app_social_link->save();

        return response()->json(['success' => true, 'message' => 'Image is successfully Uploaded']);
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
     * Show the form for editing the specified BaseAppSocialLinks.
     *
     * @param int $id
     * @return Factory|View
     */
    public function edit($id)
    {
        $base_social_link = BaseAppSocialLinks::where('id', $id)->first();
        $socialLinks = BaseAppSocialLinks::all();
        if ($base_social_link) {

            return view('admin.BaseAppSocialLinks.edit', ['base_social_link' => $base_social_link, 'socialLinks' => $socialLinks]);
        } else {
            abort(404);
        }
    }

    /**
     * Update the specified BaseAppSocialLinks in storage.
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

    /**Change Status for BaseAppSocialLinks
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
        BaseAppSocialLinks::where('id', $id)->update(['basl_status' => $status_new]);
        return response()->json(['success' => true, 'message' => 'Social Link status is successfully Updated']);
    }
}
