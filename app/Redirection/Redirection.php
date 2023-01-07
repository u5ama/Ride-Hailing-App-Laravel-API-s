<?php
namespace App\Redirection;

use App\BaseAppSocialLinks;
use App\Driver;
use App\EmailBodyTranslation;
use App\EmailFooterTranslation;
use App\EmailHeader;
use App\EmailHeaderTranslation;
use App\Mail\DriverStatusEmail;
use App\Mail\DriverVerifyEmail;
use App\Mail\PassengerVerifyEmail;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Unicodeveloper\Identify\Identify;

class Redirection
{

    public function createnewpassword(Request $request){

        try {

            $identity = new Identify();
            $osname = $identity->os()->getName();

            $token = $request->input('token');
            $device = $request->input('device');
            $email = $request->input('email');

            $url = URL::current();

            $data = array();
            if ($osname == 'iOS') {
                $data['primaryRedirection'] = 'intent://'.$url."?token=".$token.'&email='.$email;
                $data['secndaryRedirection'] = 'intent://'.$url."?token=".$token.'&email='.$email;
                $url= 'https://app.travelistkw.com/reset_password.php?email='.$email.'&token='.$token;
                return redirect()->away($url);
            }
            if ($osname == 'Android') {
                $data['primaryRedirection'] = 'intent://reset_password/#Intent;scheme=travelist;package=com.travelist.hi5;S.email='.$email.';S.token='.$token.';end';
                $data['secndaryRedirection'] = 'intent://reset_password/#Intent;scheme=travelist;package=com.travelist.hi5;S.email='.$email.';S.token='.$token.';end';
                $data['primaryRedirection'] = 'https://nodejsblog.000webhostapp.com/reset_password.php?email='.$email.'&token='.$token;
                $data['secndaryRedirection'] = 'https://nodejsblog.000webhostapp.com/reset_password.php?email='.$email.'&token='.$token;
                $url= 'https://app.travelistkw.com/reset_password.php?email='.$email.'&token='.$token;
                return redirect()->away($url);
            }
            if($osname!=='iOS' && $osname !== 'Android'){

                $url = 'https://app.travelistkw.com/';
                return redirect()->away($url);

            }
        } catch (\Exception $e) {
            $error = ['field'=>'User','message'=>"Device is not recognised"];
            $errors =[$error];
            return response()->json(['success'=>false,'code'=>'500','errors' => $errors], 500);
        }

    }

    public static function verifyEmailPassenger($id){

        try{
        $identity = new Identify();
            $osname = $identity->os()->getName();

            $user = User::where('id',$id)->update(['email_verified'=>1]);
            $user = User::where('id',$id)->first();
            $passenger_name = $user->name;
            $socialLinks = BaseAppSocialLinks::all();
            $header = EmailHeader::where('id',10)->first();
            $headerTrans = EmailHeaderTranslation::where(['email_header_id' => 10, 'locale' => $user->locale])->first();

            $bodyTrans = EmailBodyTranslation::where(['email_body_id'=> 10, 'locale' => $user->locale])->first();

            $footerTrans = EmailFooterTranslation::where(['email_footer_id'=> 10,'locale' => $user->locale])->first();
            $langtxt = $user->locale;
            $user_type = "user";

            Mail::to($user->email)->send(new PassengerVerifyEmail($passenger_name,$user->id,$socialLinks,$header,$headerTrans,$bodyTrans,$footerTrans,$langtxt,$user_type));

            if($osname == "iOS"){
                $url = 'https://app.apis.ridewhipp.com/verifiedphp';
                return $url;
            }
            if($osname == "Android"){
                $url = 'https://app.apis.ridewhipp.com/verifiedphp';
                return $url;
            }
            if($osname != 'iOS' && $osname != 'Android'){
                $url = 'https://app.apis.ridewhipp.com/';
                return $url;
            }

        } catch (\Exception $e) {
            $error = ['field'=>'User','message'=>"Device is not recognised"];
            $errors =[$error];
            return response()->json(['success'=>false,'code'=>'500','errors' => $errors], 500);
        }
    }
    public static function verifyEmailDriver($id){

        try{
        $identity = new Identify();
            $osname = $identity->os()->getName();

            $user = Driver::where('id',$id)->update(['is_email_verified'=>1]);

            $driver = Driver::where('id',$id)->first();
            $driver_name = $driver->du_full_name;

            $socialLinks = BaseAppSocialLinks::all();
            $header = EmailHeader::where('id',9)->first();
            $headerTrans = EmailHeaderTranslation::where(['email_header_id' => 9, 'locale' => $driver->locale])->first();

            $bodyTrans = EmailBodyTranslation::where(['email_body_id'=> 9, 'locale' => $driver->locale])->first();

            $footerTrans = EmailFooterTranslation::where(['email_footer_id'=> 9,'locale' => $driver->locale])->first();
            $langtxt = $driver->locale;
            $user_type = "driver";

            $m = Mail::to($driver->email)->send(new DriverVerifyEmail($driver_name,$driver->id,$socialLinks,$header,$headerTrans,$bodyTrans,$footerTrans,$langtxt,$user_type));

            if($osname == "iOS"){
                $url = 'https://app.apis.ridewhipp.com/driververifiedphp';
                return $url;
            }
            if($osname == "Android"){
                $url = 'https://app.apis.ridewhipp.com/driververifiedphp';
                return $url;
            }
            if($osname != 'iOS' && $osname != 'Android'){
                $url = 'https://app.apis.ridewhipp.com/';
                return $url;
            }

        } catch (\Exception $e) {
            $error = ['field'=>'User','message'=>"Device is not recognised"];
            $errors =[$error];
            return response()->json(['success'=>false,'code'=>'500','errors' => $errors], 500);
        }
    }

