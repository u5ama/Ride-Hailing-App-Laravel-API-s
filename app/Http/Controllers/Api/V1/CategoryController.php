<?php

namespace App\Http\Controllers\Api\V1;

use App\Category;
use App\Company;
use App\CompanyAddress;
use App\Country;
use App\Http\Resources\CategoryResource;
use App\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    /**
     * Display a listing of  category
     * @param Request $request
     * @return Response
     * @throws Exception
     */

    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'longitude' => 'required',
            'latitude' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 403);
        }


        $companies = CompanyAddress::query()
            ->selectDistanceTo($request->input('longitude'), $request->input('latitude'), 25)
            ->pluck('id');

        $response = Category::withCount(['CategoryVehicle' => function ($query) use ($companies) {
            $query->whereIn('category_vehicles.company_id', $companies);
        }])->orderBy('category_order', 'ASC')->get();

        $categories = CategoryResource::collection($response);

        if (count($categories) > 0) {
            return [
                'success' => true,
                'data' => $categories,
            ];
        } else {
            return [
                'success' => false,
                'message' => trans('messages.data_empty')
            ];
        }

    }
}
