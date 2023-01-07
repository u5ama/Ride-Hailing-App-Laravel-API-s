<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\LanguageStringResource;
use App\LanguageString;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LanguageStringController extends Controller
{
    /**
     *  Display a listing of Language String
     * @param Request $request
     * @return Response
     * @throws Exception
     */

    public function index($driver_passenger)
    {
       try{


           $language_strings = [];
           if($driver_passenger == "Driver") {
               $language_strings = LanguageStringResource::collection(
                   LanguageString::translated()->where(['bls_app_or_panel'=>1,'bls_driver_or_passenger'=>$driver_passenger])->get()
               );
           }if($driver_passenger == "Passenger") {
               $language_strings = LanguageStringResource::collection(
                   LanguageString::translated()->where(['bls_app_or_panel'=>1,'bls_driver_or_passenger'=>$driver_passenger])->get()
               );
           }


        return response()->json( $language_strings,200, ['Content-Type' => 'application/json; charset=UTF-8',
            'charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);;
            }catch(\Exception $e){
        $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
        $error = ['field'=>'language_strings','message'=>$message];
        $errors =[$error];
        return response()->json(['success'=>false,'code'=>'500','errors' => $errors], 500);
        }
    }
    
}
