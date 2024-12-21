<?php

namespace App\Http\Controllers\Taxi\Web\Request;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController;
use App\Transformers\Request\TripRequestTransformer;

use App\Models\taxi\Requests\Request as RequestModel;
use App\Models\taxi\Requests\RequestHistory;
use App\Models\taxi\Promocode;
use App\Models\taxi\ZonePrice;
use App\Models\taxi\Driver;
use App\Models\taxi\Requests\RequestBill;
use App\Models\User;
use App\Models\taxi\Settings;
use App\Models\taxi\CancellationRequest;
use App\Models\taxi\Wallet;
use App\Models\taxi\WalletTransaction;
use App\Models\taxi\PackageMaster;
use App\Models\taxi\PackageItem;
use App\Models\taxi\OutstationUploadImages;
use App\Models\taxi\Requests\NoDriverTrips;

use App\Constants\PushEnum;
use App\Constants\RideType;
use App\Constants\AdminCommissionType;
use App\Jobs\SendPushNotification;
use Carbon\Carbon;
use App\Traits\CommanFunctions;
use DateTime;
use DB;

class DriverSummaryController extends BaseController
{
    use CommanFunctions;
    public function SummaryView(Request $request)
    {
       
    	$requests = RequestModel::leftjoin('request_bills','request_bills.request_id','requests.id')->leftjoin('request_places','request_places.request_id','requests.id')->orderBy('requests.created_at','desc')->where('requests.is_completed',1);
        $requests_bill = RequestBill::leftjoin('requests','requests.id','request_bills.request_id');
        $driver_list = User::role('driver')->where('active',1)->get();
        $customer_list = User::role('user')->where('active',1)->get();
       
        if($request->has('start_date') && $request->start_date != "" && $request->has('end_date') && $request->end_date != "" ){
            $weekStartDate = $request->start_date." 00:00:00";
            $weekEndDate = $request->end_date." 23:59:59";
            $requests = $requests->whereBetween('trip_start_time',[$weekStartDate,$weekEndDate]);
            $requests_bill = $requests_bill->whereBetween('requests.trip_start_time',[$weekStartDate,$weekEndDate]);
           
        }

        if($request->has('driver') && $request->driver != ""){
            $requests = $requests->where('driver_id',$request->driver);
            $requests_bill = $requests_bill->where('driver_id',$request->driver);
        }
        if($request->has('customer') && $request->customer != ""){
            $requests = $requests->where('user_id',$request->customer);
            $requests_bill = $requests_bill->where('user_id',$request->customer);
        }
        if($request->has('trip_type') && $request->trip_type != ""){
            $requests = $requests->where('trip_type',$request->trip_type);
            $requests_bill = $requests_bill->where('trip_type',$request->trip_type);
        }

        $requests = $requests->get();
        
       

        $requests_base_price = $requests_bill->sum('request_bills.base_price');
        $requests_waiting_charges = $requests_bill->sum('request_bills.waiting_charge');
        $requests_promo_discount = $requests_bill->sum('request_bills.promo_discount');
        $requests_distance_price = $requests_bill->sum('request_bills.distance_price');
        $requests_admin_commission = $requests_bill->sum('request_bills.admin_commision');
        $requests_service_tax = $requests_bill->sum('request_bills.service_tax');
        $requests_driver_earning = $requests_bill->sum('request_bills.driver_commision');
        $requests_total_amount = $requests_bill->sum('request_bills.total_amount');
        return view('taxi.summary.Summary',['driver_list' => $driver_list,'customer_list' => $customer_list ,'request' => $request,'requests' => $requests,'requests_base_price' => $requests_base_price,'requests_waiting_charges' => $requests_waiting_charges,'requests_promo_discount' => $requests_promo_discount,'requests_distance_price' => $requests_distance_price,'requests_bill' => $requests_bill,'requests_admin_commission' => $requests_admin_commission,'requests_service_tax' => $requests_service_tax,'requests_driver_earning' => $requests_driver_earning,'requests_total_amount' => $requests_total_amount]);
    }

