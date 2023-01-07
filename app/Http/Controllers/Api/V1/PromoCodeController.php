<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\GEtVoucherCodeResource;
use App\LanguageString;
use App\PassengerAccount;
use App\PromoCode;
use App\User;
use App\VoucherCode;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class PromoCodeController extends Controller
{
    /**
     *  Create Redeem Promo Code
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function redeemPromoCode(Request  $request){
        try{

            $token=JWTAuth::getToken();
            $user = JWTAuth::toUser($token);
            $promo_code = $request->promo_code;
            $messages = [
                'required' => 'the_field_is_required',
                'string' => 'the_string_field_is_required',
                'max' => 'the_field_is_out_from_max',
                'min' => 'the_field_is_low_from_min',
                'unique' => 'the_field_should_unique',
                'confirmed' => 'the_field_should_confirmed',
                'email' => 'the_field_should_email',
                'exists' => 'the_field_should_exists',
            ];
            $validator = Validator::make($request->all(), [
                'promo_code' => 'required',
            ], $messages);
            if ($validator->fails()) {

                $errors = [];
                foreach ($validator->errors()->messages() as $field => $message) {
                    $messageval = LanguageString::translated()->where('bls_name_key', $message[0] )->first()->name;
                    $field_msg = LanguageString::translated()->where('bls_name_key', $field )->first()->name;
                    $errors[] = [
                        'field' => $field,
                        'message' => strtolower($field_msg) . ' ' . strtolower($messageval),
                    ];
                }
                return response()->json(compact('errors'), 401);
            }


            Log::info('app.requests', ['request' => $request->all(),'URL'=>$request->url()]);
//            PromoCode::where(['pco_is_active'=>1,'pco_promo_code'=>$promo_code])->whereDate('pco_start_date','<=',now())->whereTime('pco_start_time','<=',date('H:i:s',strtotime(now())))->whereDate('pco_end_date','>',now())->whereTime('pco_end_time','>',date('H:i:s',strtotime(now())))->exists()
            $currentDateTime = date('Y-m-d');
            if(PromoCode::where(['pco_is_active'=>1,'pco_promo_code'=>$promo_code])->whereDate('pco_start_date','<=',$currentDateTime)->whereDate('pco_end_date','>',$currentDateTime)->exists()){
                $promo_code = PromoCode::where(['pco_is_active'=>1,'pco_promo_code'=>$promo_code])->whereDate('pco_start_date','<=',$currentDateTime)->whereDate('pco_end_date','>',$currentDateTime)->orderBy('id','desc')->first();
                return response()->json(['success'=>true,'promo_id'=>$promo_code->id],200);
            }else{
                $message = LanguageString::translated()->where('bls_name_key','promo_code_expired')->first()->name;
                $error = ['field'=>'language_strings','message'=>$message];
                $errors =[$error];
                return response()->json(['errors' => $errors], 401);
            }
        }catch(\Exception $e){
            $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
            $error = ['field'=>'language_strings','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }
    }

    /**
     *  Create Redeem Voucher Code
     * @param Request $request
     * @return Response
     * @throws Exception
     */

    public function redeemVoucherCode(Request  $request){
        try{

            $token=JWTAuth::getToken();
            $user = JWTAuth::toUser($token);
            $voucher_code = $request->voucher_code;
            $messages = [
                'required' => 'the_field_is_required',
                'string' => 'the_string_field_is_required',
                'max' => 'the_field_is_out_from_max',
                'min' => 'the_field_is_low_from_min',
                'unique' => 'the_field_should_unique',
                'confirmed' => 'the_field_should_confirmed',
                'email' => 'the_field_should_email',
                'exists' => 'the_field_should_exists',
            ];
            $validator = Validator::make($request->all(), [
                'voucher_code' => 'required',
            ], $messages);
            if ($validator->fails()) {

                $errors = [];
                foreach ($validator->errors()->messages() as $field => $message) {
                    $messageval = LanguageString::translated()->where('bls_name_key', $message[0] )->first()->name;
                    $field_msg = LanguageString::translated()->where('bls_name_key', $field )->first()->name;
                    $errors[] = [
                        'field' => $field,
                        'message' => strtolower($field_msg) . ' ' . strtolower($messageval),
                    ];
                }
                return response()->json(compact('errors'), 401);
            }


            Log::info('app.requests', ['request' => $request->all(),'URL'=>$request->url()]);
            if(VoucherCode::where(['vc_status'=>1,'vc_voucher_code'=>$voucher_code,'vc_voucher_used_status'=>0])->whereDate('vc_issue_date','<=',now())->whereTime('vc_issue_time','<=',date('H:i:s',strtotime(now())))->whereDate('vc_expiry_date','>',now())->whereTime('vc_expiry_time','>',date('H:i:s',strtotime(now())))->exists()){
                $voucher_code_accepted = VoucherCode::where(['vc_status'=>1,'vc_voucher_code'=>$voucher_code,'vc_voucher_used_status'=>0])->whereDate('vc_issue_date','<=',now())->whereTime('vc_issue_time','<=',now())->whereDate('vc_expiry_date','>',date('H:i:s',strtotime(now())))->whereTime('vc_expiry_time','>',date('H:i:s',strtotime(now())))->orderBy('id','desc')->first();
                $voucher_code_accepted->vc_user_id = $user->id;
                $voucher_code_accepted->vc_redeemed_at = now();
                $voucher_code_accepted->vc_voucher_used_status = 1;
                $voucher_code_accepted->save();
                $rate = $voucher_code_accepted->vc_amount;
                $passenger_account = PassengerAccount::where(['pc_target_id'=>$user->id,'pc_target_type'=>'passenger'])->orderBy('id','desc')->first();
                if(isset($passenger_account->pc_balance) && $passenger_account->pc_balance != null){
                    $bal = $passenger_account->pc_balance;
                }else{

                    $bal = 0;
                }
                $balance =  $bal + $rate;
                $ratedata = [
                    'pc_operation_type'=>1,
                    'pc_source_type'=>4,
                    'pc_source_id'=>$user->id,
                    'pc_target_id'=>$user->id,
                    'pc_amount'=>$rate,
                    'pc_balance'=>$balance
                ];
                $passenger_account = PassengerAccount::create($ratedata);
                $voucher = GEtVoucherCodeResource::collection(VoucherCode::where('id',$voucher_code_accepted->id)->get());
                return response()->json(['success'=>true,'voucher'=>$voucher[0],'message'=>'your_voucher_is_redeemed','user'=>User::getuser($user->id)],200);
            }else{

                $message = LanguageString::translated()->where('bls_name_key','Voucher_code_expired')->first()->name;
                $error = ['field'=>'language_strings','message'=>$message];
                $errors =[$error];
                return response()->json(['errors' => $errors], 401);

             }
        }catch(\Exception $e){
            $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
            $error = ['field'=>'language_strings','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }

    }

    /**
     *  Display a listing of Voucher Code
     * @param Request $request,voucher_code
     * @return Response
     * @throws Exception
     */

    public function getVoucherCode(Request  $request){
        try{

            $token=JWTAuth::getToken();
            $user = JWTAuth::toUser($token);
            $voucher_code = $request->voucher_code;
            $messages = [
                'required' => 'the_field_is_required',
                'string' => 'the_string_field_is_required',
                'max' => 'the_field_is_out_from_max',
                'min' => 'the_field_is_low_from_min',
                'unique' => 'the_field_should_unique',
                'confirmed' => 'the_field_should_confirmed',
                'email' => 'the_field_should_email',
                'exists' => 'the_field_should_exists',
            ];
            $validator = Validator::make($request->all(), [
                'voucher_code' => 'required',
            ], $messages);
            if ($validator->fails()) {

                $errors = [];
                foreach ($validator->errors()->messages() as $field => $message) {
                    $messageval = LanguageString::translated()->where('bls_name_key', $message[0] )->first()->name;
                    $field_msg = LanguageString::translated()->where('bls_name_key', $field )->first()->name;
                    $errors[] = [
                        'field' => $field,
                        'message' => strtolower($field_msg) . ' ' . strtolower($messageval),
                    ];
                }
                return response()->json(compact('errors'), 401);
            }


            Log::info('app.requests', ['request' => $request->all(),'URL'=>$request->url()]);
            if(VoucherCode::where(['vc_status'=>1,'vc_voucher_code'=>$voucher_code,'vc_voucher_used_status'=>0])->whereDate('vc_issue_date','<=',now())->whereDate('vc_expiry_date','>',now())->exists()){
                $voucher_code_accepted = VoucherCode::where(['vc_status'=>1,'vc_voucher_code'=>$voucher_code,'vc_voucher_used_status'=>0])->whereDate('vc_issue_date','<=',now())->whereDate('vc_expiry_date','>',now())->orderBy('id','desc')->first();
                $voucher = GEtVoucherCodeResource::collection(VoucherCode::where('id',$voucher_code_accepted->id)->get());
                return response()->json(['success'=>true,'voucher'=>$voucher[0],'message'=>'your voucher is available','user'=>User::getuser($user->id)],200);
            }else{
                return response()->json(['success'=>false,'voucher'=>null,'message'=>'your voucher is not available','user'=>User::getuser($user->id)],200);
            }

        }catch(\Exception $e){
            $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
            $error = ['field'=>'language_strings','message'=>$message];
            $errors =[$error];
            return response()->json(['errors' => $errors], 500);
        }

    }






}
