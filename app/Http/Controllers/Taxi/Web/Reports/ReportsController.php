<?php

namespace App\Http\Controllers\Taxi\Web\Reports;

use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Jobs\SendPushNotification;
use App\Constants\PushEnum;
use App\Models\taxi\Vehicle;
use App\Models\taxi\Driver;
use App\Models\taxi\RequestRating;
use App\Models\taxi\DriverDocument;
use App\Models\taxi\Documents;
use App\Models\taxi\Wallet;
use App\Models\taxi\WalletTransaction;
use App\Models\taxi\InvoiceQuestions;
use App\Models\taxi\RequestQuestions;
use App\Models\User;
use App\Models\taxi\Requests\Request as RequestModel;
use App\Models\taxi\DriverLogs;
use App\Models\taxi\IndividualPromoMarketing;
use App\Models\taxi\Requests\RequestDriverLog;
use App\Transformers\Request\TripRequestTransformer;
use App\Models\taxi\UpdatePaymentStatus;
use App\Models\taxi\Settings;
use Illuminate\Support\Carbon;
use Carbon\CarbonInterval;
use Carbon\CarbonPeriod;
use DateTime;
use DB;
use DatePeriod;
use App\Traits\CommanFunctions;
use DateInterval;
use App\Http\Controllers\Taxi\API\CancelRequest\UserCancelRequestController;

class ReportsController extends Controller
{

    use CommanFunctions;

