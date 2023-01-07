<?php

namespace App\Http\Controllers\Api\V1;

use App\CustomerCreditCard;
use App\Http\Resources\GetMyCreditCardsResource;
use App\LanguageString;
use App\Utility\Utility;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class CustomerCreditCardController extends Controller
{
    /**
     * Create Customer Creddit Card
     * @param Request $request
     * @return Response
     * @throws Exception
     */

    public function createCreditCard(Request $request){

            try{

                $token = JWTAuth::getToken();
                $user = JWTAuth::toUser($token);
                Log::info('app.requests', ['request' => $request->all(),'URL'=>$request->url(),'trxID'=>$user->TransactionId->last()]);
                $CVV = $request->CVV;
                $CVV = Crypt::encryptString($CVV);
                $card_number = $request->card_number;
                $card_number = Crypt::encryptString($card_number);
                $expire_year = Carbon::parse($request->expire_year);
                $card_holder_name = $request->card_holder_name;
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
                    'CVV' => 'required',
                    'card_number' => 'required',
                    'expire_year' => 'required',
                    'card_holder_name' => 'required',
                ], $messages);
                if ($validator->fails()) {

                    $errors = [];
                    foreach ($validator->errors()->messages() as $field => $message) {
                        Log::error('app.validationError', ['field' => $field, 'message' => $message, 'errorCode' => 401, 'URL' => $request->url(), 'passenger' => $user, 'token' => $token]);
                        $messageval = LanguageString::translated()->where('bls_name_key', $message[0])->first()->name;
                        $field_msg = LanguageString::translated()->where('bls_name_key', $field)->first()->name;
                        $errors[] = [
                            'field' => $field,
                            'message' => strtolower($field_msg) . ' ' . strtolower($messageval),
                        ];
                    }
                    return response()->json(compact('errors'), 401);
                }

                $card_data = [
                    'ccc_user_id'=>$user->id,
                    'ccc_user_type'=>$user->user_type,
                    'ccc_card_number'=>$card_number,
                    'ccc_expire_year'=>$expire_year,
                    'ccc_card_holder_name'=>$card_holder_name,
                    'ccc_CVV'=>$CVV,
                    'ccc_created_at'=>now(),
                    'ccc_updated_at'=>now()
                ];
                $valid_number = Utility::validateChecksum($request->card_number);
                if($valid_number == true){
                    $card = CustomerCreditCard::create($card_data);
                    $cards = CustomerCreditCard::where('ccc_user_id',$user->id)->get();
                    $cardsjson = GetMyCreditCardsResource::collection($cards);
                    if(count($cards) > 0){
                        $cards = $cardsjson;
                    }else{
                        $cards = [];
                    }
                    Log::info('app.response', ['response' => $cards,'statusCode'=>200,'URL'=>$request->url(),'trxID'=>$user->TransactionId->last()]);
                    return response()->json($cards,200);

                }else{

                    $message = LanguageString::translated()->where('bls_name_key','CC_number_not_valid')->first()->name;
                    $error = ['field'=>'language_strings','message'=>$message];
                    $errors =[$error];
                    return response()->json(['errors' => $errors], 401);
                }

               } catch(\Exception $e){
                    $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
                    $error = ['field'=>'language_strings','message'=>$message];
                    $errors =[$error];
                    return response()->json(['errors' => $errors], 500);
                }
                }
          
          /**
     * Display a listing of  Customer Credit Cards
     * @param Request $request
     * @return Response
     * @throws Exception
     */

         public function getCreditCards(Request $request){
            try{
                $token = JWTAuth::getToken();
                $user = JWTAuth::toUser($token);
                Log::info('app.requests', ['request' => $request->all(),'URL'=>$request->url(),'trxID'=>$user->TransactionId->last()]);

                    $cards = CustomerCreditCard::where('ccc_user_id',$user->id)->get();
                    $cardsjson = GetMyCreditCardsResource::collection($cards);
                    Log::info('app.response', ['response' => $cards,'statusCode'=>200,'URL'=>$request->url(),'trxID'=>$user->TransactionId->last()]);
                    if(count($cards) > 0){
                        $cards = $cardsjson;
                    }else{
                        $cards = [];
                    }
                    return response()->json($cards,200);
               } catch(\Exception $e){
                    $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
                    $error = ['field'=>'language_strings','message'=>$message];
                    $errors =[$error];
                    return response()->json(['errors' => $errors], 500);
                }
                }

    /**
     * Delete Customer Credit Cards
     * @param Request $request,$id
     * @return Response
     * @throws Exception
     */

        public function deleteCreditCards(Request $request,$id){

            try{

                $token = JWTAuth::getToken();
                $user = JWTAuth::toUser($token);
                Log::info('app.requests', ['request' => $request->all(),'URL'=>$request->url(),'trxID'=>$user->TransactionId->last()]);

                if(CustomerCreditCard::where(['id' => $id, 'ccc_user_id' => $user->id])->exists()) {
                    $card = CustomerCreditCard::where(['id' => $id, 'ccc_user_id' => $user->id])->delete();
                    Log::info('app.response', ['response' => $card, 'statusCode' => 200, 'URL' => $request->url(), 'trxID' => $user->TransactionId->last()]);

                }else{

                    $message = LanguageString::translated()->where('bls_name_key','your_card_not_deleted')->first()->name;
                    $error = ['field'=>'language_strings','message'=>$message];
                    $errors =[$error];
                    return response()->json(['errors' => $errors], 401);
                }
                $message = LanguageString::translated()->where('bls_name_key','your_card_deleted_successfully')->first()->name;
                    return response()->json(['message'=>$message],200);
               } catch(\Exception $e){
                    $message = LanguageString::translated()->where('bls_name_key','error')->first()->name;
                    $error = ['field'=>'language_strings','message'=>$message];
                    $errors =[$error];
                    return response()->json(['errors' => $errors], 500);
                }
                }

}
