<?php


namespace App\Notification;

use App\Device;
use App\LanguageString;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use LaravelFCM\Message\Topics;
use FCM;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use App\User;

class Notification
{

    public static function sendnotificationall()
    {

        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60 * 20);

        $notificationBuilder = new PayloadNotificationBuilder('my title');
        $notificationBuilder->setBody('Hello world')
            ->setSound('default');

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData(['a_data' => 'my_data']);

        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

// You must change it to get your tokens
        //MYDATABASE::pluck('fcm_token')->toArray()
//        $tokens = Device::pluck('device_token')->toArray();
        $tokens = ['dKE0v5hyTTuaZ1CnNVFQ8H:APA91bG5elVkvY_1Giqe_J3fFk_-9ms2TLvV6tPje8yp4_O7Xr8xs-zT2edB8OU2AAOiBzuVyGsBTN6iZljH-G0U0JmmZL8vaHYGQYPB2R1ZDrTCU7Ozq899DB6v8hMoTDriu0YMXASf'];

        $downstreamResponse = FCM::sendTo($tokens, $option, $notification, $data);

        $downstreamResponse->numberSuccess();
        $downstreamResponse->numberFailure();
        $downstreamResponse->numberModification();
        return $downstreamResponse;

    }public static function sendnotificationtopic($topic_name)
    {
    $notificationBuilder = new PayloadNotificationBuilder('my title');
    $notificationBuilder->setBody('Hello world')
        ->setSound('default');

    $notification = $notificationBuilder->build();

    $topic = new Topics();
    $topic->topic($topic_name);

    $topicResponse = FCM::sendToTopic($topic, null, $notification, null);

    $downstreamResponse = $topicResponse->isSuccess();
    $topicResponse->shouldRetry();
    $topicResponse->error();
    return $downstreamResponse;
    }

    public static function sendnotificationtome($token)
    {

        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60 * 20);

        $notificationBuilder = new PayloadNotificationBuilder('my title');
        $notificationBuilder->setBody('Hello world')
            ->setSound('default');

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData(['a_data' => 'my_data']);

        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

// You must change it to get your tokens
        //MYDATABASE::pluck('fcm_token')->toArray()
