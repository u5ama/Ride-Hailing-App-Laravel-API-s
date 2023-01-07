<?php

namespace App\Http\Controllers\Api\V1;

use App\BaseAppNotification;
use App\Http\Resources\GetMyNotificationResource;
use App\LanguageString;
use App\Utility\Utility;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;

class AppNotificationController extends Controller
{
     /**
     * Display a listing of the App Notification.
     * @param  $token
     * @param Request $request
     * @return Response
     * @throws Exception
     */

    public function getNotifications(Request  $request){
        try{

            $token=JWTAuth::getToken();
            $driver = \Auth::guard('driver')->user();;
            if($driver) {
                $user = $driver;
                $user_type = 'Driver';
                Log::info('app.requests', ['request' => $request->all(),'URL'=>$request->url()]);
                DB::table('base_app_notifications')->update(['ban_is_unread' => 0]);
                $notifications = GetMyNotificationResource::collection(BaseAppNotification::where(['ban_recipient_id'=>$user->id,'ban_recipient_type'=>$user_type,'ban_is_hidden'=>0])->orderBy('id','desc')->get());
                $results = Utility::paginate($notifications,$request);
                return response()->json(['notifications'=>$results['data'],'pagination_urls'=>$results['pagination_urls']],200);

            }else{
                $user = JWTAuth::toUser($token);
                $user_type = 'Passenger';
                Log::info('app.requests', ['request' => $request->all(),'URL'=>$request->url()]);
                DB::table('base_app_notifications')->update(['ban_is_unread' => 0]);
                $notifications = GetMyNotificationResource::collection(BaseAppNotification::where(['ban_recipient_id'=>$user->id,'ban_recipient_type'=>$user_type,'ban_is_hidden'=>0])->orderBy('id','desc')->get());
                $results = Utility::paginate($notifications,$request);
                return response()->json(['notifications'=>$results['data'],'pagination_urls'=>$results['pagination_urls']],200);

            }
 }catch(\Exception $e){
            $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
            $error = ['field'=>'language_strings','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }

    }

    /**
     * Display a listing of hide Notification.
     * @param  $id
     * @param Request $request
     * @return Response
     * @throws Exception
     */

    public function hideNotification(Request  $request,$id){
        try{

            $token=JWTAuth::getToken();
            $user = JWTAuth::toUser($token);
            if(!$user) {
                $user = \Auth::guard('driver')->user();
                $user_type = 'Driver';
            }else{
                $user_type = $user->user_type;
                $user_type = 'Passenger';
            }
            Log::info('app.requests', ['request' => $request->all(),'URL'=>$request->url()]);
            $hide = BaseAppNotification::where(['id'=>$id,'ban_recipient_id'=>$user->id])->update(['ban_is_hidden'=>1]);
            $notifications = GetMyNotificationResource::collection(BaseAppNotification::where(['ban_recipient_id'=>$user->id,'ban_recipient_type'=>$user_type,'ban_is_hidden'=>0])->orderBy('id','desc')->get());
            $results = Utility::paginate($notifications,$request);
            return response()->json(['notifications'=>$results['data'],'pagination_urls'=>$results['pagination_urls']],200);
        }catch(\Exception $e){
            $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
            $error = ['field'=>'language_strings','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }

    }

    /**
     * Display a listing of Seen and Unseen Notification.
     * @param  $id
     * @param Request $request
     * @return Response
     * @throws Exception
     */

    public function SeenUnseenNotification(Request  $request,$id){
        try{
            $token=JWTAuth::getToken();
            $user = JWTAuth::toUser($token);
            if(!$user) {
                $user = \Auth::guard('driver')->user();
                $user_type = 'Driver';
            }else{
                $user_type = $user->user_type;
                $user_type = 'Passenger';
            }
            // log created
            Log::info('app.requests', ['request' => $request->all(),'URL'=>$request->url()]);
            $read = BaseAppNotification::where(['id'=>$id,'ban_recipient_id'=>$user->id])->update(['ban_is_unread'=>0]);
            $notifications = GetMyNotificationResource::collection(BaseAppNotification::where(['ban_recipient_id'=>$user->id,'ban_recipient_type'=>$user_type,'ban_is_hidden'=>0])->orderBy('id','desc')->get());
            $results = Utility::paginate($notifications,$request);
            return response()->json(['notifications'=>$results['data'],'pagination_urls'=>$results['pagination_urls']],200);
        }catch(\Exception $e){
            $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
            $error = ['field'=>'language_strings','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }
    }
}
