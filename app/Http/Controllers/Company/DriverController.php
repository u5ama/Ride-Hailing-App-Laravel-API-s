<?php

namespace App\Http\Controllers\Company;

use App\BaseAppSocialLinks;
use App\BaseNumber;
use App\Company;
use App\Driver;
use App\EmailBodyTranslation;
use App\EmailFooterTranslation;
use App\EmailHeader;
use App\EmailHeaderTranslation;
use App\Mail\DriverStatusEmail;
use App\Mail\WelcomeDriverEmail;
use App\Mail\WelcomeEmail;
use App\TransportModelYear;
use App\TransportMake;
use App\TransportType;
use App\TransportFuel;
use App\TransportModel;
use App\TransportModelColor;
use App\TransportModelColorTranslation;
use App\Language;
use App\DriverProfile;
use App\BaseMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Utility\Utility;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use League\Flysystem\Config;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class DriverController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            //DB::enableQueryLog();
            $driver = Driver::with('DriverProfile')->where('du_com_id',auth()->guard('company')->user()->id)->orderBy('id','DESC')->get();
            // dd(DB::getQueryLog());
            return Datatables::of($driver)
            ->addColumn('image', function ($driver) {
                $url = asset($driver->du_profile_pic);
                if(!empty($driver->du_profile_pic)){
                   $img = '<img   src="' . $url . '" width="50" height="50">';
                }else{
                    $url = asset('assets/default/driver.png');
                    $img = '<img   src="' . $url . '" width="100" height="100">';
                }
                return $img;
                })

                ->addColumn('type', function ($driver) {
                    $transport_name = '';
                    if(isset($driver->driverProf) && $driver->driverProf != null){
                        if (isset($driver->driverProf->dp_transport_type_id_ref) && $driver->driverProf->dp_transport_type_id_ref != null){

                            $id_type =$driver->driverProf->dp_transport_type_id_ref;
                            if(!empty($id_type)){
                                $t_type =  TransportType::listsTranslations('name')->where('transport_types.id',$id_type)->first();
                               $transport_name =  $t_type['name'];
                            }
                        }
                    }
                    return $transport_name;
                })
                ->addColumn('du_created_at', function ($driver) {
                    return Utility:: convertTimeToUSERzone($driver->du_created_at,Utility::getUserTimeZone(auth()->guard('company')->user()->com_time_zone));
                })->addColumn('dr_reg_status', function ($driver) {
                        if ($driver->du_is_reg_active == 1) {
                            $class = "badge badge-success";
                            $name = "Allow";
                        }
                        if ($driver->du_is_reg_active == 0) {
                            $class = "badge badge-warning";
                            $name = "Not Allow";
                        }
                        $dr_reg_status = '<a type="button" onclick="changeDriverRegStatus(' . $driver->id . ',' . $driver->du_is_reg_active . ')" class="' . $class . '" data-toggle="tooltip" data-placement="top" >' . $name . '</a>';
                    return $dr_reg_status;
                })
               ->addColumn('status', function ($driver) {
                   if(isset($driver->driverProf) && $driver->driverProf != null) {
                       if ($driver->du_driver_status == 'driver_status_when_block') {
                           $class = "badge badge-danger";
                           $name = "Block";
                       }
                       if ($driver->du_driver_status == 'driver_status_when_pending') {
                           $class = "badge badge-warning";
                           $name = "Pending";
                       }
                       if ($driver->du_driver_status == 'driver_status_when_approved') {
                           $class = "badge badge-success";
                           $name = "Approve";
                       }

                       $status_button = '<a type="button" data-id="' . $driver->id . '" class="' . $class . '" data-toggle="tooltip" data-placement="top">' . $name . '</a>';
                   }
                   else{
                       $status_button = '<td colspan="3"><p>Driver Profile Not Completed</p></td>';
                   }
                    return $status_button;
                })->addColumn('change_status', function ($driver) {
                    if(isset($driver->driverProf) && $driver->driverProf != null) {
                        $select_option = '<select class="form-control" onchange="updatestatus(' . $driver->id . ')" id="driver_status_' . $driver->id . '">';
                        $select_option .= ($driver->du_driver_status == "driver_status_when_block") ? "<option value='driver_status_when_block' selected>Block</option>" : "<option value='driver_status_when_block'>Block</option>";
                        $select_option .= ($driver->du_driver_status == "driver_status_when_pending") ? "<option value='driver_status_when_pending' selected>Pending</option>" : "<option value='driver_status_when_pending'>Pending</option>";
                        $select_option .= ($driver->du_driver_status == "driver_status_when_approved") ? "<option value='driver_status_when_approved' selected>Approve</option>" : "<option value='driver_status_when_approved'>Approve</option>";
                        $select_option .= "</select>";
                    }
                    else{
                        $select_option = '';
                    }
                    return $select_option;
                })
                ->addColumn('action', function ($driver) {
                    $edit_button = '<a href="' . route('company::driver.edit', [$driver->id]) . '" class="btn btn-sm btn-outline-info waves-effect waves-light" data-toggle="tooltip" data-placement="top" title="Edit"><i class="bx bx-pencil font-size-16 align-middle"></i></a> <br>';

                    $view_detail_button = '<button data-id="' . $driver->id . '" class="driver-detail btn btn-sm btn-outline-primary waves-effect waves-light" data-effect="effect-fall" data-toggle="tooltip" data-placement="top" title="View Details"><i class="bx bx-bullseye font-size-16 align-middle"></i></button>';
                    return $edit_button. ' '. $view_detail_button ;
                })->rawColumns(['action','image','status','change_status','dr_reg_status'])
                ->make(true);
        }
        return view('company.driver.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $transports = TransportType::listsTranslations('name')->get();
        return view('company.driver.create',['transports'=>$transports]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $id = $request->input('edit_value');


        if ($id == NULL) {
            $validator_array = [
                'du_full_name' => 'required',
                'du_mobile_number'=>'required|unique:drivers',
                'du_full_mobile_number'=>'required|unique:drivers',
                'du_otp_manual'=>'required|unique:drivers'
            ];

            $validator = Validator::make($request->all(), $validator_array);
            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
            }

            $driver = new Driver();

        if ($request->hasFile('du_profile_pic')){
            $mime= $request->du_profile_pic->getMimeType();
            $pic = $request->file('du_profile_pic');
            $pic_name =  preg_replace('/\s+/', '', $pic->getClientOriginalName());
            $picName = time() .'-'.$pic_name;
            $pic->move('./assets/user/driver/', $picName);
            $compic = 'assets/user/driver/'.$picName;
            $driver->du_profile_pic = $compic;
        }else{
            $driver->du_profile_pic = 'assets/default/driver.png';
        }

            if(BaseNumber::where(["country_code" => $request->country_code,
                "mobile_number" => $request->du_mobile_number])->exists()){
                $basenumber =   BaseNumber::where(["country_code" => $request->country_code,
                    "mobile_number" => $request->du_mobile_number])->first();
            }else {
                $basenumber = new BaseNumber();
            }
            $basenumber->country_code = $request->country_code;
            $basenumber->mobile_number = $request->du_mobile_number;
            $basenumber->full_mobile_number = $request->du_full_mobile_number;
            $basenumber->otp_api_response = '03 Message (M) is mandatory';
            $basenumber->otp_api_response_status = '1';
            $basenumber->verification_code = $request->input('du_otp_manual');;
            $basenumber->otp_verified = 1;
            $basenumber->save();

            $driver->du_full_name = $request->input('du_full_name');
            $driver->du_mobile_number = $request->input('du_mobile_number');
            $driver->du_full_mobile_number = $request->input('du_full_mobile_number');
            $driver->du_user_name = $request->input('du_user_name');
            $driver->du_country_code = $request->input('country_code');
            $driver->email = $request->input('email');
            $driver->password = Hash::make($request->input('password'));
            $driver->du_com_id = auth()->guard('company')->user()->id;
            $driver->du_otp_manual = $request->input('du_otp_manual');
            $driver->du_otp_flag = $request->input('du_otp_flag');
            $driver->locale = 'en';

            $driver->save();

            $driverObj = Driver::find($driver->id);

            $name = $driverObj->du_full_name;
            $id = $driverObj->id;
            $socialLinks = BaseAppSocialLinks::all();
            $header = EmailHeader::where('id',7)->first();
            $headerTrans = EmailHeaderTranslation::where(['email_header_id' => 7, 'locale' => $driverObj->locale])->first();

            $bodyTrans = EmailBodyTranslation::where(['email_body_id'=> 7, 'locale' => $driverObj->locale])->first();

            $footerTrans = EmailFooterTranslation::where(['email_footer_id'=> 7,'locale' => $driverObj->locale])->first();
            $user_type = 'driver';
            $langtxt = $driverObj->locale;
            Mail::to($driverObj->email)->send(new WelcomeDriverEmail($name,$id,$socialLinks,$header,$headerTrans,$bodyTrans,$footerTrans,$langtxt,$user_type));


//            $driver_profile = new DriverProfile();
//            $driver_profile->dp_license_number = $request->input('dp_license_number');
//            $driver_profile->dp_transport_type_id_ref = $request->input('dp_transport_type_id_ref');
//            $driver_profile->car_registration = $request->input('car_registration');
//            $driver_profile->dp_date_manufacture = $request->input('dp_date_manufacture');
//            $driver_profile->dp_date_registration = $request->input('dp_date_registration');
//            $driver_profile->dp_user_id = $driver->id;
//
//
//            $driver_profile->save();

             return response()->json(['success' => true, 'message' => trans('adminMessages.driver_inserted'),'driver_id'=>$driver->id]);
            } else {
                $driver = Driver::find($id);

            /*if(BaseNumber::where(["country_code" => $driver->du_country_code,
                "mobile_number" => $driver->du_mobile_number])->exists()){
                $basenumber =   BaseNumber::where(["country_code" => $request->country_code,
                    "mobile_number" => $request->du_mobile_number])->first();
            }else {
                $basenumber = new BaseNumber();
            }
            $basenumber->country_code = $driver->du_country_code;
            $basenumber->mobile_number = $driver->du_mobile_number;
            $basenumber->full_mobile_number = $driver->du_full_mobile_number;
            $basenumber->otp_api_response = '03 Message (M) is mandatory';
            $basenumber->otp_api_response_status = '1';
            $basenumber->verification_code = $driver->du_otp_manual;;
            $basenumber->otp_verified = 1;
            $basenumber->save();
            $validator_array = [

//                'du_otp_manual'=>'required|unique:drivers',
                'du_otp_manual'=>'required',
            ];

            $validator = Validator::make($request->all(), $validator_array);
            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
            }*/

                if ($request->hasFile('du_profile_pic')){
            $mime= $request->du_profile_pic->getMimeType();
            $pic = $request->file('du_profile_pic');
            $pic_name =  preg_replace('/\s+/', '', $pic->getClientOriginalName());
            $picName = time() .'-'.$pic_name;
            $pic->move('./assets/user/driver/', $picName);
            $compic = 'assets/user/driver/'.$picName;
            $driver->du_profile_pic = $compic;
        }
                else{
                    $driver->du_profile_pic = 'assets/default/driver.png';
                }

            $driver->du_full_name = $request->input('du_full_name');
            $driver->du_mobile_number = $request->input('du_mobile_number');
            $driver->du_full_mobile_number = $request->input('du_full_mobile_number');
            $driver->du_user_name = $request->input('du_user_name');
            $driver->du_country_code = $request->input('country_code');
            $driver->du_otp_manual = $request->input('du_otp_manual');
            $driver->du_otp_flag = $request->input('du_otp_flag');
            $driver->email = $request->input('email');

        if(!empty($request->password)){

           $driver->password = Hash::make($request->input('password'));
        }

            $driver->save();

//            $driver_profile = new DriverProfile();
//            $driver_profile->dp_license_number = $request->input('dp_license_number');
//            $driver_profile->dp_transport_type_id_ref = $request->input('dp_transport_type_id_ref');
//            $driver_profile->car_registration = $request->input('car_registration');
//            $driver_profile->dp_date_manufacture = $request->input('dp_date_manufacture');
//            $driver_profile->dp_date_registration = $request->input('dp_date_registration');
//            $driver_profile->dp_user_id = $driver->id;
//
//
//            $driver_profile->save();

                return response()->json(['success' => true, 'message' => trans('adminMessages.driver_updated'),'driver_id'=>$id]);
            }
        }


    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $driver = Driver::find($id);
        if ($driver) {
            $transports = TransportType::listsTranslations('name')->get();
            $driver_profile = DriverProfile::where('dp_user_id',$driver->id)->first();
            return view('company.driver.edit', ['driver' => $driver,'driver_profile'=>$driver_profile,'transports'=>$transports]);
        } else {
            abort(404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {

        return response()->json(['success' => true, 'message' => trans('adminMessages.country_deleted')]);
    }

    public function status($id, $status)
    {
        $driver = Driver::where('id',$id)->update(['du_driver_status'=>$status]);
        $driver = Driver::where('id', $id)->first();
        if ($driver->du_driver_status == 'driver_status_when_approved'){

            $driver_name = $driver->du_full_name;
            $socialLinks = BaseAppSocialLinks::all();
            $header = EmailHeader::where('id',8)->first();
            $headerTrans = EmailHeaderTranslation::where(['email_header_id' => 8, 'locale' => $driver->locale])->first();

            $bodyTrans = EmailBodyTranslation::where(['email_body_id'=> 8, 'locale' => $driver->locale])->first();

            $footerTrans = EmailFooterTranslation::where(['email_footer_id'=> 8,'locale' => $driver->locale])->first();
            $langtxt = $driver->locale;
            $user_type = "driver";

            Mail::to($driver->email)->send(new DriverStatusEmail($driver_name,$driver->id,$socialLinks,$header,$headerTrans,$bodyTrans,$footerTrans,$langtxt,$user_type));
        }
        return response()->json(['success' => true, 'message' => trans('adminMessages.driver_status_updated')]);
    }

    public function driverRegistration($driver_id){
         $transportFuel = TransportFuel::listsTranslations('name')->where('tf_status',1)->get();
         $transportTypes = TransportType::listsTranslations('name')->where('tt_status',1)->get();
        return view('company.driver.driverRegistration', ['transportTypes' => $transportTypes,'driver_id'=>$driver_id,'transportFuel'=>$transportFuel]);

    }

    public function addEditDriverRegistration(Request $request){

        $id = $request->input('edit_value');

        if ($id == NULL) {
            $validator_array = [

                'driver_license_number' => 'required',
                'personal_id_card'=>'required'
            ];

            $validator = Validator::make($request->all(), $validator_array);
            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => $validator->errors()->first()]);
            }

            $driver = new DriverProfile();


            $driver->dp_license_number = $request->input('driver_license_number');
            $driver->dp_personal_id = $request->input('personal_id_card');
            $driver->dp_transport_type_id_ref = $request->input('type_id');
            $driver->dp_transport_make_id = $request->input('make_id');
            $driver->dp_transport_model_id = $request->input('model_id');
            $driver->dp_transport_color_id = $request->input('model_color_id');
            $driver->dp_transport_year_id = $request->input('model_year_id');
            $driver->car_registration = $request->input('car_reg');
            $driver->dp_fuel_id_ref = $request->input('fuel_type_id');
            $driver->dp_date_manufacture = $request->input('date_manufacture');
            $driver->dp_date_registration = $request->input('date_reg');
            $driver->dp_user_id = $request->input('driver_id');
            $driver->dp_created_by = auth()->guard('company')->user()->id;
            $driver->save();

            if ($request->hasFile('license_front_image')){
                $mime= $request->license_front_image->getMimeType();
                $image = $request->file('license_front_image');
                $image_name =  preg_replace('/\s+/', '', $image->getClientOriginalName());
                $ImageName = time() .uniqid(rand()) .'-'.$image_name;
                $image->move('./assets/DriverRegistration/', $ImageName);
                $image_path = 'assets/DriverRegistration/'.$ImageName;
                $fileSize = \File::size(public_path($image_path));

                $license = [
                'bm_file_name'=>$ImageName,
                'bm_file_path'=>$image_path,
                'bm_mime_type'=>$mime,
                'bm_section_order'=>0,
                'bm_user_id'=> $request->driver_id,
                'bm_user_type'=>'driver',
                'bm_mediable_id'=> $request->driver_id,
                'bm_mediable_type'=>'App/Driver',
                'bm_activity_category'=>'LicenseScreen',
                'bm_file_size'=>$fileSize
            ];
              $media = BaseMedia::create($license);
            }
            if ($request->hasFile('license_back_image')){
                $mime= $request->license_back_image->getMimeType();
                $image = $request->file('license_back_image');
                $image_name =  preg_replace('/\s+/', '', $image->getClientOriginalName());
                $ImageName = time() .uniqid(rand()) .'-'.$image_name;
                $image->move('./assets/DriverRegistration/', $ImageName);
                $image_path = 'assets/DriverRegistration/'.$ImageName;
                $fileSize = \File::size(public_path($image_path));

                $backlicense = [
                'bm_file_name'=>$ImageName,
                'bm_file_path'=>$image_path,
                'bm_mime_type'=>$mime,
                'bm_section_order'=>1,
                'bm_user_id'=> $request->driver_id,
                'bm_user_type'=>'driver',
                'bm_mediable_id'=> $request->driver_id,
                'bm_mediable_type'=>'App/Driver',
                'bm_activity_category'=>'LicenseScreen',
                'bm_file_size'=>$fileSize
            ];
              $media = BaseMedia::create($backlicense);
            }

            if ($request->hasFile('personal_front_image')){
                $mime= $request->personal_front_image->getMimeType();
                $image = $request->file('personal_front_image');
                $image_name =  preg_replace('/\s+/', '', $image->getClientOriginalName());
                $ImageName = time() .uniqid(rand()) .'-'.$image_name;
                $image->move('./assets/DriverRegistration/', $ImageName);
                $image_path = 'assets/DriverRegistration/'.$ImageName;
                $fileSize = \File::size(public_path($image_path));

                $personal_front = [
                'bm_file_name'=>$ImageName,
                'bm_file_path'=>$image_path,
                'bm_mime_type'=>$mime,
                'bm_section_order'=>0,
                'bm_user_id'=> $request->driver_id,
                'bm_user_type'=>'driver',
                'bm_mediable_id'=> $request->driver_id,
                'bm_mediable_type'=>'App/Driver',
                'bm_activity_category'=>'IdScreen',
                'bm_file_size'=>$fileSize
            ];
              $media = BaseMedia::create($personal_front);
            }
            if ($request->hasFile('personal_back_image')){
                $mime= $request->personal_back_image->getMimeType();
                $image = $request->file('personal_back_image');
                $image_name =  preg_replace('/\s+/', '', $image->getClientOriginalName());
                $ImageName = time() .uniqid(rand()) .'-'.$image_name;
                $image->move('./assets/DriverRegistration/', $ImageName);
                $image_path = 'assets/DriverRegistration/'.$ImageName;
                $fileSize = \File::size(public_path($image_path));

                $personal_back = [
                'bm_file_name'=>$ImageName,
                'bm_file_path'=>$image_path,
                'bm_mime_type'=>$mime,
                'bm_section_order'=>1,
                'bm_user_id'=> $request->driver_id,
                'bm_user_type'=>'driver',
                'bm_mediable_id'=> $request->driver_id,
                'bm_mediable_type'=>'App/Driver',
                'bm_activity_category'=>'IdScreen',
                'bm_file_size'=>$fileSize
            ];
              $media = BaseMedia::create($personal_back);
            }

            if ($request->hasFile('car_regitration_images')){
                $mime= $request->car_regitration_images->getMimeType();
                $image = $request->file('car_regitration_images');
                $image_name =  preg_replace('/\s+/', '', $image->getClientOriginalName());
                $ImageName = time() .uniqid(rand()) .'-'.$image_name;
                $image->move('./assets/DriverRegistration/', $ImageName);
                $image_path = 'assets/DriverRegistration/'.$ImageName;
                $fileSize = \File::size(public_path($image_path));

                $car_regitration = [
                'bm_file_name'=>$ImageName,
                'bm_file_path'=>$image_path,
                'bm_mime_type'=>$mime,
                'bm_section_order'=>0,
                'bm_user_id'=> $request->driver_id,
                'bm_user_type'=>'driver',
                'bm_mediable_id'=> $request->driver_id,
                'bm_mediable_type'=>'App/Driver',
                'bm_activity_category'=>'CarScreen',
                'bm_file_size'=>$fileSize
            ];
              $media = BaseMedia::create($car_regitration);
            }

            if ($request->hasFile('car_cert_front')){
                $mime= $request->car_cert_front->getMimeType();
                $image = $request->file('car_cert_front');
                $image_name =  preg_replace('/\s+/', '', $image->getClientOriginalName());
                $ImageName = time() .uniqid(rand()) .'-'.$image_name;
                $image->move('./assets/DriverRegistration/', $ImageName);
                $image_path = 'assets/DriverRegistration/'.$ImageName;
                $fileSize = \File::size(public_path($image_path));

                $car_regitration = [
                'bm_file_name'=>$ImageName,
                'bm_file_path'=>$image_path,
                'bm_mime_type'=>$mime,
                'bm_section_order'=>1,
                'bm_user_id'=> $request->driver_id,
                'bm_user_type'=>'driver',
                'bm_mediable_id'=> $request->driver_id,
                'bm_mediable_type'=>'App/Driver',
                'bm_activity_category'=>'CarScreen',
                'bm_file_size'=>$fileSize
            ];
              $media = BaseMedia::create($car_regitration);
            }

            if ($request->hasFile('car_cert_back')){
                $mime= $request->car_cert_back->getMimeType();
                $image = $request->file('car_cert_back');
                $image_name =  preg_replace('/\s+/', '', $image->getClientOriginalName());
                $ImageName = time() .uniqid(rand()) .'-'.$image_name;
                $image->move('./assets/DriverRegistration/', $ImageName);
                $image_path = 'assets/DriverRegistration/'.$ImageName;
                $fileSize = \File::size(public_path($image_path));

                $car_regitration = [
                'bm_file_name'=>$ImageName,
                'bm_file_path'=>$image_path,
                'bm_mime_type'=>$mime,
                'bm_section_order'=>2,
                'bm_user_id'=> $request->driver_id,
                'bm_user_type'=>'driver',
                'bm_mediable_id'=> $request->driver_id,
                'bm_mediable_type'=>'App/Driver',
                'bm_activity_category'=>'CarScreen',
                'bm_file_size'=>$fileSize
            ];
              $media = BaseMedia::create($car_regitration);
            }
            // multiple images-------------------------------
            $i = 0;
            if ($request->hasFile('car_images')){
                $car_images = $request->file('car_images');
                foreach ($car_images as $single_file){
                $mime=  $single_file->getMimeType();
                $image_name =  preg_replace('/\s+/', '', $single_file->getClientOriginalName());
                $ImageName = time() .uniqid(rand()) .'-'.$image_name;
                $single_file->move('./assets/DriverRegistration/', $ImageName);
                $image_path = 'assets/DriverRegistration/'.$ImageName;
                $fileSize = \File::size(public_path($image_path));

                $car_regitration = [
                'bm_file_name'=>$ImageName,
                'bm_file_path'=>$image_path,
                'bm_mime_type'=>$mime,
                'bm_section_order'=>$i++,
                'bm_user_id'=> $request->driver_id,
                'bm_user_type'=>'driver',
                'bm_mediable_id'=> $request->driver_id,
                'bm_mediable_type'=>'App/Driver',
                'bm_activity_category'=>'CarMultiImages',
                'bm_file_size'=>$fileSize
            ];
              $media = BaseMedia::create($car_regitration);
          }
            }

             return response()->json(['success' => true, 'message' => trans('adminMessages.driver_inserted'),'driver_id'=>$driver->id]);
            } else {
                $drivs = DriverProfile::where('dp_user_id',$id)->first();
                if(isset($drivs) && !empty( $drivs)){
                   $driver = $drivs;
                }else{
                    $driver = new DriverProfile();
                    $driver->dp_user_id = $id;
                }

            $driver->dp_license_number = $request->input('driver_license_number');
            $driver->dp_personal_id = $request->input('personal_id_card');
            $driver->dp_transport_type_id_ref = $request->input('type_id');
            $driver->dp_transport_make_id = $request->input('make_id');
            $driver->dp_transport_model_id = $request->input('model_id');
            $driver->dp_transport_color_id = $request->input('model_color_id');
            $driver->dp_transport_year_id = $request->input('model_year_id');
            $driver->car_registration = $request->input('car_reg');
            $driver->dp_fuel_id_ref = $request->input('fuel_type_id');
            $driver->dp_date_manufacture = $request->input('date_manufacture');
            $driver->dp_date_registration = $request->input('date_reg');
            $driver->dp_updated_by = auth()->guard('company')->user()->id;
            $driver->save();


            if ($request->hasFile('license_front_image')){
                $mime= $request->license_front_image->getMimeType();
                $image = $request->file('license_front_image');
                $image_name =  preg_replace('/\s+/', '', $image->getClientOriginalName());
                $ImageName = time() .uniqid(rand()) .'-'.$image_name;
                $image->move('./assets/DriverRegistration/', $ImageName);
                $image_path = 'assets/DriverRegistration/'.$ImageName;
                $fileSize = \File::size(public_path($image_path));

                $license = [
                'bm_file_name'=>$ImageName,
                'bm_file_path'=>$image_path,
                'bm_mime_type'=>$mime,
                'bm_section_order'=>0,
                'bm_user_id'=> $id,
                'bm_user_type'=>'driver',
                'bm_mediable_id'=> $id,
                'bm_mediable_type'=>'App/Driver',
                'bm_activity_category'=>'LicenseScreen',
                'bm_file_size'=>$fileSize
            ];
              $media = BaseMedia::create($license);
            }
            if ($request->hasFile('license_back_image')){
                $mime= $request->license_back_image->getMimeType();
                $image = $request->file('license_back_image');
                $image_name =  preg_replace('/\s+/', '', $image->getClientOriginalName());
                $ImageName = time() .uniqid(rand()) .'-'.$image_name;
                $image->move('./assets/DriverRegistration/', $ImageName);
                $image_path = 'assets/DriverRegistration/'.$ImageName;
                $fileSize = \File::size(public_path($image_path));

                $backlicense = [
                'bm_file_name'=>$ImageName,
                'bm_file_path'=>$image_path,
                'bm_mime_type'=>$mime,
                'bm_section_order'=>1,
                'bm_user_id'=> $id,
                'bm_user_type'=>'driver',
                'bm_mediable_id'=> $id,
                'bm_mediable_type'=>'App/Driver',
                'bm_activity_category'=>'LicenseScreen',
                'bm_file_size'=>$fileSize
            ];
              $media = BaseMedia::create($backlicense);
            }

            if ($request->hasFile('personal_front_image')){
                $mime= $request->personal_front_image->getMimeType();
                $image = $request->file('personal_front_image');
                $image_name =  preg_replace('/\s+/', '', $image->getClientOriginalName());
                $ImageName = time() .uniqid(rand()) .'-'.$image_name;
                $image->move('./assets/DriverRegistration/', $ImageName);
                $image_path = 'assets/DriverRegistration/'.$ImageName;
                $fileSize = \File::size(public_path($image_path));

                $personal_front = [
                'bm_file_name'=>$ImageName,
                'bm_file_path'=>$image_path,
                'bm_mime_type'=>$mime,
                'bm_section_order'=>0,
                'bm_user_id'=> $id,
                'bm_user_type'=>'driver',
                'bm_mediable_id'=> $id,
                'bm_mediable_type'=>'App/Driver',
                'bm_activity_category'=>'IdScreen',
                'bm_file_size'=>$fileSize
            ];
              $media = BaseMedia::create($personal_front);
            }
            if ($request->hasFile('personal_back_image')){
                $mime= $request->personal_back_image->getMimeType();
                $image = $request->file('personal_back_image');
                $image_name =  preg_replace('/\s+/', '', $image->getClientOriginalName());
                $ImageName = time() .uniqid(rand()) .'-'.$image_name;
                $image->move('./assets/DriverRegistration/', $ImageName);
                $image_path = 'assets/DriverRegistration/'.$ImageName;
                $fileSize = \File::size(public_path($image_path));

                $personal_back = [
                'bm_file_name'=>$ImageName,
                'bm_file_path'=>$image_path,
                'bm_mime_type'=>$mime,
                'bm_section_order'=>1,
                'bm_user_id'=> $id,
                'bm_user_type'=>'driver',
                'bm_mediable_id'=> $id,
                'bm_mediable_type'=>'App/Driver',
                'bm_activity_category'=>'IdScreen',
                'bm_file_size'=>$fileSize
            ];
              $media = BaseMedia::create($personal_back);
            }

            if ($request->hasFile('car_regitration_images')){
                $mime= $request->car_regitration_images->getMimeType();
                $image = $request->file('car_regitration_images');
                $image_name =  preg_replace('/\s+/', '', $image->getClientOriginalName());
                $ImageName = time() .uniqid(rand()) .'-'.$image_name;
                $image->move('./assets/DriverRegistration/', $ImageName);
                $image_path = 'assets/DriverRegistration/'.$ImageName;
                $fileSize = \File::size(public_path($image_path));

                $car_regitration = [
                'bm_file_name'=>$ImageName,
                'bm_file_path'=>$image_path,
                'bm_mime_type'=>$mime,
                'bm_section_order'=>0,
                'bm_user_id'=> $id,
                'bm_user_type'=>'driver',
                'bm_mediable_id'=> $id,
                'bm_mediable_type'=>'App/Driver',
                'bm_activity_category'=>'CarScreen',
                'bm_file_size'=>$fileSize
            ];
              $media = BaseMedia::create($car_regitration);
            }

            if ($request->hasFile('car_cert_front')){
                $mime= $request->car_cert_front->getMimeType();
                $image = $request->file('car_cert_front');
                $image_name =  preg_replace('/\s+/', '', $image->getClientOriginalName());
                $ImageName = time() .uniqid(rand()) .'-'.$image_name;
                $image->move('./assets/DriverRegistration/', $ImageName);
                $image_path = 'assets/DriverRegistration/'.$ImageName;
                $fileSize = \File::size(public_path($image_path));

                $car_regitration = [
                'bm_file_name'=>$ImageName,
                'bm_file_path'=>$image_path,
                'bm_mime_type'=>$mime,
                'bm_section_order'=>1,
                'bm_user_id'=> $id,
                'bm_user_type'=>'driver',
                'bm_mediable_id'=> $id,
                'bm_mediable_type'=>'App/Driver',
                'bm_activity_category'=>'CarScreen',
                'bm_file_size'=>$fileSize
            ];
              $media = BaseMedia::create($car_regitration);
            }

            if ($request->hasFile('car_cert_back')){
                $mime= $request->car_cert_back->getMimeType();
                $image = $request->file('car_cert_back');
                $image_name =  preg_replace('/\s+/', '', $image->getClientOriginalName());
                $ImageName = time() .uniqid(rand()) .'-'.$image_name;
                $image->move('./assets/DriverRegistration/', $ImageName);
                $image_path = 'assets/DriverRegistration/'.$ImageName;
                $fileSize = \File::size(public_path($image_path));

                $car_regitration = [
                'bm_file_name'=>$ImageName,
                'bm_file_path'=>$image_path,
                'bm_mime_type'=>$mime,
                'bm_section_order'=>2,
                'bm_user_id'=> $id,
                'bm_user_type'=>'driver',
                'bm_mediable_id'=> $id,
                'bm_mediable_type'=>'App/Driver',
                'bm_activity_category'=>'CarScreen',
                'bm_file_size'=>$fileSize
            ];
              $media = BaseMedia::create($car_regitration);
            }
            // multiple images-------------------------------
            $i = 0;
            if ($request->hasFile('car_images')){
                $car_images = $request->file('car_images');
                foreach ($car_images as $single_file){
                $mime=  $single_file->getMimeType();
                $image_name =  preg_replace('/\s+/', '', $single_file->getClientOriginalName());
                $ImageName = time() .uniqid(rand()) .'-'.$image_name;
                $single_file->move('./assets/DriverRegistration/', $ImageName);
                $image_path = 'assets/DriverRegistration/'.$ImageName;
                $fileSize = \File::size(public_path($image_path));

                $car_regitration = [
                'bm_file_name'=>$ImageName,
                'bm_file_path'=>$image_path,
                'bm_mime_type'=>$mime,
                'bm_section_order'=>$i++,
                'bm_user_id'=> $id,
                'bm_user_type'=>'driver',
                'bm_mediable_id'=> $id,
                'bm_mediable_type'=>'App/Driver',
                'bm_activity_category'=>'CarMultiImages',
                'bm_file_size'=>$fileSize
            ];
              $media = BaseMedia::create($car_regitration);
          }
            }
            return response()->json(['success' => true, 'message' => trans('adminMessages.driver_updated'),'driver_id'=>$id]);
        }
    }


    public function editDriverRegistration($driver_id){
         $driverProfile = DriverProfile::where('dp_user_id',$driver_id)->first();
         $transportFuel = TransportFuel::where('tf_status',1)->get();
         if($driverProfile){

         $driverLicenseFiles = BaseMedia::where(['bm_user_id'=>$driver_id,'bm_activity_category'=>'LicenseScreen','bm_mediable_type'=>'App/Driver'])->get();

          $driverPersonalIdFiles = BaseMedia::where(['bm_user_id'=>$driver_id,'bm_activity_category'=>'IdScreen','bm_mediable_type'=>'App/Driver'])->get();

          $carRegistrationFiles = BaseMedia::where(['bm_user_id'=>$driver_id,'bm_activity_category'=>'CarScreen','bm_mediable_type'=>'App/Driver'])->get();

           $carRegistrationFiles = BaseMedia::where(['bm_user_id'=>$driver_id,'bm_activity_category'=>'CarScreen','bm_mediable_type'=>'App/Driver'])->get();

            $carMultiImages = BaseMedia::where(['bm_user_id'=>$driver_id,'bm_activity_category'=>'CarMultiImages','bm_mediable_type'=>'App/Driver'])->get();

         $transportTypes = TransportType::where('tt_status',1)->get();
         $transportMakes = TransportMake::where('tm_type_ref_id',$driverProfile->dp_transport_type_id_ref)->get();
            $transportModels = TransportModel::where(['tmo_tm_id_ref'=>$driverProfile->dp_transport_make_id,'tmo_tt_ref_id'=>$driverProfile->dp_transport_type_id_ref])->get();

        $transportModelColors = TransportModelColor::where(['tmc_tm_ref_id'=>$driverProfile->dp_transport_make_id,'tmc_tt_ref_id'=>$driverProfile->dp_transport_type_id_ref,'tmc_tmo_id_ref'=>$driverProfile->dp_transport_model_id])->get();

             $transportModelYears = TransportModelYear::where(['tmy_tt_ref_id'=>$driverProfile->dp_transport_type_id_ref,'tmy_tm_ref_id'=>$driverProfile->dp_transport_make_id,'tmy_tmo_ref_id'=>$driverProfile->dp_transport_model_id,'tmc_tmo_id_ref'=>$driverProfile->dp_transport_color_id])->get();
         }else{
            $driverPersonalIdFiles = [];
            $driverLicenseFiles = [];
            $carRegistrationFiles = [];
            $transportMakes = [];
            $transportModels = [];
            $transportModelColors = [];
            $transportModelYears = [];
            $carMultiImages = [];
            $transportTypes = TransportType::where('tt_status',1)->get();
         }

        return view('company.driver.editDriverRegistration', ['transportTypes' => $transportTypes,
            'driver_id'=>$driver_id,
            'driverProfile'=>$driverProfile,
            'transportMakes'=>$transportMakes,
            'transportModels'=>$transportModels,
            'transportModelColors'=>$transportModelColors,
            'transportModelYears'=>$transportModelYears,
            'driverLicenseFiles' =>$driverLicenseFiles,
            'driverPersonalIdFiles' =>$driverPersonalIdFiles,
            'carRegistrationFiles' =>$carRegistrationFiles,
            'transportFuel'=>$transportFuel,
            'carMultiImages'=>$carMultiImages,
        ]);
    }

    public function getDriverDetail($id)
    {
         $dirver_list = Driver::where('id',$id)->first();

         $driverProfile = DriverProfile::where('dp_user_id',$id)->first();
         if(isset($driverProfile)){
         $driverLicenseFiles = BaseMedia::where(['bm_user_id'=>$id,'bm_activity_category'=>'LicenseScreen','bm_mediable_type'=>'App/Driver'])->get();
          $driverPersonalIdFiles = BaseMedia::where(['bm_user_id'=>$id,'bm_activity_category'=>'IdScreen','bm_mediable_type'=>'App/Driver'])->get();
          $carRegistrationFiles = BaseMedia::where(['bm_user_id'=>$id,'bm_activity_category'=>'CarScreen','bm_mediable_type'=>'App/Driver'])->get();

          $carMultiImages = BaseMedia::where(['bm_user_id'=>$id,'bm_activity_category'=>'CarMultiImages','bm_mediable_type'=>'App/Driver'])->get();

         $transportTypes = TransportType::listsTranslations('name')->select('transport_types.id', 'transport_type_translations.name')->where('transport_types.id',$driverProfile->dp_transport_type_id_ref)->first();
         $transportMakes = TransportMake::listsTranslations('name')->select('transport_makes.id', 'transport_make_translations.name')->where('transport_makes.id',$driverProfile->dp_transport_make_id)->first();

         $transportModels = TransportModel::where(['tmo_tm_id_ref'=>$driverProfile->dp_transport_make_id,'tmo_tt_ref_id'=>$driverProfile->dp_transport_type_id_ref])->first();

         $transportFuels = TransportFuel::where(['id'=>$driverProfile->dp_fuel_id_ref])->first();

         $transportModelColors = TransportModelColor::where(['tmc_tm_ref_id'=>$driverProfile->dp_transport_make_id,'tmc_tt_ref_id'=>$driverProfile->dp_transport_type_id_ref,'tmc_tmo_id_ref'=>$driverProfile->dp_transport_model_id])->first();

         $transportModelYears = TransportModelYear::where(['tmy_tt_ref_id'=>$driverProfile->dp_transport_type_id_ref,'tmy_tm_ref_id'=>$driverProfile->dp_transport_make_id,'tmy_tmo_ref_id'=>$driverProfile->dp_transport_model_id,'tmc_tmo_id_ref'=>$driverProfile->dp_transport_color_id])->first();
         }


        $array['globalModalTitle'] = 'Driver : ' . $dirver_list->du_full_name . ' | ' . $dirver_list->du_country_code . ' | ' . $dirver_list->du_mobile_number.' | '.$dirver_list->du_full_mobile_number.'| Manual Otp :'.$dirver_list->du_otp_manual ;

        $array['globalModalDetails'] = '<table class="table table-bordered">';
        $array['globalModalDetails'] .= '<thead class="thead-light"><tr><th colspan="6" class="text-center"> Driver Profile</th></tr></thead>';
      if(isset($driverProfile->dp_license_number)){
        $array['globalModalDetails'] .= '<table class="table table-bordered">';
        $array['globalModalDetails'] .= '<thead class="thead-light"><tr><th colspan="6" class="text-center"> Driver License Number : '.$driverProfile->dp_license_number.' </th></tr></thead>';

        $array['globalModalDetails'] .= '<thead class="thead-dark"><tr><th>Front Image</th><th>Back Image</th></tr></thead>';
        $i = 1;
        $array['globalModalDetails'] .= '<tr>';
        foreach($driverLicenseFiles as $drlicense){
             $url = asset($drlicense->bm_file_path);

            if($i == 1){
            $array['globalModalDetails'] .= '<td> ' . "<img src='" . $url . "' width='200' />" . '</td>';
        }
        if($i == 2){
            $array['globalModalDetails'] .= '<td> ' . "<img src='" . $url . "' width='200'  />" . '</td>';
        }


        }
          $array['globalModalDetails'] .= '</tr>';
        $array['globalModalDetails'] .= '</table>';
    }


    if(isset($driverProfile->dp_personal_id)){
        $array['globalModalDetails'] .= '<table class="table table-bordered">';
        $array['globalModalDetails'] .= '<thead class="thead-light"><tr><th colspan="6" class="text-center"> Driver Personal ID Card Number : '.$driverProfile->dp_personal_id.' </th></tr></thead>';

        $array['globalModalDetails'] .= '<thead class="thead-dark"><tr><th>Front Image</th><th>Back Image</th></tr></thead>';
        $i = 1;
        $array['globalModalDetails'] .= '<tr>';
        foreach($driverPersonalIdFiles as $driverPId){
             $url = asset($driverPId->bm_file_path);

            if($i == 1){
            $array['globalModalDetails'] .= '<td> ' . "<img src='" . $url . "' width='200' />" . '</td>';
        }
        if($i == 2){
            $array['globalModalDetails'] .= '<td> ' . "<img src='" . $url . "' width='200'  />" . '</td>';
        }


        }
        $array['globalModalDetails'] .= '</tr>';
        $array['globalModalDetails'] .= '</table>';
    }


    if(isset($transportTypes)){
        $array['globalModalDetails'] .= '<table class="table table-bordered">';
        $array['globalModalDetails'] .= '<thead class="thead-light"><tr><th colspan="8" class="text-center"> Car Registration Transport : Registration No.'.$driverProfile->car_registration.'</th></tr></thead>';

        $array['globalModalDetails'] .= '<thead class="thead-dark"><tr><th>Type</th><th>Make</th><th>Model</th><th>Model Color</th><th>Model Year</th><th>Fuel Type</th><th>Manufacture Date</th><th>Registration Date</th></tr></thead>';
        $i = 1;
        $array['globalModalDetails'] .= '<tr>';

        $array['globalModalDetails'] .= '<td> ' . $transportTypes->name . '</td>';

        if(isset($transportMakes)) {
            $array['globalModalDetails'] .= '<td> ' . $transportMakes->name . '</td>';
        }

        if(isset($transportModels)) {
            $array['globalModalDetails'] .= '<td> ' . $transportModels->name . '</td>';
        }

        if(isset($transportModelColors)) {
            $array['globalModalDetails'] .= '<td> ' . $transportModelColors->name . '</td>';
        }

        if(isset($transportModelYears)) {
            $array['globalModalDetails'] .= '<td> ' . $transportModelYears->tmy_name . '</td>';
        }

        if(isset($transportFuels->name) && !empty($transportFuels->name)){
             $array['globalModalDetails'] .= '<td> ' .  $transportFuels->name . '</td>';
         }else{
             $array['globalModalDetails'] .= '<td></td>';
         }

        $array['globalModalDetails'] .= '<td> ' . date('Y-m-d',strtotime($driverProfile->dp_date_manufacture)) . '</td>';
        $array['globalModalDetails'] .= '<td> ' . date('Y-m-d',strtotime($driverProfile->dp_date_registration)) . '</td>';

        $array['globalModalDetails'] .= '</tr>';



        $array['globalModalDetails'] .= '</table>';
    }

    if(isset($carRegistrationFiles)){
        $array['globalModalDetails'] .= '<table class="table table-bordered">';
        $array['globalModalDetails'] .= '<thead class="thead-light"><tr><th colspan="8" class="text-center"> Car Registration : '.' </th></tr></thead>';

        $array['globalModalDetails'] .= '<thead class="thead-dark"><tr><th>Main Image</th><th>Front Image</th><th>Back Image</th></tr></thead>';
        $i = 1;
        $array['globalModalDetails'] .= '<tr>';
        foreach($carRegistrationFiles as $carReg){
             $url = asset($carReg->bm_file_path);

            if($carReg->bm_section_order == 0){
            $array['globalModalDetails'] .= '<td> ' . "<img src='" . $url . "' width='200' />" . '</td>';
        }
        if($carReg->bm_section_order == 1){
            $array['globalModalDetails'] .= '<td> ' . "<img src='" . $url . "' width='200' />" . '</td>';
        }
        if($carReg->bm_section_order == 2){
            $array['globalModalDetails'] .= '<td> ' . "<img src='" . $url . "' width='200'  />" . '</td>';
        }


        }
          $array['globalModalDetails'] .= '</tr>';
        $array['globalModalDetails'] .= '</table>';
    }

    if(isset($carMultiImages)){
        $array['globalModalDetails'] .= '<table class="table table-bordered">';
        $array['globalModalDetails'] .= '<thead class="thead-light"><tr><th colspan="8" class="text-center"> Car Multiple Images : '.' </th></tr></thead>';


        //for($i=1;$i<=count($carMultiImages)/3;$i++){
           $array['globalModalDetails'] .= '<tr>';

        foreach($carMultiImages as $key => $carReg){

             $url = asset($carReg->bm_file_path);

            $array['globalModalDetails'] .= '<td> ' . "<img src='" . $url . "' width='200' />" . '</td>';


        }

         $array['globalModalDetails'] .= '</tr>';
    //}

        $array['globalModalDetails'] .= '</table>';
    }






        $array['globalModalDetails'] .= '</table>';


        return response()->json(['success' => true, 'data' => $array]);
    }


    public function changeDriverRegStatus($id, $status)
    {
        if ($status == 1) {
            $status_new = 0;
        }
        if ($status == 0) {
            $status_new = 1;
        }
        Driver::where('id', $id)->update(['du_is_reg_active' => $status_new]);
        return response()->json(['success' => true, 'message' => 'Driver Registration In App status is successfully Updated']);
    }


}