    public function reports(Request $request)
    {
        $drivers_online = User::role('driver')->with(['getCountry','driver','driver.vehicletype'])->where('online_by',1)->get();
        $drivers_offline = User::role('driver')->with(['getCountry','driver','driver.vehicletype'])->where('online_by',0)->get();

        foreach ($drivers_online as $key => $value) {
            $drivers_online[$key]->trip_completed = RequestModel::where('driver_id',$value->id)->where('is_completed',1)->count();
            $drivers_online[$key]->trip_cancelled = RequestModel::where('driver_id',$value->id)->where('is_cancelled',1)->count();
            $driver_logs = DriverLogs::where('driver_id',$value->id)->where('date',date('Y-m-d'))->get();
            $today = '00:00:00';
            foreach ($driver_logs as $keys => $values) {
                if($values->online_time && $values->offline_time){
                    $today = $this->timeAdd($today,$values->working_time);
                }
                if($values->online_time && !$values->offline_time){
                    $date1 = Carbon::createFromFormat('Y-m-d H:i:s', $values->online_time);
                    $date2 = Carbon::createFromFormat('Y-m-d H:i:s',NOW());
                    // $login_hours= $date1->diff($date2)->format('%Y-%M-%D %H:%I:%S');
                    $login_hours= $date1->diff($date2)->format('%H:%I:%S');
                    $today = $this->timeAdd($today,$login_hours);
                }
            }
            $drivers_online[$key]->today_working = $today;

            $yesterday = '00:00:00';
            $date = Carbon::yesterday()->format('Y-m-d'); 
            // dd($date);
            $driver_logs = DriverLogs::where('driver_id',$value->id)->where('date',$date)->get();
            foreach ($driver_logs as $keys => $values) {
                if($values->online_time && $values->offline_time){
                    $yesterday = $this->timeAdd($yesterday,$values->working_time);
                }
            }
            $drivers_online[$key]->yesterday_working = $yesterday;

            $weekhours = "00:00:00";
            $date = Carbon::now()->format('Y-m-d');
            $startdate = Carbon::now()->subDays(7)->format('Y-m-d');
            $dateRange = CarbonPeriod::create($startdate, $date)->toArray();
            foreach ($dateRange as $key1 => $value1) {
                $dateRange[$key] = date("y-m-d",strtotime($value));
            }
            $driver_logs = DriverLogs::where('driver_id',$value->id)->whereIn('date',$dateRange)->get();
            foreach ($driver_logs as $keys => $values) {
                if($values->online_time && $values->offline_time){
                    $weekhours = $this->timeAdd($weekhours,$values->working_time);
                }
                if($values->online_time && !$values->offline_time){
                    $date1 = Carbon::createFromFormat('Y-m-d H:i:s', $values->online_time);
                    $date2 = Carbon::createFromFormat('Y-m-d H:i:s',NOW());
                    // $login_hours= $date1->diff($date2)->format('%Y-%M-%D %H:%I:%S');
                    $login_hours= $date1->diff($date2)->format('%H:%I:%S');
                    $weekhours = $this->timeAdd($weekhours,$login_hours);
                }
            }
            $drivers_online[$key]->weekhours_working = $weekhours;

            $monthhours = "00:00:00";
            $date = Carbon::now()->format('Y-m-d');
            $startdate = Carbon::now()->startOfMonth()->format('Y-m-d');
            $dateRange = CarbonPeriod::create($startdate, $date)->toArray();
            foreach ($dateRange as $key1 => $value1) {
                $dateRange[$key] = date("y-m-d",strtotime($value));
            }
            $driver_logs = DriverLogs::where('driver_id',$value->id)->whereIn('date',$dateRange)->get();
            foreach ($driver_logs as $keys => $values) {
                if($values->online_time && $values->offline_time){
                    $monthhours = $this->timeAdd($monthhours,$values->working_time);
                }
                if($values->online_time && !$values->offline_time){
                    $date1 = Carbon::createFromFormat('Y-m-d H:i:s', $values->online_time);
                    $date2 = Carbon::createFromFormat('Y-m-d H:i:s',NOW());
                    // $login_hours= $date1->diff($date2)->format('%Y-%M-%D %H:%I:%S');
                    $login_hours= $date1->diff($date2)->format('%H:%I:%S');
                    $monthhours = $this->timeAdd($monthhours,$login_hours);
                }
            }
            $drivers_online[$key]->monthhours_working = $monthhours;
        }

        foreach ($drivers_offline as $key => $value) {
            $drivers_offline[$key]->trip_completed = RequestModel::where('driver_id',$value->id)->where('is_completed',1)->count();
            $drivers_offline[$key]->trip_cancelled = RequestModel::where('driver_id',$value->id)->where('is_cancelled',1)->count();
            $driver_logs = DriverLogs::where('driver_id',$value->id)->where('date',date('Y-m-d'))->get();
            $today = '00:00:00';
            foreach ($driver_logs as $keys => $values) {
                if($values->online_time && $values->offline_time){
                    $today = $this->timeAdd($today,$values->working_time);
                }
                // if($values->online_time && !$values->offline_time){
                //     $date1 = Carbon::createFromFormat('Y-m-d H:i:s', $values->online_time);
                //     $date2 = Carbon::createFromFormat('Y-m-d H:i:s',NOW());
                //     // $login_hours= $date1->diff($date2)->format('%Y-%M-%D %H:%I:%S');
                //     $login_hours= $date1->diff($date2)->format('%H:%I:%S');
                //     $today = $this->timeAdd($today,$login_hours);
                // }
            }
            $drivers_offline[$key]->today_working = $today;

            $yesterday = '00:00:00';
            $date = Carbon::yesterday()->format('Y-m-d');
            // dd($date);
            $driver_logs = DriverLogs::where('driver_id',$value->id)->where('date',$date)->get();
            foreach ($driver_logs as $keys => $values) {
                if($values->online_time && $values->offline_time){
                    $yesterday = $this->timeAdd($yesterday,$values->working_time);
                }
            }
            $drivers_offline[$key]->yesterday_working = $yesterday;

            $weekhours = "00:00:00";
            $date = Carbon::now()->format('Y-m-d');
            $startdate = Carbon::now()->subDays(7)->format('Y-m-d');
            $dateRange = CarbonPeriod::create($startdate, $date)->toArray();
            foreach ($dateRange as $key1 => $value1) {
                $dateRange[$key] = date("y-m-d",strtotime($value));
            }
            $driver_logs = DriverLogs::where('driver_id',$value->id)->whereIn('date',$dateRange)->get();
            foreach ($driver_logs as $keys => $values) {
                if($values->online_time && $values->offline_time){
                    $weekhours = $this->timeAdd($weekhours,$values->working_time);
                }
                // if($values->online_time && !$values->offline_time){
                //     $date1 = Carbon::createFromFormat('Y-m-d H:i:s', $values->online_time);
                //     $date2 = Carbon::createFromFormat('Y-m-d H:i:s',NOW());
                //     // $login_hours= $date1->diff($date2)->format('%Y-%M-%D %H:%I:%S');
                //     $login_hours= $date1->diff($date2)->format('%H:%I:%S');
                //     $weekhours = $this->timeAdd($weekhours,$login_hours);
                // }
            }
            $drivers_offline[$key]->weekhours_working = $weekhours;

            $monthhours = "00:00:00";
            $date = Carbon::now()->format('Y-m-d');
            $startdate = Carbon::now()->startOfMonth()->format('Y-m-d');
            $dateRange = CarbonPeriod::create($startdate, $date)->toArray();
            foreach ($dateRange as $key1 => $value1) {
                $dateRange[$key] = date("y-m-d",strtotime($value));
            }
            $driver_logs = DriverLogs::where('driver_id',$value->id)->whereIn('date',$dateRange)->get();
            foreach ($driver_logs as $keys => $values) {
                if($values->online_time && $values->offline_time){
                    $monthhours = $this->timeAdd($monthhours,$values->working_time);
                }
                // if($values->online_time && !$values->offline_time){
                //     $date1 = Carbon::createFromFormat('Y-m-d H:i:s', $values->online_time);
                //     $date2 = Carbon::createFromFormat('Y-m-d H:i:s',NOW());
                //     // $login_hours= $date1->diff($date2)->format('%Y-%M-%D %H:%I:%S');
                //     $login_hours= $date1->diff($date2)->format('%H:%I:%S');
                //     $monthhours = $this->timeAdd($monthhours,$login_hours);
                // }
            }
            $drivers_offline[$key]->monthhours_working = $monthhours;
        }
       

        return view('taxi.reports.reports',['drivers_online' => $drivers_online,'drivers_offline' => $drivers_offline]);
    }

