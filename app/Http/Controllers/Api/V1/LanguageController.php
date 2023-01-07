<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\LangaugeResource;
use App\Language;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\LanguageString;

class LanguageController extends Controller
{
    /**
     *  Display a listing of Language
     * @return Response
     * @throws Exception
     */

    public function index()
    {


        try{
            $languages = LangaugeResource::collection(Language::where('status', 1)->get());
            if($languages == null){
                $languages = [];
            }

            return $languages;
        }catch(\Exception $e){
            $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
            $error = ['field'=>'Languages','message'=>$message];
            $errors =[$error];
            return response()->json(['success'=>false,'code'=>'500','errors' => $errors], 500);
        }
    }
}
