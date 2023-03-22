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

<div class="content">
                        
    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title">{{ __('setting') }}</h5>
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
            <ul class="nav navbar-nav ">
                @if(auth()->user()->can('trip-setting'))
                <li class="nav-item">
                    <a href="#trip_setting"  class="navbar-nav-link active" data-toggle="tab">
                        <i class="icon-car2 mr-2"></i>
                        {{ __('trip_setting') }}
                    </a>
                </li>
                @endif
                @if(auth()->user()->can('wallet'))
                <li class="nav-item">
                    <a href="#wallet" class="navbar-nav-link" data-toggle="tab">
                        <i class="icon-wallet mr-2"></i>
                        {{ __('wallet') }}
                       
                    </a>
                </li>
                @endif
                @if(auth()->user()->can('installation-setting'))
                <li class="nav-item">
                    <a href="#installation_setting" class="navbar-nav-link" data-toggle="tab">
                        <i class="icon-hash mr-2"></i>
                        {{ __('installation_setting') }}
                    </a>
                </li>
                @endif
                @if(auth()->user()->can('general'))
                <li class="nav-item">
                    <a href="#general" class="navbar-nav-link" data-toggle="tab">
                        <i class="icon-cog3 mr-2"></i>
                        {{ __('general') }}
                    </a>
                </li>
                @endif
            </ul>
        </div>
    </div>
    <div class="content">

        <!-- Simple lists -->
        <div class="row">
            <div class="col-md-12">

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
                                                    <label class="col-form-label col-sm-3"><b>{{ __('driver_block_wallet_balance') }}</b></label>
                                                    <div class="col-sm-9">
                                                    <input id="driver_block_wallet_balance" name="driver_block_wallet_balance" placeholder="{{ __('driver_block_wallet_balance') }}" type="text" class="form-control" value="{{array_key_exists('driver_block_wallet_balance', $settings) ? $settings['driver_block_wallet_balance'] : ''}}" required>
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-form-label col-sm-3"><b>{{ __('dispute_timing') }}</b></label>
                                                    <div class="col-sm-9">
                                                    <input id="dispute_timing" name="dispute_timing" placeholder="{{ __('dispute_timing') }}" type="text" class="form-control" value="{{array_key_exists('dispute_timing', $settings) ? $settings['dispute_timing'] : ''}}" required>
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-form-label col-sm-3"><b>{{ __('auto_araive_radius_pickup') }}</b></label>
                                                    <div class="col-sm-9">
                                                    <input id="auto_araive_radius_pickup" name="auto_araive_radius_pickup" placeholder="{{ __('auto_araive_radius_pickup') }}" type="text" class="form-control" value="{{array_key_exists('auto_araive_radius_pickup', $settings) ? $settings['auto_araive_radius_pickup'] : ''}}" required>
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-form-label col-sm-3"><b>{{ __('auto_araive_radius_drop') }}</b></label>
                                                    <div class="col-sm-9">
                                                    <input id="auto_araive_radius_drop" name="auto_araive_radius_drop" placeholder="{{ __('auto_araive_radius_drop') }}" type="text" class="form-control" value="{{array_key_exists('auto_araive_radius_drop', $settings) ? $settings['auto_araive_radius_drop'] : ''}}" required>
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-form-label col-sm-3"><b>{{ __('cancel_fees_distance') }}</b></label>
                                                    <div class="col-sm-9">
                                                    <input id="cancel_fees_distance" name="cancel_fees_distance" placeholder="{{ __('cancel_fees_distance') }}" type="text" class="form-control" value="{{array_key_exists('cancel_fees_distance', $settings) ? $settings['cancel_fees_distance'] : ''}}" required>
                                                    </div>
                                                </div>
                                                <button type="submit" id="trip_setting_saveBtn" class="btn bg-primary ">{{ __('save-changes') }}</button>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="wallet">
                                        <div class="card-body">
                                            <form method="post" id="wallet_form" >
                                                @csrf
                                                <h5 class="card-title"><b>{{ __('wallet') }}</b></h5>
                                                <div class="alert alert-danger alert-dismissible" id="walleterrorbox">
                                                    <span id="errorContent"></span>
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

                                                <div class="form-group row">
                                                    <label class="col-form-label col-sm-3"><b>{{ __('user_user_referal_amount') }}</b></label>
                                                    <div class="col-sm-9">
                                                    <input id="user_user_referal_amount" name="user_user_referal_amount" placeholder="{{ __('user_user_referal_amount') }}" type="text" class="form-control" value="{{array_key_exists('user_user_referal_amount', $settings) ? $settings['user_user_referal_amount'] : ''}}" required>
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-form-label col-sm-3"><b>{{ __('user_user_referal_trip') }}</b></label>
                                                    <div class="col-sm-9">
                                                    <input id="user_user_referal_trip" name="user_user_referal_trip" placeholder="{{ __('user_user_referal_trip') }}" type="text" class="form-control" value="{{array_key_exists('user_user_referal_trip', $settings) ? $settings['user_user_referal_trip'] : ''}}" required>
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-form-label col-sm-3"><b>{{ __('user_driver_referal_amount') }}</b></label>
                                                    <div class="col-sm-9">
                                                    <input id="user_driver_referal_amount" name="user_driver_referal_amount" placeholder="{{ __('user_driver_referal_amount') }}" type="text" class="form-control" value="{{array_key_exists('user_driver_referal_amount', $settings) ? $settings['user_driver_referal_amount'] : ''}}" required>
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-form-label col-sm-3"><b>{{ __('user_driver_referal_trip') }}</b></label>
                                                    <div class="col-sm-9">
                                                    <input id="user_driver_referal_trip" name="user_driver_referal_trip" placeholder="{{ __('user_driver_referal_trip') }}" type="text" class="form-control" value="{{array_key_exists('user_driver_referal_trip', $settings) ? $settings['user_driver_referal_trip'] : ''}}" required>
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-form-label col-sm-3"><b>{{ __('driver_driver_referal_amount') }}</b></label>
                                                    <div class="col-sm-9">
                                                    <input id="driver_driver_referal_amount" name="driver_driver_referal_amount" placeholder="{{ __('driver_driver_referal_amount') }}" type="text" class="form-control" value="{{array_key_exists('driver_driver_referal_amount', $settings) ? $settings['driver_driver_referal_amount'] : ''}}" required>
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-form-label col-sm-3"><b>{{ __('driver_driver_referal_trip') }}</b></label>
                                                    <div class="col-sm-9">
                                                    <input id="driver_driver_referal_trip" name="driver_driver_referal_trip" placeholder="{{ __('driver_driver_referal_trip') }}" type="text" class="form-control" value="{{array_key_exists('driver_driver_referal_trip', $settings) ? $settings['driver_driver_referal_trip'] : ''}}" required>
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-form-label col-sm-3"><b>{{ __('driver_user_referal_amount') }}</b></label>
                                                    <div class="col-sm-9">
                                                    <input id="driver_user_referal_amount" name="driver_user_referal_amount" placeholder="{{ __('driver_user_referal_amount') }}" type="text" class="form-control" value="{{array_key_exists('driver_user_referal_amount', $settings) ? $settings['driver_user_referal_amount'] : ''}}" required>
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-form-label col-sm-3"><b>{{ __('driver_user_referal_trip') }}</b></label>
                                                    <div class="col-sm-9">
                                                    <input id="driver_user_referal_trip" name="driver_user_referal_trip" placeholder="{{ __('driver_user_referal_trip') }}" type="text" class="form-control" value="{{array_key_exists('driver_user_referal_trip', $settings) ? $settings['driver_user_referal_trip'] : ''}}" required>
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-form-label col-sm-3"><b>{{ __('referal_one_time') }}</b></label>
                                                    <div class="col-sm-9">
                                                        <div class="form-check form-check-inline">
                                                            <label class="form-check-label">
                                                                <input type="radio" name="referal_one_time" class="form-input-styled required" data-fouc value="true" @if(array_key_exists('referal_one_time', $settings) && $settings['referal_one_time'] == 'true') checked @endif>
                                                                True
                                                            </label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <label class="form-check-label">
                                                                <input type="radio" name="referal_one_time" class="form-input-styled required" data-fouc value="false" @if(array_key_exists('referal_one_time', $settings) && $settings['referal_one_time'] == 'false') checked @endif>
                                                                False
                                                            </label>
                                                        </div>
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
                                                    <label class="col-form-label col-sm-3"><b>{{ __('fcm_key') }}</b></label>
                                                    <div class="col-sm-9">
                                                    <input id="fcm_key" name="fcm_key" placeholder="{{ __('fcm_key') }}" type="text" class="form-control" value="{{array_key_exists('fcm_key', $settings) ? $settings['fcm_key'] : ''}}" required>
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-form-label col-sm-3"><b>{{ __('google_map_key') }}</b></label>
                                                    <div class="col-sm-9">
                                                    <input id="google_map_key" name="google_map_key" placeholder="{{ __('google_map_key') }}" type="text" class="form-control" value="{{array_key_exists('google_map_key', $settings) ? $settings['google_map_key'] : ''}}" required>
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-form-label col-sm-3"><b>{{ __('geo_coder') }}</b></label>
                                                    <div class="col-sm-9">
                                                    <input id="geo_coder" name="geo_coder" placeholder="{{ __('geo_coder') }}" type="text" class="form-control" value="{{array_key_exists('geo_coder', $settings) ? $settings['geo_coder'] : ''}}" required>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-form-label col-sm-3"><b>{{ __('distance_matrix') }}</b></label>
                                                    <div class="col-sm-9">
                                                    <input id="distance_matrix" name="distance_matrix" placeholder="{{ __('distance_matrix') }}" type="text" class="form-control" value="{{array_key_exists('distance_matrix', $settings) ? $settings['distance_matrix'] : ''}}" required>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-form-label col-sm-3"><b>{{ __('google_map_token') }}</b></label>
                                                    <div class="col-sm-9">
                                                    <input id="google_map_token" name="google_map_token" placeholder="{{ __('google_map_token') }}" type="text" class="form-control" value="{{array_key_exists('google_map_token', $settings) ? $settings['google_map_token'] : ''}}" required>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-form-label col-sm-3"><b>{{ __('firebase_api_key') }}</b></label>
                                                    <div class="col-sm-9">
                                                    <input id="firebase_api_key" name="firebase_api_key" placeholder="{{ __('firebase_api_key') }}" type="text" class="form-control" value="{{array_key_exists('firebase_api_key', $settings) ? $settings['firebase_api_key'] : ''}}" required>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-form-label col-sm-3"><b>{{ __('firebase_auth_domain') }}</b></label>
                                                    <div class="col-sm-9">
                                                    <input id="firebase_auth_domain" name="firebase_auth_domain" placeholder="{{ __('firebase_auth_domain') }}" type="text" class="form-control" value="{{array_key_exists('firebase_auth_domain', $settings) ? $settings['firebase_auth_domain'] : ''}}" required>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-form-label col-sm-3"><b>{{ __('firebase_database_url') }}</b></label>
                                                    <div class="col-sm-9">
                                                    <input id="firebase_database_url" name="firebase_database_url" placeholder="{{ __('firebase_database_url') }}" type="text" class="form-control" value="{{array_key_exists('firebase_database_url', $settings) ? $settings['firebase_database_url'] : ''}}" required>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-form-label col-sm-3"><b>{{ __('firebase_project_id') }}</b></label>
                                                    <div class="col-sm-9">
                                                    <input id="firebase_project_id" name="firebase_project_id" placeholder="{{ __('firebase_project_id') }}" type="text" class="form-control" value="{{array_key_exists('firebase_project_id', $settings) ? $settings['firebase_project_id'] : ''}}" required>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-form-label col-sm-3"><b>{{ __('firebase_storage_bucket') }}</b></label>
                                                    <div class="col-sm-9">
                                                    <input id="firebase_storage_bucket" name="firebase_storage_bucket" placeholder="{{ __('firebase_storage_bucket') }}" type="text" class="form-control" value="{{array_key_exists('firebase_storage_bucket', $settings) ? $settings['firebase_storage_bucket'] : ''}}" required>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-form-label col-sm-3"><b>{{ __('firebase_messaging_sender_id') }}</b></label>
                                                    <div class="col-sm-9">
                                                    <input id="firebase_messaging_sender_id" name="firebase_messaging_sender_id" placeholder="{{ __('firebase_messaging_sender_id') }}" type="text" class="form-control" value="{{array_key_exists('firebase_messaging_sender_id', $settings) ? $settings['firebase_messaging_sender_id'] : ''}}" required>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-form-label col-sm-3"><b>{{ __('firebase_app_id') }}</b></label>
                                                    <div class="col-sm-9">
                                                    <input id="firebase_app_id" name="firebase_app_id" placeholder="{{ __('firebase_app_id') }}" type="text" class="form-control" value="{{array_key_exists('firebase_app_id', $settings) ? $settings['firebase_app_id'] : ''}}" required>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label class="col-form-label col-sm-3"><b>{{ __('firebase_measurement_id') }}</b></label>
                                                    <div class="col-sm-9">
                                                    <input id="firebase_measurement_id" name="firebase_measurement_id" placeholder="{{ __('firebase_measurement_id') }}" type="text" class="form-control" value="{{array_key_exists('firebase_measurement_id', $settings) ? $settings['firebase_measurement_id'] : ''}}" required>
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
                                                    <label class="col-form-label col-sm-3"><b>{{ __('mini_logo') }}</b></label>
                                                    <div class="col-sm-9">
                                                        <input id="mini_logo" name="mini_logo" placeholder="{{ __('mini_logo') }}" type="file" class="form-control" value="" required>
                                                        <img src="{{array_key_exists('mini_logo', $settings) ? $settings['mini_logo'] : ''}}" height="40px" width="auto" alt="" id="mini_view_image" />
                                                    </div>
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

                                                <div class="form-group row">
                                                    <label class="col-form-label col-sm-3"><b>{{ __('passenger_upload_images') }}</b></label>
                                                    <div class="col-sm-9">
                                                    <input id="passenger_upload_images" name="passenger_upload_images" placeholder="{{ __('passenger_upload_images') }}" type="email" class="form-control" value="{{array_key_exists('passenger_upload_images', $settings) ? $settings['passenger_upload_images'] : ''}}" required>
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-form-label col-sm-3"><b>{{ __('start_night_time') }}</b></label>
                                                    <div class="col-sm-9">
                                                    <input id="start_night_time" name="start_night_time" placeholder="{{ __('Fill out 24hrs format  like HH:MM:SS ') }}" type="email" class="form-control" value="{{array_key_exists('start_night_time', $settings) ? $settings['start_night_time'] : ''}}" required>
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <label class="col-form-label col-sm-3"><b>{{ __('end_night_time') }}</b></label>
                                                    <div class="col-sm-9">
                                                    <input id="end_night_time" name="end_night_time" placeholder="{{ __('Fill out 24hrs format  like HH:MM:SS ') }}" type="email" class="form-control" value="{{array_key_exists('end_night_time', $settings) ? $settings['end_night_time'] : ''}}" required>
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

    $('#mini_logo').change(function(){
        let reader = new FileReader();
        reader.onload = (e) => { 
            $("#mini_view_image").attr('src',e.target.result);
        }
        reader.readAsDataURL(this.files[0]); 
    });

    $("#services_tax_igst").on('keyup',function(){
        var value = $(this).val();
        value = value/2;
        $("#services_tax_cgst").val(value);
        $("#services_tax_sgst").val(value);
    })

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#general_saveBtn').click(function (e) {
        e.preventDefault();
        var formData = new FormData();
        formData.append('logo',$('#logo').prop('files').length > 0 ? $('#logo').prop('files')[0] : '');
        formData.append('mini_logo',$('#mini_logo').prop('files').length > 0 ? $('#mini_logo').prop('files')[0] : '');
        formData.append('application_name',$('#application_name').val());
        formData.append('head_office_number',$('#head_office_number').val());
        formData.append('customer_care_number',$('#customer_care_number').val());
        formData.append('help_email',$('#help_email').val());
        formData.append('time_zone',$('#time_zone').val());
        formData.append('language',$('#language').val());
        formData.append('passenger_upload_images',$('#passenger_upload_images').val());
        formData.append('start_night_time',$('#start_night_time').val());
        formData.append('end_night_time',$('#end_night_time').val());
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