    public function CompletedLocalView(Request $request)
    {
    	$requests = RequestModel::leftjoin('request_bills','request_bills.request_id','requests.id')->leftjoin('request_places','request_places.request_id','requests.id')->orderBy('requests.created_at','desc')->where('requests.is_completed',1)->where('requests.trip_type',"LOCAL");
        $requests_bill = RequestBill::leftjoin('requests','requests.id','request_bills.request_id');
        $driver_list = User::role('driver')->where('active',1)->get();
        $customer_list = User::role('user')->where('active',1)->get();
        
        if($request->has('start_date') && $request->start_date != "" && $request->has('end_date') && $request->end_date != "" ){
            $weekStartDate = $request->start_date." 00:00:00";
            $weekEndDate = $request->end_date." 23:59:59";
            $requests = $requests->whereBetween('trip_start_time',[$weekStartDate,$weekEndDate]);
            $requests_bill = $requests_bill->whereBetween('requests.trip_start_time',[$weekStartDate,$weekEndDate]);
        }

        if($request->has('driver') && $request->driver != ""){
            $requests = $requests->where('driver_id',$request->driver);
            $requests_bill = $requests_bill->where('driver_id',$request->driver);
        }
        if($request->has('customer') && $request->customer != ""){
            $requests = $requests->where('user_id',$request->customer);
            $requests_bill = $requests_bill->where('user_id',$request->customer);
        }

        $requests = $requests->get();
        $requests_base_price = $requests_bill->sum('request_bills.base_price');
        $requests_waiting_charges = $requests_bill->sum('request_bills.waiting_charge');
        $requests_promo_discount = $requests_bill->sum('request_bills.promo_discount');
        $requests_distance_price = $requests_bill->sum('request_bills.distance_price');
        $requests_admin_commission = $requests_bill->sum('request_bills.admin_commision');
        $requests_service_tax = $requests_bill->sum('request_bills.service_tax');
        $requests_driver_earning = $requests_bill->sum('request_bills.driver_commision');
        $requests_total_amount = $requests_bill->sum('request_bills.total_amount');
        return view('taxi.summary.completedlocalview',['driver_list' => $driver_list,'customer_list' => $customer_list ,'requests' => $requests,'request' => $request,'requests_base_price' => $requests_base_price,'requests_waiting_charges' => $requests_waiting_charges,'requests_promo_discount' => $requests_promo_discount,'requests_distance_price' => $requests_distance_price,'requests_bill' => $requests_bill,'requests_admin_commission' => $requests_admin_commission,'requests_service_tax' => $requests_service_tax,'requests_driver_earning' => $requests_driver_earning,'requests_total_amount' => $requests_total_amount]);
    }

    public function CompletedRentalView(Request $request)
    {
    	$requests = RequestModel::leftjoin('request_bills','request_bills.request_id','requests.id')->leftjoin('request_places','request_places.request_id','requests.id')->orderBy('requests.created_at','desc')->where('requests.is_completed',1)->where('requests.trip_type',"RENTAL");
        $requests_bill = RequestBill::leftjoin('requests','requests.id','request_bills.request_id');
        $driver_list = User::role('driver')->where('active',1)->get();
        $customer_list = User::role('user')->where('active',1)->get();
        
        if($request->has('start_date') && $request->start_date != "" && $request->has('end_date') && $request->end_date != "" ){
            $weekStartDate = $request->start_date." 00:00:00";
            $weekEndDate = $request->end_date." 23:59:59";
            $requests = $requests->whereBetween('trip_start_time',[$weekStartDate,$weekEndDate]);
            $requests_bill = $requests_bill->whereBetween('requests.trip_start_time',[$weekStartDate,$weekEndDate]);
        }

        if($request->has('driver') && $request->driver != ""){
            $requests = $requests->where('driver_id',$request->driver);
            $requests_bill = $requests_bill->where('driver_id',$request->driver);
        }
        if($request->has('customer') && $request->customer != ""){
            $requests = $requests->where('user_id',$request->customer);
            $requests_bill = $requests_bill->where('user_id',$request->customer);
        }

        $requests = $requests->get();
        $requests_base_price = $requests_bill->sum('request_bills.base_price');
        $requests_waiting_charges = $requests_bill->sum('request_bills.waiting_charge');
        $requests_promo_discount = $requests_bill->sum('request_bills.promo_discount');
        $requests_distance_price = $requests_bill->sum('request_bills.distance_price');
        $requests_admin_commission = $requests_bill->sum('request_bills.admin_commision');
        $requests_service_tax = $requests_bill->sum('request_bills.service_tax');
        $requests_driver_earning = $requests_bill->sum('request_bills.driver_commision');
        $requests_total_amount = $requests_bill->sum('request_bills.total_amount');
        return view('taxi.summary.completedrentalview',['driver_list' => $driver_list,'customer_list' => $customer_list ,'requests' => $requests,'request' => $request,'requests_base_price' => $requests_base_price,'requests_waiting_charges' => $requests_waiting_charges,'requests_promo_discount' => $requests_promo_discount,'requests_distance_price' => $requests_distance_price,'requests_bill' => $requests_bill,'requests_admin_commission' => $requests_admin_commission,'requests_service_tax' => $requests_service_tax,'requests_driver_earning' => $requests_driver_earning,'requests_total_amount' => $requests_total_amount]);
    }

