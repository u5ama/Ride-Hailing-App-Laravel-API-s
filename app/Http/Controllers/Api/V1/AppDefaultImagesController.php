<?php

namespace App\Http\Controllers\Api\V1;

use App\BaseAppTheme;
use App\BaseDefaultImage;
use App\Http\Resources\AppDefaultImagesResource;
use App\LanguageString;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AppDefaultImagesController extends Controller
{
    /**
     * Display a listing of the App Default Images.
     * @throws Exception
     */

    public function getimages(Request $request){


        $device_type = $request->get('device_type');
        try {
            // get active app theme  
            $rowtheme = BaseAppTheme::where('bat_status', 1)->select('id','bat_theme_name as theme_name')->first();
            $results = AppDefaultImagesResource::collection(BaseDefaultImage::where(['bdi_status'=> 1,'bdi_device_type'=>$device_type,'bdi_theme_ref_id'=> $rowtheme->id])->get());
            $theme['theme_id'] = $rowtheme->id;
            $theme['theme_name'] = $rowtheme->theme_name;

            return ['theme'=>$theme,"data"=>$results];
        }catch(\Exception $e){
            $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
            $error = ['field'=>'all_app_default_images_not_created','message'=>$message];
            $errors =[$error];
            return response()->json(['success'=>false,'code'=>'500','errors' => $errors], 500);
        }

    }
}
