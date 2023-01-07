<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\AppSocialLinkResource;
use App\BaseAppSocialLinks;
use App\LanguageString;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AppSocialLinkController extends Controller
{
    /**
     * Display a listing of App Social Link
     * @return Response
     * @throws Exception
     */ 

    public function index()
    {

        try{
        $social_links = AppSocialLinkResource::collection(BaseAppSocialLinks::where('basl_status',1)->get());

        return $social_links;
            }catch(\Exception $e){
        $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
        $error = ['field'=>'language_strings','message'=>$message];
        $errors =[$error];
        return response()->json(['success'=>false,'code'=>'500','errors' => $errors], 500);
        }
    }
}
