<?php

namespace App\Http\Controllers\Taxi\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\taxi\Requests\Request as RequestModel;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\taxi\IndividualPromoMarketing;
use App\Models\taxi\Promocode;
use App\Traits\CommanFunctions;
use App\Jobs\SendPushNotification;
use App\Constants\PushEnum;
use App\Models\taxi\Zone;
use App\Traits\RandomHelper;
use DB;
use App\Models\User;
use File;
use Validator;

class PromoMarketingController extends BaseController
{
    use CommanFunctions,RandomHelper;

    public function promoMarketingList(Request $request)
    {
        try{
            $clientlogin = $this::getCurrentClient(request());
      
            if(is_null($clientlogin)) 
                return $this->sendError('Token Expired',[],401);
         
            $user = User::find($clientlogin->user_id);
            if(is_null($user))
                return $this->sendError('Unauthorized',[],401);
            
            // if($user->active == false)
            //     return $this->sendError('User is blocked so please contact admin',[],403);

                /** to check Role Marketing  */
            // if(!$user->hasRole('marketing'))
            // return $this->sendError('No Driver found',[],403);
                
            $promo_marketing = IndividualPromoMarketing::where('status',1)->inRandomOrder()->limit(20)->get();

                $data['promo_marketing'] = $promo_marketing;
                
                return $this->sendResponse('Data Found',$data,200);  

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError('Catch error','failure.'.$e,400);  
        }
    }

    public function promoMarketing(Request $request)
    {
        try{
            $clientlogin = $this::getCurrentClient(request());
      
            if(is_null($clientlogin)) 
                return $this->sendError('Token Expired',[],401);
         
            $user = User::find($clientlogin->user_id);
            if(is_null($user))
                return $this->sendError('Unauthorized',[],401);

            $params = $request->only(['slug','phone_number']);
            // dd($params);
            $promo_user = User::role('user')->where('phone_number',$params['phone_number'])->first();
            // dd($promo_user);
            if(is_null($promo_user)){
                return response()->json(['success' => false,'message' => "User Not Found"]);
            }else{
                $user_check = IndividualPromoMarketing::where('user_id',$promo_user->id)->first();
                if(!is_null($user_check))
                return response()->json(['success' => false,'message' => "User is already using the promo"]);

                $promo_code = IndividualPromoMarketing::where('slug',$params['slug'])->where('status',1)->first();

                if(is_null($promo_code)){
                    // return $this->sendError('Invalid Promo Code',[],403); 
                    return response()->json(['success' => false,'message' => "Invalid Promo Code"]);
                }else{

                    $zone = Zone::where('zone_name','=','Coimbatore')->where('status',1)->first();
                    do{
                        $promocode = "PROMO100-".$this->RandomString(6);
                    } while (Promocode::where('promo_code', '=', $promocode)->exists());

                    $promo = Promocode::create([
                        'zone_id' => $zone->id,
                        'promo_code' => $promocode,
                        'user_id' => $promo_user->id,
                        'select_offer_option' => 5,
                        'target_amount' => (int)$promo_code->target_amount,
                        'promo_type' => $promo_code->promo_amount_type == 1 ? 1 : 2,
                        'amount' => $promo_code->promo_amount,
                        'percentage' => $promo_code->promo_percentage,
                        'trip_type' => $promo_code->trip_type,
                        'promo_use_count' => 1,
                        'promo_user_reuse_count' => 1,
                        'new_user_count' => 1
                    ]);

                    $users = IndividualPromoMarketing::where('slug',$params['slug'])->update([
                        'user_id' => $promo_user->id,
                        'marker_id' => $user->id,
                        'status' => 0,
                    ]);

                    $data = array();
                    $data['promo_user'] = $promo_user;
                    $data['promo_detail'] = $promo;

                    $lang = $promo_user->language;

                    $push_data = $this->pushlanguage($lang,'new-promo-code');
                    if(is_null($push_data)){
                        $title = 'You have new Promo code for '.$promo_code->trip_type;
                        $body = 'Congratulations, You have a new Promo Code for trip';
                        $sub_title = 'Congratulations, You have a new Promo Code for trip';

                    }else{
                        $title = $push_data->title;
                        $body =  $push_data->description;
                        $sub_title =  $push_data->description;

                    }

                    $pushData = ['notification_enum' => PushEnum::NEW_PROMO_CODE, 'result' => $data];
                    dispatch(new SendPushNotification($title,$sub_title, $pushData, $promo_user->device_info_hash, $promo_user->mobile_application_type,0));

                    return $this->sendResponse('true',$data,200); 
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError('Catch error','failure.'.$e,400);  
        }

    }

}
