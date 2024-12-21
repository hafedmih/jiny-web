<?php

namespace App\Http\Controllers\Taxi\Web\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\taxi\Requests\Request as RequestModel;
use App\Models\User;
use App\Models\taxi\Zone;

use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Http;
use Carbon\CarbonPeriod;
use App\Traits\CommanFunctions;
use DateTime;


use App\Models\taxi\DriverDocument;
use App\Models\taxi\Documents;
use App\Models\taxi\Driver;



class DashboardController extends Controller
{
    use CommanFunctions;
    public function dashboard(Request $request)
    {
        $today = date('Y-m-d');
        $trips = array();
        // dd($this->getDistance(28.612960,77.229455,28.459033,74.497071));

        $completed_count = RequestModel::where('is_completed',1)->where('is_cancelled',0)->whereDate('completed_at',$today);
        $cancelled_count = RequestModel::where('is_completed',0)->where('is_cancelled',1)->whereDate('cancelled_at',$today);
        $pending_count = RequestModel::where('is_completed',0)->where('is_cancelled',0)->whereDate('created_at',$today);

        if(!auth()->user()->hasRole("Super Admin")){
            $completed_count = $completed_count->where('created_by',auth()->user()->id);
            $cancelled_count = $cancelled_count->where('created_by',auth()->user()->id);
            $pending_count = $pending_count->where('created_by',auth()->user()->id);
        }

        $trips['completed'] = $completed_count->count();
        $trips['cancelled'] = $cancelled_count->count();
        $trips['pending'] = $pending_count->count();
        $trips['total'] = $trips['completed'] + $trips['cancelled'] + $trips['pending'];

        $amount = array();
        $end_day = Carbon::now()->subDays(7);
        $end_day = date("Y-m-d",strtotime($end_day));
        $days = array();
        $dates = array();
        for ($i=0; $i < 7; $i++) { 
            $days[$i] = date("l",strtotime(Carbon::now()->subDays($i)));
            $dates[$i] = date("Y-m-d",strtotime(Carbon::now()->subDays($i)));
        }
        $amount['week_days'] = $days;
        $total_amount = array();
        $admin_amount = array();
        $driver_amount = array();
        $tax_amount = array();
        $total_amount_add = 0;
        $admin_amount_add = 0;
        $driver_amount_add = 0;
        $tax_amount_add = 0;

        $total_cancel = array();
        $user_cancel = array();
        $driver_cancel = array();
        $dispatcher_cancel = array();
        $automatic_cancel = array();
        $total_cancel_add = 0;
        $user_cancel_add = 0;
        $driver_cancel_add = 0;
        $automatic_cancel_add = 0;
        $dispatcher_cancel_add = 0;

        $zone = array();
        
        $zone['dates'] = $dates;

        $zone_data = array();
        foreach ($dates as $key => $value) {
            $amounts = RequestModel::join('request_bills','request_bills.request_id','=','requests.id')->where('is_completed',1)->whereDate('completed_at',$value);
            $amount1 = RequestModel::join('request_bills','request_bills.request_id','=','requests.id')->where('is_completed',1)->whereDate('completed_at',$value);
            $amount2 = RequestModel::join('request_bills','request_bills.request_id','=','requests.id')->where('is_completed',1)->whereDate('completed_at',$value);
            $amount3 = RequestModel::join('request_bills','request_bills.request_id','=','requests.id')->where('is_completed',1)->whereDate('completed_at',$value);

            if(!auth()->user()->hasRole("Super Admin")){
                $amounts = $amounts->where('created_by',auth()->user()->id);
                $amount1 = $amount1->where('created_by',auth()->user()->id);
                $amount2 = $amount2->where('created_by',auth()->user()->id);
                $amount3 = $amount3->where('created_by',auth()->user()->id);
            }

            $amounts = $amounts->sum('request_bills.total_amount');
            $amount1 = $amount1->sum('request_bills.admin_commision');
            $amount2 = $amount2->sum('request_bills.driver_commision');
            $amount3 = $amount3->sum('request_bills.service_tax');

            array_push($total_amount, $amounts);
            array_push($admin_amount, $amount1);
            array_push($driver_amount, $amount2);
            array_push($tax_amount, $amount3);
            $total_amount_add += $amounts;
            $admin_amount_add += $amount1;
            $driver_amount_add += $amount2;
            $tax_amount_add += $amount3;

            $total_cancels = RequestModel::whereDate('cancelled_at',$value)->where('is_cancelled',1);
            $total_user_cancel = RequestModel::whereDate('cancelled_at',$value)->where('is_cancelled',1)->where('cancel_method','User');
            $total_driver_cancel = RequestModel::whereDate('cancelled_at',$value)->where('is_cancelled',1)->where('cancel_method','Driver');
            $total_dispatcher_cancel = RequestModel::whereDate('cancelled_at',$value)->where('is_cancelled',1)->where('cancel_method','Dispatcher');
            $total_automatic_cancel = RequestModel::whereDate('cancelled_at',$value)->where('is_cancelled',1)->where('cancel_method','Automatic');

            if(!auth()->user()->hasRole("Super Admin")){
                $total_cancels = $total_cancels->where('created_by',auth()->user()->id);
                $total_user_cancel = $total_user_cancel->where('created_by',auth()->user()->id);
                $total_driver_cancel = $total_driver_cancel->where('created_by',auth()->user()->id);
                $total_dispatcher_cancel = $total_dispatcher_cancel->where('created_by',auth()->user()->id);
                $total_automatic_cancel = $total_automatic_cancel->where('created_by',auth()->user()->id);
            }

            $total_cancels = $total_cancels->count();
            $total_user_cancel = $total_user_cancel->count();
            $total_driver_cancel = $total_driver_cancel->count();
            $total_dispatcher_cancel = $total_dispatcher_cancel->count();
            $total_automatic_cancel = $total_automatic_cancel->count();

            array_push($total_cancel, $total_cancels);
            array_push($user_cancel, $total_user_cancel);
            array_push($driver_cancel, $total_driver_cancel);
            array_push($dispatcher_cancel, $total_dispatcher_cancel);
            array_push($automatic_cancel, $total_automatic_cancel);
            $total_cancel_add += $total_cancels;
            $user_cancel_add += $total_user_cancel;
            $driver_cancel_add += $total_driver_cancel;
            $dispatcher_cancel_add += $total_dispatcher_cancel;
            $automatic_cancel_add += $total_automatic_cancel;
        }
        
        $zone_trips = RequestModel::join('zone_price','zone_price.id','=','requests.zone_type_id')->join('zone','zone_price.zone_id','=','zone.id')
            ->groupBy('zone_price.zone_id')->pluck('zone.zone_name','zone.id');
        $zone_trips = $zone_trips->toArray();

        foreach ($zone_trips as $keys => $values) {
            $data_value = array();
            foreach ($dates as $key => $value) {
                $zone_request_count = RequestModel::join('zone_price','zone_price.id','=','requests.zone_type_id')->where('zone_price.zone_id',$keys)->whereDate('requests.created_at',$value);
                if(!auth()->user()->hasRole("Super Admin")){
                    $zone_request_count = $zone_request_count->where('created_by',auth()->user()->id);
                }
                $zone_request_count = $zone_request_count->count();
                array_push($data_value, $zone_request_count);
            }
            array_push($zone_data, $data_value);
        }
        $zone_trips = RequestModel::join('zone_price','zone_price.id','=','requests.zone_type_id')->join('zone','zone_price.zone_id','=','zone.id')
        ->groupBy('zone_price.zone_id')->pluck('zone.zone_name');
        $zone_trips = $zone_trips->toArray();
        $out = implode("|",array_map(function($a) {return implode(",",$a);},$zone_data));
        $zone['data'] = $out;
        $zone['zone_name'] = $zone_trips;
        $amount['total_amount'] = $total_amount;
        $amount['admin_amount'] = $admin_amount;
        $amount['driver_amount'] = $driver_amount;
        $amount['tax_amount'] = $tax_amount;
        $amount['total_amount_add'] = number_format($total_amount_add,2);
        $amount['admin_amount_add'] = number_format($admin_amount_add,2);
        $amount['driver_amount_add'] = number_format($driver_amount_add,2);
        $amount['tax_amount_add'] = number_format($tax_amount_add,2);
        
        $total_wallet_amount = RequestModel::join('request_bills','request_bills.request_id','=','requests.id')->where('is_completed',1)->where('requests.payment_opt','WALLET')->whereDate('completed_at',$today);
        $total_card_amount = RequestModel::join('request_bills','request_bills.request_id','=','requests.id')->where('is_completed',1)->where('requests.payment_opt','CARD')->whereDate('completed_at',$today);
        $total_cash_amount = RequestModel::join('request_bills','request_bills.request_id','=','requests.id')->where('is_completed',1)->where('requests.payment_opt','CASH')->whereDate('completed_at',$today);

        if(!auth()->user()->hasRole("Super Admin")){
            $total_wallet_amount = $total_wallet_amount->where('created_by',auth()->user()->id);
            $total_card_amount = $total_card_amount->where('created_by',auth()->user()->id);
            $total_cash_amount = $total_cash_amount->where('created_by',auth()->user()->id);
        }
        $total_wallet_amount = $total_wallet_amount->sum('request_bills.total_amount');
        $total_card_amount = $total_card_amount->sum('request_bills.total_amount');
        $total_cash_amount = $total_cash_amount->sum('request_bills.total_amount');

        $amount['today_amount']['wallet'] = number_format($total_wallet_amount,2);
        $amount['today_amount']['card'] = number_format($total_card_amount,2);
        $amount['today_amount']['cash'] = number_format($total_cash_amount,2);

        $users = array();
        $users['users']['total'] = User::role('user')->count();
        $users['users']['active'] = User::role('user')->where('active',1)->count();
        $users['users']['block'] = User::role('user')->where('active',0)->count();

        $users['driver']['total'] = User::role('driver')->count();
        $users['driver']['active'] = User::role('driver')->where('active',1)->count();
        $users['driver']['block'] = User::role('driver')->where('active',0)->count();

        if(!auth()->user()->hasRole("Super Admin")){
            $users['driver']['total'] = User::role('driver')->join('drivers','drivers.user_id','=','users.id')->where('drivers.company_id',auth()->user()->id)->count();
            $users['driver']['active'] = User::role('driver')->join('drivers','drivers.user_id','=','users.id')->where('drivers.company_id',auth()->user()->id)->where('users.active',1)->count();
            $users['driver']['block'] = User::role('driver')->join('drivers','drivers.user_id','=','users.id')->where('drivers.company_id',auth()->user()->id)->where('users.active',0)->count();
        }

        $users['zone']['total'] = Zone::count();
        $users['zone']['active'] = Zone::where('status',1)->count();
        $users['zone']['block'] = Zone::where('status',0)->count();

        $cancellation = array();

        $cancellation['week_days'] = $dates;
        $cancellation['option'] = ['Total','User','Driver','Dispatcher','Automatic'];
        $cancellation['total'] = $total_cancel;
        $cancellation['total_count'] = $total_cancel_add;
        $cancellation['user'] = $user_cancel;
        $cancellation['user_count'] = $user_cancel_add;
        $cancellation['driver'] = $driver_cancel;
        $cancellation['driver_count'] = $driver_cancel_add;
        $cancellation['dispatcher'] = $dispatcher_cancel;
        $cancellation['dispatcher_count'] = $dispatcher_cancel_add;
        $cancellation['automatic'] = $automatic_cancel;
        $cancellation['automatic_count'] = $automatic_cancel_add;

        $currency = RequestModel::pluck('requested_currency_symbol');
        if(!auth()->user()->hasRole("Super Admin")){
            $currency = $currency->where('created_by',auth()->user()->id);
        }
        $currency = $currency->first();

        return view('taxi.dashboard.index',['trips' => $trips,'amount' => $amount,'users' => $users,'zone' => $zone,'cancellation' => $cancellation,'currency' => $currency]);
    }

