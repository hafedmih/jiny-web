<?php

namespace App\Http\Controllers\Taxi\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\taxi\Requests\Request as RequestModel;
use App\Models\taxi\RequestRating;
use App\Models\taxi\Driver;
use App\Models\taxi\Settings;
use App\Models\boilerplate\Country;
use DB;
use App\Models\User;
use App\Models\taxi\Requests\RequestBill;

use File;
use Validator;
use Carbon\Carbon;


class DashboardController extends BaseController
{
    public function DashboardList(Request $request)
    {
        try{
            $clientlogin = $this::getCurrentClient(request());
      
            if(is_null($clientlogin)) 
                return $this->sendError('Token Expired',[],401);
         
            $user = User::find($clientlogin->user_id);
            if(is_null($user))
                return $this->sendError('Unauthorized',[],401);
            
            if($user->active == false)
                return $this->sendError('User is blocked so please contact admin',[],403);

            if(!$user->hasRole('driver'))
            return $this->sendError('No Driver found',[],403);

            $countrydetails = Country::find($user->country_code);
            if(is_null($countrydetails))
                return $this->sendError('No country details found',[],404);

            /* Today Completed  Trips */
            $today_request_completed = RequestModel::where('driver_id',$user->id)->whereDate('trip_start_time',date('Y-m-d'))->where('is_completed',1);

            $today_request_completed_cash = RequestModel::leftjoin('request_bills','request_bills.request_id','requests.id')->select('request_bills.total_amount')->where('requests.driver_id',$user->id)->whereDate('requests.trip_start_time',date('Y-m-d'))->where('requests.is_completed',1)->where('requests.payment_opt',"Cash");

            $today_request_completed_card = RequestModel::leftjoin('request_bills','request_bills.request_id','requests.id')->select('request_bills.total_amount')->where('requests.driver_id',$user->id)->whereDate('requests.trip_start_time',date('Y-m-d'))->where('requests.is_completed',1)->where('requests.payment_opt',"Card");

            $today_request_completed_wallet = RequestModel::leftjoin('request_bills','request_bills.request_id','requests.id')->select('request_bills.total_amount')->where('requests.driver_id',$user->id)->whereDate('requests.trip_start_time',date('Y-m-d'))->where('requests.is_completed',1)->where('requests.payment_opt',"Wallet");

            /* Yesterday completed Trips */
            $yesterday = Carbon::yesterday();

            $yesterday_request_completed = RequestModel::where('driver_id',$user->id)->whereDate('trip_start_time',$yesterday)->where('is_completed',1);

            $yesterday_request_completed_cash = RequestModel::leftjoin('request_bills','request_bills.request_id','requests.id')->select('request_bills.total_amount')->where('requests.driver_id',$user->id)->whereDate('requests.trip_start_time',$yesterday)->where('requests.is_completed',1)->where('requests.payment_opt',"Cash");

            $yesterday_request_completed_card = RequestModel::leftjoin('request_bills','request_bills.request_id','requests.id')->select('request_bills.total_amount')->where('requests.driver_id',$user->id)->whereDate('requests.trip_start_time',$yesterday)->where('requests.is_completed',1)->where('requests.payment_opt',"Card");

            $yesterday_request_completed_wallet = RequestModel::leftjoin('request_bills','request_bills.request_id','requests.id')->select('request_bills.total_amount')->where('requests.driver_id',$user->id)->whereDate('requests.trip_start_time',$yesterday)->where('requests.is_completed',1)->where('requests.payment_opt',"Wallet");

            
            /* Today Cancelled Trips */
            $today_request_cancelled = RequestModel::where('driver_id',$user->id)->whereDate('trip_start_time',date('Y-m-d'))->where('is_cancelled',1);

            /* Yesterday Cancelled Trips */
            $yesterday_request_cancelled = RequestModel::where('driver_id',$user->id)->whereDate('trip_start_time',$yesterday)->where('is_cancelled',1);

            /*Weekly Completed Trips */
            $date = Carbon::now()->subDays(7);
            $weekly_request_completed = RequestModel::where('trip_start_time', '>=', $date)->where('driver_id',$user->id)->where('is_completed',1)->get();

            $weekly_request_completed_cash = RequestModel::leftjoin('request_bills','request_bills.request_id','requests.id')->select('request_bills.total_amount')->where('requests.trip_start_time', '>=', $date)->where('requests.driver_id',$user->id)->where('requests.is_completed',1)->where('requests.payment_opt',"Cash");
            $weekly_request_completed_card = RequestModel::leftjoin('request_bills','request_bills.request_id','requests.id')->select('request_bills.total_amount')->where('requests.trip_start_time', '>=', $date)->where('requests.driver_id',$user->id)->where('requests.is_completed',1)->where('requests.payment_opt',"Card");
            $weekly_request_completed_wallet = RequestModel::leftjoin('request_bills','request_bills.request_id','requests.id')->select('request_bills.total_amount')->where('requests.trip_start_time', '>=', $date)->where('requests.driver_id',$user->id)->where('requests.is_completed',1)->where('requests.payment_opt',"Wallet");

            /*Weekly Cancelled Trips */
            $weekly_request_cancelled = RequestModel::where('trip_start_time', '>=', $date)->where('driver_id',$user->id)->where('is_cancelled',1)->get();
            
            /*Monthly Completed Trips */
            $monthly_request_completed = RequestModel::whereMonth('trip_start_time',date('m'))->where('driver_id',$user->id)->where('is_completed',1)->get();

            $monthly_request_completed_cash = RequestModel::leftjoin('request_bills','request_bills.request_id','requests.id')->select('request_bills.total_amount')->whereMonth('requests.trip_start_time',date('m'))->where('requests.driver_id',$user->id)->where('requests.is_completed',1)->where('requests.payment_opt',"Cash")->get();
            $monthly_request_completed_card = RequestModel::leftjoin('request_bills','request_bills.request_id','requests.id')->select('request_bills.total_amount')->whereMonth('requests.trip_start_time',date('m'))->where('requests.driver_id',$user->id)->where('requests.is_completed',1)->where('requests.payment_opt',"Card")->get();

            $monthly_request_completed_wallet = RequestModel::leftjoin('request_bills','request_bills.request_id','requests.id')->select('request_bills.total_amount')->whereMonth('requests.trip_start_time',date('m'))->where('requests.driver_id',$user->id)->where('requests.is_completed',1)->where('requests.payment_opt',"Wallet")->get();



            /*Monthly Cancelled Trips */
            $monthly_request_cancelled = RequestModel::whereMonth('trip_start_time',date('m'))->where('driver_id',$user->id)->where('is_cancelled',1)->get();

            $request_today_complete = RequestModel::leftjoin('request_bills','request_bills.request_id','requests.id')->where('requests.driver_id',$user->id)
                ->select('request_bills.driver_commision')->whereDate('trip_start_time',date('Y-m-d'))->where('is_completed',1)
                ->sum('driver_commision');

            $request_yesterday_complete = RequestModel::leftjoin('request_bills','request_bills.request_id','requests.id')->where('requests.driver_id',$user->id)
                    ->select('request_bills.driver_commision')->whereDate('trip_start_time',$yesterday)->where('is_completed',1)
                    ->sum('driver_commision');

            $request_weekly_complete = RequestModel::leftjoin('request_bills','request_bills.request_id','requests.id')->where('requests.driver_id',$user->id)
                ->select('request_bills.driver_commision')->where('trip_start_time', '>=', $date)->where('is_completed',1)
                ->sum('driver_commision');


            $request_month_complete = RequestModel::leftjoin('request_bills','request_bills.request_id','requests.id')->where('requests.driver_id',$user->id)
                ->select('request_bills.driver_commision')->whereMonth('trip_start_time',date('m'))->where('is_completed',1)
                ->sum('driver_commision');


            /*Monthly Cancelled Trips */
            $monthly_request_cancelled = RequestModel::whereMonth('trip_start_time',date('m'))->where('driver_id',$user->id)->where('is_cancelled',1)->get();
            $monthly_request_cancelled_cash = RequestModel::whereMonth('trip_start_time',date('m'))->where('driver_id',$user->id)->where('is_cancelled',1)->where('payment_opt',"Cash")->get();
            $monthly_request_cancelled_card = RequestModel::whereMonth('trip_start_time',date('m'))->where('driver_id',$user->id)->where('is_cancelled',1)->where('payment_opt',"Card")->get();
            $monthly_request_cancelled_wallet = RequestModel::whereMonth('trip_start_time',date('m'))->where('driver_id',$user->id)->where('is_cancelled',1)->where('payment_opt',"Wallet")->get();

            
            $total_trip_completed = RequestModel::where('driver_id',$user->id)->where('is_completed',1)->get();

            $total_trip_cancelled = RequestModel::where('driver_id',$user->id)->where('is_cancelled',1)->get();

            $request_total_complete = RequestModel::leftjoin('request_bills','request_bills.request_id','requests.id')->where('requests.driver_id',$user->id)
            ->select('request_bills.total_amount')->where('is_completed',1)
            ->sum('total_amount');

            $total_request_completed_cash = RequestModel::leftjoin('request_bills','request_bills.request_id','requests.id')->where('requests.driver_id',$user->id)->where('requests.is_completed',1)->where('requests.payment_opt',"Cash");

            $total_request_completed_card = RequestModel::leftjoin('request_bills','request_bills.request_id','requests.id')->where('requests.driver_id',$user->id)->where('requests.is_completed',1)->where('requests.payment_opt',"Card");

            $total_request_completed_wallet = RequestModel::leftjoin('request_bills','request_bills.request_id','requests.id')->where('requests.driver_id',$user->id)->where('requests.is_completed',1)->where('requests.payment_opt',"Wallet");


            $total_request_cancelled_cash = RequestModel::leftjoin('request_bills','request_bills.request_id','requests.id')->where('requests.driver_id',$user->id)->where('requests.is_cancelled',1)->where('requests.payment_opt',"Cash");

            $total_request_cancelled_card = RequestModel::leftjoin('request_bills','request_bills.request_id','requests.id')->where('requests.driver_id',$user->id)->where('requests.is_cancelled',1)->where('requests.payment_opt',"Card");

            $total_request_cancelled_wallet = RequestModel::leftjoin('request_bills','request_bills.request_id','requests.id')->where('requests.driver_id',$user->id)->where('requests.is_cancelled',1)->where('requests.payment_opt',"Wallet");
            


            $rating_driver = RequestRating::where('user_id',$user->id)->avg('rating');

            $accept_ratio =  Driver::where('user_id',$user->id)->first();
        $data['currency'] = $countrydetails->currency_symbol;
        $data['today_trips']['is_completed']       = $today_request_completed->count();
        $data['today_trips']['is_cancelled']       = $today_request_cancelled->count();
        $data['today_trips']['total_amount']       = $request_today_complete;
        $data['today_trips']['amount']['cash']     = $today_request_completed_cash->sum('total_amount');
        $data['today_trips']['amount']['card']     = $today_request_completed_card->sum('total_amount');
        $data['today_trips']['amount']['wallet']   = $today_request_completed_wallet->sum('total_amount');
        $data['yesterday_trips']['is_completed']       = $yesterday_request_completed->count();
        $data['yesterday_trips']['is_cancelled']       = $yesterday_request_cancelled->count();
        $data['yesterday_trips']['total_amount']       = $request_yesterday_complete;
        $data['yesterday_trips']['amount']['cash']     = $yesterday_request_completed_cash->sum('total_amount');
        $data['yesterday_trips']['amount']['card']     = $yesterday_request_completed_card->sum('total_amount');
        $data['yesterday_trips']['amount']['wallet']   = $yesterday_request_completed_wallet->sum('total_amount');
        $data['weekly_trips']['is_completed']      = $weekly_request_completed->count();
        $data['weekly_trips']['is_cancelled']      = $weekly_request_cancelled->count();
        $data['weekly_trips']['total_amount']      = $request_weekly_complete;
        $data['weekly_trips']['amount']['cash']    = $weekly_request_completed_cash->sum('total_amount');
        $data['weekly_trips']['amount']['card']    = $weekly_request_completed_card->sum('total_amount');
        $data['weekly_trips']['amount']['wallet']  = $weekly_request_completed_wallet->sum('total_amount');
        $data['monthly_trips']['is_completed']     = $monthly_request_completed->count();
        $data['monthly_trips']['is_cancelled']     = $monthly_request_cancelled->count();
        $data['monthly_trips']['total_amount']     = $request_month_complete;
        $data['monthly_trips']['amount']['cash']   = $monthly_request_completed_cash->sum('total_amount');
        $data['monthly_trips']['amount']['card']   = $monthly_request_completed_card->sum('total_amount');
        $data['monthly_trips']['amount']['wallet'] = $monthly_request_completed_wallet->sum('total_amount');
        $data['total_trips']['is_completed']       = $total_trip_completed->count();
        $data['total_trips']['is_cancelled']       = $total_trip_cancelled->count();

        $data['total_trips']['total_amount']       = $request_total_complete;
        $data['total_trips']['amount']['cash']     = $total_request_completed_cash->sum('total_amount');
        $data['total_trips']['amount']['card']     = $total_request_completed_card->sum('total_amount');
        $data['total_trips']['amount']['wallet']   = $total_request_completed_wallet->sum('total_amount');
        $data['rating'] = $rating_driver;
        // $data['requested_currency_symbol'] = $requested_currency_symbol;
        $data['accept_ratio'] = $accept_ratio ? $accept_ratio['acceptance_ratio'] : '';
        $data['fine_amount'] = "0.00";
        $data['recent_fine'] = "0.00";
        $data['description'] = "Smoking is injurious to health";
        $data['cancellation_fee_amount'] = "0.00";
        $data['cancellation_earn_amount'] = "0.00";
        $data['balance_cancellation_amount'] = $data['cancellation_fee_amount'] - $data['cancellation_earn_amount'];

        /* Chart Report Datas */
        // Days for report
        $days = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
        $days_amount = [];
        foreach ($days as $key => $value) {
            $monday = date( 'Y-m-d', strtotime( $value.' this week' ) );
            $days_amount[$key] = (string)RequestBill::leftjoin('requests','requests.id','request_bills.request_id')->whereDate('requests.trip_start_time',$monday)->where('requests.driver_id',$user->id)->sum('request_bills.driver_commision');
        }
        $data['report']['day']['horizontal_keys'] = $days;
        $data['report']['day']['is_available'] = 1;
        $data['report']['day']['values'] = $days_amount;

        // Hours for Report
        $hours = 24;
        $times = [];
        $times_amount = [];
        for ($i=0; $i < $hours; $i++) { 
            $times[$i] = sprintf("%02d", $i);
            $from = date("Y-m-d")." ".$i.":00:00";
            $to = date("Y-m-d")." ".$i.":59:59";
            $times_amount[$i] = (string)RequestBill::leftjoin('requests','requests.id','request_bills.request_id')->where('requests.driver_id',$user->id)->whereBetween('requests.trip_start_time',[$from, $to])->sum('request_bills.driver_commision');
        }
        $data['report']['hour']['horizontal_keys'] = $times;
        $data['report']['hour']['is_available'] = 1;
        $data['report']['hour']['values'] = $times_amount;

        // Months for Reports
        $m = 12;
        $months = [];
        $months_amount = [];
        for ($i=1; $i <= $m; $i++) { 
            array_push($months, date('F', mktime(0, 0, 0, $i, 10)));
            $amount = RequestBill::leftjoin('requests','requests.id','request_bills.request_id')->where('requests.driver_id',$user->id)->whereMonth('requests.trip_start_time',$i)->sum('request_bills.driver_commision');
            array_push($months_amount, (string)$amount);
        }
        $data['report']['month']['horizontal_keys'] = $months;
        $data['report']['month']['is_available'] = 1;
        $data['report']['month']['values'] = $months_amount;

        // Yearly Reports
        $year = date('Y',strtotime($user->created_at));
        $years = [];
        $years_amount = [];
        for ($i=$year; $i <= date('Y'); $i++) { 
            array_push($years, $i);
            $amount = RequestBill::leftjoin('requests','requests.id','request_bills.request_id')->where('requests.driver_id',$user->id)->whereYear('requests.trip_start_time',$i)->sum('request_bills.driver_commision');
            array_push($years_amount, (string)$amount);
        }
        $data['report']['year']['horizontal_keys'] = $years;
        $data['report']['year']['is_available'] = 1;
        $data['report']['year']['values'] = $years_amount;


                return $this->sendResponse('Data Found',$data,200);  

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError('Catch error','failure.'.$e,400);  
        }
    }

    public function customerCare(Request $request)
    {
        $clientlogin = $this::getCurrentClient(request());
      
        if(is_null($clientlogin)) 
            return $this->sendError('Token Expired',[],401);
         
        $user = User::find($clientlogin->user_id);
        if(is_null($user))
            return $this->sendError('Unauthorized',[],401);
            
        if($user->active == false)
            return $this->sendError('User is blocked so please contact admin',[],403);

        $customer_care_number = Settings::where('name','customer_care_number')->first();

        $data['customer_care_number'] = $customer_care_number ? $customer_care_number->value : '';

        return $this->sendResponse('Data Found',$data,200);
    }
}
