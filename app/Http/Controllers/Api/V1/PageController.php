<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\LanguageStringResource;
use App\Http\Resources\PageResource;
use App\LanguageString;
use App\Page;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;

class PageController extends Controller
{
    /**
     *  Display a listing of Page
     * @param $pptype
     * @return Response
     * @throws Exception
     */
    public function index($appType)
    {

        try{
            if ($appType == 'Driver'){
                $slug = ['privacy_policy', 'terms_and_conditions', 'about_whipp', 'language'];
            }else{
                $slug = ['privacy_policy_passenger', 'terms_and_conditions_passenger', 'about_whipp_passenger', 'language'];
            }
            App::setLocale('en');
            $pages = PageResource::collection(Page::translated()->whereIn('slug', $slug)->where(['page_status'=>1,'app_type'=>$appType])->orderBy('id','DESC')->get());
            if($pages == null){
                $pages = [];
            }

            return response()->json($pages);
        }catch(\Exception $e){
            $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
            $error = ['field'=>'pages','message'=>$message];
            $errors =[$error];
            return response()->json(['success'=>false,'code'=>'500','errors' => $errors], 500);
        }
    }
    /**
     *  Display a listing of setting page
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function settingPages($appType)
    {
        try{
            $pages = PageResource::collection(Page::translated()->where(['page_status'=>1,'app_type'=>$appType])->get());
            if($pages == null){
                $pages = [];
            }

            return $pages;
        }catch(\Exception $e){
            $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
            $error = ['field'=>'pages','message'=>$message];
            $errors =[$error];
            return response()->json(['success'=>false,'code'=>'500','errors' => $errors], 500);
        }
    }
}
