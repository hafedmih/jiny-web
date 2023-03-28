@extends('layouts.dispatcher-layout')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<style>
    .select2-container{
        width : 100% !important;
    }
    .select2-container--default .select2-search--dropdown .select2-search__field {
        padding-left: 40px;
    }
    .autocompletes{
        z-index: 9999;
        position: absolute;
        background: #fff;
        border: solid 1px #000;
        height:auto;
        max-height: 250px;
        margin-top: -18px;
        overflow: hidden;
        overflow-y: scroll;
    }
    .fieldset{
        height:350px;
        overflow: hidden;
        overflow-y: scroll;
    }
    #map{
        height: 550px;
    }
    .autocompletes ul{
        padding: 0px;
    }
    .actions{
        padding: 10px;
        padding-top: 0;
    }
    .actions ul li a{
        padding: 3px 7px;
    }
    .autocompletes ul li{
        list-style: none;
        padding: 10px;
        font-size: 15px;
    }
    #legend {
        font-family: Arial, sans-serif;
        background: #fff;/*transparent;*/
        padding: 5px;
        margin: 5px;
        border: 3px solid #000;
        width:140px;
        font-size: 10px;
    }
    #legend h5 {
        margin-top: 0;
        font-size: 15px;
        display: flex;
        justify-content: space-between;
    }
    #legend img {
        vertical-align: middle;
        width:45px;
        height:30px;
    }
    
    #type_count,#drivers_list {
        font-family: Arial, sans-serif;
        background: #fff;/*transparent;*/
        padding: 5px;
        margin: 5px;
        border: 3px solid #000;
        width: 320px;
        height: 76px;
        font-size: 10px;
    }
    #type_count h5{
        margin-top: 0;
        text-align: center;
        font-size: 15px;
    }
    .rental_price lable{
        color: #000;
        font-weight: bold;
        background: #f8bc35;
        padding: 10px;
    }
    .count_list{
        float: left;
        padding: 0px 10px 0px 0px;
        font-size: 13px;
    }
    .autocompletes ul li:hover{
        cursor: pointer;
        background: #52abeb;
        color: #fff;
    }
    .list-group-item{
        line-height: 1;
    }
    .clickCheck{
        display:none;
    }
    .alert{
        z-index: 99;
        width: 93%;
        margin-left: 20px;
        position: fixed;
    }
    /* .card-form{
        z-index: 9;
        margin: 10px 0px 0px 175px;
        position: absolute;
    } */
    .card-title .nav-item a{
        padding: 3px 42px;
    }
    .header-elements-inline{
        padding: 5px 10px;
        justify-content: center;
    }
    .mapboxgl-ctrl-top-left{
        display: none;
    }
    .btn-yellow{
        color: #000;
        background-color: #ffdd34;
    }
    .form-group {
        margin: 0px;
        margin-bottom:5px;
    }
    .badge-danger{
        cursor: pointer;
    }
    
    .form-group label {
        margin: 0px;
    }
    .view-history{
        padding: 10px 8px !important;
    }
    .list-group-item-action{
        padding: 5px 10px;
    }
    .type_location_input{
        padding-right: 2.9rem !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: normal;
    }
    @media only screen and (max-width: 700px) {
        .card-form{
            margin: 0px;
        }
    }
    .media-list{
        width: 100%;
        height: 300px;
        overflow: hidden;
        overflow-y: scroll;
    }
    ::-webkit-scrollbar {
        width: 5px;
        height: 5px;
    }
    ::-webkit-scrollbar-thumb {
        background: #ffd60c; 
        border-radius: 10px;
    }

    .customMarker {
    position:absolute;
    cursor:pointer;
    text-align: center;
    background:#424242;
    width: 72px;
    height: 92px;
    margin-left: -33px;
    margin-top: -120px;
    border-radius:10px;
    padding:0px;
    }
    .customMarker:after {
        content:"";
        position: absolute;
        bottom: -10px;
        left: 28px;
        border-width: 10px 10px 0;
        border-style: solid;
        border-color: #424242 transparent;
        display: block;
        width: 0;
    }
    .customMarker img {
        width: 45px;
        height: 45px;
        margin: 3px 13px;
        border-radius: 10px;
    }
    .customMarker lable{
        color: #fff;
        font-size: 11px;
    }
</style>
<!-- <div class="page-header page-header-light">
    <div class="page-header-content header-elements-md-inline">
        <div class="page-title d-flex">
            <h4> <span class="font-weight-semibold">{{ __('dispatcher') }} </span> </h4>
            <a href="#" class="header-elements-toggle text-default d-md-none"><i class="icon-more"></i></a>
        </div>
    </div>

    