    public function dashboardTotalTrips($value)
    {
        if($value == 1){
            $startdate = date('Y-m-d')." 00:00:00";
            $enddate = date('Y-m-d')." 23:59:59";
        }
        elseif($value == 2){
            $now = Carbon::now();
            $startdate = $now->startOfWeek(Carbon::SUNDAY)->format('Y-m-d H:i:s');
            $enddate = $now->endOfWeek(Carbon::SATURDAY)->format('Y-m-d H:i:s');
        }
        elseif($value == 3){
            $now = Carbon::now()->subDay(7);
            $startdate = $now->startOfWeek(Carbon::SUNDAY)->format('Y-m-d H:i:s');
            $enddate = $now->endOfWeek(Carbon::SATURDAY)->format('Y-m-d H:i:s');
        }
        elseif($value == 4){
            $now = Carbon::now();
            $startdate = $now->startOfMonth()->format('Y-m-d H:i:s');
            $enddate = $now->endOfMonth()->format('Y-m-d H:i:s');
        }
        elseif($value == 5){
            $now = Carbon::now()->subMonth();
            $startdate = $now->startOfMonth()->format('Y-m-d H:i:s');
            $enddate = $now->endOfMonth()->format('Y-m-d H:i:s');
        }
        $trips = array();
        $completed_count = RequestModel::where('is_completed',1)->where('is_cancelled',0)->whereBetween('completed_at',[$startdate,$enddate]);
        $cancelled_count = RequestModel::where('is_completed',0)->where('is_cancelled',1)->whereBetween('cancelled_at',[$startdate,$enddate]);
        $pending_count = RequestModel::where('is_completed',0)->where('is_cancelled',0)->whereBetween('created_at',[$startdate,$enddate]);

        if(!auth()->user()->hasRole("Super Admin")){
            $completed_count = $completed_count->where('created_by',auth()->user()->id);
            $cancelled_count = $cancelled_count->where('created_by',auth()->user()->id);
            $pending_count = $pending_count->where('created_by',auth()->user()->id);
        }

        $trips['completed'] = $completed_count->count();
        $trips['cancelled'] = $cancelled_count->count();
        $trips['pending'] = $pending_count->count();
        $trips['total'] = $trips['completed'] + $trips['cancelled'] + $trips['pending'];
        return response()->json(['message' =>'success','data' => $trips], 200);
    }