    public function CompletedOutstationView(Request $request)
    {
    	$requests = RequestModel::leftjoin('request_bills','request_bills.request_id','requests.id')->leftjoin('request_places','request_places.request_id','requests.id')->orderBy('requests.created_at','desc')->where('requests.is_completed',1)->where('requests.trip_type',"OUTSTATION");
        $requests_bill = RequestBill::leftjoin('requests','requests.id','request_bills.request_id');
        $driver_list = User::role('driver')->where('active',1)->get();
        $customer_list = User::role('user')->where('active',1)->get();
       
        if($request->has('start_date') && $request->start_date != "" && $request->has('end_date') && $request->end_date != "" ){
            $weekStartDate = $request->start_date." 00:00:00";
            $weekEndDate = $request->end_date." 23:59:59";
            $requests = $requests->whereBetween('trip_start_time',[$weekStartDate,$weekEndDate]);
            $requests_bill = $requests_bill->whereBetween('requests.trip_start_time',[$weekStartDate,$weekEndDate]);
        }

        if($request->has('driver') && $request->driver != ""){
            $requests = $requests->where('driver_id',$request->driver);
            $requests_bill = $requests_bill->where('driver_id',$request->driver);
        }
        if($request->has('customer') && $request->customer != ""){
            $requests = $requests->where('user_id',$request->customer);
            $requests_bill = $requests_bill->where('user_id',$request->customer);
        }

        $requests = $requests->get();
        $requests_base_price = $requests_bill->sum('request_bills.base_price');
        $requests_waiting_charges = $requests_bill->sum('request_bills.waiting_charge');
        $requests_promo_discount = $requests_bill->sum('request_bills.promo_discount');
        $requests_distance_price = $requests_bill->sum('request_bills.distance_price');
        $requests_admin_commission = $requests_bill->sum('request_bills.admin_commision');
        $requests_service_tax = $requests_bill->sum('request_bills.service_tax');
        $requests_driver_earning = $requests_bill->sum('request_bills.driver_commision');
        $requests_total_amount = $requests_bill->sum('request_bills.total_amount');
        return view('taxi.summary.completedoutstationview',['driver_list' => $driver_list,'customer_list' => $customer_list ,'requests' => $requests,'request' => $request,'requests_base_price' => $requests_base_price,'requests_waiting_charges' => $requests_waiting_charges,'requests_promo_discount' => $requests_promo_discount,'requests_distance_price' => $requests_distance_price,'requests_bill' => $requests_bill,'requests_admin_commission' => $requests_admin_commission,'requests_service_tax' => $requests_service_tax,'requests_driver_earning' => $requests_driver_earning,'requests_total_amount' => $requests_total_amount]);
    }
}