//        $tokens = Device::pluck('device_token')->toArray();
//        $tokens = ['fBYPeTLu7kRXvuwqQdJ6ts:APA91bF-RtE6J0Q-ZRoWZgFzhUYoNZ2jXQhYJ-gBM5VuNu_WfFeEq-M8VCcoHFgecs54_zT8RVdd62yUsW-gJzc1RdUdq_bsyV9eOVw69WIksLTFeQ-NU51yde8pAuUeBqGwxVKK2VAi'];

        $downstreamResponse = FCM::sendTo($token, $option, $notification, $data);

       $result = $downstreamResponse->numberSuccess();
        $downstreamResponse->numberFailure();
        $downstreamResponse->numberModification();
        return $result;

    }


    public static function sendnotificationstest($tokensios,$tokensand,$title,$body,$sound,$action,$id,$type)
    {

        try {
            $optionBuilder = new OptionsBuilder();
            $optionBuilder->setTimeToLive(60 * 20);

            $notificationBuilder = new PayloadNotificationBuilder($title);
            $notificationBuilder->setBody($body)
                ->setSound($sound);
            $fcm_data = $notificationBuilder->build();
            $data=['action_name'=>$action,'type'=>$type,'id'=>$id,'title'=>$title,'body'=>$body];
            $dataBuilder = new PayloadDataBuilder();
            $dataBuilder->addData(['data' => $data]);

            $option = $optionBuilder->build();
            $notification = $notificationBuilder->build();
            $data = $dataBuilder->build();

// You must change it to get your tokens
            //MYDATABASE::pluck('fcm_token')->toArray()
//        $tokens = Device::pluck('device_token')->toArray();
//        $tokens = ['fBYPeTLu7kRXvuwqQdJ6ts:APA91bF-RtE6J0Q-ZRoWZgFzhUYoNZ2jXQhYJ-gBM5VuNu_WfFeEq-M8VCcoHFgecs54_zT8RVdd62yUsW-gJzc1RdUdq_bsyV9eOVw69WIksLTFeQ-NU51yde8pAuUeBqGwxVKK2VAi'];

            if(count($tokensand)>0) {
                $downstreamResponse = FCM::sendTo($tokensand, $option, null, $data);
            }
            if(count($tokensios)>0) {
                $downstreamResponse = FCM::sendTo($tokensios, $option, $notification, $data);
            }
            $result = $downstreamResponse->numberSuccess();
            $downstreamResponse->numberFailure();
            $downstreamResponse->numberModification();
            return $result;

        }catch(\Exception $e){
            $message = app('App\LanguageString')->get_string_message('attempted_is_fail', 1);
            $error = ['field'=>'User','message'=>$message];
            $errors =[$error];
            return response()->json(['success'=>false,'code'=>'500','errors' => $errors], 500);
        }

    }
    public static function sendnotifications($tokensios,$tokensand,$title,$body,$sound,$action,$id,$type,$driver_id,$passenger_id,$request,$drivers)
    {
        try {

            $optionBuilder = new OptionsBuilder();
            $optionBuilder->setTimeToLive(60 * 20);

            $optionBuilder->setPriority('high');
            $optionBuilder->setDelayWhileIdle(false);

            $notificationBuilder = new PayloadNotificationBuilder($title);
            $notificationBuilder->setBody($body)
                ->setSound($sound);
            $data=['action_name'=>$action,'type'=>$type,'id'=>$id,'title'=>$title,'body'=>$body,'driver_id'=>$driver_id,'passenger_id'=>$passenger_id,'request'=>(isset($request) && $request != null) ? $request->getAttributes() : null ,'total_drivers'=>$drivers];
            $dataBuilder = new PayloadDataBuilder();
            $dataBuilder->addData(['data' => $data]);

            $option = $optionBuilder->build();
            $notification = $notificationBuilder->build();
            $data = $dataBuilder->build();

// You must change it to get your tokens
            //MYDATABASE::pluck('fcm_token')->toArray()
//        $tokens = Device::pluck('device_token')->toArray();
//        $tokens = ['fBYPeTLu7kRXvuwqQdJ6ts:APA91bF-RtE6J0Q-ZRoWZgFzhUYoNZ2jXQhYJ-gBM5VuNu_WfFeEq-M8VCcoHFgecs54_zT8RVdd62yUsW-gJzc1RdUdq_bsyV9eOVw69WIksLTFeQ-NU51yde8pAuUeBqGwxVKK2VAi'];

            if(count($tokensand)>0) {
                $downstreamResponse = FCM::sendTo($tokensand, $option, null, $data);
            }
            if(count($tokensios)>0) {
                $downstreamResponse = FCM::sendTo($tokensios, $option, $notification, $data);
            }
            $result = $downstreamResponse->numberSuccess();
            $downstreamResponse->numberFailure();
            $downstreamResponse->numberModification();
            return $result;

        }catch(\Exception $e){
            $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
            $error = ['field'=>'User','message'=>$message];
            $errors =[$error];
//            return ['success'=>false,'code'=>'500','errors' => $errors];
            return 0;
        }

    }

    public static function sendnotificationsByAdminByTopic($topic_name,$title,$body,$sound)
    {
        try {
            $notificationBuilder = new PayloadNotificationBuilder($title);
            $notificationBuilder->setBody($body)
                ->setSound($sound);

            $notification = $notificationBuilder->build();

            $topic = new Topics();
            $topic->topic($topic_name);

            $topicResponse = FCM::sendToTopic($topic, null, $notification, null);

            $downstreamResponse = $topicResponse->isSuccess();
            $topicResponse->shouldRetry();
            $topicResponse->error();
            return $downstreamResponse;

        }catch(\Exception $e){
            $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
            $error = ['field'=>'User','message'=>$message];
            $errors =[$error];
            return response()->json(['success'=>false,'code'=>'500','errors' => $errors], 500);
        }

    }

    public static function sendnotificationsByAdmin($tokensios,$tokensand,$title,$body,$sound,$action,$type)
    {

        try {
            $optionBuilder = new OptionsBuilder();
            $optionBuilder->setTimeToLive(60 * 20);

            $optionBuilder->setPriority('high');

            $notificationBuilder = new PayloadNotificationBuilder($title);
            $notificationBuilder->setBody($body)
                ->setSound($sound);
            $data=['action_name'=>$action,'type'=>$type,'title'=>$title,'body'=>$body];
            $dataBuilder = new PayloadDataBuilder();
            $dataBuilder->addData(['data' => $data]);

            $option = $optionBuilder->build();
            $notification = $notificationBuilder->build();
            $data = $dataBuilder->build();

            if(count($tokensand)>0) {
                $downstreamResponse = FCM::sendTo($tokensand, $option, null, $data);
            }
            if(count($tokensios)>0) {
                $downstreamResponse = FCM::sendTo($tokensios, $option, $notification, $data);
            }
            $result = $downstreamResponse->numberSuccess();
            $downstreamResponse->numberFailure();
            $downstreamResponse->numberModification();
            return $result;

        }catch(\Exception $e){
            $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
            $error = ['field'=>'User','message'=>$message];
            $errors =[$error];
            return response()->json(['success'=>false,'code'=>'500','errors' => $errors], 500);
        }

    }
    public static function sendnotificationssilent($tokensios,$tokensand,$title,$body,$sound,$action,$id,$type,$driver_id,$passenger_id,$request,$drivers)
    {

        try {
            $optionBuilder = new OptionsBuilder();
            $optionBuilder->setTimeToLive(60 * 20);
            $optionBuilder->setPriority('high');
            $notificationBuilder = new PayloadNotificationBuilder($title);
            $notificationBuilder->setBody($body)
                ->setSound($sound);
            $fcm_data = $notificationBuilder->build();
            $data=['action_name'=>$action,'type'=>$type,'id'=>$id,'title'=>$title,'body'=>$body,'driver_id'=>$driver_id,'passenger_id'=>$passenger_id,'request'=>$request,'total_drivers'=>$drivers];
            $dataBuilder = new PayloadDataBuilder();
            $dataBuilder->addData(['data' => $data]);

            $option = $optionBuilder->build();

            $option1 = $optionBuilder->setContentAvailable(true)->build();
            $notification = $notificationBuilder->build();
            $data = $dataBuilder->build();

// You must change it to get your tokens
            //MYDATABASE::pluck('fcm_token')->toArray()
//        $tokens = Device::pluck('device_token')->toArray();
//        $tokens = ['fBYPeTLu7kRXvuwqQdJ6ts:APA91bF-RtE6J0Q-ZRoWZgFzhUYoNZ2jXQhYJ-gBM5VuNu_WfFeEq-M8VCcoHFgecs54_zT8RVdd62yUsW-gJzc1RdUdq_bsyV9eOVw69WIksLTFeQ-NU51yde8pAuUeBqGwxVKK2VAi'];

            if(count($tokensand)>0) {
                $downstreamResponse = FCM::sendTo($tokensand, $option, null, $data);
            }
            if(count($tokensios)>0) {
                $downstreamResponse = FCM::sendTo($tokensios, $option1, null, $data);
            }
            $result = $downstreamResponse->numberSuccess();
            $downstreamResponse->numberFailure();
            $downstreamResponse->numberModification();
            return $result;

        }catch(\Exception $e){
            $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
        $error = ['field'=>'language_strings','message'=>$message];
        $errors =[$error];
        return response()->json(['errors' => $errors], 500);
        }

    }
    public static function shortNumber($num)
    {
        $units = ['', 'K', 'M', 'B', 'T'];
        for ($i = 0; $num >= 1000; $i++) {
            $num /= 1000;
        }
        return round($num, 1) . $units[$i];
    }

}