    public function timeAdd($start_time,$end_time)
    {
        $hovers = date('H',strtotime($start_time)) * 60 * 60;
        $minits = date('i',strtotime($start_time)) * 60;
        $seconds = date('s',strtotime($start_time));
        $start = $hovers+$minits+$seconds;
        
        $hovers = date('H',strtotime($end_time)) * 60 * 60;
        $minits = date('i',strtotime($end_time)) * 60;
        $seconds = date('s',strtotime($end_time));
        $end = $hovers+$minits+$seconds;

        $value = $start + $end;

        $dt = Carbon::now();
        // $days = $dt->diffInDays($dt->copy()->addSeconds($value));
        $hours = $dt->diffInHours($dt->copy()->addSeconds($value));
        $minutes = $dt->diffInMinutes($dt->copy()->addSeconds($value)->subHours($hours));
        $seconds = $dt->diffInSeconds($dt->copy()->addSeconds($value)->subHours($hours)->subMinutes($minutes));
        return $hours.":".$minutes.":".$seconds;
    }

    /* Uers App and Dispatcher Trip Reports */

    public function tripReports(Request $request)
    {
        
        $trips = RequestModel::orderBy('created_at', 'desc')
            ->groupBy(DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d')"))
            ->get(array(
            DB::raw('Date(created_at) as date'),
            DB::raw("SUM(CASE 
            WHEN if_dispatch='1' THEN (is_completed) ELSE 0 END) completed"),
            DB::raw("SUM(CASE 
            WHEN if_dispatch='1' THEN (is_cancelled) ELSE 0 END) cancelled"),
            DB::raw("SUM(CASE 
            WHEN if_dispatch='0' THEN (is_completed) ELSE 0 END) mobile_completed"),
            DB::raw("SUM(CASE 
            WHEN if_dispatch='0' THEN (is_cancelled) ELSE 0 END) mobile_cancelled")
        ));

        return view('taxi.reports.trip_reports',['trips'=>  $trips]);
    }

    public function tripWiseReports(Request $request)
    {
        
        $trips = RequestModel::orderBy('created_at', 'desc')
            ->groupBy(DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d')"))
            ->get(array(
            DB::raw('Date(created_at) as date'),
            DB::raw("SUM(CASE 
            WHEN trip_type='LOCAL' THEN (is_completed) ELSE 0 END) local_completed"),
            DB::raw("SUM(CASE 
            WHEN trip_type='LOCAL' THEN (is_cancelled) ELSE 0 END) local_cancelled"),
            DB::raw("SUM(CASE 
            WHEN trip_type='RENTAL' THEN (is_completed) ELSE 0 END) rental_completed"),
            DB::raw("SUM(CASE 
            WHEN trip_type='RENTAL' THEN (is_cancelled) ELSE 0 END) rental_cancelled"),
            DB::raw("SUM(CASE 
            WHEN trip_type='OUTSTATION' THEN (is_completed) ELSE 0 END) outstation_completed"),
            DB::raw("SUM(CASE 
            WHEN trip_type='OUTSTATION' THEN (is_cancelled) ELSE 0 END) outstation_cancelled")
        ));

        return view('taxi.reports.trip_wise_reports',['trips'=>  $trips]);
    }

    public function transactionReports(Request $request)
    {
        
        $earned = WalletTransaction::where('type',"EARNED")->orderBy('id', 'desc')->get();
        $spent = WalletTransaction::where('type',"SPENT")->orderBy('id', 'desc')->get();
        

        return view('taxi.reports.transaction',['earned' => $earned, 'spent' => $spent]);
        
    }

    public function driverWallet(Request $request)
    {
        
        $balance = Wallet::orderBy('balance_amount', 'asc')->get();
        

        return view('taxi.reports.driver_wallet_balance',[ 'balance' => $balance]);
        
    }

    public function requestQuestions(Request $request)
    {
        $questions = InvoiceQuestions::where('status',1)->get();
       
        foreach ($questions as $key => $value) {
            $total =  RequestQuestions::where('question_id',$value->id)->count();
            
            $up_request_count = RequestQuestions::where('question_id',$value->id)->where('answer',"YES")->count();
            $down_request_count = RequestQuestions::where('question_id',$value->id)->where('answer',"NO")->count();
            if($up_request_count > 0) {
                $questions[$key]->up_percentage = ($up_request_count / $total) * 100;
                
            } else {
                $questions[$key]->up_percentage = 0;
            }
            
            // dd($down_request_count);
            if($down_request_count > 0) {
                $questions[$key]->down_percentage = ($down_request_count / $total) * 100;
                
            } else {
                $questions[$key]->down_percentage = 0;
            }
        }
        

        return view('taxi.reports.invoiceQuestions',['questions' => $questions]);
    }

    public function driverQuestions($id)
    {
        
        $drivers = RequestQuestions::where('question_id',$id)->get();

        return view('taxi.reports.DriverQuestions',['drivers' => $drivers]);
        
    }

    public function totalIncomeReports(Request $request)
    {

        

        $days = array();
        $dates = array();
        $value = 3;
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
        
        $currency = RequestModel::pluck('requested_currency_symbol');
        if(!auth()->user()->hasRole("Super Admin")){
            $currency = $currency->where('created_by',auth()->user()->id);
        }
        $currency = $currency->first();
        $amount['currency'] = $currency;
        return view('taxi.reports.income_reports',['amount' => $amount]);
    }

    public function incomeReports(Request $request)
    {

        $begin = new DateTime( $request->from );
        $end = new DateTime( $request->to );

        $interval = new DateInterval('P1D');
        $daterange = new DatePeriod($begin, $interval ,$end);

        $dates = [];

        foreach($daterange as $date){
        $dates[] = $date->format("Y-m-d");
        }

        // dd($dates);


        $days = array();
        // $dates = array();
        $value = 2;
        if($value == 2){
            for ($i=0; $i < 7; $i++) { 
                $days[$i] = date("l",strtotime(Carbon::now()->subDays($i)));
                // $dates[$i] = date("Y-m-d",strtotime(Carbon::now()->subDays($i)));
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
        
        $currency = RequestModel::pluck('requested_currency_symbol');
        if(!auth()->user()->hasRole("Super Admin")){
            $currency = $currency->where('created_by',auth()->user()->id);
        }
        $currency = $currency->first();
        $amount['currency'] =  $currency;
        return response()->json(['message' =>'success','amount' => $amount], 200);
        // dd($amount);
        // return view('taxi.reports.income_reports',['amount' => $amount,'currency' => $currency]);
    }

    public function userReports(Request $request)
    {
        $total = User::role('user')->count();
        $dispatcher = User::role('user')->where('mobile_application_type','ANDROID')->where('device_info_hash',null)->count();
        $android = User::role('user')->where('mobile_application_type','ANDROID')->where('device_info_hash','!=', null)->count();
        $ios = User::role('user')->where('mobile_application_type','IOS')->count();
        
        $amount['total'] = $total;
        $amount['android'] = $android;
        $amount['ios'] = $ios;
        $amount['dispatcher'] = $dispatcher;
        
        return view('taxi.reports.usersReports',['amount' => $amount]);
        
    }

    public function incomeList(Request $request,$value)
    {
        $list = RequestModel::join('request_bills','request_bills.request_id','=','requests.id')->where('is_completed',1)->get();
       
        return view('taxi.reports.incomeList',['list' => $list,'value' => $value]);
    }

    public function promoUseList(Request $request)
    {
        $list = IndividualPromoMarketing::where('status',0)->select(DB::raw('DATE(updated_at) as date'), DB::raw('count(*) as total'))
        ->groupBy('date')
        ->orderBy('date', 'desc')
        ->take(7)
        ->get();


        return view('taxi.reports.promoUseList',['list' => $list]);
    }

    public function paymentList(Request $request)
    {
        $payment = UpdatePaymentStatus::get();
        if($request->has('from') && $request->has('to') && $request->from != "" && $request->to != "" ){
            $weekStartDate = $request->from." 00:00:00";
            $weekEndDate = $request->to." 23:59:59";
            $payment = $payment->whereBetween('created_at',[$weekStartDate,$weekEndDate]);
        }
        return view('taxi.reports.card_payment_report',['payment' => $payment,'request' => $request]);
    }

    public function driverTripCancel(Request $request)
    {
        $list = RequestModel::where('trip_driver_cancel',1)->where('is_trip_start',0)->get();
        return view('taxi.reports.DriverCancelTrip',['list' => $list]);
    }

    public function driverTripCancelSave($id)
    {
        $requestModel = RequestModel::where('trip_driver_cancel',1)->where('is_trip_start',0)->where('id',$id)->first();
        if(is_null($requestModel)){
            return redirect()->route('driverTripCancel');
        }
        $requestModel->is_cancelled = 1;
        $requestModel->cancelled_at = NOW();
        $requestModel->trip_driver_cancel = 0;
        $requestModel->save();

        $driver = $requestModel->driverDetail;
        $driver->trips_count = 0;
        $driver->save();
        $driver = $driver->driver;
        $driver->is_available = true;
        $driver->save();

        $cancellationFee = (new UserCancelRequestController())->calculateFee($requestModel);

    // dd($cancellationFee);
    // dd($requestModel->requestPlace);
        $driver_accept = RequestDriverLog::where('request_id',$requestModel->id)->where('user_id',$driver->id)->where('type','ACCEPT')->first();

        if($driver_accept && $driver_accept->driver_lat != "" && $driver_accept->driver_lng){
            $driver_reject = RequestDriverLog::where('request_id',$requestModel->id)->where('user_id',$driver->id)->where('type','CANCELLED')->first();
            $travel_destance = $this->getDistance($driver_accept->driver_lat,$driver_accept->driver_lng,$driver_reject->driver_lat,$driver_reject->driver_lng);
            $cancel_fees_distance = Settings::where('name','cancel_fees_distance')->first();
            $cancel_fees_distance = $cancel_fees_distance ? $cancel_fees_distance->value : 0;
            if($travel_destance >= $cancel_fees_distance){
                $wallet = Wallet::where('user_id',$requestModel->driver_id)->first();
                if(!$wallet){
                    $wallet = new Wallet();
                    $wallet->user_id = $requestModel->driver_id;
                }
                $wallet->earned_amount +=  $cancellationFee;
                $wallet->balance_amount +=  $cancellationFee;
                $wallet->save();

                WalletTransaction::create([
                    'wallet_id' => $wallet->id,
                    'request_id' => $requestModel->id,
                    'amount' => $cancellationFee,
                    'user_id' => $requestModel->driver_id,
                    'purpose' => 'Request Cancellation Fees',
                    'type' => 'EARNED',
                ]);
            }
        }
        $requestModel->cancellationRequest()->update([
            'active'         => 0,
        ]);

        RequestDriverLog::where('request_id',$requestModel->id)->update(['status' => 1]);

        $request_result =  fractal($requestModel, new TripRequestTransformer);

        $user = $requestModel->userDetail;

        if ($user) {
            $title = Null;
            $body = '';
            $lang = $user->language;
            $push_data = $this->pushlanguage($lang,'trip-driver-cancel');
            if(is_null($push_data)){
                $title = 'Trip Cancelled By Driver';
                $body = 'The driver cancelled the ride, please create another ride';
                $sub_title = 'The driver cancelled the ride, please create another ride';
            }else{
                $title = $push_data->title;
                $body =  $push_data->description;
                $sub_title =  $push_data->description;
            } 
            // $pushData = ['notification_enum' => PushEnum::REQUEST_CANCELLED_BY_DRIVER, 'result' => (string)$request_result->toJson()];
            $pushData = ['notification_enum' => PushEnum::REQUEST_CANCELLED_BY_DRIVER];
            $socket_data = new \stdClass();
            $socket_data->success = true;
            $socket_data->success_message  = PushEnum::REQUEST_CANCELLED_BY_DRIVER;
            $socket_data->result = $request_result;
            $socketData = ['event' => 'request_'.$user->slug,'message' => $socket_data];
            sendSocketData($socketData);
            dispatch(new SendPushNotification($title, $pushData, $user->device_info_hash, $user->mobile_application_type,0,$sub_title));
        }

        $driver = $requestModel->driverDetail;

        if ($driver) {
            $title = Null;
            $body = '';
            $lang = $driver->language;
            $push_data = $this->pushlanguage($lang,'admin-accepted-trip-cancelled');
            if(is_null($push_data)){
                $title = 'Your cancellation request is accepted by admin, Trip Cancelled';
                $body = 'Your cancellation request is accepted by admin, Trip Cancelled';
                $sub_title = 'Your cancellation request is accepted by admin, Trip Cancelled';
            }else{
                $title = $push_data->title;
                $body =  $push_data->description;
                $sub_title =  $push_data->description;
            } 
            // $pushData = ['notification_enum' => PushEnum::REQUEST_CANCELLED_BY_DRIVER, 'result' => (string)$request_result->toJson()];
            $pushData = ['notification_enum' => PushEnum::REQUEST_CANCELLED_BY_DRIVER];
            $socket_data = new \stdClass();
            $socket_data->success = true;
            $socket_data->success_message  = PushEnum::REQUEST_CANCELLED_BY_DRIVER;
            $socket_data->result = $request_result;
            $socketData = ['event' => 'request_'.$driver->slug,'message' => $socket_data];
            sendSocketData($socketData);
            // dispatch(new SendPushNotification($title, $pushData, $driver->device_info_hash, $driver->mobile_application_type,0,$sub_title));
            sendPush($title,$sub_title, $pushData, $driver->device_info_hash, $driver->mobile_application_type,0);
        }

        return redirect()->route('driverTripCancel');
    }

    public function driverTripReject($id)
    {
        $requestModel = RequestModel::where('trip_driver_cancel',1)->where('id',$id)->first();
        $requestModel->reason = NULL;
        $requestModel->custom_reason = NULL;
        $requestModel->cancel_method = NULL;
        $requestModel->trip_driver_cancel = 0;
        $requestModel->save();

        $requestModel->cancellationRequest()->delete();

        RequestDriverLog::where('request_id',$requestModel->id)->delete();

        $request_result =  fractal($requestModel, new TripRequestTransformer);

        $driver = $requestModel->driverDetail;

        if ($driver) {
            $title = Null;
            $body = '';
            $lang = $driver->language;
            $push_data = $this->pushlanguage($lang,'admin-accepted-trip-cancelled-rejected');
            if(is_null($push_data)){
                $title = 'Sorry! You are not cancel this Trip';
                $body = 'Sorry! You are not cancel this Trip';
                $sub_title = 'Sorry! You are not cancel this Trip';
            }else{
                $title = $push_data->title;
                $body =  $push_data->description;
                $sub_title =  $push_data->description;
            } 
            // $pushData = ['notification_enum' => PushEnum::REQUEST_CANCELLED_BY_DRIVER, 'result' => (string)$request_result->toJson()];
            $pushData = ['notification_enum' => PushEnum::REQUEST_CANCELLED_BY_DRIVER_REJECTED];
            $socket_data = new \stdClass();
            $socket_data->success = true;
            $socket_data->success_message  = PushEnum::REQUEST_CANCELLED_BY_DRIVER_REJECTED;
            $socket_data->result = $request_result;
            $socketData = ['event' => 'request_'.$driver->slug,'message' => $socket_data];
            sendSocketData($socketData);
            // dispatch(new SendPushNotification($title, $pushData, $driver->device_info_hash, $driver->mobile_application_type,0,$sub_title));
            sendPush($title,$sub_title, $pushData, $driver->device_info_hash, $driver->mobile_application_type,0);
        }

        return redirect()->route('driverTripCancel');
    }
}