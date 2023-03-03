@extends('layouts.app')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<style>

.select2-container{
    width: 100% !important;
    height:auto;
}

.zone-map{
    position:relative;
}
.zone-map ul{
    list-style-type:none;
    position: absolute;
    right: 30px;
    top: 110px;
}
.zone-map ul li{
    margin-bottom:35px;
    cursor: pointer;
}
.zone-map ul li a{
    padding:10px 12px;
    background:#556ee6;
    border-radius:50%;
}
#map {
    height: 650px;
    width: 90%;
    left: 10px;
}
#search-box {
   /* width: 70%;
    margin: 20px;
    padding: 15px;
    position: absolute;
    left: 40px;
    top: 5px;
    z-index: 1;*/
}
#loader {
    display: none;
    text-align: center;
    padding: 10px;
    margin: 25px;
}
</style>



<div class="content">

    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title">Manage Zone</h5>
            <div class="header-elements">
                <div class="list-icons">
                    <a href="{{ route('zone') }}" id="add_new_btn" class="btn bg-purple btn-sm legitRipple"><i class="icon-menu3 mr-2"></i> List</a>
                </div>
            </div>
        </div>
    </div>
    <div class="card">  
        <div class="card-header bg-white header-elements-inline">
            <h6 class="card-title">Zone Management</h6>
            <div class="header-elements">
                <div class="list-icons">
                    <a class="list-icons-item" data-action="collapse"></a>
                    <a class="list-icons-item" data-action="reload"></a>
                    <a class="list-icons-item" data-action="remove"></a>
                </div>
            </div>
        </div>
        <form class="wizard-form steps-validation" id="form_id" action="{{ route('updateZone') }}" data-fouc autocomplete="off" method="POST" enctype="multipart/form-data">
            @csrf
            <input name="zone_id" id="zone_id" value="{{$zone->id}}" type="hidden" />
            <h6>Service Location</h6>
            <fieldset>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="col-form-label col-sm-6"><b>Zone Level</b><span class="text-danger">*</span></label>
                            <div class="col-sm-8">
                                <select class="form-control" name="zone_level" id="zone_level" required>
                                    <option value="">Select Zone Level</option>
                                    <option value="PRIMARY" {{(old('zone_level',$zone->zone_level) == 'PRIMARY' )?'selected':''}}>
                                    Primary</option>
                                    <option value="SECONDARY" {{(old('zone_level',$zone->zone_level) == 'SECONDARY' )?'selected':''}}>
                                        Secondary </option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="col-form-label col-sm-6"><b>Driver Assign method</b><span class="text-danger">*</span></label>
                            <div class="col-sm-8">
                                <select class="form-control" name="driver_assign_method" id="driver_assign_method" required>
                                    <option value="">Select Driver Assign Method</option>
                                    <option value="DISTANCE" {{(old('driver_assign_method',$zone->driver_assign_method) == 'DISTANCE' )?'selected':''}}>
                                        Distance</option>
                                    <option value="FIFO" {{(old('driver_assign_method',$zone->driver_assign_method) == 'FIFO' )?'selected':''}}>
                                        FIFO </option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="col-form-label col-md-4"><b>Select Country</b><span class="text-danger">*</span></label>
                            <div class="form-group col-md-8">
                                <select name="country" id="country" class="form-control required" required>
                                    <option value="" selected disabled>Select Country</option>
                                    @foreach ($countries as $country)
                                        <option value="{{$country->id}}" @if($zone->country == $country->id) selected  @endif>{{$country->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="col-form-label col-lg-6"><b>Service location area (Zone Name)</b><span class="text-danger">*</span></label>
                            <div class="col-lg-8">
                                <input id="zone_name" name="zone_name" value="{{$zone->zone_name}}" placeholder="Service location area" type="text" class="form-control"
                                        value="" required>
                                @if ($errors->has('icon'))
                                    <span class="form-text text-danger">
                                        {{ $errors->first('icon') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="col-form-label col-sm-4"><b>Admin commission type</b><span class="text-danger">*</span></label>
                            <div class="col-sm-8">
                                <select class="form-control" name="admin_commission_type" id="admin_commission_type" required>
                                    <option value="1" {{(old('admin_commission_type',1) == 1 )?'selected':''}} @if($zone->admin_commission_type == '1') selected  @endif >
                                        Percentage</option>
                                    <option value="0" {{(old('admin_commission_type',1) == 0 )?'selected':''}} @if($zone->admin_commission_type == '0') selected  @endif >
                                        Fixed </option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <input type="hidden" id="bounds" name="bounds" value='{{$zone->map_cooder}}'>
                            <label class="col-form-label col-md-4"><b>Admin commission</b><span class="text-danger">*</span></label>
                            <div class="col-md-8">
                                <input id="admin_commission" name="admin_commission" value="{{$zone->admin_commission}}" placeholder="Admin commission" type="text" class="form-control" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="map-canvas" style="width:100%;height:400px;"></div>

            </fieldset>

            <h6>Basic Features</h6>
            <fieldset>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group row">
                            <label class="col-form-label col-lg-12"><b>Select Types</b><span class="text-danger">*</span></label>
                            <div class="col-lg-12">
                                <select multiple class="form-control multiple-select" name="vehicle_type[]" id="vehicle_type" required>
                                    @foreach($vehicleList as $vehicledet)
                                        <option value="{{ $vehicledet->slug }}" @if(in_array($vehicledet->id, explode(',',$zone->types_id))) selected  @endif>{{ $vehicledet->vehicle_name }} </option>
                                    @endforeach
                                   
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group row">
                            <label class="col-form-label col-lg-12"><b>Payment Types</b><span class="text-danger">*</span></label>
                            <div class="col-lg-12">
                                <select multiple="multiple" class="form-control" name="payment_type[]" id="payment_type" required>
                                    <option value="CASH" @if(in_array("CASH", explode(',',$zone->payment_types))) selected  @endif>Cash</option>
                                    <option value="CARD" @if(in_array("CARD", explode(',',$zone->payment_types))) selected  @endif>Card</option>
                                    <option value="WALLET" @if(in_array("WALLET", explode(',',$zone->payment_types))) selected  @endif>Wallet</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><b> Unit </b><span class="text-danger">*</span></label>
                            <select name="unit" id="unit" data-placeholder="Choose a unit..." class="form-control" required>
                                <option value="">Select the Unit</option> 
                                <option value="KM" @if($zone->unit == 'KM') selected  @endif>Kilo Meter</option> 
                                <option value="MILES" @if($zone->unit == 'MILES') selected  @endif>Miles</option> 
                            </select>
                        </div>

                       
                    </div>

                    <div class="col-md-6"><br><br>
                        <div class="form-check mb-0">
                            <label class="form-check-label">
                                <input type="checkbox" id="remember_me" name="non_service_zone" @if($zone->non_service_zone == 'Yes') checked  @endif class="form-input-styled" data-fouc>
                                Non Service Zone
                            </label>
                        </div>
                    </div>
                </div>
            </fieldset>

            <h6>Ride Now</h6>
            <fieldset>
                <table class="table" id="ride_now">
                @foreach($zone->getZonePrice as $key => $values)
                    <tr><td><label class="text-uppercase font-size-sm font-weight-bold">{{$values->getType->vehicle_name}}</label><input type="hidden" name="type_id[{{$key}}]" value="{{$values->getType->slug}}"></td><td><div class="row"><div class="col-md-6"><div class="form-group"><label><b class="base_price">Base Price ({{$zone->unit}})</b><span class="text-danger">*</span>:</label><input type="text" name="ridenow_base_price[{{$key}}]" value="{{$values->ridenow_base_price}}" placeholder="Base Price" class="form-control" required></div></div><div class="col-md-6"><div class="form-group"><label><b>Base Distance</b><span class="text-danger">*</span>:</label><select name="ridenow_base_distance[{{$key}}]" id="ridenow_base_distance" class="form-control" required><option value="" selected disabled>Select Base Distance</option>@for ($i = 1; $i < 15; $i++)<option value="{{$i}}" @if($values->ridenow_base_distance == $i) selected  @endif>{{$i}}</option>@endfor</select></div></div></div><div class="row"><div class="col-md-6"><div class="form-group"><label><b class="price_per_distance">Price per Distance ({{$zone->unit}})</b><span class="text-danger">*</span>:</label><input type="text" name="ridenow_price_per_distance[{{$key}}]" value="{{$values->ridenow_price_per_distance}}" placeholder="Price per Distance" class="form-control" required></div></div><div class="col-md-6"><div class="form-group"><label><b>Price per time</b><span class="text-danger">*</span>:</label><input type="text" name="ridenow_price_per_time[{{$key}}]" value="{{$values->ridenow_price_per_time}}" placeholder="Price per time" class="form-control" required></div></div></div><div class="row"><div class="col-md-4"><div class="form-group"><label><b>Free Waiting time (Minutes)</b><span class="text-danger">*</span>:</label><input type="text" name="ridenow_free_waiting_time[{{$key}}]" value="{{$values->ridenow_free_waiting_time}}" placeholder="Free Waiting time" class="form-control" required></div></div><div class="col-md-4"><div class="form-group"><label><b>Waiting Change</b><span class="text-danger">*</span>:</label><input type="text" name="ridenow_waiting_charge[{{$key}}]" value="{{$values->ridenow_waiting_charge}}" placeholder="Waiting Change" class="form-control" required></div></div><div class="col-md-4"><div class="form-group"><label><b>Cancellation fee</b><span class="text-danger">*</span>:</label><input type="text" name="ridenow_cancellation_fee[{{$key}}]" value="{{$values->ridenow_cancellation_fee}}" placeholder="Cancellation Fee" class="form-control" required></div></div></div></td><tr>
                @endforeach
                </table>
            </fieldset>

            <h6>Ride Later</h6>
            <fieldset>
                <table class="table" id="ride_later">
                @foreach($zone->getZonePrice as $key => $values)
                    <tr><td><label class="text-uppercase font-size-sm font-weight-bold">{{$values->getType->vehicle_name}}</label></td><td><div class="row"><div class="col-md-6"><div class="form-group"><label><b class="base_price">Base Price ({{$zone->unit}})</b><span class="text-danger">*</span>:</label><input type="text" name="ridelater_base_price[{{$key}}]" value="{{$values->ridelater_base_price}}" placeholder="Base Price" class="form-control" required></div></div><div class="col-md-6"><div class="form-group"><label><b>Base Distance</b><span class="text-danger">*</span>:</label><select name="ridelater_base_distance[{{$key}}]" id="ridelater_base_distance" class="form-control" required><option value="" selected disabled>Select Base Distance</option>@for ($i = 1; $i < 15; $i++)<option value="{{$i}}" @if($values->ridelater_base_distance == $i) selected  @endif>{{$i}}</option>@endfor</select></div></div></div><div class="row"><div class="col-md-6"><div class="form-group"><label><b class="price_per_distance">Price per Distance ({{$zone->unit}})</b><span class="text-danger">*</span>:</label><input type="text" name="ridelater_price_per_distance[{{$key}}]" value="{{$values->ridelater_price_per_distance}}" placeholder="Price per Distance" class="form-control" required></div></div><div class="col-md-6"><div class="form-group"><label><b>Price per time</b><span class="text-danger">*</span>:</label><input type="text" name="ridelater_price_per_time[{{$key}}]" value="{{$values->ridelater_price_per_time}}" placeholder="Price per time" class="form-control" required></div></div></div><div class="row"><div class="col-md-4"><div class="form-group"><label><b>Free Waiting time (Minutes)</b><span class="text-danger">*</span>:</label><input type="text" name="ridelater_free_waiting_time[{{$key}}]" value="{{$values->ridelater_free_waiting_time}}" placeholder="Free Waiting time" class="form-control" required></div></div><div class="col-md-4"><div class="form-group"><label><b>Waiting Change</b><span class="text-danger">*</span>:</label><input type="text" name="ridelater_waiting_charge[{{$key}}]" value="{{$values->ridelater_waiting_charge}}" placeholder="Waiting Change" class="form-control" required></div></div><div class="col-md-4"><div class="form-group"><label><b>Cancellation fee</b><span class="text-danger">*</span>:</label><input type="text" name="ridelater_cancellation_fee[{{$key}}]" value="{{$values->ridelater_cancellation_fee}}" placeholder="Cancellation Fee" class="form-control" required></div></div></div></td><tr>
                @endforeach
                </table>
            </fieldset>

            <h6>Surge Price</h6>
            <fieldset>
                <table class="table" id="surge_price">
                    @foreach($zone->getZonePrice as $key => $values)
                    <tr><td><label class="text-uppercase font-size-sm font-weight-bold">{{$values->getType->vehicle_name}}</label><br><button type="button" class="btn bg-green-400 btn-icon rounded-round legitRipple addrow" id="{{$values->getType->slug}}" value="{{$key}}" data-popup="tooltip" data-value="{{count($values->getSurgePrice)}}" title="" data-placement="bottom" data-original-title="Add"> <i class="icon-plus3"></i> </button></td><td class="{{$values->getType->slug}}">
                        @if(count($values->getSurgePrice) != 0)
                        @foreach($values->getSurgePrice as $keys => $value)
                        <hr><div class="row"><div class="col-md-6"><div class="form-group"><label><b>Surge Price</b>:</label><input type="text" name="sruge_price[{{$key}}][{{$keys}}]" value="{{$value->surge_price}}" placeholder="Sruge Price" class="form-control" ><input type="hidden" name="sruge_price_id[{{$key}}][{{$keys}}]" value="{{$value->id}}"></div></div><div class="col-md-6"><div class="form-group"><label><b>Start Time</b>:</label><input type="time" name="start_time[{{$key}}][{{$keys}}]" value="{{$value->start_time}}" class="form-control" ></div></div><div class="col-md-6"><div class="form-group"><label><b>End Time</b>:</label><input type="time" name="end_time[{{$key}}][{{$keys}}]" value="{{$value->end_time}}" class="form-control" ></div></div><div class="col-md-6"><div class="form-group"><label><b>Available Days</b>:</label><select name="available_days[{{$key}}][{{$keys}}][]" id="available_days_{{$key}}_{{$keys}}" multiple="multiple" class="form-control" ><option value="Sunday" @if(in_array("Sunday", explode(',',$value->available_days))) selected  @endif>Sunday</option><option value="Monday" @if(in_array("Monday", explode(',',$value->available_days))) selected  @endif>Monday</option><option value="Tuesday" @if(in_array("Tuesday", explode(',',$value->available_days))) selected  @endif>Tuesday</option><option value="Wednesday" @if(in_array("Wednesday", explode(',',$value->available_days))) selected  @endif>Wednesday</option><option value="Thursday" @if(in_array("Thursday", explode(',',$value->available_days))) selected  @endif>Thursday</option><option value="Friday" @if(in_array("Friday", explode(',',$value->available_days))) selected  @endif>Friday</option><option value="Saturday" @if(in_array("Saturday", explode(',',$value->available_days))) selected  @endif>Saturday</option></select></div></div></div>
                        @endforeach
                        @else
                        <div class="row"><div class="col-md-6"><div class="form-group"><label><b>Surge Price</b>:</label><input type="text" name="sruge_price[{{$key}}][0]" value="" placeholder="Sruge Price" class="form-control" ><input type="hidden" name="sruge_price_id[{{$key}}][0]" value=""></div></div><div class="col-md-6"><div class="form-group"><label><b>Start Time</b>:</label><input type="time" name="start_time[{{$key}}][0]" value="" class="form-control" ></div></div><div class="col-md-6"><div class="form-group"><label><b>End Time</b>:</label><input type="time" name="end_time[{{$key}}][0]" value="" class="form-control" ></div></div><div class="col-md-6"><div class="form-group"><label><b>Available Days</b>:</label><select name="available_days[{{$key}}][0][]" id="available_days_{{$key}}_0" multiple="multiple" class="form-control" ><option value="Sunday">Sunday</option><option value="Monday">Monday</option><option value="Tuesday">Tuesday</option><option value="Wednesday">Wednesday</option><option value="Thursday">Thursday</option><option value="Friday">Friday</option><option value="Saturday" >Saturday</option></select></div></div></div>
                        @endif
                    </td><tr>
                    @endforeach
                            
                
                </table>
            </fieldset>
            <button type="submit" id="submit"></button>
        </form>
    </div>
    



</div>

<script>
$(document).ready(function() {
      

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


$("#submit").hide();
    $(document).on('click','.submit',function(){
        if($("#form_id").valid()){
            // swal({
            //     title: "{{ __('data-added') }}",
            //     text: "{{ __('data-added-successfully') }}",
            //     icon: "success",
            // }).then((value) => {                 
                $("#submit").click();
            // });
        }
    });

    $(document).on('change','#vehicle_type',function(){
        var a = $('#vehicle_type').val();
        var unit = $('#unit').val();
        var form1 = "";
        var form2 = "";
        var form3 = "";
        var s= 0;
        console.log(a);
        $("#ride_now").html(form1);
        $("#ride_later").html(form2);
        $("#surge_price").html(form3);
        for(var i = 0; i < a.length; i++){
            
            $.ajax({
                url: "{{ route('getTypePrices') }}",
                type: "GET",
                dataType: 'json',
                data: {type : a[i],id : $("#zone_id").val()},
                success: function (data) {
                    if(data.success){
                        form1 = '<tr><td><label class="text-uppercase font-size-sm font-weight-bold">'+data.datas.get_type.vehicle_name+'</label><input type="hidden" name="type_id['+s+']" value="'+data.datas.get_type.slug+'"></td><td><div class="row"><div class="col-md-6"><div class="form-group"><label><b class="base_price">Base Price ('+unit+')</b><span class="text-danger">*</span>:</label><input type="text" name="ridenow_base_price['+s+']" value="'+data.datas.ridenow_base_price+'" placeholder="Base Price" class="form-control" required></div></div><div class="col-md-6"><div class="form-group"><label><b>Base Distance</b><span class="text-danger">*</span>:</label><select name="ridenow_base_distance['+s+']" id="ridenow_base_distance_'+s+'" class="form-control" required><option value="" selected disabled>Select Base Distance</option>@for($i = 1; $i < 15; $i++) ';
                        var selected = "";
                        if("{{$i}}" == data.datas.ridenow_base_distance ){ selected = "selected"; }

                        form1 += '<option value="{{$i}}" '+selected+'>{{$i}}</option>@endfor</select></div></div></div><div class="row"><div class="col-md-6"><div class="form-group"><label><b class="price_per_distance">Price per Distance (Kilometer)</b><span class="text-danger">*</span>:</label><input type="text" name="ridenow_price_per_distance['+s+']" value="'+data.datas.ridenow_price_per_distance+'" placeholder="Price per Distance" class="form-control" required></div></div><div class="col-md-6"><div class="form-group"><label><b>Price per time</b><span class="text-danger">*</span>:</label><input type="text" name="ridenow_price_per_time['+s+']" value="'+data.datas.ridenow_price_per_time+'" placeholder="Price per time" class="form-control" required></div></div></div><div class="row"><div class="col-md-4"><div class="form-group"><label><b>Free Waiting time (Minutes)</b><span class="text-danger">*</span>:</label><input type="text" name="ridenow_free_waiting_time['+s+']" value="'+data.datas.ridenow_free_waiting_time+'" placeholder="Free Waiting time" class="form-control" required></div></div><div class="col-md-4"><div class="form-group"><label><b>Waiting Change</b><span class="text-danger">*</span>:</label><input type="text" name="ridenow_waiting_charge['+s+']" value="'+data.datas.ridenow_waiting_charge+'" placeholder="Waiting Change" class="form-control" required></div></div><div class="col-md-4"><div class="form-group"><label><b>Cancellation fee</b><span class="text-danger">*</span>:</label><input type="text" name="ridenow_cancellation_fee['+s+']" value="'+data.datas.ridenow_cancellation_fee+'" placeholder="Cancellation Fee" class="form-control" required></div></div></div></td><tr>';

                        

                        form2 = '<tr><td><label class="text-uppercase font-size-sm font-weight-bold">'+data.datas.get_type.vehicle_name+'</label></td><td><div class="row"><div class="col-md-6"><div class="form-group"><label><b class="base_price">Base Price ('+unit+')</b><span class="text-danger">*</span>:</label><input type="text" name="ridelater_base_price['+s+']" placeholder="Base Price" class="form-control" value="'+data.datas.ridelater_base_price+'" required></div></div><div class="col-md-6"><div class="form-group"><label><b>Base Distance</b><span class="text-danger">*</span>:</label><select name="ridelater_base_distance['+s+']" id="ridelater_base_distance" class="form-control" required><option value="" selected disabled>Select Base Distance</option>@for ($i = 1; $i < 15; $i++)';
                        if("{{$i}}" == data.datas.ridelater_base_distance ){ selected = "selected"; }else{ selected = ""; }
                        form2 += '<option value="{{$i}}" '+selected+'>{{$i}}</option>@endfor</select></div></div></div><div class="row"><div class="col-md-6"><div class="form-group"><label><b>Price per time</b><span class="text-danger">*</span>:</label><input type="text" name="ridelater_price_per_time['+s+']" value="'+data.datas.ridelater_price_per_time+'" placeholder="Price per time" class="form-control" required></div></div><div class="col-md-6"><div class="form-group"><label><b class="price_per_distance">Price per Distance ('+unit+')</b><span class="text-danger">*</span>:</label><input type="text" name="ridelater_price_per_distance['+s+']" value="'+data.datas.ridelater_price_per_distance+'" placeholder="Price per Distance" class="form-control" required></div></div></div><div class="row"><div class="col-md-4"><div class="form-group"><label><b>Free Waiting time (Minutes)</b>:</label><input type="text" name="ridelater_free_waiting_time['+s+']" value="'+data.datas.ridelater_free_waiting_time+'" placeholder="Free Waiting time" class="form-control" required></div></div><div class="col-md-4"><div class="form-group"><label><b>Waiting Change</b><span class="text-danger">*</span>:</label><input type="text" name="ridelater_waiting_charge['+s+']" value="'+data.datas.ridelater_waiting_charge+'" placeholder="Waiting Change" class="form-control" required></div></div><div class="col-md-4"><div class="form-group"><label><b>Cancellation fee</b><span class="text-danger">*</span>:</label><input type="text" name="ridelater_cancellation_fee['+s+']" value="'+data.datas.ridelater_cancellation_fee+'" placeholder="Cancellation Fee" class="form-control" required></div></div></div></td><tr>';

                        form3 = '<tr><td><label class="text-uppercase font-size-sm font-weight-bold">'+data.datas.get_type.vehicle_name+'</label><br><button type="button" class="btn bg-green-400 btn-icon rounded-round legitRipple addrow" id="'+data.datas.get_type.slug+'" value="'+s+'" data-popup="tooltip" data-value="1" title="" data-placement="bottom" data-original-title="Add"> <i class="icon-plus3"></i> </button></td><td class="'+data.datas.get_type.slug+'">';
                        if(data.datas.get_surge_price.length){
                            data.datas.get_surge_price.forEach((element,index) => {
                                var days = element.available_days.split(',');
                                var val = '';
                                form3 += '<div class="row"><div class="col-md-6"><div class="form-group"><label><b>Surge Price</b>:</label><input type="text" name="sruge_price['+s+']['+index+']" placeholder="Sruge Price" value="'+element.surge_price+'" class="form-control" ><input type="hidden" name="sruge_price_id['+s+']['+index+']" value="'+element.id+'"></div></div><div class="col-md-6"><div class="form-group"><label><b>Start Time</b>:</label><input type="time" name="start_time['+s+']['+index+']" value="'+element.start_time+'" class="form-control" ></div></div><div class="col-md-6"><div class="form-group"><label><b>End Time</b>:</label><input type="time" name="end_time['+s+']['+index+']" value="'+element.end_time+'" class="form-control" ></div></div><div class="col-md-6"><div class="form-group"><label><b>Available Days</b>:</label><select name="available_days['+s+']['+index+'][]" id="available_days_'+s+'_'+index+'" multiple="multiple" class="form-control" ><option value="Sunday" ';
                                if(jQuery.inArray("Sunday", days) != -1){ val = "selected"; }else{ val = ""; }
                                form3 += val+'>Sunday</option><option value="Monday" ';
                                if(jQuery.inArray("Monday", days) != -1){ val = "selected"; }else{ val = ""; }
                                form3 += val+'>Monday</option><option value="Tuesday" ';
                                if(jQuery.inArray("Tuesday", days) != -1){ val = "selected"; }else{ val = ""; }
                                form3 += val+'>Tuesday</option><option value="Wednesday" ';
                                if(jQuery.inArray("Wednesday", days) != -1){ val = "selected"; }else{ val = ""; }
                                form3 += val+'>Wednesday</option><option value="Thursday" ';
                                if(jQuery.inArray("Thursday", days) != -1){ val = "selected"; }else{ val = ""; }
                                form3 += val+'>Thursday</option><option value="Friday" ';
                                if(jQuery.inArray("Friday", days) != -1){ val = "selected"; }else{ val = ""; }
                                form3 += val+'>Friday</option><option value="Saturday" ';
                                if(jQuery.inArray("Saturday", days) != -1){ val = "selected"; }else{ val = ""; }
                                form3 += val+'>Saturday</option></select></div></div></div><hr>';
                                $('#available_days_'+s+'_'+index).select2();
                            });
                        }
                        else{
                            form3 += '<div class="row"><div class="col-md-6"><div class="form-group"><label><b>Surge Price</b>:</label><input type="text" name="sruge_price['+s+'][0]" placeholder="Sruge Price" class="form-control" ><input type="hidden" name="sruge_price_id['+s+'][0]" value=""></div></div><div class="col-md-6"><div class="form-group"><label><b>Start Time</b>:</label><input type="time" name="start_time['+s+'][0]" class="form-control" ></div></div><div class="col-md-6"><div class="form-group"><label><b>End Time</b>:</label><input type="time" name="end_time['+s+'][0]" class="form-control" ></div></div><div class="col-md-6"><div class="form-group"><label><b>Available Days</b>:</label><select name="available_days['+s+'][0][]" id="available_days_'+s+'_0" multiple="multiple" class="form-control" ><option value="Sunday">Sunday</option><option value="Monday">Monday</option><option value="Tuesday">Tuesday</option><option value="Wednesday">Wednesday</option><option value="Thursday">Thursday</option><option value="Friday">Friday</option><option value="Saturday">Saturday</option></select></div></div></div>';
                        }
                        form3 += '</td><tr>';
                    }
                    else{
                        form1 = '<tr><td><label class="text-uppercase font-size-sm font-weight-bold">'+data.vehicle.vehicle_name+'</label></td><td><div class="row"><div class="col-md-6"><div class="form-group"><label><b class="base_price">Base Price (Kilometer)</b><span class="text-danger">*</span>:</label><input type="text" name="ridenow_base_price['+s+']" placeholder="Base Price" class="form-control" required></div></div><div class="col-md-6"><div class="form-group"><label><b>Base Distance</b><span class="text-danger">*</span>:</label><select name="ridenow_base_distance['+s+']" id="ridenow_base_distance" class="form-control" required><option value="" selected disabled>Select Base Distance</option>@for ($i = 1; $i < 15; $i++)<option value="{{$i}}" >{{$i}}</option>@endfor</select></div></div></div><div class="row"><div class="col-md-6"><div class="form-group"><label><b class="price_per_distance">Price per Distance (Kilometer)</b><span class="text-danger">*</span>:</label><input type="text" name="ridenow_price_per_distance['+s+']" placeholder="Price per Distance" class="form-control" required></div></div><div class="col-md-6"><div class="form-group"><label><b>Price per time</b><span class="text-danger">*</span>:</label><input type="text" name="ridenow_price_per_time['+s+']" placeholder="Price per time" class="form-control" required></div></div></div><div class="row"><div class="col-md-4"><div class="form-group"><label><b>Free Waiting time (Minutes)</b><span class="text-danger">*</span>:</label><input type="text" name="ridenow_free_waiting_time['+s+']" placeholder="Free Waiting time" class="form-control" required></div></div><div class="col-md-4"><div class="form-group"><label><b>Waiting Change</b><span class="text-danger">*</span>:</label><input type="text" name="ridenow_waiting_charge['+s+']" placeholder="Waiting Change" class="form-control" required></div></div><div class="col-md-4"><div class="form-group"><label><b>Cancellation fee</b><span class="text-danger">*</span>:</label><input type="text" name="ridenow_cancellation_fee['+s+']" placeholder="Cancellation Fee" class="form-control" required></div></div></div></td><tr>';

                        form2 = '<tr><td><label class="text-uppercase font-size-sm font-weight-bold">'+data.vehicle.vehicle_name+'</label></td><td><div class="row"><div class="col-md-6"><div class="form-group"><label><b class="base_price">Base Price (Kilometer)</b><span class="text-danger">*</span>:</label><input type="text" name="ridelater_base_price['+s+']" placeholder="Base Price" class="form-control" required></div></div><div class="col-md-6"><div class="form-group"><label><b>Base Distance</b><span class="text-danger">*</span>:</label><select name="ridelater_base_distance['+s+']" id="ridelater_base_distance" class="form-control" required><option value="" selected disabled>Select Base Distance</option>@for ($i = 1; $i < 15; $i++)<option value="{{$i}}" >{{$i}}</option>@endfor</select></div></div></div><div class="row"><div class="col-md-6"><div class="form-group"><label><b>Price per time</b><span class="text-danger">*</span>:</label><input type="text" name="ridelater_price_per_time['+s+']" placeholder="Price per time" class="form-control" required></div></div><div class="col-md-6"><div class="form-group"><label><b class="price_per_distance">Price per Distance (Kilometer)</b><span class="text-danger">*</span>:</label><input type="text" name="ridelater_price_per_distance['+s+']" placeholder="Price per Distance" class="form-control" required></div></div></div><div class="row"><div class="col-md-4"><div class="form-group"><label><b>Free Waiting time (Minutes)</b>:</label><input type="text" name="ridelater_free_waiting_time['+s+']" placeholder="Free Waiting time" class="form-control" required></div></div><div class="col-md-4"><div class="form-group"><label><b>Waiting Change</b><span class="text-danger">*</span>:</label><input type="text" name="ridelater_waiting_charge['+s+']" placeholder="Waiting Change" class="form-control" required></div></div><div class="col-md-4"><div class="form-group"><label><b>Cancellation fee</b><span class="text-danger">*</span>:</label><input type="text" name="ridelater_cancellation_fee['+s+']" placeholder="Cancellation Fee" class="form-control" required></div></div></div></td><tr>';

                        form3 = '<tr><td><label class="text-uppercase font-size-sm font-weight-bold">'+data.vehicle.vehicle_name+'</label><br><button type="button" class="btn bg-green-400 btn-icon rounded-round legitRipple addrow" id="'+data.vehicle.slug+'" value="'+s+'" data-popup="tooltip" data-value="1" title="" data-placement="bottom" data-original-title="Add"> <i class="icon-plus3"></i> </button></td><td class="'+data.vehicle.slug+'"><div class="row"><div class="col-md-6"><div class="form-group"><label><b>Surge Price</b>:</label><input type="text" name="sruge_price['+s+'][0]" placeholder="Sruge Price" class="form-control" ><input type="hidden" name="sruge_price_id['+s+'][0]" value=""></div></div><div class="col-md-6"><div class="form-group"><label><b>Start Time</b>:</label><input type="time" name="start_time['+s+'][0]" class="form-control" ></div></div><div class="col-md-6"><div class="form-group"><label><b>End Time</b>:</label><input type="time" name="end_time['+s+'][0]" class="form-control" ></div></div><div class="col-md-6"><div class="form-group"><label><b>Available Days</b>:</label><select name="available_days['+s+'][0][]" id="available_days_'+s+'_0" multiple="multiple" class="form-control" ><option value="Sunday">Sunday</option><option value="Monday">Monday</option><option value="Tuesday">Tuesday</option><option value="Wednesday">Wednesday</option><option value="Thursday">Thursday</option><option value="Friday">Friday</option><option value="Saturday">Saturday</option></select></div></div></div></td><tr>';
                    }
                    $("#ride_now").append(form1);
                    $("#ride_later").append(form2);
                    $("#surge_price").append(form3);
                    console.log(data.datas);
                    if(data.success){
                        $('#available_days_'+s+'_0').select2();
                        data.datas.get_surge_price.forEach((element,index) => {
                            $('#available_days_'+s+'_'+index).select2();
                        });
                    }
                    else{
                        $('#available_days_'+s+'_0').select2();
                    }
                    s++;
                },
                error: function (data) {
                    console.log('Error:', data);
                }
            });
            
        }
        
        
    });

    $(document).on('click',".addrow",function(){
        var ids = $(this).attr('id');
        var datavalue = $(this).attr('data-value');
        var value = $(this).val();
        var text = '<hr><div class="row"><div class="col-md-6"><div class="form-group"><label><b>Surge Price</b>:</label><input type="text" name="sruge_price['+value+']['+datavalue+']" placeholder="Sruge Price" class="form-control" ><input type="hidden" name="sruge_price_id['+value+']['+datavalue+']" value=""></div></div><div class="col-md-6"><div class="form-group"><label><b>Start Time</b>:</label><input type="time" name="start_time['+value+']['+datavalue+']" class="form-control" ></div></div><div class="col-md-6"><div class="form-group"><label><b>End Time</b>:</label><input type="time" name="end_time['+value+']['+datavalue+']" class="form-control" ></div></div><div class="col-md-6"><div class="form-group"><label><b>Available Days</b>:</label><select name="available_days['+value+']['+datavalue+'][]" id="available_days_'+value+'_'+datavalue+'" multiple="multiple" class="form-control" ><option value="Sunday">Sunday</option><option value="Monday">Monday</option><option value="Tuesday">Tuesday</option><option value="Wednesday">Wednesday</option><option value="Thursday">Thursday</option><option value="Friday">Friday</option><option value="Saturday">Saturday</option></select></div></div></div>';
        $("."+ids).append(text);
        $("#available_days_"+value+"_"+datavalue).select2();
        datavalue = (datavalue*1)+1;
        $(this).attr('data-value',datavalue);

    })

    $(document).on('change','#unit',function(){
        var value = $(this).val();
        $(".base_price").text('Base Price ('+value+')');
        $(".price_per_distance").text('Price per Distance ('+value+')');
    })

    $("#inputFile").change(function () {
        if (this.files && this.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $('#imgLogo').attr('src', e.target.result);
        }
        reader.readAsDataURL(this.files[0]);
        }
    });
      
});
</script>

<script>
    $('#pin_the_location').val("");
    var service_location = $('#bounds').val();
    var already_has_lat_lng_list = false;
    if (service_location != '') {
        var service_location_json = JSON.parse(service_location);
        var length = service_location_json.length;
        service_location_json[length] = {};
        service_location_json[length].lat = parseFloat(service_location_json[0].lat);
        service_location_json[length].lng = parseFloat(service_location_json[0].lng);
        var already_has_lat_lng_list = true;
    }
    function text_to_search_fun()
    {
        $.ajaxSetup(
        {
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        var triangleCoords = [];
        $.ajax({
            type:'POST',
            url:"{{ route('service-location.suggestion') }}",
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')},
            data:{'text': $('#text-to-search').val()},
            success:function(data){
            if(data.success)
            {
                drawMapNew(data.coords);            
            }
        }
        });
    }
    function initMap() {
        console.log("hi");
        var map = new google.maps.Map(document.getElementById('map-canvas'), {
            center: {
                lat: 52.3993534,
                lng: 4.9421651
            },
            zoom: 10
        });

        console.log(map);
        // var pickupPoint = document.getElementById('pin_the_location');
        // var pickupPointAutocomplete = new google.maps.places.Autocomplete(pickupPoint);
        // pickupPointAutocomplete.addListener('place_changed', function () {
        //     var pickupPointPlace = pickupPointAutocomplete.getPlace();
        //     $('#pick_latitude').val(pickupPointPlace.geometry['location'].lat());
        //     $('#pick_longitude').val(pickupPointPlace.geometry['location'].lng());
        //     pickupPoint.focus();
        //     lat = pickupPointPlace.geometry['location'].lat();
        //     lng = pickupPointPlace.geometry['location'].lng();
        //     map_reset(lat, lng);
        // });
        // if (already_has_lat_lng_list) {
        //     var flightPath = new google.maps.Polygon({
        //         path: service_location_json,
        //         fillColor: '#FF0000',
        //         fillOpacity: 0.35,
        //         // geodesic: true,
        //         strokeColor: '#FF0000',
        //         strokeOpacity: 1.0,
        //         strokeWeight: 2,
        //     });
        //     flightPath.setMap(map);
        // }
        // drawing_add(map);
    }
    function drawing_add(map) {
        var listOfPolygons =[];
        drawingManager = new google.maps.drawing.DrawingManager({
            drawingMode: google.maps.drawing.OverlayType.POLYGON,
            drawingControl: true,
            drawingControlOptions: {
                position: google.maps.ControlPosition.TOP_CENTER,
                drawingModes: ['polygon']
            },
            polygonOptions: {
                editable: true
            }
        });
        drawingManager.setMap(map);
        google.maps.event.addListener(drawingManager, 'overlaycomplete', function (event) {
            event.overlay.set('editable', false);
            drawingManager.setMap(null);
            listOfPolygons.push(new google.maps.Polygon({
                paths: event.overlay.getPath().getArray(),
                strokeColor: '#FF0000',
                strokeOpacity: 0.8,
                strokeWeight: 3,
                fillColor: '#FF0000',
                fillOpacity: 0.35,

            }));
            showArrays(event.overlay);
            listOfPolygons[listOfPolygons.length - 1].setMap(map);
            listOfPolygons[listOfPolygons.length - 1].addListener('click', showArrays);
        });
        google.maps.event.addDomListener(document.getElementById('map-canvas'), 'click', function (event) {
            if (already_has_lat_lng_list == true) {
                already_has_lat_lng_list = false;
                removeShape();
            }
        });
    }
    function removeShape() {
        initMap();
        var lat_lng_list = [];
        var myJSON = JSON.stringify(lat_lng_list);
        $('#bounds').val(myJSON);
        $('#pin_the_location').val("");
    }
    function showArrays(event) {
        var vertices = event.getPath();
        var contentString = '';
        var lat_lng_list = [];
        for (var i = 0; i < vertices.getLength(); i++) {
            var xy = vertices.getAt(i);
            lat_lng_list[i] = {};
            lat_lng_list[i].lat = xy.lat();
            lat_lng_list[i].lng = xy.lng();
        }
        var myJSON = JSON.stringify(lat_lng_list);
        $('#bounds').val(myJSON);
    }



  function map_reset(lat, lng) {

    var myLatLng = {
      lat: lat,
      lng: lng
    };
    var map = new google.maps.Map(document.getElementById('map-canvas'), {
      zoom: 15,
      center: myLatLng
    });

    var marker = new google.maps.Marker({
      position: myLatLng,
      map: map,
      draggable: true,
      animation: google.maps.Animation.DROP
    });

    marker.setMap(map);

    google.maps.event.addListener(marker, 'dragend', function (evt) {
      $('#pick_latitude').val(evt.latLng.lat().toFixed(3));
      $('#pick_longitude').val(evt.latLng.lng().toFixed(3));

      var geocoder = new google.maps.Geocoder();

      geocoder.geocode({
        'latLng': marker.getPosition()
      }, function (results, status) {

        if (status == google.maps.GeocoderStatus.OK) {

          if (results[0]) {

            $('#pin_the_location').val(results[0].formatted_address);
          }
        }
      });
    });

    google.maps.event.addListener(marker, 'dragstart', function (evt) {

    });
    drawing_add(map);
  }


    function getAreaCoordinates(id){
        $.ajaxSetup(
    {
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
        $.ajax({
            url: '{{ route("service-location.suggestion") }}',
            data: {id:id},
            method: 'get',
            success: function(results){
                if(results){
                    getZoneCoordinates(id).then(function(response) {
                        drawMap(results,id,response);
                    });
                }
            }
        });
    }

    function drawMapNew(data){
        // console.log(data);
        var drawingManager;
        let area = JSON.parse(data);
        var zones = [];
        var stands = [];
        $('#bounds').val(data);
        var polygonCoords = [[area,'#007cff',0.8,0.25]];

        var latLng = findAvg(area);
        var default_lat = latLng['lat'];
        var default_lng = latLng['lng'];

        var map = new google.maps.Map(document.getElementById('map-canvas'), {
                zoom: 12,
                center: new google.maps.LatLng(default_lat, default_lng),
                mapTypeId:google.maps.MapTypeId.ROADMAP,
            });

        polygonCoords.forEach((ele,i) => {
          var latLng = findAvg(area);
            for (var index = 0; index < ele[0].length; index++) {
                new google.maps.Polygon({
                    map: map,
                    paths: ele[0][index],
                    strokeColor: ele[1],
                    strokeOpacity: ele[2],
                    strokeWeight: 2,
                    fillColor: ele[1],
                    fillOpacity: ele[3]
                });
            }
        });
    }

    function findAvg(Coords){
        var lat = [];
        var lng = [];
        var LatAndLng = [];
        Object.keys(Coords).forEach((key) => {
            // console.log(Coords[key][key][key]);
            // Object.keys(Coords[key]).forEach((key) => {
            // console.log(Coords[key][key]);
                // Object.keys(Coords[key][key]).forEach((key) => {
                    var data = Coords[key][key];
                    var output = '';
                    for (var property in data) {
                        // console.log(property);
                        // output += property + ': ' + data[property]+'; ';
                        if(property == 'lat'){
                            lat.push(data[property]);
                        }else{
                            lng.push(data[property]);
                        }
                    }   
                    // console.log(output);
                    // lat.push(Coords[key][key].lat);
                    // lng.push(Coords[key][key].lng);
                // });
            // });
        });

        LatAndLng['lat'] = lat.reduce((a, b) => a + b) / lat.length
        LatAndLng['lng'] = lng.reduce((a, b) => a + b) / lng.length

        console.log(LatAndLng['lat']);

        return LatAndLng;
    }

</script>

<script src="https://maps.googleapis.com/maps/api/js?key={{settingValue('geo_coder')}}&libraries=drawing,places&callback=initMap" async defer>    
</script>
<script>
$(document).ready(function() {
    $('#vehicle_type').select2();
    $('#payment_type').select2();
@foreach($zone->getZonePrice as $key => $values)
@if(count($values->getSurgePrice) != 0)
@foreach($values->getSurgePrice as $keys => $value)
    $('#available_days_{{$key}}_{{$keys}}').select2();
@endforeach
@else
$('#available_days_{{$key}}_0').select2();
@endif
@endforeach
    
});
</script>

@endsection