    public function dashboardAmountTransaction($value)
    {
        $days = array();
        $dates = array();
   
        if($value == 2){
            for ($i=0; $i < 7; $i++) { 
                $days[$i] = date("l",strtotime(Carbon::now()->subDays($i)));
                $dates[$i] = date("Y-m-d",strtotime(Carbon::now()->subDays($i)));
            }
        }
        elseif($value == 3){
            for ($i=7; $i < 14; $i++) { 
                $days[$i] = date("l",strtotime(Carbon::now()->subDays($i)));
                $dates[$i] = date("Y-m-d",strtotime(Carbon::now()->subDays($i)));
            }
        }
        
        $amount['week_days'] = implode(",",$days);
        $total_amount = array();
        $admin_amount = array();
        $driver_amount = array();
        $tax_amount = array();
        $total_amount_add = 0;
        $admin_amount_add = 0;
        $driver_amount_add = 0;
        $tax_amount_add = 0;
        foreach ($dates as $key => $value) {
            $amounts = RequestModel::join('request_bills','request_bills.request_id','=','requests.id')->where('is_completed',1)->whereDate('completed_at',$value);
            $amount1 = RequestModel::join('request_bills','request_bills.request_id','=','requests.id')->where('is_completed',1)->whereDate('completed_at',$value);
            $amount2 = RequestModel::join('request_bills','request_bills.request_id','=','requests.id')->where('is_completed',1)->whereDate('completed_at',$value);
            $amount3 = RequestModel::join('request_bills','request_bills.request_id','=','requests.id')->where('is_completed',1)->whereDate('completed_at',$value);

            if(!auth()->user()->hasRole("Super Admin")){
                $amounts = $amounts->where('created_by',auth()->user()->id);
                $amount1 = $amount1->where('created_by',auth()->user()->id);
                $amount2 = $amount2->where('created_by',auth()->user()->id);
                $amount3 = $amount3->where('created_by',auth()->user()->id);
            }

            $amounts = $amounts->sum('request_bills.total_amount');
            $amount1 = $amount1->sum('request_bills.admin_commision');
            $amount2 = $amount2->sum('request_bills.driver_commision');
            $amount3 = $amount3->sum('request_bills.service_tax');

            array_push($total_amount, $amounts);
            array_push($admin_amount, $amount1);
            array_push($driver_amount, $amount2);
            array_push($tax_amount, $amount3);
            $total_amount_add += $amounts;
            $admin_amount_add += $amount1;
            $driver_amount_add += $amount2;
            $tax_amount_add += $amount3;
        }
        $amount['total_amount'] = implode(",",$total_amount);
        $amount['admin_amount'] = implode(",",$admin_amount);
        $amount['driver_amount'] = implode(",",$driver_amount);
        $amount['tax_amount'] = implode(",",$tax_amount);
        $amount['total_amount_add'] = number_format($total_amount_add,2);
        $amount['admin_amount_add'] = number_format($admin_amount_add,2);
        $amount['driver_amount_add'] = number_format($driver_amount_add,2);
        $amount['tax_amount_add'] = number_format($tax_amount_add,2);
        return response()->json(['message' =>'success','data' => $amount], 200);
    }