    public function changeemail(Request $request){

        try{
            $identity = new Identify();
            $osname = $identity->os()->getName();
            $email = $request->get('email');
            $id = $request->get('id');
            $password = $request->get('password');
            $user = User::where('id',$id)->update(['is_Verified'=>1,'f_uu_id'=> null,'g_uu_id'=> null,'ios_id'=> null,'email'=>$email,'password'=>bcrypt($password)]);

            if($osname == "iOS"){
                $url = 'https://app.travelistkw.com/';
                return redirect()->away($url);;
            }
            if($osname == "Android"){
                $url = 'https://app.travelistkw.com/';
                return redirect()->away($url);;

            }
            if($osname!=='iOS' && $osname !== 'Android'){

                $url = 'https://app.travelistkw.com/';
                return redirect()->away($url);

            }

        } catch (\Exception $e) {
            $error = ['field'=>'User','message'=>"Device is not recognised"];
            $errors =[$error];
            return response()->json(['success'=>false,'code'=>'500','errors' => $errors], 500);
        }

    }

    public function shareapp(Request $request){

        try{
            $identity = new Identify();
            $osname = $identity->os()->getName();

            if($osname == "iOS"){
                $url = 'https://apps.apple.com/us/app/travelist/id1498411647';
                return redirect()->away($url);
            }
            if($osname == "Android"){
                $url = 'https://play.google.com/store/apps/details?id=com.travelist.hi5';
                return redirect()->away($url);

            }
            if($osname!=='iOS' && $osname !== 'Android'){

                $url = 'http://hi5.travelistkw.com/';
                return redirect()->away($url);

            }

        } catch (\Exception $e) {
            $error = ['field'=>'User','message'=>"Device is not recognised"];
            $errors =[$error];
            return response()->json(['success'=>false,'code'=>'500','errors' => $errors], 500);
        }

    }
    public function sharelist(Request $request,$id){

        try{
            $osname = \Identify::os()->getName();

            if($osname == "iOS"){
                $url = 'https://apps.apple.com/us/app/travelist/id1498411647';
                return redirect()->away($url);;
            }
            if($osname == "Android"){
                $url = 'https://play.google.com/store/apps/details?id=com.travelist.hi5';
                return redirect()->away($url);;

            }
            if($osname!=='iOS' && $osname !== 'Android'){

                $url = 'http://hi5.travelistkw.com/';
                return redirect()->away($url);

            }

        } catch (Exception $e) {
            $error = ['field'=>'User','message'=>"Device is not recognised"];
            $errors =[$error];
            return response()->json(['success'=>false,'code'=>'500','errors' => $errors], 500);
        }

    }


    public static function is_in_polygon($points_polygon, $vertices_x, $vertices_y, $longitude_x, $latitude_y)
    {
        $i = $j = $c = 0;
        for ($i = 0, $j = $points_polygon ; $i < $points_polygon; $j = $i++) {
            if ( (($vertices_y[$i]  >  $latitude_y != ($vertices_y[$j] > $latitude_y)) &&
                ($longitude_x < ($vertices_x[$j] - $vertices_x[$i]) * ($latitude_y - $vertices_y[$i]) / ($vertices_y[$j] - $vertices_y[$i]) + $vertices_x[$i]) ) )
                $c = !$c;
        }
        return $c;
    }
}
