<?php

namespace App\Http\Controllers\Api\V1;

use App\Announcement;
use App\CompanyAddress;
use App\RydeFeature;
use App\Setting;
use App\Brand;
use App\Body;
use App\BrandModel;
use App\Company;
use App\Country;
use App\Vehicle;
use App\Engine;
use App\Gearbox;
use App\Door;
use App\Fuel;
use App\Insurance;
use App\VehicleFeature;
use App\Http\Resources\BodyResource;
use App\Http\Resources\ModelResource;
use App\Http\Resources\MakeResource;
use App\Http\Resources\CompanyResource;
use App\Http\Resources\InsuranceResource;
use App\Http\Resources\SettingResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class VehicleController extends Controller
{
    /**
     * Vehicle List
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'page' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'pick_up_date' => 'required',
            'return_date' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 403);
        }

        $filter_array['location'] = $request->input('location');
        $filter_array['latitude'] = $request->input('latitude');
        $filter_array['longitude'] = $request->input('longitude');
        $filter_array['pick_up_date'] = $request->input('pick_up_date');
        $filter_array['body_id'] = $request->input('body_id');
        $filter_array['return_date'] = $request->input('return_date');
        $filter_array['sort_by'] = $request->input('sort_by');
        $filter_array['start_price'] = $request->input('start_price');
        $filter_array['end_price'] = $request->input('end_price');
        $filter_array['dealer_id'] = $request->input('dealer_id');
        $filter_array['insurance_id'] = $request->input('insurance_id');
        $filter_array['make'] = $request->input('make');
        $filter_array['model'] = $request->input('model');
        $filter_array['page'] = $request->input('page');
        $filter_array['limit'] = 50;


        $var = new CompanyAddress();

        $vehicles = $var->haversine($var, $filter_array);
        $array = [];
        $i = 0;


        foreach ($vehicles as $vehicle) {
            $array[$i]['vehicle_id'] = $vehicle->vehicle_id;
            $array[$i]['company_id'] = $vehicle->company_id;
            $array[$i]['company_address_id'] = $vehicle->id;
            $array[$i]['company_name'] = $vehicle->company_name;
            $array[$i]['hourly_amount'] = $vehicle->hourly_amount;
            $array[$i]['daily_amount'] = $vehicle->daily_amount;
            $array[$i]['weekly_amount'] = $vehicle->weekly_amount;
            $array[$i]['monthly_amount'] = $vehicle->monthly_amount;
            $array[$i]['image'] = $vehicle->image;
            $array[$i]['make'] = MakeResource::collection(Brand::where('id', $vehicle->brand_id)->get());
            $array[$i]['model'] = ModelResource::collection(BrandModel::where('id', $vehicle->brand_model_id)->get());
            $feature_array = [];

            $features = RydeFeature::with('feature')
                ->where('ryde_id', $vehicle->ryde_id)
                ->get();

            $j = 0;
            foreach ($features as $feature) {
                $feature_array[$j]['image'] = $feature->feature->image;
                $feature_array[$j]['name'] = $feature->feature->name;
                $j++;
            }
            $array[$i]['features'] = $feature_array;
            $i++;

        }

        $announcement = '';
        if ($request->input('country_code') != NULL) {
            $country_data = $this->getCountryId($request->input('country_code'));
            if ($country_data) {
                $announcements_data = Announcement::where("country_id", $country_data->id)->first();
                $announcement = $announcements_data->name;
            }
        }

        $filters = $var->haverSineData($var, $filter_array);

        $bodies_array = $makes_array = $models_array = $insurances_array = $dealers_array = $price_array = [];
        foreach ($filters as $filter) {
            $bodies_array[] = $filter->body_id;
            $makes_array[] = $filter->brand_id;
            $models_array[] = $filter->brand_model_id;
            $insurances_array[] = $filter->insurance_id;
            $dealers_array[] = $filter->id;
            $price_array[] = $filter->hourly_amount;
        }

        $price_value = $this->priceMinMax($price_array);

        return response()->json([
            'success' => true,
            'data' => $array,
            'announcements' => $announcement,
            'dealers_array' => $this->getDealers($dealers_array),
            'bodies_array' => $this->getBodies($bodies_array),
            'makes_array' => $this->getMakes($makes_array),
            'models_array' => $this->getModel($models_array, $makes_array),
            'insurances_array' => $this->getInsurance($insurances_array),
            'price_min' => $price_value['price_min'],
            'price_max' => $price_value['price_max'],
            'search' => $this->settingValue()
        ]);
    }

    /**
     * Price Min max
     * @param $price_array
     * @return Response
     * @throws Exception
     */

    public function priceMinMax($price_array)
    {
        if (count($price_array) > 0) {
            return ['price_min' => min($price_array), 'price_max' => max($price_array)];
        }
        return ['price_min' => 0, 'price_max' => 0];
    }

    /**
     * setting Value
     * @param $price_array
     * @return Response
     * @throws Exception
     */

    public function settingValue()
    {
        $settings = Setting::select('meta_value', 'meta_key')->get();
        $array = [];
        foreach ($settings as $setting) {
            $array[$setting->meta_key] = $setting->meta_value;
        }
        return $array;

    }

    /**
     * get Insurance
     * @param $insurances_array
     * @return Response
     * @throws Exception
     */

    public function getInsurance($insurances_array)
    {
        $insurances_array = array_unique($insurances_array);

        return InsuranceResource::collection(
            Insurance::orderBy('insurance_order', 'ASC')->whereIn('id', $insurances_array)->get()
        );

    }

   /**
     * get Makes
     * @param $makes_array
     * @return Response
     * @throws Exception
     */

    public function getMakes($makes_array)
    {
        $makes_array = array_unique($makes_array);

        return MakeResource::collection(
            Brand::orderBy('brand_order', 'ASC')->whereIn('id', $makes_array)->get()
        );

    }

    public function getModel($models_array, $makes_array)
    {
        $makes_array = array_unique($makes_array);
        $models_array = array_unique($models_array);


        $array = [];
        $i = 0;
        foreach ($makes_array as $makes) {
            $models = ModelResource::collection(
                BrandModel::orderBy('model_order', 'ASC')->where('brand_id', $makes)->whereIn('id', $models_array)->get()
            );
            $array[$i]['id'] = $makes;
            $array[$i]['models'] = $models;
            $i++;
        }
        return $array;
    }

    public function getBodies($bodies_array)
    {
        $bodies_array = array_unique($bodies_array);

        return BodyResource::collection(
            Body::orderBy('body_order', 'ASC')->whereIn('id', $bodies_array)->get()
        );

    }

    public function getDealers($dealers_array)
    {
        $dealers_array = array_unique($dealers_array);

        return CompanyResource::collection(
            Company::orderBy('name', 'ASC')->whereIn('id', $dealers_array)->get()
        );

    }

    public function getCountryId($country_code)
    {
        return Country::where('country_code', $country_code)->first();
    }

    public function show($id)
    {
        // DB::enableQueryLog();

        $response = Vehicle::with(['ryde' => function ($query) {
            $query->with('brand', 'modelYear', 'model', 'rydeInstance', 'rydeFeatures');
        }, 'vehicleExtra' => function ($query) {
            $query->with('extra');
        }, 'insurances','color'])->first();
        $extra_array = [];
        // dd($response);

        $engine = Engine::where('id', $response->ryde->rydeInstance->engine_id)->first();
        $fuel = Fuel::where('id', $response->ryde->rydeInstance->fuel_id)->first();
        $gearBox = GearBox::where('id', $response->ryde->rydeInstance->gearbox_id)->first();
        $bodies = Body::where('id', $response->ryde->rydeInstance->body_id)->first();
        $door = Door::where('id', $response->ryde->rydeInstance->door_id)->first();


        foreach ($response->vehicleExtra as $key => $extra) {
            $extra_array[$key]['id'] = $extra->extra->id;
            $extra_array[$key]['name'] = $extra->extra->translate('en')->name;
            $extra_array[$key]['price'] = $extra->price;
        }
        return [
            'success' => true,
            'make' => $response->ryde->brand->translate('en')->name,
            'model' => $response->ryde->model->translate('en')->name,
            'bodies' => $bodies->translate('en')->name,
            'engines' => $engine->translate('en')->name,
            'fuels' => $fuel->translate('en')->name,
            'door' => $door->translate('en')->name,
            'gearboxes' => $gearBox->translate('en')->name,
            'insurances' => $response->insurances->translate('en')->name,
            'seats' => $response->ryde->rydeInstance->seats,
            'color' => $response->color->translate('en')->name,
            'extra' => $extra_array,
        ];
    }
}
