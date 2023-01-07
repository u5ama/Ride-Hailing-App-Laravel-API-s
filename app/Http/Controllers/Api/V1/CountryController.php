<?php

namespace App\Http\Controllers\Api\V1;

use App\Country;
use App\Http\Resources\CountryResource;
use App\LanguageString;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CountryController extends Controller
{
    /**
     * Display a listing of  Countries
     * @return Response
     * @throws Exception
     */

    public function index()
    {
        try{
        $countries = CountryResource::collection(
            Country::where('status', 'Active')->orderBy('country_order', 'ASC')->get()
        );

        return $countries;
            }catch(\Exception $e){
        $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
        $error = ['field'=>'Languages','message'=>$message];
        $errors =[$error];
        return response()->json(['success'=>false,'code'=>'500','errors' => $errors], 500);
        }
    }
}