    public function dashboardZoneTrips($value)
    {
        $days = array();
        $dates = array();
        if($value == 2){
            for ($i=0; $i < 7; $i++) { 
                $days[$i] = date("l",strtotime(Carbon::now()->subDays($i)));
                $dates[$i] = date("Y-m-d",strtotime(Carbon::now()->subDays($i)));
            }
        }
        elseif($value == 3){
            for ($i=7; $i < 14; $i++) { 
                $days[$i] = date("l",strtotime(Carbon::now()->subDays($i)));
                $dates[$i] = date("Y-m-d",strtotime(Carbon::now()->subDays($i)));
            }
        }
        elseif($value == 4){
            $now = Carbon::now();
            $startdate = $now->startOfMonth()->format('Y-m-d');
            $enddate = $now->endOfMonth()->format('Y-m-d');
            $period = CarbonPeriod::create($startdate,$enddate);
            // Iterate over the period
            foreach ($period as $i => $date) {
                $days[$i] =$date->format('l');
                $dates[$i] = $date->format('Y-m-d');
            }
            $days = collect($days)->sortDesc()->values()->all();
            $dates = collect($dates)->sortDesc()->values()->all();
        }
        elseif($value == 5){
            $now = Carbon::now()->subMonth();
            $startdate = $now->startOfMonth()->format('Y-m-d');
            $enddate = $now->endOfMonth()->format('Y-m-d');
            $period = CarbonPeriod::create($startdate,$enddate);
            // Iterate over the period
            foreach ($period as $i => $date) {
                $days[$i] =$date->format('l');
                $dates[$i] = $date->format('Y-m-d');
            }
            $days = collect($days)->sortDesc()->values()->all();
            $dates = collect($dates)->sortDesc()->values()->all();
        }
        elseif($value == 6){
            $now = Carbon::now()->subMonth();
            $startdate = $now->startOfMonth()->format('Y-m-d');
            $now = Carbon::now();
            $enddate = $now->endOfMonth()->format('Y-m-d');
            $period = CarbonPeriod::create($startdate,$enddate);
            // Iterate over the period
            foreach ($period as $i => $date) {
                $days[$i] =$date->format('l');
                $dates[$i] = $date->format('Y-m-d');
            }
            $days = collect($days)->sortDesc()->values()->all();
            $dates = collect($dates)->sortDesc()->values()->all();
        }
        $zone_data = array();
        $zone['dates'] = implode(",",$dates);
        $zone_trips = RequestModel::join('zone_price','zone_price.id','=','requests.zone_type_id')->join('zone','zone_price.zone_id','=','zone.id')
            ->groupBy('zone_price.zone_id')->pluck('zone.zone_name','zone.id');
        $zone_trips = $zone_trips->toArray();
        foreach ($zone_trips as $keys => $values) {
            $data_value = array();
            foreach ($dates as $key => $value) {
                $zone_request_count = RequestModel::join('zone_price','zone_price.id','=','requests.zone_type_id')->where('zone_price.zone_id',$keys)->whereDate('requests.created_at',$value);
                if(!auth()->user()->hasRole("Super Admin")){
                    $zone_request_count = $zone_request_count->where('created_by',auth()->user()->id);
                }
                $zone_request_count = $zone_request_count->count();

                array_push($data_value, $zone_request_count);
            }
            array_push($zone_data, $data_value);
        }
        $zone_trips = RequestModel::join('zone_price','zone_price.id','=','requests.zone_type_id')->join('zone','zone_price.zone_id','=','zone.id')
        ->groupBy('zone_price.zone_id')->pluck('zone.zone_name');
        $zone_trips = $zone_trips->toArray();
        $out = implode("|",array_map(function($a) {return implode(",",$a);},$zone_data));
        $zone['data'] = $out;
        $zone['zone_name'] = implode(",",$zone_trips);
        return response()->json(['message' =>'success','data' => $zone], 200);
    }

