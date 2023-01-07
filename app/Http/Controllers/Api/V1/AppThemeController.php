<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\AppThemeResource;
use App\BaseAppTheme;
use App\LanguageString;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AppThemeController extends Controller
{
    /**
     * Display a listing of App Theme
     * @return Response
     * @throws Exception
     */ 

    public function index()
    {
        try{

        $themes = AppThemeResource::collection(BaseAppTheme::where('bat_status',1)->get());

        return $themes;
            }catch(\Exception $e){
        $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
        $error = ['field'=>'language_strings','message'=>$message];
        $errors =[$error];
        return response()->json(['success'=>false,'code'=>'500','errors' => $errors], 500);
        }
    }
}
