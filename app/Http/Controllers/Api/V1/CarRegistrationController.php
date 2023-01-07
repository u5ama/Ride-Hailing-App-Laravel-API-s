<?php

namespace App\Http\Controllers\Api\V1;

use App\AppReference;
use App\Http\Resources\FuelResource;
use App\Http\Resources\TransportModelResource;
use App\Http\Resources\TransportTypeResource;
use App\LanguageString;
use App\TransportModel;
use App\TransportType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CarRegistrationController extends Controller
{
    /**
     * Display a listing of  Transport Type
     * @return Response
     * @throws Exception
     */ 

    public function getTransportType()
    {

        try{

        $transportType = TransportTypeResource::collection(TransportType::listsTranslations('name','ttt_description')->get());

            if(count($transportType)>0) {
                return response()->json($transportType, 200);
            }else{

                return response()->json((object) null, 200);
            }

        }catch(\Exception $e){
            $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
            $error = ['field'=>'pages','message'=>$message];
            $errors =[$error];
            return response()->json(['success'=>false,'code'=>'500','errors' => $errors], 500);
        }
    }

    /**
     * Display a listing of  Transport Data
     * @return Response
     * @throws Exception
     */

    public function getTransportData()
    {

        try{

        $transportData = TransportTypeResource::collection(TransportType::listsTranslations('name','ttt_description')->get());

            if(count($transportData)>0) {
                return response()->json($transportData, 200);
            }else{

                return response()->json((object) null, 200);
            }

        }catch(\Exception $e){
            $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
            $error = ['field'=>'pages','message'=>$message];
            $errors =[$error];
            return response()->json(['success'=>false,'code'=>'500','errors' => $errors], 500);
        }
    }
    

    /**
     * Display a listing of  Transport Fuel
     * @return Response
     * @throws Exception
     */


    public function getTransportFuel()
    {

        try{

        $transportFuel = FuelResource::collection( AppReference::listsTranslations('name')->where(['bar_status'=>1,'bar_mod_id_ref'=>3,'bar_ref_type_id'=>4])->get());

            if(count($transportFuel)>0) {
                return response()->json($transportFuel, 200);
            }else{

                return response()->json((object) null, 200);
            }
        }catch(\Exception $e){
            $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
            $error = ['field'=>'pages','message'=>$message];
            $errors =[$error];
            return response()->json(['success'=>false,'code'=>'500','errors' => $errors], 500);
        }
    }




}
