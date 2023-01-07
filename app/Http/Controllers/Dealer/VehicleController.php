<?php

namespace App\Http\Controllers\Dealer;


use App\Body;
use App\Brand;
use App\BrandModel;
use App\RydeInstance;
use App\Category;
use App\ModelYear;
use App\CategoryVehicle;
use App\Company;
use App\CompanyAddress;
use App\Door;
use App\Engine;
use App\Extra;
use App\Feature;
use App\Fuel;
use App\Gearbox;
use App\Color;
use App\Insurance;
use App\Language;
use App\Vehicle;
use App\Ryde;
use App\VehicleAttribute;
use App\VehicleExtra;
use App\VehicleFeature;
use App\VehicleNotAvailable;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class VehicleController extends Controller
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
            $vehicles = Vehicle::with('companies', 'companyAddress','color')->get();
            // dd(DB::getQueryLog());
            return Datatables::of($vehicles)
                ->addColumn('address', function ($vehicles) {
                    return $vehicles->companyAddress->address;
                })
                ->addColumn('name', function ($vehicles) {
                    return $vehicles->companies->name;
                })
                ->addColumn('color', function ($vehicles) {
                    return $vehicles->color->name;
                })

                ->addColumn('status', function ($vehicles) {
                    if($vehicles->status == 'Active'){
                        $status = '<a class=" badge badge-primary" href="#">Active</a>';
                    }else{
                        $status = '<a  class=" badge badge-danger" href="#">InActive</a>';
                    }

                    return $status;

                })
                
                ->addColumn('action', function ($vehicles) {
                    $edit_button = '<a href="' . route('dealer::vehicle.edit', [$vehicles->id]) . '" class="btn btn-sm btn-outline-info waves-effect waves-light" data-toggle="tooltip" data-placement="top" title="Edit"><i class="bx bx-pencil font-size-16 align-middle"></i></a>';
                    $delete_button = '<button data-id="' . $vehicles->id . '" class="delete-single btn btn-sm btn-outline-danger waves-effect waves-light" data-toggle="tooltip" data-placement="top" title="Delete"><i class="bx bx-trash font-size-16 align-middle"></i></button>';
                    $view_detail_button = '<button data-id="' . $vehicles->id . '" class="vehicle-details btn btn-sm btn-outline-primary waves-effect waves-light" data-effect="effect-fall" data-toggle="tooltip" data-placement="top" title="View Details"><i class="bx bx-bullseye font-size-16 align-middle"></i></button>';
                        $status = 'Active';
                        if($vehicles->status == 'Active'){
                            $status = 'InActive';
                        }
                      $status_button = '<button data-id="' . $vehicles->id . '" data-status="' . $status . '" class="status-change btn btn-sm btn-outline-success waves-effect waves-light" data-effect="effect-fall" data-toggle="tooltip" data-placement="top" title="' . $status . '" ><i class="bx bx-refresh font-size-16 align-middle"></i></button>';

                     $vehicle_not_available = '';
                    if($vehicles->status == 'Active'){
                
                     $vehicle_not_available = '<a href="' . route('dealer::vehicleNotAvailable', [$vehicles->id]) . '" class="btn btn-sm btn-outline-info waves-effect waves-light" data-toggle="tooltip" data-placement="top" title="Vehicle Not Available">Vehicle Not Available</a>';
                    }


                    return $edit_button . ' ' . $delete_button.' '.$view_detail_button.' '.$status_button.' '.$vehicle_not_available;
                })
                ->rawColumns(['action','status','vehicle_not_available'])
                ->make(true);
        }
        return view('dealer.vehicle.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $languages = Language::all();
        $companies = Company::all();
        $company_id = 3;
        $company_addresses = CompanyAddress::where('company_id', $company_id)->get();
        $makes = Brand::all();
        $features = Feature::all();
        $extras = Extra::all();
        $categories = Category::all();
        $modelyears = ModelYear::all();
        $colors = Color::all();
        return view('dealer.vehicle.create', [
            'languages' => $languages,
            'companies' => $companies,
            'company_addresses'=>$company_addresses,
            'makes' => $makes,
            'modelyears'=>$modelyears,
            'features' => $features,
            'extras' => $extras,
            'categories' => $categories,
            'colors'=>$colors,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $company_id = 3;
        $id = $request->input('edit_value');
        if ($id == NULL) {
            $validator_array = [
                
                'hourly_amount' => 'required',
                'weekly_amount' => 'required',
                'daily_amount' => 'required',
                'monthly_amount' => 'required',
                'ryde_id' => 'required',
                'color' => 'required',

            ];
            $validator = Validator::make($request->all(), $validator_array);
            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
            }
        }

        
        if ($id == NULL) {
            
            $company_address_id = CompanyAddress::where('company_id', $company_id)->first();
            $insert_id = Vehicle::create([
                'company_id' => $company_id,
                'company_address_id' => $company_address_id->id,
                'vehicle_type_id' => 1,
                'hourly_amount' => $request->input('hourly_amount'),
                'daily_amount' => $request->input('daily_amount'),
                'weekly_amount' => $request->input('weekly_amount'),
                'monthly_amount' => $request->input('monthly_amount'),
                'ryde_id' => $request->input('ryde_id'),
                'color_id' => $request->input('color'),
                'status' => 'Active',
            ]);


            if ($request->input('price') != NULL) {
                foreach ($request->input('price') as $key => $value) {
                    foreach ($value as $values) {
                        VehicleExtra::create([
                            'vehicle_id' => $insert_id->id,
                            'extra_id' => $key,
                            'price' => $values,
                        ]);
                    }
                }
            }

            if ($request->input('featured') != NULL) {
                foreach ($request->input('featured') as $value) {
                    CategoryVehicle::create([
                        'vehicle_id' => $insert_id->id,
                        'company_id' => $company_id,
                        'category_id' => $value,
                        'company_address_id' =>$company_address_id->id,
                    ]);
                }
            }

            return response()->json(['success' => true, 'message' => trans('adminMessages.vehicle_inserted')]);
        } else {
            $company_id = 3;
            $company_address_id = CompanyAddress::where('company_id', $company_id)->first();

            Vehicle::where('id', $id)->update([
                'company_id' => $company_id,
                'company_address_id' => $company_address_id->id,
                'hourly_amount' => $request->input('hourly_amount'),
                'daily_amount' => $request->input('daily_amount'),
                'weekly_amount' => $request->input('weekly_amount'),
                'monthly_amount' => $request->input('monthly_amount'),
                'ryde_id' => $request->input('ryde_id'),
                'color_id' => $request->input('color'),
            ]);



            VehicleExtra::where('vehicle_id', $id)->delete();
            CategoryVehicle::where('vehicle_id', $id)->delete();

            if ($request->input('price') != NULL) {
                foreach ($request->input('price') as $key => $value) {
                    foreach ($value as $values) {
                        VehicleExtra::create([
                            'vehicle_id' => $id,
                            'extra_id' => $key,
                            'price' => $values,
                        ]);
                    }
                }
            }

            if ($request->input('featured') != NULL) {
                foreach ($request->input('featured') as $value) {
                    CategoryVehicle::create([
                        'vehicle_id' => $id,
                        'company_id' => $company_id,
                        'category_id' => $value,
                        'company_address_id' =>$company_address_id->id,
                    ]);
                }
            }
            return response()->json(['success' => true, 'message' => trans('adminMessages.vehicle_updated')]);
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
        $vehicle = Vehicle::with('vehicleExtra', 'categoryVehicle','ryde')->find($id);
       
        if ($vehicle) {
            $languages = Language::all();
            $companies = Company::all();
            $makes = Brand::all();
            $features = Feature::all();
            $extras = Extra::all();
            $categories = Category::all();
            $modelyears = ModelYear::all();
            $colors = Color::all();
            $company_id = 3;
            $company_addresses = CompanyAddress::where('company_id', $company_id)->get();
            $brand_models = BrandModel::where('brand_id', $vehicle->ryde->brand_id)->get();
            $rydes = Ryde::where(['id'=>$vehicle->ryde_id])->with('brand','modelYear','model','rydeInstance')->first();

            return view('dealer.vehicle.edit', [
                'vehicle' => $vehicle,
                'languages' => $languages,
                'companies' => $companies,
                'company_addresses'=>$company_addresses,
                'makes' => $makes,
                'extras' => $extras,
                'brand_models' => $brand_models,
                'categories' => $categories,
                'colors'=>$colors,
                'modelyears'=>$modelyears,

                //ryde detail
                'id'=>$rydes->id,
                'brand'=>$rydes->Brand->name,
                'year'=>$rydes->modelYear->name,
                'model'=>$rydes->model->name,
                'model_image'=>$rydes->model->image,
                'engine'=>$rydes->rydeInstance->engine->name,
                'body'=>$rydes->rydeInstance->body->name,
                'door'=>$rydes->rydeInstance->door->name,
                'fuel'=>$rydes->rydeInstance->fuel->name,
                'gearbox'=>$rydes->rydeInstance->gearbox->name,
                'seats'=>$rydes->rydeInstance->seats,

            ]);
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
        Vehicle::where('id', $id)->delete();
        VehicleExtra::where('vehicle_id', $id)->delete();
        CategoryVehicle::where('vehicle_id', $id)->delete();
        VehicleNotAvailable::where('vehicle_id', $id)->delete();
        return response()->json(['success' => true, 'message' => trans('adminMessages.vehicle_deleted')]);
    }

    public function changeStatus($id,$status){

        Vehicle::where('id', $id)->update(['status' => $status]);
        return response()->json(['success' => true, 'message' => trans('adminMessages.vehicle_status')]);

    }

     public function vehicleNotAvailable($id){
        $vehicle = Vehicle::with('vehicleExtra', 'categoryVehicle','ryde','companies', 'companyAddress','color')->find($id);
         $vehicleNotAvailability = VehicleNotAvailable::with('vehicle')->get();
          $rydes = Ryde::where(['id'=>$vehicle->ryde_id])->with('brand','modelYear','model','rydeInstance')->first();

        if ($vehicle) {
            return view('dealer.vehicle.vehicleNotAvailable', [
                'vehicle' => $vehicle,
                'vehicleNotAvailability'=>$vehicleNotAvailability,
            ]);
        } else {
            abort(404);
        }
    }

    public function UpdateVehicleNotAvailable(Request $request){
        $validator_array = [
                'start_date' => 'required',
                'end_date' => 'required',
                
            ];
            $validator = Validator::make($request->all(), $validator_array);
            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
            }

            $start_date = $request->input('start_date');

            $end_date = $request->input('end_date');

            $duplicate = VehicleNotAvailable::where(['start_date'=>$start_date,'end_date'=>$end_date])->first();
          if($duplicate){

            return response()->json(['success' => false, 'message' => trans('adminMessages.vehicleNotAvailable_duplicate')]);

          }else{


            
        $check_exist = VehicleNotAvailable::where(['vehicle_id'=>$request->input('id')])->first();
        if($check_exist){

            VehicleNotAvailable::where('vehicle_id', $request->input('id'))->update([
                'start_date' => $start_date,
                'end_date' => $end_date,
            ]);
            $vehicleNotAvailability = VehicleNotAvailable::with('vehicle')->get();

           
            return response()->json(['success' => true, 'message' => trans('adminMessages.vehicleNotAvailable_updated'),'id'=>$request->input('id')]);

      }else{
       
        VehicleNotAvailable::create([
                        'vehicle_id' => $request->input('id'),
                        'start_date' => $start_date,
                        'end_date' => $end_date,
                    ]);
        
        
        return response()->json(['success' => true, 'message' => trans('adminMessages.vehicleNotAvailable_inserted'),'id'=>$request->input('id')]);
      }
   }   
}


    public function getBranch(Request $request)
    {
        $company_id = $request->input('company_id');

        $company_addresses = CompanyAddress::where('company_id', $company_id)->get();
        echo '<option value="">Please Select Branch</option>';
        foreach ($company_addresses as $company_address) {
            echo "<option value='" . $company_address->id . "'>" . $company_address->address . "</option>";
        }

    }

    public function getRyde(Request $request){
        $brand_id = $request->brand_id;
        $model_id = $request->model_id;
        $year_id  = $request->year_id;
        if(!empty($brand_id) && !empty($model_id) && !empty($year_id)){
          $rydes = Ryde::where(['brand_id'=>$brand_id,'brand_model_id'=>$model_id,'model_year_id'=>$year_id])->with('brand','modelYear','model','rydeInstance')->first();
           if($rydes){
                 return view('dealer.vehicle.rydeshow', [
                'id'=>$rydes->id,
                'brand'=>$rydes->Brand->name,
                'year'=>$rydes->modelYear->name,
                'model'=>$rydes->model->name,
                'model_image'=>$rydes->model->image,
                'engine'=>$rydes->rydeInstance->engine->name,
                'body'=>$rydes->rydeInstance->body->name,
                'door'=>$rydes->rydeInstance->door->name,
                'fuel'=>$rydes->rydeInstance->fuel->name,
                'gearbox'=>$rydes->rydeInstance->gearbox->name,
                'seats'=>$rydes->rydeInstance->seats,
                ]);

          }else{
            return '';
          }

        }
    }

    public function vehicleDetails($id)
    {
        $vehicle = Vehicle::where('id',$id)->with('vehicleExtra', 'categoryVehicle','ryde','companies', 'companyAddress','color')->first();

         $vehicleNotAvailable = VehicleNotAvailable::where(['vehicle_id'=>$id])->first();

        $rydes = Ryde::where('id',$vehicle->ryde_id)->with('brand','modelYear','model','rydeInstance','rydeFeatures')->first();

        $array['globalModalTitle'] = 'Vehicle Details';

        $array['globalModalDetails'] = '<table class="table table-bordered">';
        $array['globalModalDetails'] .= '<thead class="thead-light"><tr><th colspan="6" class="text-center">Vehicle Details</th></tr></thead>';
        $array['globalModalDetails'] .= '<thead class="thead-dark"><tr><th>Company</th><th>Color</th><th>Hourly Amount</th><th>Daily Amount</th><th>Weekly Amount</th><th>Monthly Amount</th></tr></thead>';
        $array['globalModalDetails'] .= '<tr>';
        $array['globalModalDetails'] .= '<td>' . $vehicle->companies->name .'</td>';
        $array['globalModalDetails'] .= '<td>' . $vehicle->color->name . '</td>';
        $array['globalModalDetails'] .= '<td>' . '$ '.$vehicle->hourly_amount . '</td>';
        $array['globalModalDetails'] .= '<td>' . '$ '.$vehicle->daily_amount . '</td>';
        $array['globalModalDetails'] .= '<td>' . '$ '.$vehicle->weekly_amount . '</td>';
        $array['globalModalDetails'] .= '<td>' . '$ '.$vehicle->monthly_amount . '</td>';
        $array['globalModalDetails'] .= '</tr>';
        $array['globalModalDetails'] .= '</table>';

        $array['globalModalDetails'] .= '<table class="table table-bordered">';
        $array['globalModalDetails'] .= '<thead class="thead-light"><tr><th colspan="3" class="text-center">Vehicle Features</th></tr></thead>';
        $array['globalModalDetails'] .= '<thead class="thead-dark"><tr><th></th><th></th><th></th></tr></thead>';
            $count = 1;
            foreach ($vehicle->categoryVehicle as $categoryFeature) {
            $array['globalModalDetails'] .= '<tr>';
            $array['globalModalDetails'] .= '<td colspan="3">' .  $categoryFeature->category->name. '</td>';
            $array['globalModalDetails'] .= '</tr>';
            }
            $array['globalModalDetails'] .= '</table>';


        $array['globalModalDetails'] .= '<table class="table table-bordered">';
        $array['globalModalDetails'] .= '<thead class="thead-light"><tr><th colspan="6" class="text-center">Vehicle Extra</th></tr></thead>';
        $array['globalModalDetails'] .= '<thead class="thead-dark"><tr><th>Extra</th><th>Price</th></tr></thead>';
        foreach ($vehicle->vehicleExtra as $vehicleExtra) {
        $array['globalModalDetails'] .= '<tr>';
        $array['globalModalDetails'] .= '<td>' .  $vehicleExtra->extra->name .'</td>';
        $array['globalModalDetails'] .= '<td>' . $vehicleExtra->price . '</td>';
        $array['globalModalDetails'] .= '</tr>';
       }
        $array['globalModalDetails'] .= '</table>';

         if($vehicleNotAvailable){
        $array['globalModalDetails'] .= '<table class="table table-bordered">';
        $array['globalModalDetails'] .= '<thead class="thead-light"><tr><th colspan="6" class="text-center">Vehicle Not Available</th></tr></thead>';
        $array['globalModalDetails'] .= '<thead class="thead-dark"><tr><th>Start Date</th><th>End Date</th></tr></thead>';
        $array['globalModalDetails'] .= '<tr>';
        $array['globalModalDetails'] .= '<td>' . $vehicleNotAvailable->start_date . '</td>';
        $array['globalModalDetails'] .= '<td>' . $vehicleNotAvailable->end_date . '</td>';
        $array['globalModalDetails'] .= '</tr>';
        $array['globalModalDetails'] .= '</table>';
    }


        $array['globalModalDetails'] .= '<table class="table table-bordered">';
        $array['globalModalDetails'] .= '<thead class="thead-light"><tr><th colspan="6" class="text-center">Ryde Details : '.$rydes->Brand->name.' | '.$rydes->model->name.' | '.$rydes->modelYear->name.'</th></tr></thead>';
        $array['globalModalDetails'] .= '<thead class="thead-dark"><tr><th>Body</th><th>Engine</th><th>Door</th><th>Fuel</th><th>Gearbox</th><th>Seats</th></tr></thead>';
        $array['globalModalDetails'] .= '<tr>';
        $array['globalModalDetails'] .= '<td>' . $rydes->rydeInstance->body->name . '</td>';
        $array['globalModalDetails'] .= '<td>' . $rydes->rydeInstance->engine->name . '</td>';
        $array['globalModalDetails'] .= '<td>' . $rydes->rydeInstance->door->name . '</td>';
        $array['globalModalDetails'] .= '<td>' . $rydes->rydeInstance->fuel->name . '</td>';
        $array['globalModalDetails'] .= '<td>' . $rydes->rydeInstance->gearbox->name . '</td>';
        $array['globalModalDetails'] .= '<td>' . $rydes->rydeInstance->seats . '</td>';
        $array['globalModalDetails'] .= '</tr>';
        $array['globalModalDetails'] .= '</table>';
        $url = asset($rydes->model->image);
        $array['globalModalDetails'] .= "<img src='".$url."' />";
        $array['globalModalDetails'] .= '<table class="table table-bordered">';
        $array['globalModalDetails'] .= '<thead class="thead-light"><tr><th colspan="3" class="text-center">Ryde Features</th></tr></thead>';
        $array['globalModalDetails'] .= '<thead class="thead-dark"><tr><th></th><th></th><th></th></tr></thead>';
            $count = 1;
            foreach ($rydes->rydeFeatures as $feature) {
            $array['globalModalDetails'] .= '<tr>';
            $array['globalModalDetails'] .= '<td colspan="3">' .  $feature->option->name. '</td>';
            $array['globalModalDetails'] .= '</tr>';
            }
            $array['globalModalDetails'] .= '</table>';

        return response()->json(['success' => true, 'data' => $array]);
    }

}
