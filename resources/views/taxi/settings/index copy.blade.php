@extends('layouts.app')

@section('content')
<style>
    .nav-tabs .nav-item .nav-link{
        text-align:center;
        width: 222px;
    }
    .nav-tabs .nav-item .nav-link.active{
        font-size: 25px;
        font-weight: bold;
        background: #2196f3 !important;
        color: #fff !important;
    }
    .nav-tabs .nav-item .nav-link.active i{
        font-size: 25px;
        font-weight: bold;
    }
</style>
<div class="page-header page-header-light">
    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex">
            <h4><i class="icon-arrow-left52 mr-2"></i> <span class="font-weight-semibold">{{ __('setting') }} </span> </h4>
            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>
    </div>

    <div class="breadcrumb-line breadcrumb-line-light header-elements-md-inline">
        <div class="d-flex">
            <div class="breadcrumb">
                <a href="{{ route('dashboard')}}" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> {{ __('home') }}</a>
                <a href="" class="breadcrumb-item active">{{ __('setting') }}</a>
            </div>

            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>       
    </div>
</div>
<div class="content">

    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title">{{ __('setting') }}</h5>
            <div class="header-elements">
                <div class="list-icons">
                    @if(auth()->user()->can('add-zone'))
                        <!-- <a href="{{ route('addzone') }}" class="btn bg-purple btn-sm legitRipple"><i class="icon-plus3 mr-2"></i> {{ __('add-new') }}</a> -->
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="navbar navbar-expand-lg navbar-light bg-light border-top">
        <div class="text-center d-lg-none w-100">
            <button type="button" class="navbar-toggler dropdown-toggle" data-toggle="collapse" data-target="#navbar-second">
                <i class="icon-menu7 mr-2"></i>
                Profile navigation
            </button>
        </div>

        <div class="navbar-collapse collapse" id="navbar-second">
            <ul class="nav navbar-nav">
                <li class="nav-item">
                    <a href="#trip_setting"  class="navbar-nav-link active" data-toggle="tab">
                        <i class="icon-car2 mr-2"></i>
                        {{ __('trip_setting') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#wallet" class="navbar-nav-link" data-toggle="tab">
                        <i class="icon-wallet mr-2"></i>
                        {{ __('wallet') }}
                        <span class="badge badge-pill bg-success position-static ml-auto ml-lg-2">32</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#installation_setting" class="navbar-nav-link" data-toggle="tab">
                        <i class="icon-hash mr-2"></i>
                        {{ __('installation_setting') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#general" class="navbar-nav-link" data-toggle="tab">
                        <i class="icon-cog3 mr-2"></i>
                        {{ __('general') }}
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="content">

<!-- Simple lists -->
<div class="row">
    <div class="col-md-8">

        <div class="card " id="">
            <div class="card-body">
                <div class="content">
                    <!-- Inner container -->
                    <div class="d-flex align-items-start flex-column flex-md-row">
                        <!-- Left content -->
                        <div class="tab-content w-100 order-2 order-md-1">
                            <div class="tab-pane fade show active" id="trip_setting"> 
                                <div class="card-body  ">
                                    <form method="post" id="trip_setting_form" >
                                        @csrf
                                        <h5 class="card-title"><b>{{ __('trip_setting') }}</b></h5>
                                        <div class="alert alert-danger alert-dismissible" id="errorbox">
                                            <!-- <button type="button" class="close" data-dismiss="alert"><span>×</span></button> -->
                                            <span id="errorContent"></span>
                                        </div>
                                          
                                        <div class="form-group row">
                                            <label class="col-form-label col-sm-3"><b>{{ __('assign_method') }}</b></label>
                                            <div class="col-sm-9">
                                                    <select class="form-control" name="assign_method" id="assign_method" required>
                                                        <option value="1" {{(old('assign_method',array_key_exists('assign_method', $settings) ? $settings['assign_method'] : '') == 1 )?'selected':''}}>
                                                        {{ __('one_by_one') }}</option>
                                                    </select>                                            </div>
                                        </div>
                                         
                                        <div class="form-group row">
                                            <label class="col-form-label col-sm-3"><b>{{ __('hide_ride_otp') }}</b></label>
                                            <div class="col-sm-9">
                                                <select class="form-control" name="hide_ride_otp" id="hide_ride_otp" required>
                                                    <option value="1" {{(old('hide_ride_otp',array_key_exists('hide_ride_otp', $settings) ? $settings['hide_ride_otp'] : '') == 1 )?'selected':''}}>
                                                    {{ __('yes') }}</option>
                                                    <option value="0" {{(old('hide_ride_otp',array_key_exists('hide_ride_otp', $settings) ? $settings['hide_ride_otp'] : '') == 0 )?'selected':''}}>
                                                    {{ __('no') }} </option>
                                                </select>                    
                                            </div>
                                        </div>
                                         
                                        <div class="form-group row">
                                            <label class="col-form-label col-sm-3"><b>{{ __('driver_time_out') }}</b></label>
                                            <div class="col-sm-9">
                                            <input id="driver_time_out" name="driver_time_out" placeholder="{{ __('driver_time_out') }}" type="text" class="form-control" value="{{array_key_exists('driver_time_out', $settings) ? $settings['driver_time_out'] : ''}}" required>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row">
                                            <label class="col-form-label col-sm-3"><b>{{ __('service_tax') }}</b></label>
                                            <div class="col-sm-9">
                                            <input id="service_tax" name="service_tax" placeholder="{{ __('service_tax') }}" type="text" class="form-control" value="{{array_key_exists('service_tax', $settings) ? $settings['service_tax'] : ''}}" required>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row">
                                            <label class="col-form-label col-sm-3"><b>{{ __('driver_search_radius') }}</b></label>
                                            <div class="col-sm-9">
                                            <input id="driver_search_radius" name="driver_search_radius" placeholder="{{ __('driver_search_radius') }}" type="text" class="form-control" value="{{array_key_exists('driver_search_radius', $settings) ? $settings['driver_search_radius'] : ''}}" required>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row">
                                            <label class="col-form-label col-sm-3"><b>{{ __('auto_transfer') }}</b></label>
                                            <div class="col-sm-9">
                                            <select class="form-control" name="auto_transfer" id="auto_transfer" required>
                                                        <option value="1" {{(old('auto_transfer',array_key_exists('auto_transfer', $settings) ? $settings['auto_transfer'] : '') == 1 )?'selected':''}}>
                                                        {{ __('yes') }}</option>
                                                        <option value="0" {{(old('auto_transfer',array_key_exists('auto_transfer', $settings) ? $settings['auto_transfer'] : '') == 0 )?'selected':''}}>
                                                        {{ __('no') }} </option>
                                                    </select>                                            </div>
                                        </div>
                                        
                                        <div class="form-group row">
                                            <label class="col-form-label col-sm-3"><b>{{ __('auto_logout_driver_time') }}</b></label>
                                            <div class="col-sm-9">
                                            <select class="form-control" name="auto_logout_driver_time" id="auto_logout_driver_time" required>
                                                        <option value="9" {{(old('auto_logout_driver_time',array_key_exists('auto_logout_driver_time', $settings) ? $settings['auto_logout_driver_time'] : '') == 9 )?'selected':''}}>
                                                        {{ __('after_9_hours') }}</option>
                                                        <option value="12" {{(old('auto_logout_driver_time',array_key_exists('auto_logout_driver_time', $settings) ? $settings['auto_logout_driver_time'] : '') == 12 )?'selected':''}}>
                                                        {{ __('after_12_hours') }} </option>
                                                        <option value="24" {{(old('auto_logout_driver_time',array_key_exists('auto_logout_driver_time', $settings) ? $settings['auto_logout_driver_time'] : '') == 24 )?'selected':''}}>
                                                        {{ __('after_24_hours') }} </option>
                                                    </select>                                            </div>
                                        </div>
                                        
                                        <div class="form-group row">
                                            <label class="col-form-label col-sm-3"><b>{{ __('arrived_enabled_distance') }}</b></label>
                                            <div class="col-sm-9">
                                            <input id="arrived_enabled_distance" name="arrived_enabled_distance" placeholder="{{ __('arrived_enabled_distance') }}" type="text" class="form-control" value="{{array_key_exists('arrived_enabled_distance', $settings) ? $settings['arrived_enabled_distance'] : ''}}" required>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row">
                                            <label class="col-form-label col-sm-3"><b>{{ __('driver_trip_limit') }}</b></label>
                                            <div class="col-sm-9">
                                            <input id="driver_trip_limit" name="driver_trip_limit" placeholder="{{ __('driver_trip_limit') }}" type="text" class="form-control" value="{{array_key_exists('driver_trip_limit', $settings) ? $settings['driver_trip_limit'] : ''}}" required>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-form-label col-sm-3"><b>{{ __('how_many_trips_can_be_cancel_without_paying_cancellation_fee') }}</b></label>
                                            <div class="col-sm-9">
                                            <input id="how_many_trips_can_be_cancel_without_paying_cancellation_fee" name="how_many_trips_can_be_cancel_without_paying_cancellation_fee" placeholder="{{ __('how_many_trips_can_be_cancel_without_paying_cancellation_fee') }}" type="text" class="form-control" value="{{array_key_exists('how_many_trips_can_be_cancel_without_paying_cancellation_fee', $settings) ? $settings['how_many_trips_can_be_cancel_without_paying_cancellation_fee'] : ''}}" required>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-form-label col-sm-3"><b>{{ __('pick_up_location_change_distance_limit') }}</b></label>
                                            <div class="col-sm-9">
                                            <input id="pick_up_location_change_distance_limit" name="pick_up_location_change_distance_limit" placeholder="{{ __('pick_up_location_change_distance_limit') }}" type="text" class="form-control" value="{{array_key_exists('pick_up_location_change_distance_limit', $settings) ? $settings['pick_up_location_change_distance_limit'] : ''}}" required>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-form-label col-sm-3"><b>{{ __('top_20_drivers_minimum_rating_limit') }}</b></label>
                                            <div class="col-sm-9">
                                            <input id="top_20_drivers_minimum_rating_limit" name="top_20_drivers_minimum_rating_limit" placeholder="{{ __('top_20_drivers_minimum_rating_limit') }}" type="text" class="form-control" value="{{array_key_exists('top_20_drivers_minimum_rating_limit', $settings) ? $settings['top_20_drivers_minimum_rating_limit'] : ''}}" required>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-form-label col-sm-3"><b>{{ __('waiting_grace_time_before_start_trip') }}</b></label>
                                            <div class="col-sm-9">
                                            <input id="waiting_grace_time_before_start_trip" name="waiting_grace_time_before_start_trip" placeholder="{{ __('waiting_grace_time_before_start_trip') }}" type="text" class="form-control" value="{{array_key_exists('waiting_grace_time_before_start_trip', $settings) ? $settings['waiting_grace_time_before_start_trip'] : ''}}" required>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-form-label col-sm-3"><b>{{ __('trip_period') }}</b></label>
                                            <div class="col-sm-9">
                                            <input id="trip_period" name="trip_period" placeholder="{{ __('trip_period') }}" type="text" class="form-control" value="{{array_key_exists('trip_period', $settings) ? $settings['trip_period'] : ''}}" required>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-form-label col-sm-3"><b>{{ __('reward_point_for_five_star_rating') }}</b></label>
                                            <div class="col-sm-9">
                                            <input id="reward_point_for_five_star_rating" name="reward_point_for_five_star_rating" placeholder="{{ __('reward_point_for_five_star_rating') }}" type="text" class="form-control" value="{{array_key_exists('reward_point_for_five_star_rating', $settings) ? $settings['reward_point_for_five_star_rating'] : ''}}" required>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row">
                                            <label class="col-form-label col-sm-3"><b>{{ __('dispatch_create_request') }}</b></label>
                                            <div class="col-sm-9">
                                            <select class="form-control" name="dispatch_create_request" id="dispatch_create_request" required>
                                                        <option value="1" {{(old('dispatch_create_request',array_key_exists('dispatch_create_request', $settings) ? $settings['dispatch_create_request'] : '') == 1 )?'selected':''}}>
                                                        {{ __('automatic') }}</option>
                                                        <option value="0" {{(old('dispatch_create_request',array_key_exists('dispatch_create_request', $settings) ? $settings['dispatch_create_request'] : '') == 0 )?'selected':''}}>
                                                        {{ __('manual') }} </option>
                                                    </select>                                            </div>
                                        </div>
                                        
                                        <div class="form-group row">
                                            <label class="col-form-label col-sm-3"><b>{{ __('cancel_button_enable_after_certain_minutes') }}</b></label>
                                            <div class="col-sm-9">
                                            <input id="cancel_button_enable_after_certain_minutes" name="cancel_button_enable_after_certain_minutes" placeholder="{{ __('cancel_button_enable_after_certain_minutes') }}" type="text" class="form-control" value="{{array_key_exists('cancel_button_enable_after_certain_minutes', $settings) ? $settings['cancel_button_enable_after_certain_minutes'] : ''}}" required>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row">
                                            <label class="col-form-label col-sm-3"><b>{{ __('cancel_timer') }}</b></label>
                                            <div class="col-sm-9">
                                            <input id="cancel_timer" name="cancel_timer" placeholder="{{ __('cancel_timer') }}" type="text" class="form-control" value="{{array_key_exists('cancel_timer', $settings) ? $settings['cancel_timer'] : ''}}" required>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row">
                                            <label class="col-form-label col-sm-3"><b>{{ __('arriving_meter') }}</b></label>
                                            <div class="col-sm-9">
                                            <input id="arriving_meter" name="arriving_meter" placeholder="{{ __('arriving_meter') }}" type="text" class="form-control" value="{{array_key_exists('arriving_meter', $settings) ? $settings['arriving_meter'] : ''}}" required>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-form-label col-sm-3"><b>{{ __('end_trip_enable') }}</b></label>
                                            <div class="col-sm-9">
                                            <input id="end_trip_enable" name="end_trip_enable" placeholder="{{ __('end_trip_enable') }}" type="text" class="form-control" value="{{array_key_exists('end_trip_enable', $settings) ? $settings['end_trip_enable'] : ''}}" required>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-form-label col-sm-3"><b>{{ __('arriving_meter_button_press') }}</b></label>
                                            <div class="col-sm-9">
                                            <input id="arriving_meter_button_press" name="arriving_meter_button_press" placeholder="{{ __('arriving_meter_button_press') }}" type="text" class="form-control" value="{{array_key_exists('arriving_meter_button_press', $settings) ? $settings['arriving_meter_button_press'] : ''}}" required>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-form-label col-sm-3"><b>{{ __('auto_cancel_timer') }}</b></label>
                                            <div class="col-sm-9">
                                            <input id="auto_cancel_timer" name="auto_cancel_timer" placeholder="{{ __('auto_cancel_timer') }}" type="text" class="form-control" value="{{array_key_exists('auto_cancel_timer', $settings) ? $settings['auto_cancel_timer'] : ''}}" required>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-form-label col-sm-3"><b>{{ __('waiting_time_speed') }}</b></label>
                                            <div class="col-sm-9">
                                            <input id="waiting_time_speed" name="waiting_time_speed" placeholder="{{ __('waiting_time_speed') }}" type="text" class="form-control" value="{{array_key_exists('waiting_time_speed', $settings) ? $settings['waiting_time_speed'] : ''}}" required>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-form-label col-sm-3"><b>{{ __('user_promotional_amount') }}</b></label>
                                            <div class="col-sm-9">
                                            <input id="user_promotional_amount" name="user_promotional_amount" placeholder="{{ __('user_promotional_amount') }}" type="text" class="form-control" value="{{array_key_exists('user_promotional_amount', $settings) ? $settings['user_promotional_amount'] : ''}}" required>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row">
                                            <label class="col-form-label col-sm-3"><b>{{ __('auto_off_line_time') }}</b></label>
                                            <div class="col-sm-9">
                                            <input id="auto_off_line_time" name="auto_off_line_time" placeholder="{{ __('auto_off_line_time') }}" type="text" class="form-control" value="{{array_key_exists('auto_off_line_time', $settings) ? $settings['auto_off_line_time'] : ''}}" required>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row">
                                            <label class="col-form-label col-sm-3"><b>{{ __('free_admin_commission_type') }}</b></label>
                                            <div class="col-sm-9">
                                            <select class="form-control" name="free_admin_commission_type" id="free_admin_commission_type" required>
                                                        <option value="1" {{(old('free_admin_commission_type',array_key_exists('free_admin_commission_type', $settings) ? $settings['free_admin_commission_type'] : '') == 1 )?'selected':''}}>
                                                        {{ __('no_of_trips') }}</option>
                                                        <option value="0" {{(old('free_admin_commission_type',array_key_exists('free_admin_commission_type', $settings) ? $settings['free_admin_commission_type'] : '') == 0 )?'selected':''}}>
                                                        {{ __('no_of_months') }}</option>
                                                    </select>                                            </div>
                                        </div>
                                        
                                        <div class="form-group row">
                                            <label class="col-form-label col-sm-3"><b>{{ __('free_admin_commission_value') }}</b></label>
                                            <div class="col-sm-9">
                                            <input id="free_admin_commission_value" name="free_admin_commission_value" placeholder="{{ __('free_admin_commission_value') }}" type="text" class="form-control" value="{{array_key_exists('free_admin_commission_value', $settings) ? $settings['free_admin_commission_value'] : ''}}" required>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row">
                                            <label class="col-form-label col-sm-3"><b>{{ __('user_trip_bounes') }}</b></label>
                                            <div class="col-sm-9">
                                            <input id="user_trip_bounes" name="user_trip_bounes" placeholder="{{ __('user_trip_bounes') }}" type="text" class="form-control" value="{{array_key_exists('user_trip_bounes', $settings) ? $settings['user_trip_bounes'] : ''}}" required>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row">
                                            <label class="col-form-label col-sm-3"><b>{{ __('driver_block_rate') }}</b></label>
                                            <div class="col-sm-9">
                                            <input id="driver_block_rate" name="driver_block_rate" placeholder="{{ __('driver_block_rate') }}" type="text" class="form-control" value="{{array_key_exists('driver_block_rate', $settings) ? $settings['driver_block_rate'] : ''}}" required>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row">
                                            <label class="col-form-label col-sm-3"><b>{{ __('driver_block_wallet_balance') }}</b></label>
                                            <div class="col-sm-9">
                                            <input id="driver_block_wallet_balance" name="driver_block_wallet_balance" placeholder="{{ __('driver_block_wallet_balance') }}" type="text" class="form-control" value="{{array_key_exists('driver_block_wallet_balance', $settings) ? $settings['driver_block_wallet_balance'] : ''}}" required>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row">
                                            <label class="col-form-label col-sm-3"><b>{{ __('driver_block_acceptance_ratio') }}</b></label>
                                            <div class="col-sm-9">
                                            <input id="driver_block_acceptance_ratio" name="driver_block_acceptance_ratio" placeholder="{{ __('driver_block_acceptance_ratio') }}" type="text" class="form-control" value="{{array_key_exists('driver_block_acceptance_ratio', $settings) ? $settings['driver_block_acceptance_ratio'] : ''}}" required>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row">
                                            <label class="col-form-label col-sm-3"><b>{{ __('driver_block_trip_reject') }}</b></label>
                                            <div class="col-sm-9">
                                            <input id="driver_block_trip_reject" name="driver_block_trip_reject" placeholder="{{ __('driver_block_trip_reject') }}" type="text" class="form-control" value="{{array_key_exists('driver_block_trip_reject', $settings) ? $settings['driver_block_trip_reject'] : ''}}" required>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-form-label col-sm-3"><b>{{ __('dispute_timing') }}</b></label>
                                            <div class="col-sm-9">
                                            <input id="dispute_timing" name="dispute_timing" placeholder="{{ __('dispute_timing') }}" type="text" class="form-control" value="{{array_key_exists('dispute_timing', $settings) ? $settings['dispute_timing'] : ''}}" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="col-form-label"><b>{{ __('auto_araive_radius_pickup') }}</b></label>
                                            <input id="auto_araive_radius_pickup" name="auto_araive_radius_pickup" placeholder="{{ __('auto_araive_radius_pickup') }}" type="text" class="form-control" value="{{array_key_exists('auto_araive_radius_pickup', $settings) ? $settings['auto_araive_radius_pickup'] : ''}}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="col-form-label"><b>{{ __('auto_araive_radius_drop') }}</b></label>
                                            <input id="auto_araive_radius_drop" name="auto_araive_radius_drop" placeholder="{{ __('auto_araive_radius_drop') }}" type="text" class="form-control" value="{{array_key_exists('auto_araive_radius_drop', $settings) ? $settings['auto_araive_radius_drop'] : ''}}" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="wallet">
                                <div class="card-body">
                                    <form method="post" id="wallet_form" >
                                        @csrf
                                        <h5 class="card-title"><b>{{ __('wallet') }}</b></h5>
                                        <div class="alert alert-danger alert-dismissible" id="walleterrorbox">
                                            <!-- <button type="button" class="close" data-dismiss="alert"><span>×</span></button> -->
                                            <span id="errorContent"></span>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-form-label col-sm-3"><b>{{ __('wallet_min_amount_for_trip') }}</b></label>
                                            <div class="col-sm-9">
                                            <input id="wallet_min_amount_for_trip" name="wallet_min_amount_for_trip" placeholder="{{ __('wallet_min_amount_for_trip') }}" type="text" class="form-control" value="{{array_key_exists('wallet_min_amount_for_trip', $settings) ? $settings['wallet_min_amount_for_trip'] : ''}}" required>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row">
                                            <label class="col-form-label col-sm-3"><b>{{ __('wallet_min_amount_for_trip_driver') }}</b></label>
                                            <div class="col-sm-9">
                                            <input id="wallet_min_amount_for_trip_driver" name="wallet_min_amount_for_trip_driver" placeholder="{{ __('wallet_min_amount_for_trip_driver') }}" type="text" class="form-control" value="{{array_key_exists('wallet_min_amount_for_trip_driver', $settings) ? $settings['wallet_min_amount_for_trip_driver'] : ''}}" required>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row">
                                            <label class="col-form-label col-sm-3"><b>{{ __('wallet_max_amount_to_balance') }}</b></label>
                                            <div class="col-sm-9">
                                            <input id="wallet_max_amount_to_balance" name="wallet_max_amount_to_balance" placeholder="{{ __('wallet_max_amount_to_balance') }}" type="text" class="form-control" value="{{array_key_exists('wallet_max_amount_to_balance', $settings) ? $settings['wallet_max_amount_to_balance'] : ''}}" required>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row">
                                            <label class="col-form-label col-sm-3"><b>{{ __('wallet_min_amount_to_add') }}</b></label>
                                            <div class="col-sm-9">
                                            <input id="wallet_min_amount_to_add" name="wallet_min_amount_to_add" placeholder="{{ __('wallet_min_amount_to_add') }}" type="text" class="form-control" value="{{array_key_exists('wallet_min_amount_to_add', $settings) ? $settings['wallet_min_amount_to_add'] : ''}}" required>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row">
                                            <label class="col-form-label col-sm-3"><b>{{ __('wallet_amount_to_alert_driver') }}</b></label>
                                            <div class="col-sm-9">
                                            <input id="wallet_amount_to_alert_driver" name="wallet_amount_to_alert_driver" placeholder="{{ __('wallet_amount_to_alert_driver') }}" type="text" class="form-control" value="{{array_key_exists('wallet_amount_to_alert_driver', $settings) ? $settings['wallet_amount_to_alert_driver'] : ''}}" required>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row">
                                            <label class="col-form-label col-sm-3"><b>{{ __('wallet_negative_amount_to_alert_driver') }}</b></label>
                                            <div class="col-sm-9">
                                            <input id="wallet_negative_amount_to_alert_driver" name="wallet_negative_amount_to_alert_driver" placeholder="{{ __('wallet_negative_amount_to_alert_driver') }}" type="text" class="form-control" value="{{array_key_exists('wallet_negative_amount_to_alert_driver', $settings) ? $settings['wallet_negative_amount_to_alert_driver'] : ''}}" required>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row">
                                            <label class="col-form-label col-sm-3"><b>{{ __('wallet_driver_refernce_amount') }}</b></label>
                                            <div class="col-sm-9">
                                            <input id="wallet_driver_refernce_amount" name="wallet_driver_refernce_amount" placeholder="{{ __('wallet_driver_refernce_amount') }}" type="text" class="form-control" value="{{array_key_exists('wallet_driver_refernce_amount', $settings) ? $settings['wallet_driver_refernce_amount'] : ''}}" required>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row">
                                            <label class="col-form-label col-sm-3"><b>{{ __('wallet_user_refernce_amount') }}</b></label>
                                            <div class="col-sm-9">
                                            <input id="wallet_user_refernce_amount" name="wallet_user_refernce_amount" placeholder="{{ __('wallet_user_refernce_amount') }}" type="text" class="form-control" value="{{array_key_exists('wallet_user_refernce_amount', $settings) ? $settings['wallet_user_refernce_amount'] : ''}}" required>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row">
                                            <label class="col-form-label col-sm-3"><b>{{ __('referan_amount_trip_count') }}</b></label>
                                            <div class="col-sm-9">
                                            <input id="referan_amount_trip_count" name="referan_amount_trip_count" placeholder="{{ __('referan_amount_trip_count') }}" type="text" class="form-control" value="{{array_key_exists('referan_amount_trip_count', $settings) ? $settings['referan_amount_trip_count'] : ''}}" required>
                                            </div>
                                        </div>
                                        <!-- <button type="button" class="btn btn-link">{{ __('close') }}</button> -->
                                        <button type="submit" id="wallet_saveBtn" class="btn bg-primary">{{ __('save-changes') }}</button>
                                    </form>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="installation_setting">
                                <div class="card-body">
                                    <form method="post" id="installation_setting_form" >
                                        @csrf
                                        <h5 class="card-title"><b>{{ __('installation_setting') }}</b></h5>
                                        <div class="alert alert-danger alert-dismissible" id="installation_setting_errorbox">
                                            <!-- <button type="button" class="close" data-dismiss="alert"><span>×</span></button> -->
                                            <span id="errorContent"></span>
                                        </div>
                                        
                                        <div class="form-group row">
                                            <label class="col-form-label col-sm-3"><b>{{ __('google_browser_key') }}</b></label>
                                            <div class="col-sm-9">
                                            <input id="google_browser_key" name="google_browser_key" placeholder="{{ __('google_browser_key') }}" type="text" class="form-control" value="{{array_key_exists('google_browser_key', $settings) ? $settings['google_browser_key'] : ''}}" required>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row">
                                            <label class="col-form-label col-sm-3"><b>{{ __('twillo_account_sid') }}</b></label>
                                            <div class="col-sm-9">
                                            <input id="twillo_account_sid" name="twillo_account_sid" placeholder="{{ __('twillo_account_sid') }}" type="text" class="form-control" value="{{array_key_exists('twillo_account_sid', $settings) ? $settings['twillo_account_sid'] : ''}}" required>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row">
                                            <label class="col-form-label col-sm-3"><b>{{ __('twillo_auth_token') }}</b></label>
                                            <div class="col-sm-9">
                                            <input id="twillo_auth_token" name="twillo_auth_token" placeholder="{{ __('twillo_auth_token') }}" type="text" class="form-control" value="{{array_key_exists('twillo_auth_token', $settings) ? $settings['twillo_auth_token'] : ''}}" required>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row">
                                            <label class="col-form-label col-sm-3"><b>{{ __('twillo_number') }}</b></label>
                                            <div class="col-sm-9">
                                            <input id="twillo_number" name="twillo_number" placeholder="{{ __('twillo_number') }}" type="text" class="form-control" value="{{array_key_exists('twillo_number', $settings) ? $settings['twillo_number'] : ''}}" required>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row">
                                            <label class="col-form-label col-sm-3"><b>{{ __('btree_environment') }}</b></label>
                                            <div class="col-sm-9">
                                            <input id="btree_environment" name="btree_environment" placeholder="{{ __('btree_environment') }}" type="text" class="form-control" value="{{array_key_exists('btree_environment', $settings) ? $settings['btree_environment'] : ''}}" required>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row">
                                            <label class="col-form-label col-sm-3"><b>{{ __('btree_merchant_id') }}</b></label>
                                            <div class="col-sm-9">
                                            <input id="btree_merchant_id" name="btree_merchant_id" placeholder="{{ __('btree_merchant_id') }}" type="text" class="form-control" value="{{array_key_exists('btree_merchant_id', $settings) ? $settings['btree_merchant_id'] : ''}}" required>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row">
                                            <label class="col-form-label col-sm-3"><b>{{ __('btree_public_key') }}</b></label>
                                            <div class="col-sm-9">
                                            <input id="btree_public_key" name="btree_public_key" placeholder="{{ __('btree_public_key') }}" type="text" class="form-control" value="{{array_key_exists('btree_public_key', $settings) ? $settings['btree_public_key'] : ''}}" required>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row">
                                            <label class="col-form-label col-sm-3"><b>{{ __('btree_private_key') }}</b></label>
                                            <div class="col-sm-9">
                                            <input id="btree_private_key" name="btree_private_key" placeholder="{{ __('btree_private_key') }}" type="text" class="form-control" value="{{array_key_exists('btree_private_key', $settings) ? $settings['btree_private_key'] : ''}}" required>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row">
                                            <label class="col-form-label col-sm-3"><b>{{ __('btree_master_merchant') }}</b></label>
                                            <div class="col-sm-9">
                                            <input id="btree_master_merchant" name="btree_master_merchant" placeholder="{{ __('btree_master_merchant') }}" type="text" class="form-control" value="{{array_key_exists('btree_master_merchant', $settings) ? $settings['btree_master_merchant'] : ''}}" required>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row">
                                            <label class="col-form-label col-sm-3"><b>{{ __('btree_default_merchant') }}</b></label>
                                            <div class="col-sm-9">
                                            <input id="btree_default_merchant" name="btree_default_merchant" placeholder="{{ __('btree_default_merchant') }}" type="text" class="form-control" value="{{array_key_exists('btree_default_merchant', $settings) ? $settings['btree_default_merchant'] : ''}}" required>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row">
                                            <label class="col-form-label col-sm-3"><b>{{ __('stripe_public_key') }}</b></label>
                                            <div class="col-sm-9">
                                            <input id="stripe_public_key" name="stripe_public_key" placeholder="{{ __('stripe_public_key') }}" type="text" class="form-control" value="{{array_key_exists('stripe_public_key', $settings) ? $settings['stripe_public_key'] : ''}}" required>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row">
                                            <label class="col-form-label col-sm-3"><b>{{ __('stripe_private_key') }}</b></label>
                                            <div class="col-sm-9">
                                            <input id="stripe_private_key" name="stripe_private_key" placeholder="{{ __('stripe_private_key') }}" type="text" class="form-control" value="{{array_key_exists('stripe_private_key', $settings) ? $settings['stripe_private_key'] : ''}}" required>
                                            </div>
                                        </div>
                                        
                                        <!-- <button type="button" class="btn btn-link">{{ __('close') }}</button> -->
                                        <button type="submit" id="installation_setting_saveBtn" class="btn bg-primary">{{ __('save-changes') }}</button>
                                    </form>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="general">
                                <div class="card-body">
                                    <form method="post" id="general_form" >
                                        @csrf
                                        <h5 class="card-title"><b>{{ __('general') }}</b></h5>
                                        <div class="alert alert-danger alert-dismissible" id="general_errorbox">
                                            <!-- <button type="button" class="close" data-dismiss="alert"><span>×</span></button> -->
                                            <span id="errorContent"></span>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-form-label col-sm-3"><b>{{ __('application_name') }}</b></label>
                                            <div class="col-sm-9">
                                            <input id="application_name" name="application_name" placeholder="{{ __('application_name') }}" type="text" class="form-control" value="{{array_key_exists('application_name', $settings) ? $settings['application_name'] : ''}}" required>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                        <label class="col-form-label col-sm-3"><b>{{ __('logo') }}</b></label>
                                            <div class="col-sm-9">
                                            <input id="logo" name="logo" placeholder="{{ __('logo') }}" type="file" class="form-control" value="" required>
                                                    <img src="{{array_key_exists('logo', $settings) ? $settings['logo'] : ''}}" height="40px" width="auto" alt="" id="view_image" />                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-form-label col-sm-3"><b>{{ __('head_office_number') }}</b></label>
                                            <div class="col-sm-9">
                                            <input id="head_office_number" name="head_office_number" placeholder="{{ __('head_office_number') }}" type="text" class="form-control" value="{{array_key_exists('head_office_number', $settings) ? $settings['head_office_number'] : ''}}" required>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-form-label col-sm-3"><b>{{ __('customer_care_number') }}</b></label>
                                            <div class="col-sm-9">
                                            <input id="customer_care_number" name="customer_care_number" placeholder="{{ __('customer_care_number') }}" type="text" class="form-control" value="{{array_key_exists('customer_care_number', $settings) ? $settings['customer_care_number'] : ''}}" required>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-form-label col-sm-3"><b>{{ __('help_email') }}</b></label>
                                            <div class="col-sm-9">
                                            <input id="help_email" name="help_email" placeholder="{{ __('help_email') }}" type="email" class="form-control" value="{{array_key_exists('help_email', $settings) ? $settings['help_email'] : ''}}" required>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-form-label col-sm-3"><b>{{ __('time_zone') }}</b></label>
                                            <div class="col-sm-9">
                                                    <select class="form-control" name="time_zone" id="time_zone" required>
                                                        <option value="">Select Time Zone</option>
                                                        @foreach($time_zone as $value)
                                                            <option value="{{$value->time_zone}}" {{(old('time_zone',array_key_exists('time_zone', $settings) ? $settings['time_zone'] : '') == $value->time_zone )?'selected':''}}>
                                                            {{$value->time_zone}} ({{$value->gmt_offset}})</option>
                                                        @endforeach
                                                    </select>                                           
                                                </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-form-label col-sm-3"><b>{{ __('language') }}</b></label>
                                            <div class="col-sm-9">
                                                 <select class="form-control" name="language" id="language" required>
                                                    <option value="">Select {{ __('language') }}</option>
                                                    @foreach($languages as $values)
                                                    <option value="{{$values->code}}" {{(old('language',array_key_exists('language', $settings) ? $settings['language'] : '') == $values->code )?'selected':''}}>
                                                    {{$values->name}}</option>
                                                    @endforeach
                                                </select>                    
                                            </div>
                                        </div>

                                        

                                        
                                        <!-- <button type="button" class="btn btn-link">{{ __('close') }}</button> -->
                                        <button type="submit" id="general_saveBtn" class="btn bg-primary">{{ __('save-changes') }}</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- /left content -->
                    </div>
                </div>
            </div>
        </div>
    </div>

        <!-- <div class="card" id="">
            <div class="card-body">
                <div class="d-md-flex">
                    <ul class="nav nav-tabs nav-tabs-vertical flex-column mr-md-3 wmin-md-200 mb-md-0 border-bottom-0">
                        <li class="nav-item"><a href="#trip_setting" class="nav-link active" data-toggle="tab"><i class="icon-car2 mr-2"></i><br> {{ __('trip_setting') }}</a></li>
                        <li class="nav-item"><a href="#wallet" class="nav-link" data-toggle="tab"><i class="icon-wallet mr-2"></i><br> {{ __('wallet') }}</a></li>
                        <li class="nav-item"><a href="#installation_setting" class="nav-link" data-toggle="tab"><i class="icon-hash mr-2"></i><br> {{ __('installation_setting') }}</a></li>
                        <li class="nav-item"><a href="#general" class="nav-link" data-toggle="tab"><i class="icon-cube mr-2"></i><br> {{ __('general') }}</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="trip_setting">
                            <div class="card-body">
                                <form method="post" id="trip_setting_form" >
                                    @csrf
                                    <h5 class="card-title">{{ __('trip_setting') }}</h5>
                                    <div class="alert alert-danger alert-dismissible" id="errorbox">
                                        <span id="errorContent"></span>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="col-form-label"><b>{{ __('assign_method') }}</b></label>
                                                <select class="form-control" name="assign_method" id="assign_method" required>
                                                    <option value="1" {{(old('assign_method',array_key_exists('assign_method', $settings) ? $settings['assign_method'] : '') == 1 )?'selected':''}}>
                                                    {{ __('one_by_one') }}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="col-form-label"><b>{{ __('hide_ride_otp') }}</b></label>
                                                <select class="form-control" name="hide_ride_otp" id="hide_ride_otp" required>
                                                    <option value="1" {{(old('hide_ride_otp',array_key_exists('hide_ride_otp', $settings) ? $settings['hide_ride_otp'] : '') == 1 )?'selected':''}}>
                                                    {{ __('yes') }}</option>
                                                    <option value="0" {{(old('hide_ride_otp',array_key_exists('hide_ride_otp', $settings) ? $settings['hide_ride_otp'] : '') == 0 )?'selected':''}}>
                                                    {{ __('no') }} </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="col-form-label"><b>{{ __('driver_time_out') }}</b></label>
                                                <input id="driver_time_out" name="driver_time_out" placeholder="{{ __('driver_time_out') }}" type="text" class="form-control" value="{{array_key_exists('driver_time_out', $settings) ? $settings['driver_time_out'] : ''}}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="col-form-label"><b>{{ __('service_tax') }}</b></label>
                                                <input id="service_tax" name="service_tax" placeholder="{{ __('service_tax') }}" type="text" class="form-control" value="{{array_key_exists('service_tax', $settings) ? $settings['service_tax'] : ''}}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="col-form-label"><b>{{ __('driver_search_radius') }}</b></label>
                                                <input id="driver_search_radius" name="driver_search_radius" placeholder="{{ __('driver_search_radius') }}" type="text" class="form-control" value="{{array_key_exists('driver_search_radius', $settings) ? $settings['driver_search_radius'] : ''}}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="col-form-label"><b>{{ __('auto_transfer') }}</b></label>
                                                <select class="form-control" name="auto_transfer" id="auto_transfer" required>
                                                    <option value="1" {{(old('auto_transfer',array_key_exists('auto_transfer', $settings) ? $settings['auto_transfer'] : '') == 1 )?'selected':''}}>
                                                    {{ __('yes') }}</option>
                                                    <option value="0" {{(old('auto_transfer',array_key_exists('auto_transfer', $settings) ? $settings['auto_transfer'] : '') == 0 )?'selected':''}}>
                                                    {{ __('no') }} </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="col-form-label"><b>{{ __('auto_logout_driver_time') }}</b></label>
                                                <select class="form-control" name="auto_logout_driver_time" id="auto_logout_driver_time" required>
                                                    <option value="9" {{(old('auto_logout_driver_time',array_key_exists('auto_logout_driver_time', $settings) ? $settings['auto_logout_driver_time'] : '') == 9 )?'selected':''}}>
                                                    {{ __('after_9_hours') }}</option>
                                                    <option value="12" {{(old('auto_logout_driver_time',array_key_exists('auto_logout_driver_time', $settings) ? $settings['auto_logout_driver_time'] : '') == 12 )?'selected':''}}>
                                                    {{ __('after_12_hours') }} </option>
                                                    <option value="24" {{(old('auto_logout_driver_time',array_key_exists('auto_logout_driver_time', $settings) ? $settings['auto_logout_driver_time'] : '') == 24 )?'selected':''}}>
                                                    {{ __('after_24_hours') }} </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="col-form-label"><b>{{ __('arrived_enabled_distance') }}</b></label>
                                                <input id="arrived_enabled_distance" name="arrived_enabled_distance" placeholder="{{ __('arrived_enabled_distance') }}" type="text" class="form-control" value="{{array_key_exists('arrived_enabled_distance', $settings) ? $settings['arrived_enabled_distance'] : ''}}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="col-form-label"><b>{{ __('driver_trip_limit') }}</b></label>
                                                <input id="driver_trip_limit" name="driver_trip_limit" placeholder="{{ __('driver_trip_limit') }}" type="text" class="form-control" value="{{array_key_exists('driver_trip_limit', $settings) ? $settings['driver_trip_limit'] : ''}}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="col-form-label"><b>{{ __('how_many_trips_can_be_cancel_without_paying_cancellation_fee') }}</b></label>
                                                <input id="how_many_trips_can_be_cancel_without_paying_cancellation_fee" name="how_many_trips_can_be_cancel_without_paying_cancellation_fee" placeholder="{{ __('how_many_trips_can_be_cancel_without_paying_cancellation_fee') }}" type="text" class="form-control" value="{{array_key_exists('how_many_trips_can_be_cancel_without_paying_cancellation_fee', $settings) ? $settings['how_many_trips_can_be_cancel_without_paying_cancellation_fee'] : ''}}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="col-form-label"><b>{{ __('pick_up_location_change_distance_limit') }}</b></label>
                                                <input id="pick_up_location_change_distance_limit" name="pick_up_location_change_distance_limit" placeholder="{{ __('pick_up_location_change_distance_limit') }}" type="text" class="form-control" value="{{array_key_exists('pick_up_location_change_distance_limit', $settings) ? $settings['pick_up_location_change_distance_limit'] : ''}}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="col-form-label"><b>{{ __('top_20_drivers_minimum_rating_limit') }}</b></label>
                                                <input id="top_20_drivers_minimum_rating_limit" name="top_20_drivers_minimum_rating_limit" placeholder="{{ __('top_20_drivers_minimum_rating_limit') }}" type="text" class="form-control" value="{{array_key_exists('top_20_drivers_minimum_rating_limit', $settings) ? $settings['top_20_drivers_minimum_rating_limit'] : ''}}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="col-form-label"><b>{{ __('waiting_grace_time_before_start_trip') }}</b></label>
                                                <input id="waiting_grace_time_before_start_trip" name="waiting_grace_time_before_start_trip" placeholder="{{ __('waiting_grace_time_before_start_trip') }}" type="text" class="form-control" value="{{array_key_exists('waiting_grace_time_before_start_trip', $settings) ? $settings['waiting_grace_time_before_start_trip'] : ''}}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="col-form-label"><b>{{ __('trip_period') }}</b></label>
                                                <input id="trip_period" name="trip_period" placeholder="{{ __('trip_period') }}" type="text" class="form-control" value="{{array_key_exists('trip_period', $settings) ? $settings['trip_period'] : ''}}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="col-form-label"><b>{{ __('reward_point_for_five_star_rating') }}</b></label>
                                                <input id="reward_point_for_five_star_rating" name="reward_point_for_five_star_rating" placeholder="{{ __('reward_point_for_five_star_rating') }}" type="text" class="form-control" value="{{array_key_exists('reward_point_for_five_star_rating', $settings) ? $settings['reward_point_for_five_star_rating'] : ''}}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="col-form-label"><b>{{ __('dispatch_create_request') }}</b></label>
                                                <select class="form-control" name="dispatch_create_request" id="dispatch_create_request" required>
                                                    <option value="1" {{(old('dispatch_create_request',array_key_exists('dispatch_create_request', $settings) ? $settings['dispatch_create_request'] : '') == 1 )?'selected':''}}>
                                                    {{ __('automatic') }}</option>
                                                    <option value="0" {{(old('dispatch_create_request',array_key_exists('dispatch_create_request', $settings) ? $settings['dispatch_create_request'] : '') == 0 )?'selected':''}}>
                                                    {{ __('manual') }} </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="col-form-label"><b>{{ __('cancel_button_enable_after_certain_minutes') }}</b></label>
                                                <input id="cancel_button_enable_after_certain_minutes" name="cancel_button_enable_after_certain_minutes" placeholder="{{ __('cancel_button_enable_after_certain_minutes') }}" type="text" class="form-control" value="{{array_key_exists('cancel_button_enable_after_certain_minutes', $settings) ? $settings['cancel_button_enable_after_certain_minutes'] : ''}}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="col-form-label"><b>{{ __('cancel_timer') }}</b></label>
                                                <input id="cancel_timer" name="cancel_timer" placeholder="{{ __('cancel_timer') }}" type="text" class="form-control" value="{{array_key_exists('cancel_timer', $settings) ? $settings['cancel_timer'] : ''}}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="col-form-label"><b>{{ __('arriving_meter') }}</b></label>
                                                <input id="arriving_meter" name="arriving_meter" placeholder="{{ __('arriving_meter') }}" type="text" class="form-control" value="{{array_key_exists('arriving_meter', $settings) ? $settings['arriving_meter'] : ''}}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="col-form-label"><b>{{ __('end_trip_enable') }}</b></label>
                                                <input id="end_trip_enable" name="end_trip_enable" placeholder="{{ __('end_trip_enable') }}" type="text" class="form-control" value="{{array_key_exists('end_trip_enable', $settings) ? $settings['end_trip_enable'] : ''}}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="col-form-label"><b>{{ __('arriving_meter_button_press') }}</b></label>
                                                <input id="arriving_meter_button_press" name="arriving_meter_button_press" placeholder="{{ __('arriving_meter_button_press') }}" type="text" class="form-control" value="{{array_key_exists('arriving_meter_button_press', $settings) ? $settings['arriving_meter_button_press'] : ''}}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="col-form-label"><b>{{ __('auto_cancel_timer') }}</b></label>
                                                <input id="auto_cancel_timer" name="auto_cancel_timer" placeholder="{{ __('auto_cancel_timer') }}" type="text" class="form-control" value="{{array_key_exists('auto_cancel_timer', $settings) ? $settings['auto_cancel_timer'] : ''}}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="col-form-label"><b>{{ __('waiting_time_speed') }}</b></label>
                                                <input id="waiting_time_speed" name="waiting_time_speed" placeholder="{{ __('waiting_time_speed') }}" type="text" class="form-control" value="{{array_key_exists('waiting_time_speed', $settings) ? $settings['waiting_time_speed'] : ''}}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="col-form-label"><b>{{ __('user_promotional_amount') }}</b></label>
                                                <input id="user_promotional_amount" name="user_promotional_amount" placeholder="{{ __('user_promotional_amount') }}" type="text" class="form-control" value="{{array_key_exists('user_promotional_amount', $settings) ? $settings['user_promotional_amount'] : ''}}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="col-form-label"><b>{{ __('auto_off_line_time') }}</b></label>
                                                <input id="auto_off_line_time" name="auto_off_line_time" placeholder="{{ __('auto_off_line_time') }}" type="text" class="form-control" value="{{array_key_exists('auto_off_line_time', $settings) ? $settings['auto_off_line_time'] : ''}}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="col-form-label"><b>{{ __('free_admin_commission_type') }}</b></label>
                                                <select class="form-control" name="free_admin_commission_type" id="free_admin_commission_type" required>
                                                    <option value="1" {{(old('free_admin_commission_type',array_key_exists('free_admin_commission_type', $settings) ? $settings['free_admin_commission_type'] : '') == 1 )?'selected':''}}>
                                                    {{ __('no_of_trips') }}</option>
                                                    <option value="0" {{(old('free_admin_commission_type',array_key_exists('free_admin_commission_type', $settings) ? $settings['free_admin_commission_type'] : '') == 0 )?'selected':''}}>
                                                    {{ __('no_of_months') }}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="col-form-label"><b>{{ __('free_admin_commission_value') }}</b></label>
                                                <input id="free_admin_commission_value" name="free_admin_commission_value" placeholder="{{ __('free_admin_commission_value') }}" type="text" class="form-control" value="{{array_key_exists('free_admin_commission_value', $settings) ? $settings['free_admin_commission_value'] : ''}}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="col-form-label"><b>{{ __('user_trip_bounes') }}</b></label>
                                                <input id="user_trip_bounes" name="user_trip_bounes" placeholder="{{ __('user_trip_bounes') }}" type="text" class="form-control" value="{{array_key_exists('user_trip_bounes', $settings) ? $settings['user_trip_bounes'] : ''}}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="col-form-label"><b>{{ __('driver_block_rate') }}</b></label>
                                                <input id="driver_block_rate" name="driver_block_rate" placeholder="{{ __('driver_block_rate') }}" type="text" class="form-control" value="{{array_key_exists('driver_block_rate', $settings) ? $settings['driver_block_rate'] : ''}}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="col-form-label"><b>{{ __('driver_block_wallet_balance') }}</b></label>
                                                <input id="driver_block_wallet_balance" name="driver_block_wallet_balance" placeholder="{{ __('driver_block_wallet_balance') }}" type="text" class="form-control" value="{{array_key_exists('driver_block_wallet_balance', $settings) ? $settings['driver_block_wallet_balance'] : ''}}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="col-form-label"><b>{{ __('driver_block_acceptance_ratio') }}</b></label>
                                                <input id="driver_block_acceptance_ratio" name="driver_block_acceptance_ratio" placeholder="{{ __('driver_block_acceptance_ratio') }}" type="text" class="form-control" value="{{array_key_exists('driver_block_acceptance_ratio', $settings) ? $settings['driver_block_acceptance_ratio'] : ''}}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="col-form-label"><b>{{ __('driver_block_trip_reject') }}</b></label>
                                                <input id="driver_block_trip_reject" name="driver_block_trip_reject" placeholder="{{ __('driver_block_trip_reject') }}" type="text" class="form-control" value="{{array_key_exists('driver_block_trip_reject', $settings) ? $settings['driver_block_trip_reject'] : ''}}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="col-form-label"><b>{{ __('dispute_timing') }}</b></label>
                                                <input id="dispute_timing" name="dispute_timing" placeholder="{{ __('dispute_timing') }}" type="text" class="form-control" value="{{array_key_exists('dispute_timing', $settings) ? $settings['dispute_timing'] : ''}}" required>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" id="trip_setting_saveBtn" class="btn bg-primary">{{ __('save-changes') }}</button>
                                </form>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="wallet">
                            <div class="card-body">
                                <form method="post" id="wallet_form" >
                                    @csrf
                                    <h5 class="card-title">{{ __('wallet') }}</h5>
                                    <div class="alert alert-danger alert-dismissible" id="walleterrorbox">
                                        <span id="errorContent"></span>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="col-form-label"><b>{{ __('wallet_min_amount_for_trip') }}</b></label>
                                                <input id="wallet_min_amount_for_trip" name="wallet_min_amount_for_trip" placeholder="{{ __('wallet_min_amount_for_trip') }}" type="text" class="form-control" value="{{array_key_exists('wallet_min_amount_for_trip', $settings) ? $settings['wallet_min_amount_for_trip'] : ''}}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="col-form-label"><b>{{ __('wallet_min_amount_for_trip_driver') }}</b></label>
                                                <input id="wallet_min_amount_for_trip_driver" name="wallet_min_amount_for_trip_driver" placeholder="{{ __('wallet_min_amount_for_trip_driver') }}" type="text" class="form-control" value="{{array_key_exists('wallet_min_amount_for_trip_driver', $settings) ? $settings['wallet_min_amount_for_trip_driver'] : ''}}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="col-form-label"><b>{{ __('wallet_max_amount_to_balance') }}</b></label>
                                                <input id="wallet_max_amount_to_balance" name="wallet_max_amount_to_balance" placeholder="{{ __('wallet_max_amount_to_balance') }}" type="text" class="form-control" value="{{array_key_exists('wallet_max_amount_to_balance', $settings) ? $settings['wallet_max_amount_to_balance'] : ''}}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="col-form-label"><b>{{ __('wallet_min_amount_to_add') }}</b></label>
                                                <input id="wallet_min_amount_to_add" name="wallet_min_amount_to_add" placeholder="{{ __('wallet_min_amount_to_add') }}" type="text" class="form-control" value="{{array_key_exists('wallet_min_amount_to_add', $settings) ? $settings['wallet_min_amount_to_add'] : ''}}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="col-form-label"><b>{{ __('wallet_amount_to_alert_driver') }}</b></label>
                                                <input id="wallet_amount_to_alert_driver" name="wallet_amount_to_alert_driver" placeholder="{{ __('wallet_amount_to_alert_driver') }}" type="text" class="form-control" value="{{array_key_exists('wallet_amount_to_alert_driver', $settings) ? $settings['wallet_amount_to_alert_driver'] : ''}}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="col-form-label"><b>{{ __('wallet_negative_amount_to_alert_driver') }}</b></label>
                                                <input id="wallet_negative_amount_to_alert_driver" name="wallet_negative_amount_to_alert_driver" placeholder="{{ __('wallet_negative_amount_to_alert_driver') }}" type="text" class="form-control" value="{{array_key_exists('wallet_negative_amount_to_alert_driver', $settings) ? $settings['wallet_negative_amount_to_alert_driver'] : ''}}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="col-form-label"><b>{{ __('wallet_driver_refernce_amount') }}</b></label>
                                                <input id="wallet_driver_refernce_amount" name="wallet_driver_refernce_amount" placeholder="{{ __('wallet_driver_refernce_amount') }}" type="text" class="form-control" value="{{array_key_exists('wallet_driver_refernce_amount', $settings) ? $settings['wallet_driver_refernce_amount'] : ''}}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="col-form-label"><b>{{ __('wallet_user_refernce_amount') }}</b></label>
                                                <input id="wallet_user_refernce_amount" name="wallet_user_refernce_amount" placeholder="{{ __('wallet_user_refernce_amount') }}" type="text" class="form-control" value="{{array_key_exists('wallet_user_refernce_amount', $settings) ? $settings['wallet_user_refernce_amount'] : ''}}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="col-form-label"><b>{{ __('referan_amount_trip_count') }}</b></label>
                                                <input id="referan_amount_trip_count" name="referan_amount_trip_count" placeholder="{{ __('referan_amount_trip_count') }}" type="text" class="form-control" value="{{array_key_exists('referan_amount_trip_count', $settings) ? $settings['referan_amount_trip_count'] : ''}}" required>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" id="wallet_saveBtn" class="btn bg-primary">{{ __('save-changes') }}</button>
                                </form>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="installation_setting">
                            <div class="card-body">
                                <form method="post" id="installation_setting_form" >
                                    @csrf
                                    <h5 class="card-title">{{ __('installation_setting') }}</h5>
                                    <div class="alert alert-danger alert-dismissible" id="installation_setting_errorbox">
                                        <span id="errorContent"></span>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="col-form-label"><b>{{ __('google_browser_key') }}</b></label>
                                                <input id="google_browser_key" name="google_browser_key" placeholder="{{ __('google_browser_key') }}" type="text" class="form-control" value="{{array_key_exists('google_browser_key', $settings) ? $settings['google_browser_key'] : ''}}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="col-form-label"><b>{{ __('twillo_account_sid') }}</b></label>
                                                <input id="twillo_account_sid" name="twillo_account_sid" placeholder="{{ __('twillo_account_sid') }}" type="text" class="form-control" value="{{array_key_exists('twillo_account_sid', $settings) ? $settings['twillo_account_sid'] : ''}}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="col-form-label"><b>{{ __('twillo_auth_token') }}</b></label>
                                                <input id="twillo_auth_token" name="twillo_auth_token" placeholder="{{ __('twillo_auth_token') }}" type="text" class="form-control" value="{{array_key_exists('twillo_auth_token', $settings) ? $settings['twillo_auth_token'] : ''}}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="col-form-label"><b>{{ __('twillo_number') }}</b></label>
                                                <input id="twillo_number" name="twillo_number" placeholder="{{ __('twillo_number') }}" type="text" class="form-control" value="{{array_key_exists('twillo_number', $settings) ? $settings['twillo_number'] : ''}}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="col-form-label"><b>{{ __('btree_environment') }}</b></label>
                                                <input id="btree_environment" name="btree_environment" placeholder="{{ __('btree_environment') }}" type="text" class="form-control" value="{{array_key_exists('btree_environment', $settings) ? $settings['btree_environment'] : ''}}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="col-form-label"><b>{{ __('btree_merchant_id') }}</b></label>
                                                <input id="btree_merchant_id" name="btree_merchant_id" placeholder="{{ __('btree_merchant_id') }}" type="text" class="form-control" value="{{array_key_exists('btree_merchant_id', $settings) ? $settings['btree_merchant_id'] : ''}}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="col-form-label"><b>{{ __('btree_public_key') }}</b></label>
                                                <input id="btree_public_key" name="btree_public_key" placeholder="{{ __('btree_public_key') }}" type="text" class="form-control" value="{{array_key_exists('btree_public_key', $settings) ? $settings['btree_public_key'] : ''}}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="col-form-label"><b>{{ __('btree_private_key') }}</b></label>
                                                <input id="btree_private_key" name="btree_private_key" placeholder="{{ __('btree_private_key') }}" type="text" class="form-control" value="{{array_key_exists('btree_private_key', $settings) ? $settings['btree_private_key'] : ''}}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="col-form-label"><b>{{ __('btree_master_merchant') }}</b></label>
                                                <input id="btree_master_merchant" name="btree_master_merchant" placeholder="{{ __('btree_master_merchant') }}" type="text" class="form-control" value="{{array_key_exists('btree_master_merchant', $settings) ? $settings['btree_master_merchant'] : ''}}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="col-form-label"><b>{{ __('btree_default_merchant') }}</b></label>
                                                <input id="btree_default_merchant" name="btree_default_merchant" placeholder="{{ __('btree_default_merchant') }}" type="text" class="form-control" value="{{array_key_exists('btree_default_merchant', $settings) ? $settings['btree_default_merchant'] : ''}}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="col-form-label"><b>{{ __('stripe_public_key') }}</b></label>
                                                <input id="stripe_public_key" name="stripe_public_key" placeholder="{{ __('stripe_public_key') }}" type="text" class="form-control" value="{{array_key_exists('stripe_public_key', $settings) ? $settings['stripe_public_key'] : ''}}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="col-form-label"><b>{{ __('stripe_private_key') }}</b></label>
                                                <input id="stripe_private_key" name="stripe_private_key" placeholder="{{ __('stripe_private_key') }}" type="text" class="form-control" value="{{array_key_exists('stripe_private_key', $settings) ? $settings['stripe_private_key'] : ''}}" required>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" id="installation_setting_saveBtn" class="btn bg-primary">{{ __('save-changes') }}</button>
                                </form>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="general">
                            <div class="card-body">
                                <form method="post" id="general_form" >
                                    @csrf
                                    <h5 class="card-title">{{ __('general') }}</h5>
                                    <div class="alert alert-danger alert-dismissible" id="general_errorbox">
                                        <span id="errorContent"></span>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="col-form-label"><b>{{ __('application_name') }}</b></label>
                                                <input id="application_name" name="application_name" placeholder="{{ __('application_name') }}" type="text" class="form-control" value="{{array_key_exists('application_name', $settings) ? $settings['application_name'] : ''}}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="col-form-label"><b>{{ __('logo') }}</b></label>
                                                <input id="logo" name="logo" placeholder="{{ __('logo') }}" type="file" class="form-control" value="" required>
                                                <img src="{{array_key_exists('logo', $settings) ? $settings['logo'] : ''}}" height="40px" width="auto" alt="" id="view_image" />
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="col-form-label"><b>{{ __('paginate') }}</b></label>
                                                <input id="paginate" name="paginate" placeholder="{{ __('paginate') }}" type="text" class="form-control" value="{{array_key_exists('paginate', $settings) ? $settings['paginate'] : ''}}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="col-form-label"><b>{{ __('latitude') }}</b></label>
                                                <input id="latitude" name="latitude" placeholder="{{ __('latitude') }}" type="text" class="form-control" value="{{array_key_exists('latitude', $settings) ? $settings['latitude'] : ''}}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="col-form-label"><b>{{ __('longitude') }}</b></label>
                                                <input id="longitude" name="longitude" placeholder="{{ __('longitude') }}" type="text" class="form-control" value="{{array_key_exists('longitude', $settings) ? $settings['longitude'] : ''}}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="col-form-label"><b>{{ __('head_office_number') }}</b></label>
                                                <input id="head_office_number" name="head_office_number" placeholder="{{ __('head_office_number') }}" type="text" class="form-control" value="{{array_key_exists('head_office_number', $settings) ? $settings['head_office_number'] : ''}}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="col-form-label"><b>{{ __('customer_care_number') }}</b></label>
                                                <input id="customer_care_number" name="customer_care_number" placeholder="{{ __('customer_care_number') }}" type="text" class="form-control" value="{{array_key_exists('customer_care_number', $settings) ? $settings['customer_care_number'] : ''}}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="col-form-label"><b>{{ __('help_email') }}</b></label>
                                                <input id="help_email" name="help_email" placeholder="{{ __('help_email') }}" type="email" class="form-control" value="{{array_key_exists('help_email', $settings) ? $settings['help_email'] : ''}}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="col-form-label"><b>{{ __('time_zone') }}</b></label>
                                                <select class="form-control" name="time_zone" id="time_zone" required>
                                                    <option value="">Select Time Zone</option>
                                                    @foreach($time_zone as $value)
                                                        <option value="{{$value->time_zone}}" {{(old('time_zone',array_key_exists('time_zone', $settings) ? $settings['time_zone'] : '') == $value->time_zone )?'selected':''}}>
                                                        {{$value->time_zone}} ({{$value->gmt_offset}})</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="col-form-label"><b>{{ __('language') }}</b></label>
                                                <select class="form-control" name="language" id="language" required>
                                                    <option value="">Select {{ __('language') }}</option>
                                                    @foreach($languages as $values)
                                                    <option value="{{$values->code}}" {{(old('language',array_key_exists('language', $settings) ? $settings['language'] : '') == $values->code )?'selected':''}}>
                                                    {{$values->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" id="general_saveBtn" class="btn bg-primary">{{ __('save-changes') }}</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
     -->
</div>
</div>
</div>
<script>
    $('#errorbox').hide();
    $('#walleterrorbox').hide();
    $('#installation_setting_errorbox').hide();
    $('#general_errorbox').hide();

    $('#trip_setting_saveBtn').click(function (e) {
        e.preventDefault();
        $(this).html("{{ __('sending') }}");
        $('#errorbox').hide();
        $.ajax({
            data: $('#trip_setting_form').serialize(),
            url: "{{ route('settingsSave') }}",
            type: "POST",
            dataType: 'json',
            success: function (data) {
                swal({
                    title: "{{ __('data-added') }}",
                    text: "{{ __('data-added-successfully') }}",
                    icon: "success",
                }).then((value) => {        
                    $('#trip_setting_saveBtn').html("{{ __('save-changes') }}");            
                    // $("#reloadDiv").load("{{ route('faq-management') }}");
                });                
            },
            error: function (xhr, ajaxOptions, thrownError) {
                $('#errorbox').show();
                var err = eval("(" + xhr.responseText + ")");
                console.log(err.errors);
                $('#errorContent').html('');
                $.each(err.errors, function(key, value) {
                    $('#errorContent').append('<strong><li>'+value+'</li></strong>');
                });
                $('#saveBtn').html("{{ __('save-changes') }}");
            }
        });
    });
    
    $('#wallet_saveBtn').click(function (e) {
        e.preventDefault();
        $(this).html("{{ __('sending') }}");
        $('#walleterrorbox').hide();
        $.ajax({
            data: $('#wallet_form').serialize(),
            url: "{{ route('settingsSave') }}",
            type: "POST",
            dataType: 'json',
            success: function (data) {
                swal({
                    title: "{{ __('data-added') }}",
                    text: "{{ __('data-added-successfully') }}",
                    icon: "success",
                }).then((value) => {        
                    $("#wallet_saveBtn").html("{{ __('save-changes') }}");            
                    // $("#reloadDiv").load("{{ route('faq-management') }}");
                });                
            },
            error: function (xhr, ajaxOptions, thrownError) {
                $('#walleterrorbox').show();
                var err = eval("(" + xhr.responseText + ")");
                console.log(err.errors);
                $('#errorContent').html('');
                $.each(err.errors, function(key, value) {
                    $('#errorContent').append('<strong><li>'+value+'</li></strong>');
                });
                $('#wallet_saveBtn').html("{{ __('save-changes') }}");
            }
        });
    });
    
    $('#installation_setting_saveBtn').click(function (e) {
        e.preventDefault();
        $(this).html("{{ __('sending') }}");
        $('#installation_setting_errorbox').hide();
        $.ajax({
            data: $('#installation_setting_form').serialize(),
            url: "{{ route('settingsSave') }}",
            type: "POST",
            dataType: 'json',
            success: function (data) {
                swal({
                    title: "{{ __('data-added') }}",
                    text: "{{ __('data-added-successfully') }}",
                    icon: "success",
                }).then((value) => {        
                    $("#installation_setting_saveBtn").html("{{ __('save-changes') }}");            
                    // $("#reloadDiv").load("{{ route('faq-management') }}");
                });                
            },
            error: function (xhr, ajaxOptions, thrownError) {
                $('#installation_setting_errorbox').show();
                var err = eval("(" + xhr.responseText + ")");
                console.log(err.errors);
                $('#errorContent').html('');
                $.each(err.errors, function(key, value) {
                    $('#errorContent').append('<strong><li>'+value+'</li></strong>');
                });
                $('#installation_setting_saveBtn').html("{{ __('save-changes') }}");
            }
        });
    });

    $('#logo').change(function(){
          let reader = new FileReader();
          reader.onload = (e) => { 
            $("#view_image").attr('src',e.target.result);
          }
          reader.readAsDataURL(this.files[0]); 
    });

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#general_saveBtn').click(function (e) {
        e.preventDefault();
        var formData = new FormData();
        formData.append('logo',$('#logo').prop('files').length > 0 ? $('#logo').prop('files')[0] : '');
        formData.append('application_name',$('#application_name').val());
        // formData.append('paginate',$('#paginate').val());
        // formData.append('latitude',$('#latitude').val());
        // formData.append('longitude',$('#longitude').val());
        formData.append('head_office_number',$('#head_office_number').val());
        formData.append('customer_care_number',$('#customer_care_number').val());
        formData.append('help_email',$('#help_email').val());
        formData.append('time_zone',$('#time_zone').val());
        formData.append('language',$('#language').val());
        $(this).html("{{ __('sending') }}");
        var btnVal = $(this).val();
        $('#general_errorbox').hide();
        $.ajax({
            data: formData,
            url: "{{ route('settingsSave') }}",
            type: "POST",
            dataType: 'json',
            contentType : false,
            processData: false,
            success: function (data) {
                swal({
                    title: "{{ __('data-added') }}",
                    text: "{{ __('data-added-successfully') }}",
                    icon: "success",
                }).then((value) => {        
                    $("#general_saveBtn").html("{{ __('save-changes') }}");   
                    $('#logo').val('');                      
                    // $("#reloadDiv").load("{{ route('notification') }}");
                });                
            },
            error: function (xhr, ajaxOptions, thrownError) {
                $('#general_errorbox').show();
                var err = eval("(" + xhr.responseText + ")");
                console.log(err);
                $('#errorContent').html('');
                $.each(err.errors, function(key, value) {
                    $('#errorContent').append('<strong><li>'+value+'</li></strong>');
                });
                $('#general_saveBtn').html("{{ __('save-changes') }}");
            }
        });
    });

</script>

@endsection