</div> -->
<div class="content row">
    <div class="alert bg-warning text-white alert-styled-left alert-dismissible">
		<!-- <button type="button" class="close" data-dismiss="alert"><span>×</span></button> -->
		<span class="font-weight-semibold">Loading!</span> Searching for driver. Please wait
	</div>
    <div class="alert bg-danger text-white alert-styled-left alert-dismissible">
		<!-- <button type="button" class="close" data-dismiss="alert"><span>×</span></button> -->
		<span class="font-weight-semibold">Sorry!</span> <span id="error_message"></span>.
	</div>
    <input type="hidden" id="zone" value="{{$zone ? $zone->map_cooder : ''}}" />
    <div class="card-form card-collapsed animated bounceInDown col-md-4" id="card_local_trip">
        <div class="card-header bg-white header-elements-inline">
            <!-- <h6 class="card-title">Create Trip</h6> -->
            <ul class="card-title nav nav-pills nav-justified">
                <li class="nav-item"><a href="#solid-tab1" onclick="changeType('local')" class="nav-link rounded-round legitRipple"  data-toggle="tab">Local</a></li>
                <!-- <li class="nav-item"><a href="#solid-tab2" onclick="changeType('rental')" class="nav-link rounded-round legitRipple" data-toggle="tab">Rental</a></li>
                <li class="nav-item"><a href="#solid-tab3" onclick="changeType('outstation')" class="nav-link rounded-round legitRipple" data-toggle="tab">Out Station</a></li> -->
            </ul>
            <input type="hidden" id="trip_type" />
            <div class="header-elements">
                <!-- <div class="list-icons">
                    <a class="list-icons-item" data-action="collapse"></a>
                </div> -->
            </div>
        </div>
        
        <div class="tab-content">
            <div class="card tab-pane fade animated trip_content" id="solid-tab1">
                <form class="wizard-form steps-validation" id="request_form" action="#" method="post" data-fouc autocomplete="off">
                    @csrf
                    <input type="hidden" name="trip_types" value="LOCAL" />
                    <h6>User Details</h6>
                    <fieldset>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Passenger Number: <span class="text-danger">*</span></label>
                                    <input type="number" name="customer_number" onkeyup="myFunction()" id="customer_number" placeholder="Passenger Number" class="form-control required" onKeyPress="if(this.value.length==10) return false;">
                                    
                                    <input type="hidden" name="customer_slug" id="customer_slug" class="form-control">
                                </div>
                                <div class="autocompletes">
                                    <ul id="autocompletes"></ul>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Passenger Name: <span class="text-danger">*</span></label>
                                    <input type="text" name="customer_name" id="customer_name" placeholder="Passenger Name" class="form-control required">
                                </div>
                            </div>
                            <!-- <div class="col-md-12">
                                <div class="form-group">
                                    <label>Passenger Address: <span class="text-danger">*</span></label>
                                    <input type="text" name="customer_address" id="customer_address" placeholder="Passenger Address" class="form-control required">
                                </div>
                            </div> -->
                            <div class="col-md-12 trips_count">
                                <h6>Trip History</h6>
                                <div class="row text-center">
                                    <div class="col-6">
                                        <h5 class="font-weight-semibold mb-0 completed_trips_count"></h5>
                                        <span class="text-muted font-size-sm" style="color:#4CAF50 !important;">Completed Trips</span>
                                    </div>

                                    <div class="col-6">
                                        <h5 class="font-weight-semibold mb-0 cancelled_trips_count"></h5>
                                        <span class="text-muted font-size-sm" style="color:#F44336 !important;">Cancelled Trips</span>
                                    </div>
                                </div><br>
                                <div id="trips_id" class="text-center"></div>
                            </div>
                        </div>
                    </fieldset>
                    <h6>Trip</h6>
                    <fieldset class="fieldset">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="form-check form-check-inline">
                                        <label class="form-check-label">
                                            <input type="radio" name="destination_type" class="form-input-styled change-type required" data-fouc value="Normal" checked>
                                            Normal
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <label class="form-check-label">
                                            <input type="radio" name="destination_type" class="form-input-styled change-type required" data-fouc value="Open">
                                            Open
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group form-group-feedback form-group-feedback-right">
                                    <label>{{ __('pickup_point') }}: <span class="text-danger">*</span></label>
                                    <input type="text" name="pickup" id="pickup_point" onkeyup="getAddress('pickup')" class="form-control required type_location_input" placeholder="Pickup Point">
                                    <div class="form-control-feedback form-control-feedback-lg" style="cursor: pointer;margin-top: 25px;">
                                        <!-- <i class="icon-location3 get_location" style="font-size:20px"></i> -->
                                        <i class="icon-cross" onclick="clearData('pickup')" style="font-size:20px"></i>  
                                    </div>
                                    <input type="hidden" name="pickup_lat" id="pickup_point_lat">
                                    <input type="hidden" name="pickup_lng" id="pickup_point_lng">
                                    <input type="hidden" name="pickup_lng_id" id="pickup_point_lng_id">
                                </div>
                            </div>
                            <div class="col-md-12" id="add_rows">
                                <div class="form-group form-group-feedback form-group-feedback-right">
                                    <label>Stop Point: <span class="text-danger">*</span></label>
                                    <input type="text" name="stop" id="stop_point" onkeyup="getAddress('stop')" class="form-control type_location_input" placeholder="Stop Point">
                                    <div class="form-control-feedback form-control-feedback-lg" style="cursor: pointer;margin-top: 25px;">
                                        <i class="icon-sort change_button"></i>
                                        <i class="icon-cross" onclick="clearData('stop')" style="font-size:20px"></i>
                                    </div>
                                    <input type="hidden" name="stop_lat" id="stop_point_lat">
                                    <input type="hidden" name="stop_lng" id="stop_point_lng">
                                    <input type="hidden" name="stop_lng_id" id="stop_point_lng_id">
                                </div>
                                <div class="form-group text-right"><label class="badge badge-danger rounded-pill remove_button"><i class="icon-cross mr-2"></i> Remove Stop</label></div>
                            </div>
                            <div class="col-md-12" id="drop_row">
                                <div class="form-group form-group-feedback form-group-feedback-right">
                                    <label>{{ __('drop_point') }}: </label>
                                    <input type="text" name="drop" id="drop_point" onkeyup="getAddress('drop')" class="form-control type_location_input required" placeholder="Drop Point">
                                    <div class="form-control-feedback form-control-feedback-lg" style="cursor: pointer;margin-top: 25px;">
                                        <i class="icon-cross" onclick="clearData('drop')" style="font-size:20px"></i>  
                                    </div>
                                    <input type="hidden" name="drop_lat" id="drop_point_lat">
                                    <input type="hidden" name="drop_lng" id="drop_point_lng">
                                    <input type="hidden" name="drop_lng_id" id="drop_point_lng_id">
                                </div>
                            </div>
                            <!-- <div class="col-md-12 text-right" id="button_view">
                                <label class="badge badge-danger rounded-pill add_button"><i class="icon-plus2 mr-2"></i> Add one stop</label>
                            </div> -->
                            <div class="col-md-12 trips_count">
                                <div id="accordion-group">
                                    <div class="card mb-0 rounded-bottom-0">
                                        <div class="card-header view-history">
                                            <h6 class="card-title">
                                                <a data-toggle="collapse" class="text-body collapsess" href="#accordion-item-group1" aria-expanded="true">Pick up locations</a>
                                            </h6>
                                        </div>
                                        <div id="accordion-item-group1" class="collapse show" data-parent="#accordion-group" style="">
                                            <ul class="list-group border-0 rounded-0 pickups_list">
                                                <li class="list-group-item list-group-item-action">
                                                    <i class="icon-user mr-3"></i> Not Location Set
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="card mb-0 rounded-0 border-y-0">
                                        <div class="card-header view-history">
                                            <h6 class="card-title">
                                                <a class="text-body collapsed collapsess" data-toggle="collapse" href="#accordion-item-group2" aria-expanded="false">Drop locations</a>
                                            </h6>
                                        </div>
                                        <div id="accordion-item-group2" class="collapse" data-parent="#accordion-group" style="">
                                            <ul class="list-group border-0 rounded-0 drops_list">
                                                <li class="list-group-item list-group-item-action">
                                                    <i class="icon-user mr-3"></i> Not Location Set
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div><br>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="form-check form-check-inline">
                                        <label class="form-check-label">
                                            <input type="radio" name="manual_trip" class="form-input-styled required" data-fouc value="MANUAL">
                                            Manual
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <label class="form-check-label">
                                            <input type="radio" name="manual_trip" class="form-input-styled required" data-fouc value="AUTOMATIC">
                                            Automatic
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="form-check form-check-inline">
                                        <label class="form-check-label">
                                            <input type="radio" name="trip_type" class="form-input-styled change-time required" onchange="getVehicles('pickup')" data-fouc value="RIDE_NOW">
                                            Ride Now
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <label class="form-check-label">
                                            <input type="radio" name="trip_type" class="form-input-styled change-time required" onchange="getVehicles()" data-fouc value="RIDE_LATER">
                                            Ride Later
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12" id="trip_date_time">
                                <div class="form-group">
                                    <label>Date & Time: <span class="text-danger">*</span></label>
                                    <input type="datetime-local" onblur="getVehicles()" name="ride_date_time" id="datetime_local" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Promo Code: </label>
                                    <input type="text" onblur="getVehicles()" name="promo_code" id="coupen" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Driver Notes:</label>
                                    <textarea name="driver_notes" class="form-control"></textarea>
                                </div>
                            </div><br>
                        </div>
                    </fieldset>
                    <h6>Vehicles</h6>
                    <fieldset class="fieldset">
                        <input type="hidden" id="trip_amount" name="trip_amount" >
                        <div class="row" id="types_list"></div>
                    </fieldset>
                    <button type="reset" class="resets"></button>
                </form>
            </div>
            <div class="card tab-pane fade animated trip_content" id="solid-tab2">
                <form class="wizard-form steps-validation1" id="request_form1" method="post" data-fouc autocomplete="off">
                    @csrf
                    <input type="hidden" name="trip_types" value="RENTAL" />
                    <h6>User Details</h6>
                    <fieldset>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Passenger Number: <span class="text-danger">*</span></label>
                                    <input type="number" name="customer_number" onkeyup="myFunction1()" id="rental_customer_number" placeholder="Passenger Number" class="form-control required" onKeyPress="if(this.value.length==10) return false;">
                                    <input type="hidden" name="customer_slug" id="rental_customer_slug" class="form-control">
                                </div>
                                <div class="autocompletes">
                                    <ul id="autocompletes1"></ul>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Passenger Name: <span class="text-danger">*</span></label>
                                    <input type="text" name="customer_name" id="rental_customer_name" placeholder="Passenger Name" class="form-control required">
                                </div>
                            </div>
                            <!-- <div class="col-md-12">
                                <div class="form-group">
                                    <label>Passenger Address: <span class="text-danger">*</span></label>
                                    <input type="text" name="customer_address" id="rental_customer_address" placeholder="Passenger Address" class="form-control required">
                                </div>
                            </div> -->
                        </div>
                    </fieldset>
                    <h6>Trip</h6>
                    <fieldset>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group form-group-feedback form-group-feedback-right">
                                    <label>{{ __('pickup_point') }}: <span class="text-danger">*</span></label>
                                    <input type="text" name="pickup" onkeyup="getAddress('rental_pickup')" id="rental_pickup_point" class="form-control required type_location_input" placeholder="Pickup Point">
                                    <div class="form-control-feedback form-control-feedback-lg" style="cursor: pointer;margin-top: 25px;">
                                        <i class="icon-location3 get_location" style="font-size:20px"></i>
                                        <i class="icon-cross" onclick="clearData('pickup')" style="font-size:20px"></i>
                                    </div>
                                    <input type="hidden" name="pickup_lat" id="rental_pickup_point_lat">
                                    <input type="hidden" name="pickup_lng" id="rental_pickup_point_lng">
                                    <input type="hidden" name="pickup_lng_id" id="rental_pickup_point_lng_id">
                                </div>
                            </div>
                            <!-- <div class="col-md-12">
                                <div class="form-group">
                                    <label>{{ __('drop_point') }}:</label>
                                    <input type="text" name="drop" id="rental_drop_point" onkeyup="getAddress('rental_drop')" class="form-control" placeholder="Drop Point">
                                    <input type="hidden" name="drop_lat" id="rental_drop_point_lat">
                                    <input type="hidden" name="drop_lng" id="rental_drop_point_lng">
                                    <input type="hidden" name="drop_lng_id" id="rental_drop_point_lng_id">
                                </div>
                            </div> -->
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>{{ __('rental_package') }}: <span class="text-danger">*</span></label>
                                    <select class="form-control required" name="package" id="rental_package">
                                        <option value="">{{ __('rental_package') }}</option>
                                        @foreach($package_detail as $value)
                                            <option value="{{$value->slug}}">{{$value->name}} ({{$value->km}} KM / {{$value->hours}} H)</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>{{ __('package_items') }}: <span class="text-danger">*</span></label>
                                    <select class="form-control required" name="type" onchange="assionPromoRental()" id="package_items">
                                        <option value="">{{ __('package_items') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12"><br>
                                <div class="form-group">
                                    <div class="form-check form-check-inline">
                                        <label class="form-check-label">
                                            <input type="radio" name="manual_trip" class="form-input-styled required" data-fouc value="MANUAL">
                                            Manual
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <label class="form-check-label">
                                            <input type="radio" name="manual_trip" class="form-input-styled required" data-fouc value="AUTOMATIC">
                                            Automatic
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="form-check form-check-inline">
                                        <label class="form-check-label">
                                            <input type="radio" name="trip_type" class="form-input-styled rental-change-time required"  data-fouc value="RIDE_NOW">
                                            Ride Now
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <label class="form-check-label">
                                            <input type="radio" name="trip_type" class="form-input-styled rental-change-time required" data-fouc value="RIDE_LATER">
                                            Ride Later
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12" id="rental_trip_date_time">
                                <div class="form-group">
                                    <label>Trip Date Time: <span class="text-danger">*</span></label>
                                    <input type="datetime-local" name="ride_date_time" id="datetime" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Promo Code: </label>
                                    <input type="text" onblur="assionPromoRental()" name="promo_code" id="coupen" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Driver Notes: </label>
                                    <textarea name="driver_notes" class="form-control"></textarea>
                                </div>
                            </div><br>
                            <div class="list-group border-0 rounded-0 col-md-12 rental_price">
                                <!-- <lable class="list-group-item list-group-item-action">
                                    <i class="icon-user mr-3"></i>
                                    Distance
                                    <span class="ml-auto font-weight-semibold rental_distance">29</span>
                                </lable>
                                <lable class="list-group-item list-group-item-action">
                                    <i class="icon-cash3 mr-3"></i>
                                    Price per KM
                                    <span class="ml-auto font-weight-semibold rental_price_per_km">29</span>
                                </lable>
                                <lable class="list-group-item list-group-item-action">
                                    <i class="icon-tree7 mr-3"></i>
                                    Admin Commission <span class="rental_admin_commission_percentage">29</span>
                                    <span class="ml-auto font-weight-semibold rental_admin_commission">29</span>
                                </lable>
                                <lable class="list-group-item list-group-item-action">
                                    <i class="icon-calendar3 mr-3"></i>
                                    Driver Commission
                                    <span class="ml-auto font-weight-semibold rental_driver_commission">48</span>
                                </lable> -->
                                <lable class="list-group-item">
                                    <!-- <i class="icon-cog3 mr-3"></i> -->
                                    Total
                                    <span class="ml-auto font-weight-semibold rental_total_price">48</span>
                                </lable>
                            </div>
                        </div>
                    </fieldset>
                    <button type="reset" class="resets"></button>
                </form>
            </div>
            
            <div class="card tab-pane fade animated trip_content" id="solid-tab3">
                <form class="wizard-form steps-validation2" id="request_form2" method="post" data-fouc autocomplete="off">
                    @csrf
                    <input type="hidden" name="trip_types" value="OUTSTATION" />
                    <h6>User Details</h6>
                    <fieldset>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Passenger Number: <span class="text-danger">*</span></label>
                                    <input type="number" name="customer_number" onkeyup="myFunction2()" id="outstation_customer_number" placeholder="Passenger Number" class="form-control required" onKeyPress="if(this.value.length==10) return false;">
                                    <input type="hidden" name="customer_slug" id="outstation_customer_slug" class="form-control">
                                </div>
                                <div class="autocompletes">
                                    <ul id="autocompletes2"></ul>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Passenger Name: <span class="text-danger">*</span></label>
                                    <input type="text" name="customer_name" id="outstation_customer_name" placeholder="Passenger Name" class="form-control required">
                                </div>
                            </div>
                            <!-- <div class="col-md-12">
                                <div class="form-group">
                                    <label>Passenger Address: <span class="text-danger">*</span></label>
                                    <input type="text" name="customer_address" id="outstation_customer_address" placeholder="Passenger Address" class="form-control required">
                                </div>
                            </div> -->
                        </div>
                    </fieldset>
                    <h6>Trip</h6>
                    <fieldset class="fieldset">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group form-group-feedback form-group-feedback-right">
                                    <label>{{ __('pickup_point') }}: <span class="text-danger">*</span></label>
                                    <input type="text" name="pickup" onkeyup="getAddress('outstation_pickup')" id="outstation_pickup_point" class="form-control required type_location_input" placeholder="Pickup Point">
                                    <div class="form-control-feedback form-control-feedback-lg" style="cursor: pointer;margin-top: 25px;">
                                        <i class="icon-location3 get_location" style="font-size:20px"></i>
                                        <i class="icon-cross" onclick="clearData('pickup')" style="font-size:20px"></i>
                                    </div>
                                    <input type="hidden" name="pickup_lat" id="outstation_pickup_point_lat">
                                    <input type="hidden" name="pickup_lng" id="outstation_pickup_point_lng">
                                    <input type="hidden" name="pickup_lng_id" id="outstation_pickup_point_lng_id">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>{{ __('drop_point') }}: <span class="text-danger">*</span></label>
                                    <select class="form-control required" name="drop" id="outstation_drop_point">
                                        <option value="">{{ __('drop_point') }}</option>
                                        @foreach($outstanding_drops as $value)
                                            <option value="{{$value->drop}}">{{$value->drop}}</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="drop_lat" id="outstation_drop_point_lat">
                                    <input type="hidden" name="drop_lng" id="outstation_drop_point_lng">
                                    <input type="hidden" name="drop_lng_id" id="outstation_drop_point_lng_id">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>{{ __('types') }}: <span class="text-danger">*</span></label>
                                    <select class="form-control required" name="type" id="outstation_types" onchange="assionPromoOutstation()">
                                        <option value="">{{ __('types') }}</option>
                                        @foreach($outstation_price as $value)
                                            <option value="{{$value->id}}">{{$value->getVehicle->vehicle_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12"><br>
                                <div class="form-group">
                                    <div class="form-check form-check-inline">
                                        <label class="form-check-label">
                                            <input type="radio" name="manual_trip" class="form-input-styled required" data-fouc value="MANUAL">
                                            Manual
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <label class="form-check-label">
                                            <input type="radio" name="manual_trip" class="form-input-styled required" data-fouc value="AUTOMATIC">
                                            Automatic
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12"><br>
                                <div class="form-group">
                                    <div class="form-check form-check-inline">
                                        <label class="form-check-label">
                                            <input type="radio" name="way_trip" class="form-input-styled required" data-fouc value="ONE" checked onclick="assionPromoOutstation()">
                                            One Way
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <label class="form-check-label">
                                            <input type="radio" name="way_trip" class="form-input-styled required" data-fouc value="TWO" onclick="assionPromoOutstation()">
                                            Two Way
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Trip Date Time: <span class="text-danger">*</span></label>
                                    <input type="datetime-local" name="ride_date_time" id="datetime" onchange="assionPromoOutstation()" class="form-control required">
                                </div>
                            </div>
                            <div class="col-md-12 returndate" >
                                <div class="form-group">
                                    <label>Trip Return Date Time: <span class="text-danger">*</span></label>
                                    <input type="datetime-local" name="ride_return_date_time" id="end_datetime" onchange="assionPromoOutstation()" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Promo Code: </label>
                                    <input type="text" onblur="assionPromoOutstation()" name="promo_code" id="promo_code_outstation" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Driver Notes: </label>
                                    <textarea name="driver_notes" class="form-control"></textarea>
                                </div>
                            </div><br>
                            <div class="list-group border-0 rounded-0 col-md-12 outstation_price">
                                <lable class="list-group-item list-group-item-action">
                                    <!-- <i class="icon-user mr-3"></i> -->
                                    Distance
                                    <span class="ml-auto font-weight-semibold outstation_distance">29</span>
                                </lable>
                                <lable class="list-group-item list-group-item-action">
                                    <!-- <i class="icon-cash3 mr-3"></i> -->
                                    Price per KM
                                    <span class="ml-auto font-weight-semibold price_per_km">29</span>
                                </lable>
                                <lable class="list-group-item list-group-item-action">
                                    <!-- <i class="icon-calendar3 mr-3"></i> -->
                                    Driver Beta
                                    <span class="ml-auto font-weight-semibold driver_commission">48</span>
                                </lable>
                                <lable class="list-group-item list-group-item-action hillstation">
                                    <!-- <i class="icon-calendar3 mr-3"></i> -->
                                    Hill Station Amount
                                    <span class="ml-auto font-weight-semibold hillstation_amount">0</span>
                                </lable>
                                <lable class="list-group-item list-group-item-action promocode">
                                    <!-- <i class="icon-calendar3 mr-3"></i> -->
                                    Promo Amount
                                    <span class="ml-auto font-weight-semibold promo_amount">0</span>
                                </lable>
                                <lable class="list-group-item list-group-item-action">
                                    <!-- <i class="icon-cog3 mr-3"></i> -->
                                    Total
                                    <span class="ml-auto font-weight-semibold total_price">48</span>
                                </lable>
                                <small class="text-danger promo_msg"></small>
                            </div>
                        </div>
                    </fieldset>
                    <button type="reset" class="resets"></button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-8" id="map" ></div>
    <div id="legend"><h5> Legend <button class="btn btn-link text-warning" onclick="legendChanged('text')"><i class="icon-spinner11"></i></h5></button></div>
    <div id="type_count"><h5> Types Count </h5></div>
    <div id="drivers_list"><select class="form-control" name="select_driver" id="select_driver">
                                        <option value="">Select Driver</option>
                                    </select><button class="btn btn-link text-warning" onclick="removedriverslist('text')">Remove drivers tag</h5></button></div>
</div>

    <!-- Horizontal form modal -->
    <div id="roleModel" class="modal fade" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title " id="modelHeading">Assign Driver</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                
                <div class="modal-body">
                    <div class="form-group required">
                        <label class="col-form-label">Driver Search</label>
                        <div class="">
                            <input type="text" class="form-control" name="drivers" id="drivers">
                            <input type="hidden" class="form-control" name="driver_id" id="driver_id">
                            <input type="hidden" class="form-control" name="trip_id" id="trip_id">
                        </div>
                    </div>
                    <div class="card">
                        <ul class="media-list media-list-linked"></ul>
                    </div>
                    
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-link" data-dismiss="modal">{{ __('close') }}</button>
                    <button type="button" id="saveBtn" class="btn bg-primary">{{ __('assign') }}</button>
                </div>
            </div>
        </div>
    </div>
        
<!-- /horizontal form modal -->

<script type="text/javascript">
    var i =1;
    var trip_id = '';
    $(".autocompletes").hide();
    $(".bg-warning").hide();
    $(".bg-danger").hide();
    $("#trip_date_time").hide();
    $("#add_rows").hide();
    $(".trips_count").hide();
    $(".outstation_price").hide();
    $(".resets").hide();
    $("#rental_trip_date_time").hide();
    $(".rental_price").hide();
    $(".returndate").hide();

    $(".nav-link").on('click',function(){
        $(".card-form").removeClass("card-collapsed");
        $(".tab-content").css("display", "block")
    });

    $(document).on('change',".change-time",function(){
        var value = $(this).val();

        if(value == 'RIDE_LATER'){
            $("#trip_date_time").show();
            $("#datetime_local").addClass("required");
        }
        else{
            $("#trip_date_time").hide();
            $("#datetime_local").removeClass("required");
        }
    });

    $(document).on('change',".change-type",function(){
        var value = $(this).val();

        if(value == 'Normal'){
            $("#drop_row").show();
            $("#drop_point").addClass("required");
        }
        else{
            $("#drop_row").hide();
            $("#drop_point").removeClass("required");
        }
    });

    $(document).on('change',".rental-change-time",function(){
        var value = $(this).val();

        if(value == 'RIDE_LATER'){
            $("#rental_trip_date_time").show();
            $("#datetime").addClass("required");
        }
        else{
            $("#rental_trip_date_time").hide();
            $("#datetime").removeClass("required");
        }
    });

    $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
    });

    $(document).on('click',".add_button",function(){
        $("#button_view").hide();
        $("#add_rows").show();
    })

    $(document).on('click',".remove_button",function(){
        $("#button_view").show();
        $("#add_rows").hide();
        $("#stop_point").val('');
        $("#stop_point_lat").val('');
        $("#stop_point_lng").val('');
        $("#stop_point_lng_id").val('');
        originPlaceId = $("#pickup_point_lng_id").val();
        destinationPlaceId = $("#drop_point_lng_id").val();
        var request = {
            origin: { placeId: originPlaceId },
            destination: { placeId: destinationPlaceId },
            travelMode: google.maps.TravelMode.DRIVING
        };
        directionsService.route(request, function(response, status) {
            if (status == google.maps.DirectionsStatus.OK) {
                directionsDisplay.setDirections(response);
                directionsDisplay.setMap(map);
                google.maps.event.addListener(directionsDisplay, 'directions_changed', some_method);
            } else {
                alert("Directions Request from " + start.toUrlValue(6) + " to " + end.toUrlValue(6) + " failed: " + status);
            }
        });
        getVehicles();

    })

    function myFunction() {
        var value = $("#customer_number").val();
        var phoneno = /^\d{10}$/;
        if(!value.match(phoneno)){
            $("#customer_number").next("samp").remove();
            $("#customer_number").after("<samp class='text-danger'>invalid Phone number</samp>");
        }
        else{
            $("#customer_number").next("samp").remove();
        }
        var text = "";
        if(value){
            $.ajax({
                url: "{{ url('get_customer') }}/"+value,
                type: "GET",
                dataType: 'json',
                success: function (data) {

                    if(data.data.length > 0){
                        $.each( data.data, function( key, value ) {
                            text += "<li id='"+value.slug+"' class='getCustomer' >"+value.firstname+" "+value.lastname+" ("+value.phone_number+")</li>"
                        });
                        $(".autocompletes").show();
                        $("#autocompletes").html(text);
                    }
                    else{
                        $(".autocompletes").hide();
                    }
                    
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    // $('#errorbox').show();
                    // $(".autocompletes").hide();
                    // var err = eval("(" + xhr.responseText + ")");
                    // // console.log(err.error);
                    // $('#errorContent').html('');
                    // $.each(err.error, function(key, value) {
                    //     $('#errorContent').append('<strong><li>'+value+'</li></strong>');
                    // });
                    // $('#saveBtn').html("{{ __('save-changes') }}");
                }
            });
        }
    }

    function myFunction1() {
        var value = $("#rental_customer_number").val();
        var phoneno = /^\d{10}$/;
        var text = "";
        if(!value.match(phoneno)){
            $("#rental_customer_number").next("samp").remove();
            $("#rental_customer_number").after("<samp class='text-danger'>invalid Phone number</samp>");
        }
        else{
            $("#rental_customer_number").next("samp").remove();
        }
        if(value != ""){
            $.ajax({
                url: "{{ url('get_customer') }}/"+value,
                type: "GET",
                dataType: 'json',
                success: function (data) {

                    if(data.data.length > 0){
                        $.each( data.data, function( key, value ) {
                            text += "<li id='"+value.slug+"' class='getCustomer1' >"+value.firstname+" "+value.lastname+" ("+value.phone_number+")</li>"
                        });
                        $(".autocompletes").show();
                        $("#autocompletes1").html(text);
                    }
                    else{
                        $(".autocompletes").hide();
                    }
                    
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    $(".autocompletes").hide();
                }
            });
        }
    }

    function myFunction2() {
        var value = $("#outstation_customer_number").val();
        var phoneno = /^\d{10}$/;
        var text = "";
        if(!value.match(phoneno)){
            $("#outstation_customer_number").next("samp").remove();
            $("#outstation_customer_number").after("<samp class='text-danger'>invalid Phone number</samp>");
        }
        else{
            $("#outstation_customer_number").next("samp").remove();
        }
        if(value != ""){
            $.ajax({
                url: "{{ url('get_customer') }}/"+value,
                type: "GET",
                dataType: 'json',
                success: function (data) {

                    if(data.data.length > 0){
                        $.each( data.data, function( key, value ) {
                            text += "<li id='"+value.slug+"' class='getCustomer2' >"+value.firstname+" "+value.lastname+" ("+value.phone_number+")</li>"
                        });
                        $(".autocompletes").show();
                        $("#autocompletes2").html(text);
                    }
                    else{
                        $(".autocompletes").hide();
                    }
                    
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    $(".autocompletes").hide();
                }
            });
        }
    }

    $(document).on('change','#rental_package',function(){
        var value = $(this).val();
        var text1 = '<option value="">{{ __('package_items') }}</option>';
        $.ajax({
            url: "{{ url('get-rental-package-items') }}/"+value,
            type: "GET",
            dataType: 'json',
            success: function (data) {
                if(data.data.length > 0){
                    $.each( data.data, function( key, value ) {
                        text1 += "<option value='"+value.id+"'>"+value.get_vehicle.vehicle_name+"</option>";
                    });
                    $("#package_items").html(text1);
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                $(".autocompletes").hide();
            }
        });
    })

    function assionPromoRental(){
        $.ajax({
            url: "{{ route('getRentalPackageEta') }}",
            data: $('#request_form1').serialize(),
            type: "GET",
            dataType: 'json',
            success: function (data) {
                console.log(data);
                if(data.success){
                    var amount = data.data ? data.data.get_package ? data.data.get_package.get_country ? data.data.get_package.get_country.currency_symbol : '' : '' : '';
                    amount += data.data ? " "+data.data.price : '';
                    if(data.data && data.data.promo_price){
                        amount += " ("+data.data.promo_price+")";
                    }
                    $(".rental_total_price").text(amount);
                    $(".rental_price").next('small').remove();
                    var msg =data.data ?  "<small class='text-danger'>"+data.data.promo_msg+"</small>" : '';
                    $(".rental_price").after(msg);
                    $(".rental_price").show();
                }
                
            },
            error: function (xhr, ajaxOptions, thrownError) {
                // $(".autocompletes").hide();
                var err = eval("(" + xhr.responseText + ")");
                    // console.log(err);
                if(err.success == false){
                    $("#error_message").text(err.message);
                    $(".bg-danger").show();
                    setTimeout(function(){ $(".bg-danger").fadeOut(2000); }, 10000);
                }
            }
        });
    }

    $(document).on('change',"#outstation_drop_point",function(){
        var values = $(this).val();
        $.ajax({
            url: "{{ url('get-outstation-location') }}/"+values,
            type: "GET",
            dataType: 'json',
            success: function (data) {
                console.log(data);
                var lats = data.data.drop_lat;
                var lngs = data.data.drop_lng;
                $("#outstation_drop_point_lat").val(parseFloat(lats));
                $("#outstation_drop_point_lng").val(parseFloat(lngs));
                var geocoder = new google.maps.Geocoder;
                var latlng = {lat: parseFloat(lats), lng: parseFloat(lngs)};

                geocoder.geocode({'location': latlng}, function(results, status) {
                    if (status === google.maps.GeocoderStatus.OK) {
                        if (results[1]) {
                            $("#outstation_drop_point_lng_id").val(results[1].place_id);
                            if(markers != "" && !Array.isArray(markers)){
                                markers.setMap(null);
                            }
                            route();
                            assionPromoOutstation();
                        } else {
                            window.alert('No results found');
                        }
                    } else {
                        window.alert('Geocoder failed due to: ' + status);
                    }
                });

            },
            error: function (xhr, ajaxOptions, thrownError) {
                $(".autocompletes").hide();
            }
        });

    })

    function assionPromoOutstation(){
        var type = $('#outstation_types').val();
        var way_trip = $('input[type=radio][name=way_trip]:checked').val();
        if(way_trip == 'TWO'){
            $(".returndate").show();
            $("#end_datetime").addClass('required');
        }
        else{
            $(".returndate").hide();
            $("#end_datetime").removeClass('required');
        }
        var outstation_pickup_point = $("#promo_code_outstation").val();
        var outstation_drop_point = $("#outstation_drop_point").val();
        $.ajax({
            url: "{{ route('getOutstationEta') }}",
            type: "GET",
            data: $('#request_form2').serialize(),
            dataType: 'json',
            success: function (data) {
                $(".outstation_distance").text(data.data.distance);
                $(".price_per_km").text(data.data.currency_symbol+' '+data.data.price);
                $(".driver_commission").text(data.data.currency_symbol+' '+data.data.driver_price);
                if(data.data.promo_code){
                    $(".promocode").show();
                }
                else{
                    $(".promocode").hide();
                }
                $(".promo_amount").text(data.data.currency_symbol+' '+data.data.promo_price);
                if(data.data.hill_station_status){
                    $(".hillstation").show();
                }
                else{
                    $(".hillstation").hide();
                }
                $(".hillstation_amount").text(data.data.currency_symbol+' '+data.data.hill_station_amount);
                $(".total_price").text(data.data.currency_symbol+' '+data.data.total);
                $(".promo_msg").text(data.data.promo_msg);
                if(data.data.percentage){
                    $(".admin_commission_percentage").text(" ("+data.data.percentage+"%)");
                }
                else{
                    $(".admin_commission_percentage").text(" ");
                }
                $(".outstation_price").show();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                $(".autocompletes").hide();
            }
        });
    }

    $(document).on('click',".getCustomer",function(){
        var id = $(this).attr('id');
        var trips_id = '';
        var piickup_list = '';
        var drops_list = '';
        $("#customer_number").next("samp").remove();
        $.ajax({
            url: "{{ url('get-customer-detail') }}/"+id,
            type: "GET",
            dataType: 'json',
            success: function (data) {
                $(".trips_count").show();
                $("#customer_number").val(data.data.phone_number);
                $("#customer_name").val(data.data.firstname+" "+data.data.lastname);
                $("#customer_address").val(data.data.address);
                $("#customer_slug").val(data.data.slug);
                $(".completed_trips_count").text(data.data.completed_trips);
                $(".cancelled_trips_count").text(data.data.cancelled_trips);
                
                jQuery.each( data.data.trips, function( i, val ) {
                    trips_id += '<a href="dispatch-request-view/'+val.id+'" class="badge badge-success ml-1">'+val.request_number+'</a>';
                    if(val.request_place.pick_lng && val.request_place.pick_up_id && val.request_place.pick_lat){
                        piickup_list += '<button type="button" class="list-group-item list-group-item-action setPickup" data-value="'+val.request_place.pick_lat+';'+val.request_place.pick_lng+';'+val.request_place.pick_address+';'+val.request_place.pick_up_id+'"><i class="icon-location3 mr-2"></i>'+val.request_place.pick_address+'</button>';
                    }
                    if(val.request_place.drop_lat && val.request_place.drop_lng && val.request_place.drop_id){
                        drops_list += '<button type="button" class="list-group-item list-group-item-action setDrop" data-value="'+val.request_place.drop_lat+';'+val.request_place.drop_lng+';'+val.request_place.drop_address+';'+val.request_place.drop_id+'"><i class="icon-location3 mr-2"></i>'+val.request_place.drop_address+'</button>';
                    }
                });
                $("#trips_id").html(trips_id);
                $(".pickups_list").html(piickup_list);
                $(".drops_list").html(drops_list);
                $(".autocompletes").hide();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                // $('#errorbox').show();
                // var err = eval("(" + xhr.responseText + ")");
                // $('#errorContent').html('');
                // $.each(err.error, function(key, value) {
                //     $('#errorContent').append('<strong><li>'+value+'</li></strong>');
                // });
                // $('#saveBtn').html("{{ __('save-changes') }}");
            }
        });
    })

    $(document).on('click',".getCustomer1",function(){
        var id = $(this).attr('id');
        var trips_id = '';
        var piickup_list = '';
        var drops_list = '';
        $("#rental_customer_number").next("samp").remove();
        $.ajax({
            url: "{{ url('get-customer-detail') }}/"+id,
            type: "GET",
            dataType: 'json',
            success: function (data) {
                $(".trips_count").show();
                $("#rental_customer_number").val(data.data.phone_number);
                $("#rental_customer_name").val(data.data.firstname+" "+data.data.lastname);
                $("#rental_customer_address").val(data.data.address);
                $("#rental_customer_slug").val(data.data.slug);
                $(".autocompletes").hide();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                // $('#errorbox').show();
                // var err = eval("(" + xhr.responseText + ")");
                // $('#errorContent').html('');
                // $.each(err.error, function(key, value) {
                //     $('#errorContent').append('<strong><li>'+value+'</li></strong>');
                // });
                // $('#saveBtn').html("{{ __('save-changes') }}");
            }
        });
    })

    $(document).on('click',".getCustomer2",function(){
        var id = $(this).attr('id');
        var trips_id = '';
        var piickup_list = '';
        var drops_list = '';
        $("#outstation_customer_number").next("samp").remove();
        $.ajax({
            url: "{{ url('get-customer-detail') }}/"+id,
            type: "GET",
            dataType: 'json',
            success: function (data) {
                $(".trips_count").show();
                $("#outstation_customer_number").val(data.data.phone_number);
                $("#outstation_customer_name").val(data.data.firstname+" "+data.data.lastname);
                $("#outstation_customer_address").val(data.data.address);
                $("#outstation_customer_slug").val(data.data.slug);
                $(".autocompletes").hide();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                // $('#errorbox').show();
                // var err = eval("(" + xhr.responseText + ")");
                // $('#errorContent').html('');
                // $.each(err.error, function(key, value) {
                //     $('#errorContent').append('<strong><li>'+value+'</li></strong>');
                // });
                // $('#saveBtn').html("{{ __('save-changes') }}");
            }
        });
    })

    $(document).on('click','.setPickup',function(){
        var pick_lat = $(this).attr('data-value');
        pick_lat = pick_lat.split(";")
        // console.log(pick_lat);
        $("#pickup_point_lat").val(pick_lat[0]);
        $("#pickup_point_lng").val(pick_lat[1]);
        $("#pickup_point").val(pick_lat[2]);
        $("#pickup_point_lng_id").val(pick_lat[3]);
        $(".collapsed").click();
        $(".setPickup").removeClass( "active" );
        $(this).addClass( "active" );
        if(markers != "" && !Array.isArray(markers)){
            markers.setMap(null);
        }
        markers = new google.maps.Marker({
            position: { lat: parseFloat(pick_lat[0]), lng: parseFloat(pick_lat[1]) },
            map,
            draggable: true,
            label: 'A',
            // title: 
        });
        markers.addListener('dragend', handleEvent);
        map.setZoom(12);
        map.setCenter(markers.getPosition());
        route();
        getVehicles();
        // $("#pickup_point_lng_id").val(pick_address);
    });

    $(document).on('click','.setDrop',function(){
        var drop_lat = $(this).attr('data-value');
        drop_lat = drop_lat.split(";");
        // console.log(drop_lat);
        $("#drop_point_lat").val(drop_lat[0]);
        $("#drop_point_lng").val(drop_lat[1]);
        $("#drop_point").val(drop_lat[2]);
        $("#drop_point_lng_id").val(drop_lat[3]);
        $(".setDrop").removeClass( "active" );
        $(this).addClass( "active" );
        if(markers != "" && !Array.isArray(markers)){
            markers.setMap(null);
        }
        route();
        getVehicles();
        // $("#drop_point_lng_id").val(pick_address);
    });
    
    // $(document).on('blur',"#drop_point",function(){
    function getVehicles() {
        var texts = "";
        if($('input[type=radio][name=trip_type]:checked').val() != ""){
        var formData = new FormData();
        formData.append('pickup_lat',$('#pickup_point_lat').val());
        formData.append('pickup_long',$('#pickup_point_lng').val());
        formData.append('drop_lat',$('#drop_point_lat').val());
        formData.append('drop_long',$('#drop_point_lng').val());
        formData.append('pickup_address',$('#pickup_point').val());
        formData.append('drop_address',$('#drop_point').val());
        formData.append('ride_type',$('input[type=radio][name=trip_type]:checked').val());
        formData.append('ride_date',$('#datetime_local').val());
        formData.append('ride_time',$('#datetime_local').val());
        formData.append('promo_code',$('#coupen').val());
        formData.append('trip_type',$('#trip_type').val());
        if($('#stop_point').val() != ""){ 
            var stops = [
                {
                    'address':$('#stop_point').val(),
                    'latitude':$('#stop_point_lat').val(),
                    'longitude':$('#stop_point_lng').val(),
                    'stopsCount':1,
                }
            ];
            formData.append('stops',JSON.stringify(stops));
        }
        $(".bg-danger").hide();
        $.ajax({
            data: formData,
            url: "{{ route('getVehicles') }}",
            type: "POST",
            dataType: 'json',
            contentType : false,
            processData: false,
            success: function (data) {
                data = data.data;
                if(data.zone_type_price.length > 0){
                    $(".bg-danger").hide();
                    data.zone_type_price.forEach(element => {
                        texts += '<div class="col-xl-12 col-md-12"><div class="card card-body" for="'+element.type_slug+'" data-value="'+data.currency_symble+' '+element.promo_total_amount+'"><div class="media"><div class="mr-3"><img src="'+element.type_image+'" class="rounded-circle" width="38" height="38" alt=""></div><div class="media-body"><input type="radio" name="type" class="required clickCheck" value="'+element.type_slug+'" id="'+element.type_slug+'"><label class="media-title text-capitalize font-weight-semibold">'+element.type_name+'</label><br><span class="">'+data.currency_symble+' '+element.promo_total_amount+'</span><p class="text-danger">'+element.promo_msg+'</p></div><div class="ml-3 align-self-center"><div class="list-icons"><div class="dropdown position-static"><a href="#" class="list-icons-item" data-toggle="dropdown" aria-expanded="false"><i class="icon-menu7"></i></a><div class="dropdown-menu dropdown-menu-right" style=""><ul class="list-group"><li class="list-group-item">Base Price <span class="ml-auto">'+data.currency_symble+' '+element.base_price+'</span></li><li class="list-group-item">Rate Per Km <span class="ml-auto">'+data.currency_symble+' '+element.price_per_distance+'</span></li><li class="list-group-item">Waiting Charge <span class="ml-auto">'+data.currency_symble+' '+element.waiting_charge+'</span></li>';
                        if(element.booking_fees > 0){
                            texts += '<li class="list-group-item">Booking fees <span class="ml-auto">'+data.currency_symble+' '+element.booking_fees+'</span></li>';
                        }
                        if(element.outofzone > 0){
                            texts += '<li class="list-group-item">Out of Zone price <span class="ml-auto">'+data.currency_symble+' '+element.outofzone+'</span></li>';
                        }
                        if(element.promo_amount > 0){
                            texts += '<li class="list-group-item">Promo Bonus <span class="ml-auto">'+data.currency_symble+' '+element.promo_amount+'</span></li>';
                        }
                        texts += '<li class="list-group-item list-group-divider"></li><li class="list-group-item">Total <span class="ml-auto">'+data.currency_symble+' '+element.promo_total_amount+'</span></li></ul></div></div></div></div></div></div></div>';
                    });
                }
                $("#types_list").html(texts);
                
            },
            error: function (xhr, ajaxOptions, thrownError) {
                var err = eval("(" + xhr.responseText + ")");
                    // console.log(err);
                if(err.success == false){
                    $("#error_message").text(err.message);
                    $(".bg-danger").show();
                    // setTimeout(function(){ $(".bg-danger").fadeOut(2000); }, 10000);
                }
            }
        });
        }
    }

    $(document).on('click','.card-body',function(){
        var valu = $(this).attr("for");
        var amount = $(this).attr("data-value");
        $("#trip_amount").val(amount);
        $("#"+valu).prop('checked',true);
        $(".clickCheck").parents(".card-body").removeClass('bg-success');
        $(this).addClass('bg-success');
    });

    function changeType(value){
        num = 5000;
        $("#trip_type").val(value);
        $(".resets").click();
        $(".trips_count").hide();
        $(".outstation_price").hide();
        $("#add_rows").hide();
        $(".autocompletes").hide();
        originPlaceId = '';
        destinationPlaceId = '';
        directionsDisplay.setMap(null);
        if(markers != "" && !Array.isArray(markers)){
            markers.setMap(null);
        }
        if(value == 'local'){
            $("#solid-tab1").addClass("bounceInDown");
        }
        else if(value == 'rental'){
            $("#solid-tab2").addClass("bounceInDown");
        }
        else if(value == 'outstation'){
            $("#solid-tab3").addClass("bounceInDown");
        }
    }

    $("#drivers").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $(".media-list li").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });

    $(document).on('click',".select_driver",function(){
        var value = $(this).attr('id');
        $(".select_driver").parent().removeClass('btn-yellow');
        var full_name = $(this).attr('data-value');
        $("#drivers").val(full_name);
        $("#driver_id").val(value);
        $(this).parent().addClass('btn-yellow');
    })

    $(document).on('click','.submit',function(e){
        $(this).addClass('disabled');
        e.preventDefault();
        var text = '';
        var trip = $("#trip_type").val();
        if(trip == 'local'){
            var $form = $('#request_form');
            var data = $('#request_form').serialize();
        }
        else if(trip == 'rental'){
            var $form = $('#request_form1');
            var data = $('#request_form1').serialize();
        }
        else if(trip == 'outstation'){
            var $form = $('#request_form2');
            var data = $('#request_form2').serialize();
        }
        console.log($form.valid());

        if ($form.valid()) {

            $.ajax({
                data: data,
                url: "{{ route('createDispatchRequest') }}",
                type: "POST",
                dataType: 'json',
                success: function (data) {
                    $(".bg-danger").hide();
                    if(data.success){
                        $(".bg-danger").hide();
                        if(data.data.is_cancelled){
                            swal({
                                title: "{{ __('Error') }}",
                                text: data.message,
                                icon: "error",
                            }).then((value) => {        
                                window.location.href = "dispatcher-trip-list";
                            });
                        }
                        if(data.data.is_later){
                            swal({
                                title: "{{ __('success') }}",
                                text: data.message,
                                icon: "success",
                            }).then((value) => {        
                                window.location.href = "dispatcher/edit/"+data.data.id;
                            });
                        }
                        else{
                            if(data.data.trip_type == 'OUTSTATION'){
                                swal({
                                    title: "{{ __('success') }}",
                                    text: data.message,
                                    icon: "success",
                                }).then((value) => {        
                                    window.location.href = "dispatcher/edit/"+data.data.id;
                                });
                            }
                            // $(".bg-warning").show();
                            if(data.data && data.data.result && data.data.result.manual_trip == 'MANUAL'){
                                $("#trip_id").val(data.data.result.id);
                                // console.log(data.data.drivers);
                                if(Array.isArray(data.data.drivers) && data.data.drivers.length > 0){
                                    jQuery.each( data.data.drivers, function( i, val ) {
                                        // console.log(val);
                                        text += `<li class="">
                                                <a href="#" class="media select_driver" id="`+val.id+`" data-value="`+val.firstname+` `+val.lastname+`">
                                                    <div class="mr-3"><img src="`+val.profile_pic+`" class="rounded-circle" width="40" height="40" alt=""></div>
                                                    <div class="media-body">
                                                        <div class="media-title font-weight-semibold">`+val.firstname+` `+val.lastname+`</div>
                                                        <span class="">`+val.driver.vehicletype.vehicle_name+`</span><br>
                                                        <span class="">`+val.phone_number+`</span><br>(`+val.time+`)
                                                    </div>
                                                    <div class="align-self-center ml-3">
                                                        <span class="">Total Complete Trips : `+val.trip_complete_count+`</span><br>
                                                        <span class="">Total Cancel Trips : `+val.trip_cancel_count+`</span>
                                                    </div>
                                                    <div class="align-self-center ml-3">
                                                        <span class="">Today Completed Trips : `+val.trip_today_complete_count+`</span><br>
                                                        <span class="">Today Cancel Trips : `+val.trip_today_cancel_count+`</span>
                                                    </div>
                                                </a>
                                            </li>`;

                                    });
                                    $(".media-list").html(text);
                                    $('#roleModel').modal('show');
                                    $('.submit').removeClass('disabled');
                                }
                                else if(typeof data.data.drivers == "object"){
                                    Object.entries(data.data.drivers,).forEach(([i, val]) => {
                                    // jQuery.each( data.data.drivers, function( i, val ) {
                                        // console.log(val);
                                        text += `<li class="">
                                                <a href="#" class="media select_driver" id="`+val.id+`" data-value="`+val.firstname+` `+val.lastname+`">
                                                    <div class="mr-3"><img src="`+val.profile_pic+`" class="rounded-circle" width="40" height="40" alt=""></div>
                                                    <div class="media-body">
                                                        <div class="media-title font-weight-semibold">`+val.firstname+` `+val.lastname+`</div>
                                                        <span class="">`+val.driver.vehicletype.vehicle_name+`</span><br>
                                                        <span class="">`+val.phone_number+`</span>
                                                    </div>
                                                    <div class="align-self-center ml-3">
                                                        <span class="">Total Complete Trips : `+val.trip_complete_count+`</span><br>
                                                        <span class="">Total Cancel Trips : `+val.trip_cancel_count+`</span>
                                                    </div>
                                                    <div class="align-self-center ml-3">
                                                        <span class="">Today Completed Trips : `+val.trip_today_complete_count+`</span><br>
                                                        <span class="">Today Cancel Trips : `+val.trip_today_cancel_count+`</span>
                                                    </div>
                                                </a>
                                            </li>`;

                                    });
                                    if(text != ""){
                                        $(".media-list").html(text);
                                        $('#roleModel').modal('show');
                                        $('.submit').removeClass('disabled');
                                    }
                                    else{
                                        swal({
                                            title: "{{ __('error') }}",
                                            text: "No Driver Found",
                                            icon: "error",
                                        }).then((value) => {        
                                            $('.submit').removeClass('disabled');
                                            // window.location.href = "dispatch-request-view/"+data.data.result.id;
                                        });
                                    }
                                }
                                else{
                                    swal({
                                        title: "{{ __('error') }}",
                                        text: "No Driver Found",
                                        icon: "error",
                                    }).then((value) => {     
                                        $('.submit').removeClass('disabled');   
                                        // window.location.href = "dispatch-request-view/"+data.data.result.id;
                                    });
                                }
                            }
                            // if(data.data.result.data.manual_trip == 'MANUAL'){
                            //     $("#trip_id").val(data.data.result.data.id);
                            //     if(data.data.drivers.length > 0){
                            //         jQuery.each( data.data.drivers, function( i, val ) {
                            //             text += `<li class="">
                            //                     <a href="#" class="media select_driver" id="`+val.id+`" data-value="`+val.firstname+` `+val.lastname+`">
                            //                         <div class="mr-3"><img src="`+val.profile_pic+`" class="rounded-circle" width="40" height="40" alt=""></div>
                            //                         <div class="media-body">
                            //                             <div class="media-title font-weight-semibold">`+val.firstname+` `+val.lastname+`</div>
                            //                             <span class="">`+val.driver.vehicletype.vehicle_name+`</span><br>
                            //                             <span class="">`+val.phone_number+`</span>
                            //                         </div>
                            //                         <div class="align-self-center ml-3">
                            //                             <span class="">Total Complete Trips : `+val.trip_complet_count+`</span><br>
                            //                             <span class="">Total Cancel Trips : `+val.trip_cancel_count+`</span>
                            //                         </div>
                            //                         <div class="align-self-center ml-3">
                            //                             <span class="">Today Completed Trips : `+val.trip_today_complet_count+`</span><br>
                            //                             <span class="">Today Cancel Trips : `+val.trip_today_cancel_count+`</span>
                            //                         </div>
                            //                     </a>
                            //                 </li>`;

                            //         });
                            //         $(".media-list").html(text);
                            //         $('#roleModel').modal('show');
                            //     }
                            //     else{
                            //         swal({
                            //             title: "{{ __('success') }}",
                            //             text: "No Driver Found",
                            //             icon: "success",
                            //         }).then((value) => {        
                            //             // window.location.href = "dispatch-request-view/"+data.data.result.id;
                            //         });
                            //     }
                            // }
                            else{
                                searchDriver();
                                trip_id = data.data.data.id;
                                setInterval(function () {
                                    $.ajax({
                                        url: "{{ url('/get-dispatch-request') }}/"+data.data.data.id,
                                        type: "GET",
                                        dataType: 'json',
                                        success: function (datas) {
                                            if(datas.data.is_driver_started == 1){
                                                swal({
                                                    title: "{{ __('success') }}",
                                                    text: datas.message,
                                                    icon: "success",
                                                }).then((value) => {        
                                                    window.location.href = "dispatch-request-view/"+datas.data.id;
                                                });
                                            }
                                            else{
                                                swal({
                                                    title: "{{ __('errors') }}",
                                                    text: datas.message,
                                                    icon: "error",
                                                }).then((value) => {        
                                                    window.location.href = "{{  route('dispatcherTripList') }}";
                                                });
                                            }
                                        }
                                    })
                                }, 30000);
                            }
                        }
                    }
                    else{
                        $(".bg-danger").show();
                        $("#error_message").text(data.message);
                        $('.submit').removeClass('disabled');
                        setTimeout(function(){ $(".bg-danger").fadeOut(3000); }, 10000);
                    }
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    var err = eval("(" + xhr.responseText + ")");
                    $(".bg-danger").show();
                    $("#error_message").text(err.message);
                    $('.submit').removeClass('disabled');
                    setTimeout(function(){ $(".bg-danger").fadeOut(3000); }, 10000);
                }
            });
        }
        else{
            $(this).removeClass('disabled');
        }
    });

    $(document).on('click','#trip_cancel',function(){
        console.log(trip_id);
        $.ajax({
            url: "{{ url('admin-trip-cancel') }}/"+trip_id,
            type: "GET",
            dataType: 'json',
            success: function (data) {
                if(data.success){
                    swal({
                        title: "{{ __('Error') }}",
                        text: data.message,
                        icon: "error",
                    }).then((value) => {        
                        window.location.href = "dispatcher-trip-list";
                    });
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                
            }
        });
    })

    $(document).on('click','#saveBtn',function(){
        var trip_id = $("#trip_id").val();
        var slug = $("#driver_id").val();

        $.ajax({
            url: "{{ url('assign-driver-trip') }}/"+trip_id+"/"+slug,
            type: "GET",
            dataType: 'json',
            success: function (data) {
                if(data.success){
                    swal({
                        title: "{{ __('success') }}",
                        text: data.message,
                        icon: "success",
                    }).then((value) => {        
                        window.location.href = "dispatch-request-view/"+trip_id;
                    });
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                
            }
        });
    })

    function searchDriver(){
        $.blockUI({ 
            message: '<h1><i class="icon-spinner4 spinner"></i> <br>Loading...<br> Searching for driver. Please wait</h1><button id="trip_cancel" class="btn btn-danger">Cancel</button>',
            timeout: 30000, //unblock after 2 seconds
            overlayCSS: {
                backgroundColor: '#1b2024',
                opacity: 0.8,
                zIndex: 1200,
                cursor: 'wait'
            },
            css: {
                border: 0,
                color: '#fff',
                padding: 0,
                zIndex: 1201,
                backgroundColor: 'transparent'
            },
            onUnblock: function() { 
                // searchDriver();
            } 
        });
    };


</script>
<script type="text/javascript">
    var i =1;
    var message = "{{session()->get('message')}}";

    if(message){
        swal({
            title: "{{ __('errors') }}",
            text: message,
            icon: "error",
        }).then((value) => {        
            // window.location.href = "../driver-document/"+$('#driver_id').val();
        });
    }

</script>
<script>
    // google.maps.event.addDomListener(window, 'load', initMap);
    var map = '';
    var directionsDisplay;
    var directionsService;
    
    var markers = '';
    var originPlaceId = '';
    var destinationPlaceId = '';
    var stopPlaceId = [];
    var originInput = '';
    var originAutocomplete = '';

    function clearData(even) {
        if(even == 'pickup'){
            $("#pickup_point").val('');
            $("#pickup_point_lat").val('');
            $("#pickup_point_lng").val('');
            $("#pickup_point_lng_id").val('');
            $("#rental_pickup_point").val('');
            $("#rental_pickup_point_lat").val('');
            $("#rental_pickup_point_lng").val('');
            $("#rental_pickup_point_lng_id").val('');
            $("#outstation_pickup_point").val('');
            $("#outstation_pickup_point_lat").val('');
            $("#outstation_pickup_point_lng").val('');
            $("#outstation_pickup_point_lng_id").val('');
            originPlaceId = '';
        }
        else if(even == 'stop'){
            $("#stop_point").val('');
            $("#stop_point_lat").val('');
            $("#stop_point_lng").val('');
            $("#stop_point_lng_id").val('');
        }
        else if(even == 'drop'){
            $("#drop_point").val('');
            $("#drop_point_lat").val('');
            $("#drop_point_lng").val('');
            $("#drop_point_lng_id").val('');
            destinationPlaceId = '';
        }
        directionsDisplay.setMap(null);
        if(markers != "" && !Array.isArray(markers)){
            markers.setMap(null);
        }

    }

    function getAddress(even) {
        if(even == 'pickup'){
            // if($("#pickup_point").val().length > 10){ 
                originInput = document.getElementById("pickup_point");
                originAutocomplete = new google.maps.places.Autocomplete(
                    originInput
                );
                originAutocomplete.setFields(["place_id","geometry"]);
            // }
        }
        else if(even == 'drop'){
            // if($("#drop_point").val().length > 10){ 
                var originInput = document.getElementById("drop_point");
                originAutocomplete = new google.maps.places.Autocomplete(
                    originInput
                );
                originAutocomplete.setFields(["place_id","geometry"]);
            // }
        }
        else if(even == 'stop'){
            // if($("#stop_point").val().length > 10){ 
                var originInput = document.getElementById("stop_point");
                var originAutocomplete = new google.maps.places.Autocomplete(
                    originInput
                );
                originAutocomplete.setFields(["place_id","geometry"]);
            // }
        }
        else if(even == 'rental_pickup'){
            // if($("#rental_pickup_point").val().length > 10){ 
                var originInput = document.getElementById("rental_pickup_point");
                originAutocomplete = new google.maps.places.Autocomplete(
                    originInput
                );
                originAutocomplete.setFields(["place_id","geometry"]);
            // }
        }
        // else if(even == 'rental_drop'){
        //     if($("#rental_drop_point").val().length > 3){ 
        //         var originInput = document.getElementById("rental_drop_point");
        //         var originAutocomplete = new google.maps.places.Autocomplete(
        //             originInput
        //         );
        //         originAutocomplete.setFields(["place_id","geometry"]);
        //     }
        // }
        else if(even == 'outstation_pickup'){
            if($("#outstation_pickup_point").val().length > 10){ 
                var originInput = document.getElementById("outstation_pickup_point");
                originAutocomplete = new google.maps.places.Autocomplete(
                    originInput
                );
                originAutocomplete.setFields(["place_id","geometry"]);
            }
        }
        if(originAutocomplete){
            // originAutocomplete.setComponentRestrictions({'country': ['IN','MR']});
            originAutocomplete.bindTo("bounds", map);
            // originAutocomplete.radius(500);
            originAutocomplete.addListener("place_changed", (event) => {
                var place = originAutocomplete.getPlace();
                if (even == 'pickup') {
                    originPlaceId = place.place_id;
                    if(!destinationPlaceId){
                        // console.log(!Array.isArray(marker));
                        if(markers != "" && !Array.isArray(markers)){
                            markers.setMap(null);
                        }
                        markers = new google.maps.Marker({
                            position: { lat: place.geometry.location.lat(), lng: place.geometry.location.lng() },
                            map,
                            draggable: true,
                            label: 'A',
                            // title: 
                        });
                        markers.addListener('dragend', handleEvent);
                        map.setZoom(12);
                        map.setCenter(markers.getPosition());
                    }
                    $("#pickup_point_lat").val(place.geometry.location.lat());
                    $("#pickup_point_lng").val(place.geometry.location.lng());
                    $("#pickup_point_lng_id").val(originPlaceId);
                    getVehicles()
                } else if(even == 'drop') {
                    if(markers != "" && !Array.isArray(markers)){
                        markers.setMap(null);
                    }
                    destinationPlaceId = place.place_id;
                    $("#drop_point_lat").val(place.geometry.location.lat());
                    $("#drop_point_lng").val(place.geometry.location.lng());
                    $("#drop_point_lng_id").val(destinationPlaceId);
                    getVehicles()
                } else if(even == 'stop') {
                    stopPlaceId = [];
                    stopPlaceId.push({
                        location: $("#stop_point").val(),
                        stopover: true,
                    });
                    $("#stop_point_lat").val(place.geometry.location.lat());
                    $("#stop_point_lng").val(place.geometry.location.lng());
                    $("#stop_point_lng_id").val(place.place_id);
                    getVehicles()
                } else if(even == 'rental_pickup') {
                    originPlaceId = place.place_id;
                    // if(!destinationPlaceId){
                        if(markers != "" && !Array.isArray(markers)){
                            markers.setMap(null);
                        }
                        markers = new google.maps.Marker({
                            position: { lat: place.geometry.location.lat(), lng: place.geometry.location.lng() },
                            map,
                            draggable: true,
                            label: 'A',
                            // title: 
                        });
                        markers.addListener('dragend', handleEvent);
                        map.setZoom(12);
                        map.setCenter(markers.getPosition());
                    // }
                    $("#rental_pickup_point_lat").val(place.geometry.location.lat());
                    $("#rental_pickup_point_lng").val(place.geometry.location.lng());
                    $("#rental_pickup_point_lng_id").val(place.place_id);
                    assionPromoRental()
                }else if(even == 'outstation_pickup') {
                    originPlaceId = place.place_id;
                    // if(!destinationPlaceId){
                        if(markers != "" && !Array.isArray(markers)){
                            markers.setMap(null);
                        }
                        markers = new google.maps.Marker({
                            position: { lat: place.geometry.location.lat(), lng: place.geometry.location.lng() },
                            map,
                            draggable: true,
                            label: 'A',
                            // title: 
                        });
                        markers.addListener('dragend', handleEvent);
                        map.setZoom(12);
                        map.setCenter(markers.getPosition());
                    // }
                    $("#outstation_pickup_point_lat").val(place.geometry.location.lat());
                    $("#outstation_pickup_point_lng").val(place.geometry.location.lng());
                    $("#outstation_pickup_point_lng_id").val(place.place_id);
                    // getVehicles()
                }
                // else if(even == 'rental_drop') {
                //     destinationPlaceId = place.place_id;
                //     $("#rental_drop_point_lat").val(place.geometry.location.lat());
                //     $("#rental_drop_point_lng").val(place.geometry.location.lng());
                //     $("#rental_drop_point_lng_id").val(place.place_id);
                //     // getVehicles()
                // }

                if (!originPlaceId || !destinationPlaceId) {
                    return;
                }
                else{
                    // marker.setMap(null);
                    route();
                }
            });
        }

    }

    function route(){
        var trip = $("#trip_type").val();
        if(trip == 'local'){
            originPlaceId = $("#pickup_point_lng_id").val();
            destinationPlaceId = $("#drop_point_lng_id").val();
            stopPlaceId = [];
            if($("#stop_point").val() != ""){
                stopPlaceId.push({
                    location: $("#stop_point").val(),
                    stopover: true,
                });
            }
        }
        else if(trip == 'rental'){
            originPlaceId = $("#rental_pickup_point_lng_id").val();
            destinationPlaceId = $("#rental_drop_point_lng_id").val();
        }
        else if(trip == 'outstation'){
            originPlaceId = $("#outstation_pickup_point_lng_id").val();
            destinationPlaceId = $("#outstation_drop_point_lng_id").val();
        }
        // else if(trip == 'outstation'){
        //     var data = $('#request_form2').serialize();
        // }
        if(stopPlaceId.length > 0){
            var request = {
                // origin: start,
                // destination: end, 
                origin: { placeId: originPlaceId },
                destination: { placeId: destinationPlaceId },
                waypoints: stopPlaceId,
                travelMode: google.maps.TravelMode.DRIVING,
                avoidTolls: true,
            };
        }
        else{
            var request = {
                // origin: start,
                // destination: end,
                origin: { placeId: originPlaceId },
                destination: { placeId: destinationPlaceId },
                travelMode: google.maps.TravelMode.DRIVING,
                avoidTolls: true,
            };
        }
        // console.log(request);
        directionsService.route(request, function(response, status) {
            if (status == google.maps.DirectionsStatus.OK) {
                directionsDisplay.setDirections(response);
                directionsDisplay.setMap(map);
                google.maps.event.addListener(directionsDisplay, 'directions_changed', some_method);
            } else {
                alert("Directions Request from " + start.toUrlValue(6) + " to " + end.toUrlValue(6) + " failed: " + status);
            }
        });
    }


    $(document).on('click','.change_button',function(){
        var drop_point = $("#drop_point").val();
        var drop_point_lat = $("#drop_point_lat").val();
        var drop_point_lng = $("#drop_point_lng").val();
        var drop_point_lng_id = $("#drop_point_lng_id").val();
        
        $("#drop_point").val($("#stop_point").val());
        $("#drop_point_lat").val($("#stop_point_lat").val());
        $("#drop_point_lng").val($("#stop_point_lng").val());
        $("#drop_point_lng_id").val($("#stop_point_lng_id").val());

        $("#stop_point").val(drop_point);
        $("#stop_point_lat").val(drop_point_lat);
        $("#stop_point_lng").val(drop_point_lng);
        $("#stop_point_lng_id").val(drop_point_lng_id);

        route();
        getVehicles();
    })

    $(document).on('click','.get_location',function(){
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(showPosition);
        } else { 
            x.innerHTML = "Geolocation is not supported by this browser.";
        }
    })

    function getLocationPlace(lat,lng){
        var geocoder = new google.maps.Geocoder();
        var latlng = new google.maps.LatLng(lat, lng);
        geocoder.geocode({'latLng': latlng}, function(results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                if (results[1]) {
                    if($("#trip_type").val() == "local"){
                        $("#pickup_point_lat").val(lat);
                        $("#pickup_point_lng").val(lng);
                        $("#pickup_point").val(results[0].formatted_address);
                        $("#pickup_point_lng_id").val(results[0].place_id);
                        getVehicles();
                    }
                    else if($("#trip_type").val() == "rental"){
                        $("#rental_pickup_point_lat").val(lat);
                        $("#rental_pickup_point_lng").val(lng);
                        $("#rental_pickup_point").val(results[0].formatted_address);
                        $("#rental_pickup_point_lng_id").val(results[0].place_id);
                        assionPromoRental();
                    }
                    else if($("#trip_type").val() == "outstation"){
                        $("#outstation_pickup_point_lat").val(lat);
                        $("#outstation_pickup_point_lng").val(lng);
                        $("#outstation_pickup_point").val(results[0].formatted_address);
                        $("#outstation_pickup_point_lng_id").val(results[0].place_id);
                        assionPromoOutstation();
                    }
                    originPlaceId = results[0].place_id;
                    if(markers != "" && !Array.isArray(markers)){
                        markers.setMap(null);
                    }
                    if(destinationPlaceId){
                        route(); 
                    }
                    else{
                        markers = new google.maps.Marker({
                            position: { lat: lat, lng: lng },
                            map,
                            draggable: true,
                            label: 'A',
                            title: results[0].formatted_address
                        });
                        markers.addListener('dragend', handleEvent);
                        map.setZoom(12);
                        map.setCenter(markers.getPosition());
                    }
                } else {
                    alert("No results found");
                }
            } else {
                alert("Geocoder failed due to: " + status);
            }
        });
    }

    function handleEvent(event) {
        var lat = event.latLng.lat();
        var lng = event.latLng.lng();
        getLocationPlace(lat,lng);
    }

    function some_method() {
        var response = directionsDisplay.getDirections();
        var route = response.routes[0];
        var path = response.routes[0].overview_path;
        var legs = response.routes[0].legs;
        if(legs.length > 1){
            for (i=0;i<legs.length;i++) {
                var pickup_details = legs[i].start_location.toUrlValue(6).split(",");
                var stop_details = legs[i].end_location.toUrlValue(6).split(",");
                if(i == 0){
                    var geocoder = new google.maps.Geocoder();
                    var latlng = new google.maps.LatLng(pickup_details[0],pickup_details[1]);
                    geocoder.geocode({'latLng': latlng}, function(results, status) {
                        if (status == google.maps.GeocoderStatus.OK) {
                            if (results[1]) {
                                $("#pickup_point_lat").val(pickup_details[0]);
                                $("#pickup_point_lng").val(pickup_details[1]);
                                $("#pickup_point").val(results[0].formatted_address);
                                $("#pickup_point_lng_id").val(results[0].place_id);
                                originPlaceId = results[0].place_id;
                            } 
                        }
                    });
                    geocoder = new google.maps.Geocoder();
                    latlng = new google.maps.LatLng(stop_details[0],stop_details[1]);
                    geocoder.geocode({'latLng': latlng}, function(results, status) {
                        if (status == google.maps.GeocoderStatus.OK) {
                            if (results[1]) {
                                $("#stop_point_lat").val(stop_details[0]);
                                $("#stop_point_lng").val(stop_details[1]);
                                $("#stop_point").val(results[0].formatted_address);
                                $("#stop_point_lng_id").val(results[0].place_id);
                            } 
                        }
                    });
                }
                if(i == 1){
                    geocoder = new google.maps.Geocoder();
                    latlng = new google.maps.LatLng(stop_details[0],stop_details[1]);
                    geocoder.geocode({'latLng': latlng}, function(results, status) {
                        if (status == google.maps.GeocoderStatus.OK) {
                            if (results[1]) {
                                $("#drop_point_lat").val(stop_details[0]);
                                $("#drop_point_lng").val(stop_details[1]);
                                $("#drop_point").val(results[0].formatted_address);
                                $("#drop_point_lng_id").val(results[0].place_id);
                                destinationPlaceId = results[0].place_id;
                                getVehicles();
                            } 
                        }
                    });
                }
            }
        }
        else{
            var pickup_details = legs[0].start_location.toUrlValue(6).split(",");
            var stop_details = legs[0].end_location.toUrlValue(6).split(",");
            var geocoder = new google.maps.Geocoder();
            var latlng = new google.maps.LatLng(pickup_details[0],pickup_details[1]);
            geocoder.geocode({'latLng': latlng}, function(results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    if (results[1]) {
                        $("#pickup_point_lat").val(pickup_details[0]);
                        $("#pickup_point_lng").val(pickup_details[1]);
                        $("#pickup_point").val(results[0].formatted_address);
                        $("#pickup_point_lng_id").val(results[0].place_id);
                        $("#outstation_pickup_point_lat").val(pickup_details[0]);
                        $("#outstation_pickup_point_lng").val(pickup_details[1]);
                        $("#outstation_pickup_point").val(results[0].formatted_address);
                        $("#outstation_pickup_point_lng_id").val(results[0].place_id);
                        originPlaceId = results[0].place_id;
                    } 
                }
            });
            geocoder = new google.maps.Geocoder();
            latlng = new google.maps.LatLng(stop_details[0],stop_details[1]);
            geocoder.geocode({'latLng': latlng}, function(results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    if (results[1]) {
                        $("#drop_point_lat").val(stop_details[0]);
                        $("#drop_point_lng").val(stop_details[1]);
                        $("#drop_point").val(results[0].formatted_address);
                        $("#drop_point_lng_id").val(results[0].place_id);
                        $("#outstation_drop_point_lat").val(stop_details[0]);
                        $("#outstation_drop_point_lng").val(stop_details[1]);
                        $("#outstation_drop_point_lng_id").val(results[0].place_id);
                        destinationPlaceId = results[0].place_id;
                        getVehicles();
                    } 
                }
            });
        }
        // var lat = event.latLng.lat();
        // var lng = event.latLng.lng();
        // getLocationPlace(lat,lng);
    }

    function showPosition(position) {
        var lat = position.coords.latitude;
        var lng = position.coords.longitude;
        getLocationPlace(lat,lng);
    }
    
