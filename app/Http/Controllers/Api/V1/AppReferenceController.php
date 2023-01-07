<?php

namespace App\Http\Controllers\Api\V1;

use App\AppReference;
use App\Http\Resources\BaseAppReferenceResource;
use App\Http\Resources\PassengerAdressesResource;
use App\Http\Resources\PassengerGroupAdressesResource;
use App\LanguageString;
use App\PassengerAddress;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Facades\JWTAuth;

class AppReferenceController extends Controller
{
    
    /**
     * Display a listing of App Reference.
     * @param  ref_type_id, module_id
     * @param Request $request
     * @return Response
     * @throws Exception
     */ 

    public function index(Request  $request){
        try{

            $ref_type_id = $request->get('ref_type_id');
            $module_id = $request->get('module_id');
            $refrences = [];
            $refrences = BaseAppReferenceResource::collection(AppReference::listsTranslations('name')->select('base_app_references.id','base_app_references.bar_mod_id_ref','base_app_references.bar_ref_type_id','base_app_references.bar_icon','base_app_references.bar_image','base_app_references.bar_status','base_app_references.bar_system_flag','base_app_references.bar_order_by')->where(['bar_status'=>1,'bar_mod_id_ref'=>$module_id,'bar_ref_type_id'=>$ref_type_id])->get());

            return response()->json($refrences,200);
        }catch(\Exception $e){
            $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
            $error = ['field'=>'language_strings','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }

    }
}