    public function dashboardCancelTrips($value)
    {
        $days = array();
        $dates = array();
        if($value == 2){
            for ($i=0; $i < 7; $i++) { 
                $days[$i] = date("l",strtotime(Carbon::now()->subDays($i)));
                $dates[$i] = date("Y-m-d",strtotime(Carbon::now()->subDays($i)));
            }
        }
        elseif($value == 3){
            for ($i=7; $i < 14; $i++) { 
                $days[$i] = date("l",strtotime(Carbon::now()->subDays($i)));
                $dates[$i] = date("Y-m-d",strtotime(Carbon::now()->subDays($i)));
            }
        }
        
        $total_cancel = array();
        $user_cancel = array();
        $driver_cancel = array();
        $dispatcher_cancel = array();
        $automatic_cancel = array();
        $total_cancel_add = 0;
        $user_cancel_add = 0;
        $driver_cancel_add = 0;
        $automatic_cancel_add = 0;
        $dispatcher_cancel_add = 0;
        foreach ($dates as $key => $value) {
            $total_cancels = RequestModel::whereDate('cancelled_at',$value)->where('is_cancelled',1);
            $total_user_cancel = RequestModel::whereDate('cancelled_at',$value)->where('is_cancelled',1)->where('cancel_method','User');
            $total_driver_cancel = RequestModel::whereDate('cancelled_at',$value)->where('is_cancelled',1)->where('cancel_method','Driver');
            $total_dispatcher_cancel = RequestModel::whereDate('cancelled_at',$value)->where('is_cancelled',1)->where('cancel_method','Dispatcher');
            $total_automatic_cancel = RequestModel::whereDate('cancelled_at',$value)->where('is_cancelled',1)->where('cancel_method','Automatic');

            if(!auth()->user()->hasRole("Super Admin")){
                $total_cancels = $total_cancels->where('created_by',auth()->user()->id);
                $total_user_cancel = $total_user_cancel->where('created_by',auth()->user()->id);
                $total_driver_cancel = $total_driver_cancel->where('created_by',auth()->user()->id);
                $total_dispatcher_cancel = $total_dispatcher_cancel->where('created_by',auth()->user()->id);
                $total_automatic_cancel = $total_automatic_cancel->where('created_by',auth()->user()->id);
            }

            $total_cancels = $total_cancels->count();
            $total_user_cancel = $total_user_cancel->count();
            $total_driver_cancel = $total_driver_cancel->count();
            $total_dispatcher_cancel = $total_dispatcher_cancel->count();
            $total_automatic_cancel = $total_automatic_cancel->count();
            
            array_push($total_cancel, $total_cancels);
            array_push($user_cancel, $total_user_cancel);
            array_push($driver_cancel, $total_driver_cancel);
            array_push($dispatcher_cancel, $total_dispatcher_cancel);
            array_push($automatic_cancel, $total_automatic_cancel);
            $total_cancel_add += $total_cancels;
            $user_cancel_add += $total_user_cancel;
            $driver_cancel_add += $total_driver_cancel;
            $dispatcher_cancel_add += $total_dispatcher_cancel;
            $automatic_cancel_add += $total_automatic_cancel;
        }
        $cancellation = array();

        $cancellation['week_days'] = implode(",",$dates);
        $cancellation['option'] = implode(",",['Total','User','Driver','Dispatcher','Automatic']);
        $cancellation['total'] = implode(",",$total_cancel);
        $cancellation['total_count'] = $total_cancel_add;
        $cancellation['user'] = implode(",",$user_cancel);
        $cancellation['user_count'] = $user_cancel_add;
        $cancellation['driver'] = implode(",",$driver_cancel);
        $cancellation['driver_count'] = $driver_cancel_add;
        $cancellation['dispatcher'] = implode(",",$dispatcher_cancel);
        $cancellation['dispatcher_count'] = $dispatcher_cancel_add;
        $cancellation['automatic'] = implode(",",$automatic_cancel);
        $cancellation['automatic_count'] = $automatic_cancel_add;
        return response()->json(['message' =>'success','data' => $cancellation], 200);
    }





