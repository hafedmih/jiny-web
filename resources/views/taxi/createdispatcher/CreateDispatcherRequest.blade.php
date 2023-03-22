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
        height:425px;
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
    }
    #legend img {
        vertical-align: middle;
        width:45px;
        height:30px;
    }
    
    #type_count {
        font-family: Arial, sans-serif;
        background: #fff;/*transparent;*/
        padding: 5px;
        margin: 5px;
        border: 3px solid #000;
        width: 320px;
        height: 76px;
        font-size: 10px;
    }
    #type_count h5 {
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
    @media only screen and (max-width: 700px) {
        .card-form{
            margin: 0px;
        }
    }
    #drivers_list{
        width: 100%;
        height: 300px;
        overflow: hidden;
        display: flex;
        overflow-y: scroll;
        align-content: flex-start;
    }
    #drivers_list li{
        width: 100%;
    }
    #drivers_list li a{
        color: #000;
    }
    ::-webkit-scrollbar {
        width: 5px;
        height: 5px;
    }
    ::-webkit-scrollbar-thumb {
        background: #ffd60c; 
        border-radius: 10px;
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
    <div class="card-form card-collapsed animated bounceInDown col-md-4" id="card_local_trip">
        
        <div class="">
            <div class="card">
                <form class="wizard-form steps-validation" id="request_form" action="#" method="post" data-fouc autocomplete="off">
                    @csrf
                    <h6>User Details</h6>
                    <fieldset class="fieldset">
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
                                    <label>{{ __('category') }}: <span class="text-danger">*</span></label>
                                    <select class="form-control required" name="category" onchange="getVehicles()" onchange="changeCategory()" id="category">
                                        <option value="">{{ __('category') }}</option>
                                        <option value="LOCAL">{{ __('local') }}</option>
                                        <option value="RENTAL">{{ __('rental') }}</option>
                                        <option value="OUTSTATION">{{ __('outstation') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group form-group-feedback form-group-feedback-right">
                                    <label>{{ __('pickup_point') }}: <span class="text-danger">*</span></label>
                                    <input type="text" name="pickup" id="pickup_point" onkeyup="getAddress('pickup')" class="form-control required" placeholder="Pickup Point">
                                    <div class="form-control-feedback form-control-feedback-lg" style="cursor: pointer;margin-top: 25px;">
                                        <i class="icon-location3 get_location" style="font-size:20px"></i>
                                    </div>
                                    <input type="hidden" name="pickup_lat" id="pickup_point_lat">
                                    <input type="hidden" name="pickup_lng" id="pickup_point_lng">
                                    <input type="hidden" name="pickup_lng_id" id="pickup_point_lng_id">
                                </div>
                            </div>
                            <div class="col-md-12" id="add_rows">
                                <div class="form-group form-group-feedback form-group-feedback-right">
                                    <label>Stop Point: <span class="text-danger">*</span></label>
                                    <input type="text" name="stop" id="stop_point" onkeyup="getAddress('stop')" class="form-control" placeholder="Stop Point">
                                    <div class="form-control-feedback form-control-feedback-lg" style="cursor: pointer;margin-top: 25px;">
                                        <i class="icon-sort change_button"></i>
                                    </div>
                                    <input type="hidden" name="stop_lat" id="stop_point_lat">
                                    <input type="hidden" name="stop_lng" id="stop_point_lng">
                                    <input type="hidden" name="stop_lng_id" id="stop_point_lng_id">
                                </div>
                                <div class="form-group text-right"><label class="badge badge-danger rounded-pill remove_button"><i class="icon-x mr-2"></i> Remove Stop</label></div>
                            </div>
                            <div class="col-md-12" id="droppoint">
                                <div class="form-group">
                                    <label>{{ __('drop_point') }}: <span class="text-danger">*</span></label>
                                    <input type="text" name="drop" id="drop_point" onkeyup="getAddress('drop')" class="form-control required" placeholder="Drop Point">
                                    <input type="hidden" name="drop_lat" id="drop_point_lat">
                                    <input type="hidden" name="drop_lng" id="drop_point_lng">
                                    <input type="hidden" name="drop_lng_id" id="drop_point_lng_id">
                                </div>
                            </div>
                            <div class="col-md-12 text-right" id="button_view">
                                <label class="badge badge-danger rounded-pill add_button"><i class="icon-plus2 mr-2"></i> Add one stop</label>
                            </div>
                            <div class="col-md-12" id="rental_point">
                                <div class="form-group">
                                    <label>{{ __('package_list') }}: <span class="text-danger">*</span></label>
                                    <select class="form-control required" name="rental_id" onchange="getVehicles()" id="rental_id">
                                        <option value="">{{ __('package_list') }}</option>
                                        @foreach($package_list as $value)
                                        <option value="{{ $value->id }}">{{ $value->name }}({{$value->hours}} / {{$value->km}})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12" id="outstationpoint">
                                <div class="form-group">
                                    <label>{{ __('outstation_list') }}: <span class="text-danger">*</span></label>
                                    <select class="form-control required" name="outstation_id" onchange="getVehicles()" id="outstation_drop_point">
                                        <option value="">{{ __('outstation_list') }}</option>
                                        @foreach($outstanding_drops as $value)
                                        <option value="{{ $value->drop }}">{{ $value->drop }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>{{ __('type') }}: <span class="text-danger">*</span></label>
                                    <select class="form-control required" name="types" onchange="getDrivers()" id="types">
                                        <option value="">{{ __('type') }}</option>
                                    </select>
                                </div>
                            </div>
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
                            <div class="col-md-12" id="ride_timer">
                                <div class="form-group">
                                    <div class="form-check form-check-inline">
                                        <label class="form-check-label">
                                            <input type="radio" name="trip_type" class="form-input-styled change-time required" data-fouc value="RIDE_NOW">
                                            Ride Now
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <label class="form-check-label">
                                            <input type="radio" name="trip_type" id="RIDE_LATER" class="form-input-styled change-time required" data-fouc value="RIDE_LATER">
                                            Ride Later
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12" id="trip_date_time">
                                <div class="form-group">
                                    <label>Date & Time: <span class="text-danger">*</span></label>
                                    <input type="datetime-local" name="ride_date_time" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-12" id="overall_amount">
                                <div class="form-group">
                                    <label>{{ __('assign_amount') }}: <span class="text-danger">*</span></label>
                                    <input type="text" name="assign_amount" id="assign_amount" class="form-control required" placeholder="{{ __('assign_amount') }}">
                                </div>
                            </div>
                            <div class="col-md-12" id="km_amount">
                                <div class="form-group">
                                    <label>{{ __('assign_amount_km') }}: <span class="text-danger">*</span></label>
                                    <input type="text" name="assign_amount_km" id="assign_amount_km" class="form-control required" placeholder="{{ __('assign_amount_km') }}">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Driver Notes: <span class="text-danger">*</span></label>
                                    <textarea name="driver_notes" class="form-control"></textarea>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                    <h6>Drivers</h6>
                    <fieldset class="fieldset">
                        <div class="form-group required">
                            <label class="col-form-label">Driver Search</label>
                            <div class="">
                                <input type="text" class="form-control" name="drivers" id="drivers">
                                <input type="hidden" class="form-control" name="driver_id" id="driver_id">
                            </div>
                        </div>
                        <div class="row" id="drivers_list"></div>
                    </fieldset>
                    <button type="reset" class="resets"></button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-8" id="map" ></div>
    <div id="legend"><h5> Legend </h5></div>
    <div id="type_count"><h5> Types Count </h5></div>
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
    $("#outstationpoint").hide();
    $("#rental_point").hide();
    $("#km_amount").hide();

    $(".nav-link").on('click',function(){
        $(".card-form").removeClass("card-collapsed");
        $(".tab-content").css("display", "block")
    });

    $(document).on('change',".change-time",function(){
        var value = $(this).val();

        if(value == 'RIDE_LATER'){
            $("#trip_date_time").show();
        }
        else{
            $("#trip_date_time").hide();
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

    $(document).on('change',"#outstation_drop_point",function(){
        var values = $(this).val();
        $.ajax({
            url: "{{ url('get-outstation-location') }}/"+values,
            type: "GET",
            dataType: 'json',
            success: function (data) {
                var lats = data.data.drop_lat;
                var lngs = data.data.drop_lng;
                var drop = data.data.drop;
                $("#drop_point_lat").val(parseFloat(lats));
                $("#drop_point_lng").val(parseFloat(lngs));
                $("#drop_point").val(drop);
                var geocoder = new google.maps.Geocoder;
                var latlng = {lat: parseFloat(lats), lng: parseFloat(lngs)};

                geocoder.geocode({'location': latlng}, function(results, status) {
                    if (status === google.maps.GeocoderStatus.OK) {
                        if (results[1]) {
                            $("#drop_point_lng_id").val(results[1].place_id);
                            if(markers != "" && !Array.isArray(markers)){
                                markers.setMap(null);
                            }
                            route();
                            getVehicles();
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

    $(document).on('click','.setPickup',function(){
        var pick_lat = $(this).attr('data-value');
        pick_lat = pick_lat.split(";");
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
            label: 'A',
            draggable: true,
            // title: 
        });
        markers.addListener('dragend', handleEvent);
        map.setZoom(16);
        map.setCenter(markers.getPosition());
        route();
        getVehicles();
        // $("#pickup_point_lng_id").val(pick_address);
    });

    $(document).on('click','.setDrop',function(){
        var drop_lat = $(this).attr('data-value');
        drop_lat = drop_lat.split(";");
        $("#drop_point_lat").val(drop_lat[0]);
        $("#drop_point_lng").val(drop_lat[1]);
        $("#drop_point").val(drop_lat[2]);
        $("#drop_point_lng_id").val(drop_lat[3]);
        $(".setDrop").removeClass( "active" );
        $(this).addClass( "active" );
        if(!Array.isArray(marker)){
            marker.setMap(null);
        }
        route();
        getVehicles();
        // $("#drop_point_lng_id").val(pick_address);
    });

    $(document).on('change','#category',function (){
        if($('#category').val() == "RENTAL"){
            $("#button_view").hide();
            $("#droppoint").hide();
            $("#outstationpoint").hide();
            $("#add_rows").hide();
            $("#rental_point").show();
            $("#drop_point").val('');
            $("#drop_point_lat").val('');
            $("#drop_point_lng").val('');
            $("#drop_point_lng_id").val('');
            $("#stop_point").val('');
            $("#stop_point_lat").val('');
            $("#stop_point_lng").val('');
            $("#stop_point_lng_id").val('');
            $("#outstation_id").val('');
            $("#rental_id").val('');
            $("#km_amount").hide();
            $("#overall_amount").show();
            $("#trip_date_time").hide();
            $("#ride_timer").show();
            $("#RIDE_LATER").attr("checked", false);
            destinationPlaceId = '';
            directionsDisplay.setMap(null);
            if(markers != "" && !Array.isArray(markers)){
                markers.setMap(null);
            }
            markers = new google.maps.Marker({
                position: { lat: parseFloat($('#pickup_point_lat').val()), lng: parseFloat($('#pickup_point_lng').val()) },
                map,
                label: 'A',
                draggable: true,
                // title: 
            });
            markers.addListener('dragend', handleEvent);
            map.setZoom(16);
            map.setCenter(markers.getPosition());
        }
        else if($('#category').val() == "OUTSTATION"){
            $("#button_view").hide();
            $("#droppoint").hide();
            $("#outstationpoint").show();
            $("#rental_point").hide();
            $("#drop_point").val('');
            $("#drop_point_lat").val('');
            $("#drop_point_lng").val('');
            $("#drop_point_lng_id").val('');
            $("#stop_point").val('');
            $("#stop_point_lat").val('');
            $("#stop_point_lng").val('');
            $("#stop_point_lng_id").val('');
            $("#outstation_id").val('');
            $("#rental_id").val('');
            $("#km_amount").show();
            $("#trip_date_time").show();
            $("#ride_timer").hide();
            $("#RIDE_LATER").attr("checked", true);
            destinationPlaceId = '';
            directionsDisplay.setMap(null);
            if(markers != "" && !Array.isArray(markers)){
                markers.setMap(null);
            }
            markers = new google.maps.Marker({
                position: { lat: parseFloat($('#pickup_point_lat').val()), lng: parseFloat($('#pickup_point_lng').val()) },
                map,
                label: 'A',
                draggable: true,
                // title: 
            });
            markers.addListener('dragend', handleEvent);
            map.setZoom(16);
            map.setCenter(markers.getPosition());
        }
        else{
            $("#droppoint").show();
            $("#button_view").show();
            $("#outstationpoint").hide();
            $("#rental_point").hide();
            $("#drop_point").val('');
            $("#drop_point_lat").val('');
            $("#drop_point_lng").val('');
            $("#drop_point_lng_id").val('');
            $("#stop_point").val('');
            $("#stop_point_lat").val('');
            $("#stop_point_lng").val('');
            $("#stop_point_lng_id").val('');
            $("#outstation_id").val('');
            $("#rental_id").val('');
            $("#km_amount").hide();
            $("#trip_date_time").hide();
            $("#ride_timer").show();
            $("#RIDE_LATER").attr("checked", false);
            destinationPlaceId = '';
            directionsDisplay.setMap(null);
            if(markers != "" && !Array.isArray(markers)){
                markers.setMap(null);
            }
            markers = new google.maps.Marker({
                position: { lat: parseFloat($('#pickup_point_lat').val()), lng: parseFloat($('#pickup_point_lng').val()) },
                map,
                label: 'A',
                draggable: true,
                // title: 
            });
            markers.addListener('dragend', handleEvent);
            map.setZoom(16);
            map.setCenter(markers.getPosition());
        }
    });
    
    // $(document).on('blur',"#drop_point",function(){
    function getVehicles() {
        var texts = "";
        var formData = new FormData();
        formData.append('pickup_lat',$('#pickup_point_lat').val());
        formData.append('pickup_long',$('#pickup_point_lng').val());
        formData.append('drop_lat',$('#drop_point_lat').val());
        formData.append('drop_long',$('#drop_point_lng').val());
        formData.append('pickup_address',$('#pickup_point').val());
        formData.append('drop_address',$('#drop_point').val());
        formData.append('category',$('#category').val());
        formData.append('outstation_id',$('#outstation_drop_point').val());
        formData.append('rental_id',$('#rental_id').val());
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
            url: "{{ route('getVehicleTypesList') }}",
            type: "POST",
            dataType: 'json',
            contentType : false,
            processData: false,
            success: function (data) {
                data = data.data;
                var text = '<option value="">Select Types</option>';
                if($('#category').val() != "LOCAL"){
                    jQuery.each( data, function( i, val ) {
                        text += `<option value="`+val.get_vehicle.slug+`">`+val.get_vehicle.vehicle_name+`</option>`;
                    });
                }
                else{
                    jQuery.each( data.get_zone_price, function( i, val ) {
                        text += `<option value="`+val.get_type.slug+`">`+val.get_type.vehicle_name+`</option>`;
                    });
                }
                $("#types").html(text);
                
            },
            error: function (xhr, ajaxOptions, thrownError) {
                var err = eval("(" + xhr.responseText + ")");
                if(err.success == false){
                    $("#error_message").text(err.message);
                    $(".bg-danger").show();
                    // setTimeout(function(){ $(".bg-danger").fadeOut(2000); }, 10000);
                }
            }
        });
    }

    function getDrivers() {
        $(".bg-danger").hide();
        $.ajax({
            data: $('#request_form').serialize(),
            url: "{{ route('getVehicleDriversList') }}",
            type: "POST",
            dataType: 'json',
            success: function (data) {
                data = data.data;
                var text = '';
                if(Array.isArray(data) && data.length > 0){
                    jQuery.each( data, function( i, val ) {
                        // console.log(val);
                        text += `<li class="card">
                                <a href="#" class="card-body media select_driver" id="`+val.id+`" data-value="`+val.firstname+` `+val.lastname+`">
                                    <div class="mr-3"><img src="`+val.profile_pic+`" class="rounded-circle" width="40" height="40" alt=""></div>
                                    <div class="media-body">
                                        <div class="media-title font-weight-semibold">`+val.firstname+` `+val.lastname+`</div>
                                        <span class="">`+val.driver.vehicletype.vehicle_name+`</span><br>
                                        <span class="">`+val.phone_number+`</span><br>(`+val.time+`)
                                    </div>
                                </a>
                            </li>`;
                    });
                    $("#drivers_list").html(text);
                }
                
            },
            error: function (xhr, ajaxOptions, thrownError) {
                var err = eval("(" + xhr.responseText + ")");
                if(err.success == false){
                    $("#error_message").text(err.message);
                    $(".bg-danger").show();
                    // setTimeout(function(){ $(".bg-danger").fadeOut(2000); }, 10000);
                }
            }
        });
    }

    $(document).on('click','.card-body',function(){
        var valu = $(this).attr("for");
        $("#"+valu).prop('checked',true);
        $(".clickCheck").parents(".card-body").removeClass('bg-success');
        $(this).addClass('bg-success');
    });

    $("#drivers").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#drivers_list li").filter(function() {
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
        var data = $('#request_form').serialize();
        $.ajax({
            data: data,
            url: "{{ route('createDispatchRequestSetAmount') }}",
            type: "POST",
            dataType: 'json',
            success: function (data) {
                $('.submit').removeClass('disabled');
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
                    swal({
                        title: "{{ __('success') }}",
                        text: data.message,
                        icon: "success",
                    }).then((value) => {        
                        window.location.href = "dispatch-request-view/"+data.data.id;
                    });
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
    });

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


    function getAddress(even) {
        if(even == 'pickup'){
            if($("#pickup_point").val().length > 3){ 
                originInput = document.getElementById("pickup_point");
                originAutocomplete = new google.maps.places.Autocomplete(
                    originInput
                );
                originAutocomplete.setFields(["place_id","geometry"]);
            }
        }
        else if(even == 'drop'){
            if($("#drop_point").val().length > 3){ 
                var originInput = document.getElementById("drop_point");
                originAutocomplete = new google.maps.places.Autocomplete(
                    originInput
                );
                originAutocomplete.setFields(["place_id","geometry"]);
            }
        }
        else if(even == 'stop'){
            if($("#stop_point").val().length > 3){ 
                var originInput = document.getElementById("stop_point");
                var originAutocomplete = new google.maps.places.Autocomplete(
                    originInput
                );
                originAutocomplete.setFields(["place_id","geometry"]);
            }
        }
        if(originAutocomplete){
            originAutocomplete.setComponentRestrictions({'country': ['IN']});
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
                            label: 'A',
                            draggable: true,
                            // title: 
                        });
                        markers.addListener('dragend', handleEvent);
                        map.setZoom(16);
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
                }
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
        originPlaceId = $("#pickup_point_lng_id").val();
        destinationPlaceId = $("#drop_point_lng_id").val();
        stopPlaceId = [];
        if($("#stop_point").val() != ""){
            stopPlaceId.push({
                location: $("#stop_point").val(),
                stopover: true,
            });
        }
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
                    $("#pickup_point_lat").val(lat);
                    $("#pickup_point_lng").val(lng);
                    $("#pickup_point").val(results[0].formatted_address);
                    $("#pickup_point_lng_id").val(results[0].place_id);
                    getVehicles();
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
                        // markers.addListener('drag', handleEvent);
                        markers.addListener('dragend', handleEvent);
                        map.setZoom(16);
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

    function toggleBounce() {
        if (markers.getAnimation() !== null) {
            markers.setAnimation(null);
        } else {
            markers.setAnimation(google.maps.Animation.BOUNCE);
        }
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

    async function getPlaceId(lat, lng){
        console.log(lat, lng);
        var geocoder = new google.maps.Geocoder();
        var latlng = new google.maps.LatLng(lat, lng);
        await geocoder.geocode({'latLng': latlng}, function(results, status) {
            console.log(lat, lng);
            if (status == google.maps.GeocoderStatus.OK) {
                console.log(lat, lng);
                if (results[1]) {
                    console.log(lat, lng);
                    $data = {
                        lat : lat,
                        lng : lng,
                        address : results[0].formatted_address,
                        place_id : results[0].place_id
                    };
                    console.log($data);
                    return $data;
                } else {
                    alert("No results found");
                }
            } else {
                alert("Geocoder failed due to: " + status);
            }
        });
    }
    
</script>
<script src="https://maps.google.com/maps/api/js?key={{settingValue('google_map_key')}}&sensor=false&libraries=places"></script>

<script src="https://www.gstatic.com/firebasejs/7.19.0/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/7.19.0/firebase-database.js"></script>
<!-- TODO: Add SDKs for Firebase products that you want to use https://firebase.google.com/docs/web/setup#available-libraries -->
<script src="https://www.gstatic.com/firebasejs/7.19.0/firebase-analytics.js"></script>
<script src="https://maps.googleapis.com/maps/api/place/autocomplete/json?radius=500&components=country:IN&location=11.0150677,76.9824808&input=session('data')&sensor=false&key={{ config('app.map_key')}}"></script>

<script type="text/javascript">

    var showFreeDrivers = true;
    var showUnAvailableDrivers = true;

    var heatmapData = [];
    var pickLat = [];
    var pickLng = [];
    var default_lat = '11.0176052';
    var default_lng = '76.9586527';
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

        function loadDriverIcons(data){

            online_marker = [];
            offline_marker = [];
            var view_list = new Object();

          deleteAllMarkers();

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
            var service_category = val.service_category;
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
                // var trip_category = $("#trip_type").val();
                // if(trip_category == "local"){
                //     trip_category = "LOCAL";
                // }
                // else if(trip_category == "rental"){
                //     trip_category = "RENTAL";
                // }
                // else if(trip_category == "outstation"){
                //     trip_category = "OUTSTATION";
                // }
            // console.log(trip_category);
            // console.log(jQuery.inArray(trip_category, service_category_array));
            // if(trip_category == "" || jQuery.inArray(trip_category, service_category_array) !== -1){
                if(conditional_timestamp < val.updated_at){
                    if(val.is_active){
                        if(view_list[val.type] == undefined){
                            view_list[val.type] = 0;
                        }

                        // var count = view_list[val.type] + 1;
                        view_list[val.type] = view_list[val.type] + 1;
                    }
                    
                if(val.is_available == true && showFreeDrivers == true && val.is_active == true ) {
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
            // }

            
            }


          });
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

        
</script>

<script>
$(document).ready(function() {
    $('#outstation_drop_point').select2();
});
</script>


<!-- <script src="https://apis.mapmyindia.com/advancedmaps/api/{{settingValue('google_map_key')}}/map_sdk?layer=vector&v=2.0&callback=initMap1" defer async></script>
<script src="https://apis.mapmyindia.com/advancedmaps/api/{{settingValue('google_map_token')}}/map_sdk_plugins"></script>
<script>
    var map;
    var end = {label:'',geoposition:""};
    var start = {label:'',geoposition:""};
            function initMap1(){
                map = new MapmyIndia.Map('map-canvas', {
                    center: [28.61, 77.23],
                    zoomControl: true,
                    location: false,
                    search: false
                    // backgroundColor:"red",
                }); 

                new MapmyIndia.search(document.getElementById("pickup_point"),pickup);
                new MapmyIndia.search(document.getElementById("rental_pickup_point"),rental_pickup);
                new MapmyIndia.search(document.getElementById("drop_point"),drop);
                new MapmyIndia.search(document.getElementById("stop_point"),stops);

                var marker;
                function pickup(data) { 
                    
                    if(data)
                    {
                        if(data.error){
                            $.ajax({
                                url: "{{ route('gendrateMapToken') }}",
                                type: "GET",
                                dataType: 'json',
                                success: function (datas) {
                                    console.log(data);
                                    location.reload();
                                }
                            });
                        }
                        var dt=data[0];
                        if(!dt) return false;
                        var eloc=dt.eLoc;
                        var place=dt.placeName+", "+dt.placeAddress;
                        start.label = place;
                        start.geoposition = eloc;
                        $("#pickup_point_lng_id").val(eloc);

                        /*Use elocMarker Plugin to add marker*/
                        // if(marker) marker.remove();
                        var url1 = "http:/apis.mapmyindia.com/advancedmaps/v1/{{settingValue('google_map_token')}}/geo_code?addr=";
                        console.log(url1);
                        $.ajax({
                            url: "http:/apis.mapmyindia.com/advancedmaps/v1/{{settingValue('google_map_token')}}/geo_code?addr=".eloc,
                            type: "GET",
                            dataType: 'json',
                            success: function (data) {
                                console.log("--------------------");
                                console.log(data);
                                console.log("--------------------");
                                
                            },
                            error: function (data) {
                                console.log("#################################");
                                console.log(data);
                                console.log("#################################");
                                
                            }
                            });
                        marker=new MapmyIndia.elocMarker({map:map,eloc:eloc,popupHtml:place,popupOptions:{openPopup:true}}).fitbounds();
                        // console.log(marker);
                        console.log(marker.map.transform);
                        console.log(marker.map.transform._center);
                        // console.log(marker.map.transform._center.lat);
                        // console.log(marker.map.transform._center.lng);
                        console.log(marker.map.transform.center.lat);
                        console.log(marker.map.transform.center.lng);
                        $("#pickup_point_lat").val(marker.map.transform.center.lat);
                        $("#pickup_point_lng").val(marker.map.transform.center.lng);
                        route();
                    }
                }   
                function rental_pickup(data) { 
                    if(data)
                    {
                        if(data.error){
                            $.ajax({
                                url: "{{ route('gendrateMapToken') }}",
                                type: "GET",
                                dataType: 'json',
                                success: function (datas) {
                                    location.reload();
                                }
                            });
                        }
                        var dt=data[0];
                        if(!dt) return false;
                        var eloc=dt.eLoc;
                        var place=dt.placeName+", "+dt.placeAddress;
                        start.label = place;
                        start.geoposition = eloc;
                        $("#rental_pickup_point_lng_id").val(eloc);

                        /*Use elocMarker Plugin to add marker*/
                        if(marker) marker.remove();
                        marker=new MapmyIndia.elocMarker({map:map,eloc:eloc,popupHtml:place,popupOptions:{openPopup:true}}).fitbounds();
                        route();
                    }
                }   
                function drop(data) {
                    if(data)
                    {
                        var dt=data[0];
                        if(!dt) return false;
                        var eloc=dt.eLoc;
                        var place=dt.placeName+", "+dt.placeAddress;
                        end.label = place;
                        end.geoposition = eloc;
                        $("#drop_point_lng_id").val(eloc);
                        /*Use elocMarker Plugin to add marker*/
                        if(marker) marker.remove();
                        marker=new MapmyIndia.elocMarker({map:map,eloc:eloc,popupHtml:place,popupOptions:{openPopup:true}}).fitbounds();
                        
                        // console.log(marker);
                        // console.log(marker.map.transform);
                        // console.log(marker.map.transform._center);
                        // console.log(marker.map.transform.center.lat);
                        // console.log(marker.map.transform.center.lng);
                        $("#drop_point_lat").val(marker.map.transform.center.lat);
                        $("#drop_point_lng").val(marker.map.transform.center.lng);
                        if(marker) marker.remove();
                        route();
                    }
                }   
                function stops(data) {
                    if(data)
                    {
                        var dt=data[0];
                        if(!dt) return false;
                        var eloc=dt.eLoc;
                        var place=dt.placeName+", "+dt.placeAddress;
                        /*Use elocMarker Plugin to add marker*/
                        if(marker) marker.remove();
                        // marker=new MapmyIndia.elocMarker({map:map,eloc:eloc,popupHtml:place,popupOptions:{openPopup:true}}).fitbounds();
                        route();
                    }
                }   
            }

            function route(){
                // if(start.geoposition && end.geoposition){
                    var direction_option={
                        map:map,
                        start:start,
                        // end:{label:'Gandhipuram Coimbatore, Tamil Nadu, 641012',geoposition:"61XOVG"},
                        end:end
                    }
                    var direction_plugin=MapmyIndia.direction(direction_option); 
                    console.log(direction_plugin);
                    console.log(direction_plugin.All_routes);
                    console.log(direction_plugin['All_routes']['act']);
                    console.log(direction_plugin['All_routes']['act']['getBunods']);
                // }
            }
        </script> -->

@endsection
