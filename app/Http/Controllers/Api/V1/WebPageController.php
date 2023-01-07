<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\LanguageStringResource;
use App\Http\Resources\PageResource;
use App\Http\Resources\WebPageResource;
use App\LanguageString;
use App\Page;
use App\WebPage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class WebPageController extends Controller
{
    /**
     *  Display a listing of Page
     * @param $pptype
     * @return WebPageResource
     * @throws Exception
     */
    public function index($appType)
    {

        try{
            $webPages = WebPage::translated()->where(['page_status'=>1,'app_type'=>$appType])->orderBy('id','DESC')->first();
            if (!empty($webPages)){
                $pages = new WebPageResource($webPages);
            }else{
                $pages = null;
            }

            return $pages;
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
            $pages = new WebPageResource(WebPage::translated()->where(['page_status'=>1,'app_type'=>$appType])->orderBy('id','DESC')->first());
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
