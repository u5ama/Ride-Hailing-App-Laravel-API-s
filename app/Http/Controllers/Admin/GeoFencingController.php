<?php

namespace App\Http\Controllers\Admin;

use App\CustomerCreditCard;
use App\GeoFencing;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;


class GeoFencingController extends Controller
{
    /**
     * Display a listing of the GeoFencing.
     *
     * @param Request $request
     * @return Factory|View
     * @throws \Exception
     */
    public function index(Request $request)
    {

        if ($request->ajax()) {
           $geofencing = GeoFencing::all();
           return Datatables::of($geofencing)

               ->addColumn('action', function ($geofencing) {
               	$edit_button = '<a href="' . route('admin::geo_fencing.edit', [$geofencing->id]) . '" class="btn btn-sm btn-outline-info waves-effect waves-light" data-toggle="tooltip" data-placement="top" title="View"><i class="fas fa-eye font-size-16 align-middle"></i></a>';
                   $delete_button = '<button data-id="' . $geofencing->id . '" class="delete-single btn btn-sm btn-outline-danger waves-effect waves-light" data-toggle="tooltip" data-placement="top" title="Delete"><i class="bx bx-trash font-size-16 align-middle"></i></button>';
                   return $edit_button . ' ' . $delete_button;
               })
               ->rawColumns(['action'])
               ->make(true);
        }
        return view('admin.GeoFencing.index');
    }

    /**
     * Show the form for creating a new GeoFencing.
     *
     * @return Factory|View
     */
    public function create()
    {
        $boxmap = GeoFencing::all();

        $dataMap  = Array();
        $dataMap['type']='FeatureCollection';
        $dataMap['features']=array();
        foreach($boxmap as $value){
            $feaures = array();
            $feaures['type']='Feature';
            $geometry = array("type"=>"Point","coordinates"=>[$value->longitude, $value->lat]);
            $feaures['geometry']=$geometry;
            $properties=array('title'=>'restricted area',"description"=>'Area is restricted so here is no service');
            $feaures['properties']= $properties;
            array_push($dataMap['features'],$feaures);

        }


        return view('admin.GeoFencing.create')->with('dataArray',json_encode($dataMap));
    }

    /**
     * Store a newly created GeoFencing in storage.
     *
     * @param Request $request
     * @return Factory|View
     */
    public function store(Request $request)
    {
        $id = $request->input('edit_value');
        if ($id == NULL) {
            $validator_array = [
                'restricted_area' => 'required',

            ];
            $validator = Validator::make($request->all(), $validator_array);
            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
            }
        }


        $areas = explode(',', $request->restricted_latlng);
        foreach($areas as $Key=>$value){
           $rest_area[] = (float)$value;
        }

        $chunked  = array_chunk($rest_area,2);
        foreach($chunked as $k=>$v){
            $lat = $v[0];
            $lng = $v[1];
            $res = app('geocoder')->reverse($lat,$lng)->get()->first();
           $address[] =  $res->getFormattedAddress();
        }

       $formatted_address = implode('|', $address);

        if ($id == NULL) {

                 $insert_id = GeoFencing::create([
                'country' => $request->country,
                'city' => $request->locality,
                'address' => $request->address,
                'restricted_lat' => $request->restricted_lat,
                'restricted_lng' => $request->restricted_lng,
                'restricted_area' => $request->restricted_area,
                'formatted_address' => $formatted_address,
                'restricted_latlng_for_edit' => $request->restricted_latlng,
                'lat' => $request->lat,
                'longitude' => $request->long,

                'type' => $request->type,
                'radius' => $request->radius,
            ]);

            return response()->json(['success' => true, 'message' => 'Restricted area is added successfully']);
        } else {
        	GeoFencing::where('id', $id)->update([
                'country' => $request->country,
                'city' => $request->locality,
                'address' => $request->address,
                'restricted_lat' => $request->restricted_lat,
                'restricted_lng' => $request->restricted_lng,
                'restricted_area' => $request->restricted_area,
                'formatted_address' => $formatted_address,
                'restricted_latlng_for_edit' => $request->restricted_latlng,
                'lat' => $request->lat,
                'longitude' => $request->long,
            ]);

            return response()->json(['success' => true, 'message' => 'Restricted area is updated successfully']);
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
     * Show the form for editing the specified CreditCard.
     *
     * @param int $id
     * @return void
     */
    public function edit($id)
    {
    	 $geofencing = GeoFencing::find($id);



      return view('admin.GeoFencing.edit',['geofencing'=>$geofencing]);
    }

    /**
     * Update the specified resource in storage.
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
     * Remove the specified GeoFencing from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        GeoFencing::where("id", $id)->delete();
        return response()->json(['success' => true, 'message' => 'GeoFencing is deleted successfully']);
    }

}