</script>
<script src="https://maps.google.com/maps/api/js?key={{settingValue('google_map_key')}}&sensor=false&libraries=places"></script>

<script src="https://www.gstatic.com/firebasejs/7.19.0/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/7.19.0/firebase-database.js"></script>
<!-- TODO: Add SDKs for Firebase products that you want to use https://firebase.google.com/docs/web/setup#available-libraries -->
<script src="https://www.gstatic.com/firebasejs/7.19.0/firebase-analytics.js"></script>
<script src="https://maps.googleapis.com/maps/api/place/autocomplete/json?radius=500&components=country:IN&location=11.0150677,76.9824808&input=session('data')&sensor=false&key={{settingValue('google_map_key')}}"></script>

<script type="text/javascript">

    var showFreeDrivers = true;
    var showUnAvailableDrivers = true;

    var heatmapData = [];
    var pickLat = [];
    var pickLng = [];
    var default_lat = '18.087569035831216';
    var default_lng = '-15.962143967987672';
    var driverLat,driverLng,bearing,type;
    var marker = [];
    var online_marker = [];
    var offline_marker = [];
    var zone = $("#zone").val();
    var already_has_lat_lng_list = false;
    if (zone != '') {
        var service_location_json = JSON.parse(zone);
        var length = service_location_json.length;
        service_location_json[length] = {};
        service_location_json[length].lat = parseFloat(service_location_json[0].lat);
        service_location_json[length].lng = parseFloat(service_location_json[0].lng);
        var already_has_lat_lng_list = true;
    }

    
    const firebaseConfig = {
        // apiKey: "AIzaSyA9jY5xaHUFd4iYVyc6RnnMjM8fRbIPzjA",
        // authDomain: "roda-f7fb4.firebaseapp.com",
        // databaseURL: "https://roda-f7fb4-default-rtdb.firebaseio.com/",
        // projectId: "roda-f7fb4",
        // storageBucket: "roda-f7fb4.appspot.com",
        // messagingSenderId: "824273214716",
        // appId: "1:824273214716:web:ab519153a60f82e96fcb79",
        // measurementId: "G-TZJRR2459H"

        apiKey: "{{settingValue('firebase_api_key')}}",
        authDomain: "{{settingValue('firebase_auth_domain')}}",
        databaseURL: "{{settingValue('firebase_database_url')}}",
        projectId: "{{settingValue('firebase_project_id')}}",
        storageBucket: "{{settingValue('firebase_storage_bucket')}}",
        messagingSenderId: "{{settingValue('firebase_messaging_sender_id')}}",
        appId: "{{settingValue('firebase_app_id')}}",
        measurementId: "{{settingValue('firebase_measurement_id')}}"

        };

        // Initialize Firebase
        firebase.initializeApp(firebaseConfig);
        firebase.analytics();
        var num = 5000;
        var tripRef = firebase.database().ref('drivers');
        tripRef.on('value', async function(snapshot) {
            num++;
            // console.log(num);
            if(num < 5000){
                return false;
            }
            num = 0;
            var data = snapshot.val();
            // console.log(data);
            await loadDriverIcons(data);
        });

        map = new google.maps.Map(document.getElementById('map'), {
            center: new google.maps.LatLng(default_lat, default_lng),
            zoom: 9,
            mapTypeId: 'roadmap'
        });
        if (already_has_lat_lng_list) {
            var flightPath = new google.maps.Polygon({
                path: service_location_json,
                // fillColor: '#FF0000',
                fillOpacity: 0,
                // geodesic: true,
                strokeColor: '#000',
                strokeOpacity: 1.0,
                strokeWeight: 2,
            });
            flightPath.setMap(map);
        }

        // map.addListener('click', function(e) {
        //     if(markers != "" && !Array.isArray(markers)){
        //          markers.setMap(null);
        //     }
        //     getLocationPlace(e.latLng.lat(),e.latLng.lng());
        // });

        directionsDisplay = new google.maps.DirectionsRenderer({
            // suppressMarkers: true,
            draggable:true
        });
        directionsService = new google.maps.DirectionsService();

        var iconBase = '{{ asset("backend") }}';
        var icons = {
          available: {
            name: 'Available',
            key: 'free',
            icon: iconBase + '/on-trip.png'
          },
          ontrip: {
            name: 'OnTrip',
            key: 'ontrip',
            icon: iconBase + '/available.png'
          }
        };

        var legend = document.getElementById('legend');
        var type_count = document.getElementById('type_count');
        var drivers_list = document.getElementById('drivers_list');

        for (var key in icons) {
            var type = icons[key];
            var name = type.name;
            var icon = type.icon;
            var div = document.createElement('div');
            div.innerHTML = `<input type="checkbox" id="${name}" name="${name}" value="${type.name}" onchange='legendChanged("${type.key}")' checked> <img src="${icon}"> <b>${name}</b> (<b id="${key}_1"></b>) <br><br><br>`;
            legend.appendChild(div);
        }

        map.controls[google.maps.ControlPosition.RIGHT_BOTTOM].push(legend);
        map.controls[google.maps.ControlPosition.TOP_LEFT].push(type_count);
        map.controls[google.maps.ControlPosition.TOP_RIGHT].push(drivers_list);

        function loadDriverIcons(data){

            online_marker = [];
            offline_marker = [];
            var view_list = new Object();

          deleteAllMarkers();
          var driverslist = '<option value="">Select Driver</option>';

          // var result = Object.entries(data);
          Object.entries(data).forEach(([key, val]) => {  
              // var infowindow = new google.maps.InfoWindow({
              //     content: contentString
              // });
                
                // console.log(val);
              var typeOfFirstName = typeof val.first_name;

              if( typeOfFirstName == 'string' ) {
                var firstName  = val.first_name+' '+val.last_name;
              }else {
                var firstName  = '-';
              }

              var typeOfPhone = typeof val.phone_number;
              if( typeOfPhone == "string" ) {
                var phone  = val.phone_number;
              }else {
                var phone  = '-';
              }
              var name = "";
              var typeOfType = typeof val.type;
                if(val.type == "suv"){
                    name = "SUV";
                    icons['available'].icon = iconBase + '/suv.png';
                }
                else if(val.type == "sedan"){
                    name = "Sedan";
                    icons['available'].icon = iconBase + '/on-trip.png';
                }
                else if(val.type == "bajaj-auto"){
                    icons['available'].icon = iconBase + '/auto.png';
                    name = "Auto";
                }
                else if(val.type == "mini"){
                    name = "Mini";
                    icons['available'].icon = iconBase + '/mini.png';
                }
                else if(val.type == "eeco"){
                    name = "Eeco";
                    icons['available'].icon = iconBase + '/eeco.png';
                }
                // console.log(val);
            var service_category = val.service_category;
            // console.log(service_category);
            if(service_category){
                var service_category_array = service_category.split(',');
            }
            else{
                var service_category_array = [];
            }
            //   service_category = service_category.replace(/,/g, ", ");

              var contentString = '<div id="content">' +
            '<div id="siteNotice">' +
            '</div>' +
            '<h3 id="firstHeading" class="firstHeading"> <i class="fa fa-id-card" aria-hidden="true"></i> '+ firstName +'</h3>' +
            '<h6 id="bodyContent" style="color:#4d5051"> <i class="fa fa-phone" aria-hidden="true"></i> ' + phone +''+
            '</h6>' +
            '<h6 id="bodyContent" style="color:#4d5051"> <i class="fa fa-phone" aria-hidden="true"></i> ' + name +''+
            '</h6>' +
            '<h6 id="bodyContent" style="color:#4d5051"> <i class="fa fa-phone" aria-hidden="true"></i> ' + service_category +
            '</h6>' +
            '</div>';

            var infowindow = new google.maps.InfoWindow({
              content: contentString
            });

            if( typeof val.l !=  'undefined'  ) {
              var iconImg = '';


              if(val.is_available == true){
                  iconImg = icons['available'].icon;
              }else{
                  iconImg = icons['ontrip'].icon;
              }

            //   var date = new Date();
            //   var timestamp = date.getTime();
            //   var currentTime = +new Date(timestamp - 1 * 60000);

            let date = new Date();
            let timestamp = date.getTime();
            let conditional_timestamp = timestamp - (5 *60 *1000);
            //   console.log(val);
                var trip_category = $("#trip_type").val();
                if(trip_category == "local"){
                    trip_category = "LOCAL";
                }
                else if(trip_category == "rental"){
                    trip_category = "RENTAL";
                }
                else if(trip_category == "outstation"){
                    trip_category = "OUTSTATION";
                }
            // console.log(trip_category);
            // console.log(jQuery.inArray(trip_category, service_category_array));
            if(trip_category == "" || jQuery.inArray(trip_category, service_category_array) !== -1){
                if(conditional_timestamp < val.updated_at){
                    if(val.is_active){
                        if(view_list[val.type] == undefined){
                            view_list[val.type] = 0;
                        }

                        // var count = view_list[val.type] + 1;
                        view_list[val.type] = view_list[val.type] + 1;
                    }
                    
                if(val.is_available == true && showFreeDrivers == true && val.is_active == true ) {
                    if(firstName != "-"){
                        driverslist += '<option value="'+val.l[0]+','+val.l[1]+','+firstName+',https://cdn3.iconfinder.com/data/icons/professions-ultra/60/Professions_Ultra_090_-_Taxi_Driver-512.png">'+firstName+' '+phone+' ('+name+')</option>';
                    }
                    var carIcon = new google.maps.Marker({
                        position: new google.maps.LatLng(val.l[0],val.l[1]),
                        icon : {
                            url: iconImg, // url
                            origin: new google.maps.Point(0, 0),
                            anchor: new google.maps.Point(17, 34),
                            scaledSize: new google.maps.Size(45, 30)

                            // scaledSize: new google.maps.Size(50, 50), // scaled size
                            // origin: new google.maps.Point(0,0), // origin
                            // anchor: new google.maps.Point(0, 0) // anchor
                        },
                        map: map,
                        
                    });
                    
                    carIcon.addListener('click', function() {
                        infowindow.open(map, carIcon);

                        // alert( val.first_name );
                        // infowindow.open(map, beachMarker);
                    });


                    // deleteAllMarkers();
                    marker.push(carIcon);
                    online_marker.push(carIcon);
                    carIcon.setMap(map);

                }else if(val.is_available == false && showUnAvailableDrivers == true && val.is_active == true ) {
                    if(firstName != "-"){
                        driverslist += '<option value="'+val.l[0]+','+val.l[1]+','+firstName+',https://cdn3.iconfinder.com/data/icons/gig-economy-3/512/TaxiDriver-gigeconomy-job-occupation-profession-man-driver-512.png">'+firstName+' '+phone+' ('+name+')</option>';
                    }
                    var carIcon = new google.maps.Marker({
                        position: new google.maps.LatLng(val.l[0],val.l[1]),
                        icon : {
                            url: iconImg, // url
                            origin: new google.maps.Point(0, 0),
                            anchor: new google.maps.Point(17, 34),
                            scaledSize: new google.maps.Size(45, 30)
                            // scaledSize: new google.maps.Size(40, 40), // scaled size
                            // origin: new google.maps.Point(0,0), // origin
                            // anchor: new google.maps.Point(0, 0) // anchor
                        },
                        map: map,
                    
                    });

                    carIcon.addListener('click', function() {
                        infowindow.open(map, carIcon);
                    });

                    // deleteAllMarkers();
                    
                    marker.push(carIcon);
                    offline_marker.push(carIcon);
                    carIcon.setMap(map);

                }
                    
                $("#available_1").text(online_marker.length);
                $("#ontrip_1").text(offline_marker.length);

                }
            }

            
            }


          });
          $("#select_driver").html(driverslist);
        $("#type_count").empty();
        $("#type_count").html('<h5> Types Count </h5>');
        $.each( view_list, function( key, value ) {
            var div1 = document.createElement('div');
            var name = "";
            var color = "";
            if(key == "suv"){
                name = "SUV";
                color = "#63b2e4";
            }
            else if(key == "sedan"){
                name = "Sedan";
                color = "#70b100";
            }
            else if(key == "bajaj-auto"){
                name = "Auto";
                color = "#000";
            }
            else if(key == "mini"){
                name = "Mini";
                color = "#fad20b";
            }
            else if(key == "eeco"){
                name = "Eeco";
                color = "#b31373";
            }
            div1.innerHTML = `<div class="count_list" style="color:`+color+`"><b>${name}</b> : <b>${value}</b><div>`;
            type_count.appendChild(div1);
        });
        
          
          
        }


        function deleteAllMarkers() {
            for(var i=0;i<marker.length;i++){
                marker[i].setMap(null);
            }
            marker = [];
        }

        function legendChanged(test)
        {
          // deleteAllMarkers();

            if( test == 'free') {

              if( showFreeDrivers == true ) {
                showFreeDrivers = false;
              }else {
                showFreeDrivers = true;
              }    

            }

            if( test == 'ontrip') {

              if( showUnAvailableDrivers == true ) {
                showUnAvailableDrivers = false;
              }else {
                showUnAvailableDrivers = true;
              }    

            }
            $(".customMarker").remove();
            num = 5000;
            tripRef.on('value', async function(snapshot) {
                num++;
                if(num < 5000){
                    return false;
                }
                num = 0;
                var data = snapshot.val();
                await loadDriverIcons(data);
            });

        }
        function CustomMarker(latlng, map, imageSrc,name) {
            this.latlng_ = latlng;
            this.imageSrc = imageSrc;
            this.name = name;
            // Once the LatLng and text are set, add the overlay to the map.  This will
            // trigger a call to panes_changed which should in turn call draw.
            this.setMap(map);
        }
        CustomMarker.prototype = new google.maps.OverlayView();