    public function gendrateMapToken(Request $request)
    {
        $result = $this->getOauthToken();
        if($result){
            return response()->json(['success' =>true], 200);
        }
    }

    public function notify(Request $request)
    {
      
        $now = date('Y-m-d');
        $current_date = new DateTime($now);
        $date = Carbon::now()->addDays(10);
        $document =  DriverDocument::join('users', 'users.id', '=', 'driver_document.user_id')->where('driver_document.status',1)->join('documents', 'documents.id', '=', 'driver_document.document_id')->whereNotNull('driver_document.expiry_date')->where('documents.status',1)->where('documents.expiry_date',1)->whereDate('driver_document.expiry_date','<=',$date)->whereDate('driver_document.expiry_date','>=',date('Y-m-d'))->select('users.firstname','driver_document.expiry_date','documents.document_name','users.slug','users.phone_number','users.lastname')->get();

        $days = $current_date->diff($date)->format("%a");
        $document_count = count($document);
        $test = count($document);
        if ($test > 0) {
            foreach ($document as $key => $value) {
                $exp_date = new DateTime($value->expiry_date);
                $days = $current_date->diff($exp_date)->format("%a");
                $document[$key]->days = $days;
            }
          } else {
            return response()->json(['message' =>'failure'], 404);
        }


        if($document){
            return response()->json(['message' =>'success','data' => $document, 'document_count' => $document_count], 200);
        }
        

    }

}
