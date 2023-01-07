<?php

namespace App\Http\Resources;

use App\BaseAppNotification;
use App\LanguageString;
use App\TransportMake;
use App\TransportModel;
use App\TransportType;
use App\Utility\Utility;
use App\WebPage;
use Geocoder\Laravel\Facades\Geocoder;
use Illuminate\Http\Resources\Json\JsonResource;

class GetDriverResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $appType = 'Driver';
        $webPages = WebPage::translated()->where(['page_status'=>1,'app_type'=>$appType])->orderBy('id','DESC')->first();
        if (!empty($webPages)){
            $webpages = new WebPageResource($webPages);
        }else{
            $webpages = null;
        }
        if(isset($this->DriverProfile->dp_transport_make_id) && $this->DriverProfile->dp_transport_make_id != null) {
            $make_name = TransportMake::translated()->where('transport_makes.id', $this->DriverProfile->dp_transport_make_id)->first();
        }
        if(isset($this->DriverProfile->dp_transport_model_id) && $this->DriverProfile->dp_transport_model_id != null) {
            $model_name = TransportModel::translated()->where('transport_models.id', $this->DriverProfile->dp_transport_model_id)->first();
        }
        $all_rating = $this->DriverRating->sum('dr_rating');
        $total = $this->DriverRating->count();
        $notification = BaseAppNotification::where(['ban_recipient_id'=>$this->id,'ban_is_hidden'=>0,'ban_is_unread'=>1,'ban_recipient_type'=>'Passenger'])->get()->count();

        $lat1= $request->passenger_lat;
        $long1 =$request->passenger_long;

        $lat2 = $this->dcl_lat;
        $long2 =$this->dcl_long;

        $distance = Utility::timeAndDistance($lat1,$long1,$lat2,$long2);
        $disDri = $distance->routes[0]->legs[0]->distance->value;
        $durDri = $distance->routes[0]->legs[0]->duration->value;

        if(isset($this->DriverProfile->dp_transport_type_id_ref) && $this->DriverProfile->dp_transport_type_id_ref != null) {
            $transport = TransportType::translated()->where('transport_types.id', $this->DriverProfile->dp_transport_type_id_ref)->first();
            $transporttypemaler = $transport->tt_marker;
            $transportimage = $transport->tt_image;
            $max_seat = $transport->tt_max_seats;
            $min_seat = $transport->tt_min_seats;
            $tt_id = $this->driverProf->dp_transport_type_id_ref;
            $tt_name = $transport->name;
        }else{
            $transporttypemaler = 'assets/transport_type_marker/1609852664-economy@3x.png';
            $transportimage = "";
            $max_seat = "";
            $min_seat = "";
            $tt_id = "";
            $tt_name = "";
        }
        if ($notification > 0){
            $notification = true;
        }else{
            $notification = false;
        }

        return [
            'id'=>$this->dcl_user_id,
            'company_id'=>$this->du_com_id,
            'name'=>$this->du_full_name,
            'user_name'=>$this->du_user_name,
            'email'=>$this->email,
            'country_code'=>$this->du_country_code,
            'mobile_no'=>$this->du_mobile_number,
            'full_mobile_number'=>$this->du_full_mobile_number,
            'locale'=>$this->locale,
            'profile_pic'=>$this->du_profile_pic,
            'is_driver'=>$this->du_is_driver,
            'email_verified'=>$this->is_email_verified,
            'mobile_number_verified'=>$this->du_mobile_number_verified	,
            'status'=>$this->du_driver_status,
            'is_company_update'=>$this->is_company_update,
            'is_signup_mobile'=>$this->is_signup_mobile,
            'user_type'=>'driver',
            'transport_marker'=>$transporttypemaler,
            'transport_image'=> $transportimage,
            'transport_min_seat'=> $min_seat,
            'transport_max_seat'=> $max_seat,
            'transport_type_id'=> $tt_id,
            'transport_type_name'=> $tt_name,
            'is_active'=>$this->du_is_active,
            'notification_count'=>$notification,
            'duration' => (isset($durDri) && $durDri != null) ? number_format((float)$durDri/60 , 0, '.', '') : '1',
            'distance' => (isset($disDri) && $disDri != null) ? number_format((float)$disDri/1000 , 3, '.', '') : "0.000",
            'lat'=>(isset($this->dcl_lat) && $this->dcl_lat != null) ? $this->dcl_lat : "",
            'long'=>(isset($this->dcl_long) && $this->dcl_long != null) ? $this->dcl_long : "",
            'app_active'=>(isset($this->dcl_app_active) && $this->dcl_app_active != null) ? $this->dcl_app_active : 0,
            'city'=>(isset($this->dcl_city) && $this->dcl_city != null) ? $this->dcl_city : "",
            'country'=>(isset($this->dcl_country) && $this->dcl_country != null) ? $this->dcl_country : "",
            'car_number'=>(isset($this->DriverProfile->car_registration) && $this->DriverProfile->car_registration != null) ? $this->DriverProfile->car_registration : "",
            'car_manufacture'=>(isset($make_name) && $make_name != null) ? $make_name->name : "",
            'car_model'=>(isset($model_name) && $model_name != null) ? $model_name->name : "",
            'rating'=>(isset($all_rating) && $all_rating != null) ? number_format((float)$all_rating/$total , 2, '.', '') : '0.00',
            'driver_approval_status'=>(isset($this->du_driver_status) && $this->du_driver_status != "driver_status_when_approved") ? false : true,
            'driver_approval_status_message'=>LanguageString::translated()->where('bls_name_key',$this->du_driver_status)->first()->name,
            'promotions' => $webpages
        ];
    }
}