CustomMarker.prototype.draw = function () {
    // Check if the div has been created.
    var div = this.div_;
    if (!div) {
        // Create a overlay text DIV
        div = this.div_ = document.createElement('div');
        // Create the DIV representing our CustomMarker
        div.className = "customMarker"


        var img = document.createElement("img");
        img.src = this.imageSrc;
        div.appendChild(img);
        var name = document.createElement("lable");
        name.innerHTML = this.name;
        div.appendChild(name);

        // Then add the overlay to the DOM
        var panes = this.getPanes();
        panes.overlayImage.appendChild(div);
    }

    // Position the overlay 
    var point = this.getProjection().fromLatLngToDivPixel(this.latlng_);
    if (point) {
        div.style.left = point.x + 'px';
        div.style.top = point.y + 'px';
    }
};

CustomMarker.prototype.remove = function () {
    // Check if the overlay was on the map and needs to be removed.
    if (this.div_) {
        this.div_.parentNode.removeChild(this.div_);
        this.div_ = null;
    }
};

CustomMarker.prototype.getPosition = function () {
    return this.latlng_;
};

        var driversmark='';
    $(document).on('change','#select_driver',function(){
        $(".customMarker").remove();
        if(driversmark != ""){
            driversmark.setMap(null);
        }
        var latlang = $(this).val().split(",");
        driversmark = new google.maps.Marker({
                            position: { lat: parseFloat(latlang[0]), lng: parseFloat(latlang[1]) },
                            map,});
        driversmark.addListener('dragend', handleEvent);
        new CustomMarker(new google.maps.LatLng(latlang[0],latlang[1]), map, latlang[3],latlang[2])
        map.setZoom(17);
        map.setCenter(driversmark.getPosition());
        if(driversmark != ""){
            driversmark.setMap(null);
        }
    })

    function removedriverslist(){
        $(".customMarker").remove();
    }
</script>

<script>
$(document).ready(function() {
    $('#outstation_drop_point').select2();
    $('#select_driver').select2();
});
</script>

@endsection
