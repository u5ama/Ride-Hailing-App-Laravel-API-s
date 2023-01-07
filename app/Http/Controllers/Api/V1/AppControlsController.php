<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\AppControlsResource;
use App\BaseAppControl;
use App\LanguageString;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AppControlsController extends Controller
{
    /**
     * Display a listing of the App Controls.
     * @throws Exception
     */
    public function index()
    {

        try{
        $app_controls = AppControlsResource::collection(BaseAppControl::where('bac_status',1)->get());

        return $app_controls;
                }catch(\Exception $e){
            $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
            $error = ['field'=>'Languages','message'=>$message];
            $errors =[$error];
            return response()->json(['success'=>false,'code'=>'500','errors' => $errors], 500);
            }
    }
}
