<?php

namespace App\Http\Controllers\Api\V1;
use App\Http\Resources\AppThemeResource;
use App\Http\Resources\AppThemeDesignResource;
use App\BaseAppTheme;
use App\BaseAppThemeDesign;
use App\LanguageString;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AppThemeDesignController extends Controller
{
    /**
     * Display a listing of App Theme Design
     * @param  device_type, theme_id
     * @param Request $request
     * @return Response
     * @throws Exception
     */ 

    public function index(Request $request)
    {
        try{
       $device_type = $request->get('device_type');
       $theme_id = $request->get('theme_id');
       $rowtheme = BaseAppTheme::where(['bat_status'=>1,'id'=>$theme_id])->first();
        $app_theme_design = AppThemeDesignResource::collection(BaseAppThemeDesign::where(['batd_status'=> 1,'batd_device_type'=>$device_type,'batd_theme_ref_id'=> $theme_id])->get());

        return $app_theme_design;
            }catch(\Exception $e){
        $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
        $error = ['field'=>'language_strings','message'=>$message];
        $errors =[$error];
        return response()->json(['success'=>false,'code'=>'500','errors' => $errors], 500);
        }
    }
}
