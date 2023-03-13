@extends('layouts.app')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<style>

.select2-container{
    width: 100% !important;
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
.select2-selection{
    max-height: 0;
    overflow: hidden;
    overflow-y: scroll;
}

/*---------signup-step-------------*/
.bg-color{
  background-color: #333;
}
.signup-step-container{
  padding: 50px 0px;
  padding-bottom: 60px;
}




    .wizard .nav-tabs {
        position: relative;
        margin-bottom: 0;
        border-bottom-color: transparent;
    }

    .wizard > div.wizard-inner {
            position: relative;
    margin-bottom: 50px;
    text-align: center;
    }

.connecting-line {
    height: 2px;
    background: #e0e0e0;
    position: absolute;
    width: 80%;
    margin: 0 auto;
    left: 0;
    right: 0;
    top: 15px;
    z-index: 1;
}

.wizard .nav-tabs > li.active > a, .wizard .nav-tabs > li.active > a:hover, .wizard .nav-tabs > li.active > a:focus {
    color: #555555;
    cursor: default;
    border: 0;
    border-bottom-color: transparent;
}

span.round-tab {
    width: 40px;
    height: 40px;
    line-height: 37px;
    display: inline-block;
    border-radius: 50%;
    background: #fff;
    z-index: 2;
    position: absolute;
    left: 0;
    text-align: center;
    font-size: 20px;
    color: #0e214b;
    font-weight: 500;
    border: 1px solid #ddd;
}
span.round-tab i{
    color:#555555;
}
.wizard li.active span.round-tab {
        background: #0db02b;
    color: #fff;
    border-color: #0db02b;
}
.wizard li.active span.round-tab i{
    color: #5bc0de;
}
.wizard .nav-tabs > li.active > a i{
  color: #0db02b;
}

.wizard .nav-tabs > li {
    width: 25%;
}

.wizard li:after {
    content: " ";
    position: absolute;
    left: 46%;
    opacity: 0;
    margin: 0 auto;
    bottom: 0px;
    border: 5px solid transparent;
    border-bottom-color: red;
    transition: 0.1s ease-in-out;
}



.wizard .nav-tabs > li a {
    width: 30px;
    height: 30px;
    margin: 20px auto;
    border-radius: 100%;
    padding: 0;
    background-color: transparent;
    position: relative;
    top: 0;
}
.wizard .nav-tabs > li a i{
  position: absolute;
    top: -15px;
    font-style: normal;
    font-weight: 400;
    white-space: nowrap;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 12px;
    font-weight: 700;
    color: #000;
    margin-left: 15px;
}

    .wizard .nav-tabs > li a:hover {
        background: transparent;
    }

.wizard .tab-pane {
    position: relative;
    padding-top: 20px;
}


.wizard h3 {
    margin-top: 0;
}
.disabled a {
  pointer-events: none;
  cursor: default;
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
        <section class="signup-step-container">
            <div class="">
                <div class="row d-flex justify-content-center">
                    <div class="col-md-11">
                        <div class="wizard">
                            <div class="wizard-inner">
                                <div class="connecting-line"></div>
                                <ul class="nav nav-tabs" role="tablist">
                                    <li role="presentation" class="active">
                                        <a href="#step1" data-toggle="tab" aria-controls="step1" role="tab" aria-expanded="true"><span class="round-tab icon-pin-alt"> </span> <i>Service Location</i></a>
                                    </li>
                                    <li role="presentation" class="disabled">
                                        <a href="#step2" data-toggle="tab" aria-controls="step2" class="disable" role="tab" aria-expanded="false"><span class="round-tab icon-file-text2"></span> <i>Basic Features</i></a>
                                    </li>
                                    <li role="presentation" class="disabled">
                                        <a href="#step3" data-toggle="tab" aria-controls="step3" class="disable" role="tab"><span class="round-tab icon-car2"></span> <i>Ride Now</i></a>
                                    </li>
                                    <li role="presentation" class="disabled">
                                        <a href="#step4" data-toggle="tab" aria-controls="step4" class="disable" role="tab"><span class="round-tab icon-calendar"></span> <i>Ride Later</i></a>
                                    </li>
                                    <li role="presentation" class="disabled">
                                        <a href="#step5" data-toggle="tab" aria-controls="step5" class="disable" role="tab"><span class="round-tab icon-cash"></span> <i>Surge Price</i></a>
                                    </li>
                                </ul>
                            </div>
            
                            <form id="form_id" action="{{ route('saveZone') }}" autocomplete="off" method="POST" enctype="multipart/form-data" class="login-box">
                            @csrf
                                <div class="tab-content" id="main_form">
                                    <div class="tab-pane active" role="tabpanel" id="step1">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="col-form-label col-sm-6"><b>Zone Level</b><span class="text-danger">*</span></label>
                                                    <div class="col-sm-8">
                                                        <select class="form-control" name="zone_level" id="zone_level" required>
                                                            <option value="">Select Zone Level</option>
                                                            <option value="PRIMARY" {{(old('zone_level') == 'PRIMARY' )?'selected':''}}>
                                                            Primary</option>
                                                            <option value="SECONDARY" {{(old('zone_level') == 'SECONDARY' )?'selected':''}}>
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
                                                            <option value="DISTANCE" {{(old('driver_assign_method') == 'DISTANCE' )?'selected':''}}>
                                                                Distance</option>
                                                            <option value="FIFO" {{(old('driver_assign_method') == 'FIFO' )?'selected':''}}>
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
                                                            <option value="" selected>Select Country</option>
                                                            @foreach ($countries as $country)
                                                                <option value="{{$country->id}}">{{$country->name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="col-form-label col-lg-6"><b>Service location area (Zone Name)</b><span class="text-danger">*</span></label>
                                                    <div class="col-lg-8">
                                                        <input id="zone_name" name="zone_name" placeholder="Service location area" type="text" class="form-control"
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
                                            <div class="col-md-6" id="primary_zone_view">
                                                <div class="form-group">
                                                    <label class="col-form-label col-sm-6"><b>Primary Zone</b><span class="text-danger">*</span></label>
                                                    <div class="col-sm-8">
                                                        <select class="form-control" name="primary_zone" id="primary_zone">
                                                            <option value="">Select Primary Zone</option>
                                                            @foreach($zone as $zones)
                                                                <option value="{{ $zones->slug }}">{{ $zones->zone_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-md-12">
                                                <input id="pin_the_location" name="pin_the_location" placeholder="Search..." type="text" class="form-control">
                                                <input type="hidden" id="map_zone" name="bounds" value=''>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-md-12">
                                                <div class="d-grid gap-2 d-md-block">
                                                    <button class="btn btn-primary" type="button" id="remove_poly">Remove Zone</button>
                                                    <!-- <button class="btn btn-primary" type="button">Button</button> -->
                                                </div>
                                            </div>
                                        </div>

                                        <div id="map-canvas" style="width:100%;height:400px;"></div>
                                        <!-- <div id="map" style="width:100%;height:400px;"></div><br> -->
                                        <br>
                                        <ul class="list-inline pull-right text-right">
                                            <li><button type="button" id="step1" class="btn btn-success rounded-pill next-step">Continue to next step</button></li>
                                        </ul>
                                    </div>
                                    <div class="tab-pane" role="tabpanel" id="step2">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group row">
                                                    <label class="col-form-label col-lg-12"><b>Select Types</b><span class="text-danger">*</span></label>
                                                    <div class="col-lg-12">
                                                        <select multiple="multiple" class="form-control" name="vehicle_type[]" id="vehicle_type" required>
                                                            @foreach($vehicleList as $vehicledet)
                                                                <option value="{{ $vehicledet->slug }}">{{ $vehicledet->vehicle_name }}</option>
                                                            @endforeach
                                                        
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group row">
                                                    <label class="col-form-label col-lg-12"><b>Payment Types</b><span class="text-danger">*</span></label>
                                                    <div class="col-lg-12">
                                                        <select multiple="multiple" class="form-control multiselect111" name="payment_type[]" required id="payment_type">
                                                            <option value="CASH">Cash</option>
                                                            <option value="CARD">Card</option>
                                                            <option value="WALLET">Wallet</option>
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
                                                        <option value="KM">Kilometer</option> 
                                                        <option value="MILES">Miles</option> 
                                                    </select>
                                                </div>

                                            
                                            </div>

                                            <div class="col-md-6"><br><br>
                                                <div class="form-check mb-0">
                                                    <label class="form-check-label">
                                                        <input type="checkbox" id="remember_me" name="non_service_zone" class="form-input-styled" data-fouc>
                                                        Non Service Zone
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        
                                        <ul class="list-inline pull-right text-right">
                                            <li><button type="button" class="btn btn-danger prev-step">Back</button><button type="button" class="btn btn-success next-step ml-2" id="step2">Continue</button></li>
                                        </ul>
                                    </div>
                                    <div class="tab-pane" role="tabpanel" id="step3">
                                        <table class="table" id="ride_now"><tr><td><h1>Loading...</h1></td></tr></table>
                                        <ul class="list-inline pull-right text-right">
                                            <li><button type="button" class="btn btn-danger prev-step">Back</button><button type="button" class="btn btn-success next-step ml-2 step3" id="step3">Continue</button></li>
                                        </ul>
                                    </div>
                                    <div class="tab-pane" role="tabpanel" id="step4">
                                        <table class="table" id="ride_later"><tr><td><h1>Loading...</h1></td></tr></table>
                                        
                                        <ul class="list-inline pull-right text-right">
                                            <li><button type="button" class="btn btn-danger prev-step">Back</button><button type="button" class="btn btn-success next-step ml-2" id="step4">Continue</button></li>
                                        </ul>
                                    </div>
                                    <div class="tab-pane" role="tabpanel" id="step5">
                                        <table class="table" id="surge_price"><tr><td><h1>Loading...</h1></td></tr></table>
                                        
                                        <ul class="list-inline pull-right text-right">
                                            <li><button type="button" class="btn btn-danger prev-step">Back</button><button type="submit" id="submit" class="btn btn-success next-step ml-2" id="step5">Finish</button></li>
                                        </ul>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                                
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    



</div>


<script>
    var types = 0;
    $("#primary_zone_view").hide();
    // ------------step-wizard-------------
    $(document).ready(function () {
        $('.nav-tabs > li a[title]').tooltip();
        $(".disable").attr('disabled',true)
        
        //Wizard
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {

            var target = $(e.target);
        
            if (target.parent().hasClass('disabled')) {
                return 0;
            }
        });

        $(".next-step").click(function (e) {

            var step = $(this).attr('id');

            if(step == 'step1'){
                var i = 0;
                var array = ['zone_level','driver_assign_method','country','zone_name','admin_commission','map_zone'];
                jQuery.each( array, function( key, value ) {
                    if($("#"+value).val() == ""){
                        $("#"+value).next("samp").remove();
                        var texts = value.replace(/_/g, " ");
                        $("#"+value).after("<samp class='text-danger'>"+texts+" is required</samp>");
                        i++;
                    }
                    else{
                        $("#"+value).next("samp").remove();
                    }
                });
                if(i > 0){
                    return 0;
                }
            }
            if(step == 'step2'){
                var i = 0;
                var array = ['vehicle_type','payment_type','unit'];
                jQuery.each( array, function( key, value ) {
                    if($("#"+value).val() == ""){
                        $("#"+value).next("samp").remove();
                        var texts = value.replace(/_/g, " ");
                        $("#"+value).after("<samp class='text-danger'>"+texts+" is required</samp>");
                        i++;
                    }
                    else{
                        $("#"+value).next("samp").remove();
                    }
                });
                if(i > 0){
                    return 0;
                }
            }
            if(step == 'step3'){
                var i = 0;
                console.log(types);
                var array = ['ridenow_base_price','ridenow_base_distance','ridenow_price_per_distance','ridenow_price_per_time','ridenow_free_waiting_time','ridenow_free_waiting_time_after_start','ridenow_waiting_charge','ridenow_cancellation_fee','ridenow_admin_commission_type','ridenow_admin_commission'];
                jQuery.each( array, function( key, value ) {
                    for (let index = 0; index < types; index++) {
                        if($("#"+value+"_"+index).val() == ""){
                            $("#"+value+"_"+index).next("samp").remove();
                            var texts = value.replace(/_/g, " ");
                            $("#"+value+"_"+index).after("<samp class='text-danger'>"+texts+" is required</samp>");
                            i++;
                        }
                        else{
                            $("#"+value+"_"+index).next("samp").remove();
                        }
                    }
                });
                if(i > 0){
                    return 0;
                }
            }
            if(step == 'step4'){
                var i = 0;
                console.log(types);
                var array = ['ridelater_base_price','ridelater_base_distance','ridelater_price_per_distance','ridelater_price_per_time','ridelater_free_waiting_time','ridelater_free_waiting_time_after_start','ridelater_waiting_charge','ridelater_cancellation_fee','ridelater_admin_commission_type','ridelater_admin_commission'];
                jQuery.each( array, function( key, value ) {
                    for (let index = 0; index < types; index++) {
                        if($("#"+value+"_"+index).val() == ""){
                            $("#"+value+"_"+index).next("samp").remove();
                            var texts = value.replace(/_/g, " ");
                            $("#"+value+"_"+index).after("<samp class='text-danger'>"+texts+" is required</samp>");
                            i++;
                        }
                        else{
                            $("#"+value+"_"+index).next("samp").remove();
                        }
                    }
                });
                if(i > 0){
                    return 0;
                }
            }
            var active = $('.wizard .nav-tabs li.active');
            active.next().removeClass('disabled');
            nextTab(active);

        });
        $(".prev-step").click(function (e) {

            var active = $('.wizard .nav-tabs li.active');
            prevTab(active);

        });
    });

    function nextTab(elem) {
        $(elem).next().find('a[data-toggle="tab"]').click();
    }
    function prevTab(elem) {
        $(elem).prev().find('a[data-toggle="tab"]').click();
    }

    $("#zone_level").on('change',function(){
        if($(this).val() == 'PRIMARY'){
            $("#primary_zone_view").hide();
        }
        else{
            $("#primary_zone_view").show();
        }
    })


    $('.nav-tabs').on('click', 'li', function() {
        $('.nav-tabs li.active').removeClass('active');
        $(this).addClass('active');
    });


    $(document).on('click','.submit',function(){
        if($("#form_id").valid()){
            swal({
                title: "{{ __('data-added') }}",
                text: "{{ __('data-added-successfully') }}",
                icon: "success",
            }).then((value) => {                 
                $("#submit").click();
            });
        }
    })
    $(document).on('change','#vehicle_type',function(){
        var a = $('#vehicle_type').val();
        var form1 = "";
        var form2 = "";
        var form3 = "<tr><td></td><td><label><b>Surge Price</b></label></td><td><label><b>Surge Distance Price</b></label></td><td><label><b>Start Time</b></label></td><td><label><b>End Time</b></label></td><td><label><b>Available Days</b></label></td></tr>";
        $.ajax({
            url: "{{ route('getTypePrices') }}",
            type: "GET",
            dataType: 'json',
            data: {type : a},
            success: function (data) {
                types = data.vehicle.length;
                $.each(data.vehicle, function( i, value ) {
                    form1 += '<tr><td><label class="text-uppercase font-size-sm font-weight-bold">'+value.vehicle_name+'</label></td><td><div class="row"><div class="col-md-6"><div class="form-group"><label><b class="base_price">Base Price (Kilometer)</b><span class="text-danger">*</span>:</label><input type="text" name="ridenow_base_price['+i+']" placeholder="Base Price" class="form-control" id="ridenow_base_price_'+i+'" required></div></div><div class="col-md-6"><div class="form-group"><label><b>Base Distance</b><span class="text-danger">*</span>:</label><select name="ridenow_base_distance['+i+']" id="ridenow_base_distance_'+i+'" class="form-control" required><option value="" selected>Select Base Distance</option>@for ($i = 1; $i < 15; $i++)<option value="{{$i}}" >{{$i}}</option>@endfor</select></div></div></div><div class="row"><div class="col-md-6"><div class="form-group"><label><b class="price_per_distance">Price per Distance (Kilometer)</b><span class="text-danger">*</span>:</label><input type="text" name="ridenow_price_per_distance['+i+']" id="ridenow_price_per_distance_'+i+'" placeholder="Price per Distance" class="form-control" required></div></div><div class="col-md-6"><div class="form-group"><label><b>Price per time</b><span class="text-danger">*</span>:</label><input type="text" name="ridenow_price_per_time['+i+']" placeholder="Price per time" id="ridenow_price_per_time_'+i+'" class="form-control" required></div></div></div><div class="row"><div class="col-md-4"><div class="form-group"><label><b>Free Waiting time (Minutes)</b><span class="text-danger">*</span>:</label><input type="text" name="ridenow_free_waiting_time['+i+']"  placeholder="Free Waiting time" class="form-control" id="ridenow_free_waiting_time_'+i+'" required></div></div><div class="col-md-4"><div class="form-group"><label><b>Free Waiting time After Start (Minutes)</b><span class="text-danger">*</span>:</label><input type="text" name="ridenow_free_waiting_time_after_start['+i+']"  placeholder="Free Waiting time After Start" class="form-control" id="ridenow_free_waiting_time_after_start_'+i+'" required></div></div><div class="col-md-4"><div class="form-group"><label><b>Waiting Change</b><span class="text-danger">*</span>:</label><input type="text" name="ridenow_waiting_charge['+i+']" placeholder="Waiting Change" class="form-control" id="ridenow_waiting_charge_'+i+'" required></div></div><div class="col-md-4"><div class="form-group"><label><b>Cancellation fee</b><span class="text-danger">*</span>:</label><input type="text" name="ridenow_cancellation_fee['+i+']" id="ridenow_cancellation_fee_'+i+'" placeholder="Cancellation Fee" class="form-control" required></div></div><div class="col-md-4"><div class="form-group"><label><b>Ride Now Admin Commission Type</b><span class="text-danger">*</span>:</label><select name="ridenow_admin_commission_type['+i+']" id="ridenow_admin_commission_type_'+i+'" class="form-control" required><option value="1">Percentage</option><option value="0">Fixed </option></select></div></div><div class="col-md-4"><div class="form-group"><label><b>Ride now Admin Commission</b><span class="text-danger">*</span>:</label><input type="text" name="ridenow_admin_commission['+i+']" id="ridenow_admin_commission_'+i+'" placeholder="Ride now Admin Commission" class="form-control" required></div></div></div></td><tr>';

                    form2 += '<tr><td><label class="text-uppercase font-size-sm font-weight-bold">'+value.vehicle_name+'</label></td><td><div class="row"><div class="col-md-6"><div class="form-group"><label><b class="base_price">Base Price (Kilometer)</b><span class="text-danger">*</span>:</label><input type="text" name="ridelater_base_price['+i+']" id="ridelater_base_price_'+i+'" placeholder="Base Price" class="form-control" required></div></div><div class="col-md-6"><div class="form-group"><label><b>Base Distance</b><span class="text-danger">*</span>:</label><select name="ridelater_base_distance['+i+']" id="ridelater_base_distance_'+i+'" class="form-control" required><option value="" selected>Select Base Distance</option>@for ($i = 1; $i < 15; $i++)<option value="{{$i}}" >{{$i}}</option>@endfor</select></div></div></div><div class="row"><div class="col-md-6"><div class="form-group"><label><b>Price per time</b><span class="text-danger">*</span>:</label><input type="text" name="ridelater_price_per_time['+i+']" id="ridelater_price_per_time_'+i+'" placeholder="Price per time" class="form-control" required></div></div><div class="col-md-6"><div class="form-group"><label><b class="price_per_distance">Price per Distance (Kilometer)</b><span class="text-danger">*</span>:</label><input type="text" name="ridelater_price_per_distance['+i+']" id="ridelater_price_per_distance_'+i+'" placeholder="Price per Distance" class="form-control" required></div></div></div><div class="row"><div class="col-md-4"><div class="form-group"><label><b>Free Waiting time (Minutes)</b>:</label><input type="text" name="ridelater_free_waiting_time['+i+']" id="ridelater_free_waiting_time_'+i+'" placeholder="Free Waiting time" class="form-control" required></div></div><div class="col-md-4"><div class="form-group"><label><b>Free Waiting time After Start (Minutes)</b>:</label><input type="text" name="ridelater_free_waiting_time_after_start['+i+']" id="ridelater_free_waiting_time_after_start_'+i+'" placeholder="Free Waiting time After Start" class="form-control" required></div></div><div class="col-md-4"><div class="form-group"><label><b>Waiting Change</b><span class="text-danger">*</span>:</label><input type="text" name="ridelater_waiting_charge['+i+']" id="ridelater_waiting_charge_'+i+'" placeholder="Waiting Change" class="form-control" required></div></div><div class="col-md-4"><div class="form-group"><label><b>Cancellation fee</b><span class="text-danger">*</span>:</label><input type="text" name="ridelater_cancellation_fee['+i+']" id="ridelater_cancellation_fee_'+i+'" placeholder="Cancellation Fee" class="form-control" required></div></div><div class="col-md-4"><div class="form-group"><label><b>Ride Later Admin Commission Type</b><span class="text-danger">*</span>:</label><select name="ridelater_admin_commission_type['+i+']" id="ridelater_admin_commission_type_'+i+'" class="form-control" required><option value="1">Percentage</option><option value="0">Fixed </option></select></div></div><div class="col-md-4"><div class="form-group"><label><b>Ride later Admin Commission</b><span class="text-danger">*</span>:</label><input type="text" name="ridelater_admin_commission['+i+']" id="ridelater_admin_commission_'+i+'" placeholder="Ride later Admin Commission" class="form-control" required></div></div></div></td><tr>';

                    form3 += '<tr class="'+value.slug+'"><td rowspan="1"><label class="text-uppercase font-size-sm font-weight-bold">'+value.vehicle_name+'</label><br><button type="button" class="btn bg-green-400 btn-icon rounded-round legitRipple addrow" id="'+value.slug+'" value="'+i+'" data-popup="tooltip" data-value="1" title="" data-placement="bottom" data-original-title="Add"> <i class="icon-plus3"></i> </button></td><td><input type="text" name="sruge_price['+i+'][0]" placeholder="Sruge Price" class="form-control"></td><td><input type="text" name="surge_distance_price['+i+'][0]" placeholder="Surge Distance Price" class="form-control"></td><td><input type="time" name="start_time['+i+'][0]" class="form-control"></td><td><input type="time" name="end_time['+i+'][0]" class="form-control"></td><td><select name="available_days['+i+'][0][]" multiple="multiple" id="available_days_'+i+'_0" class="form-control"><option value="Sunday">Sunday</option><option value="Monday">Monday</option><option value="Tuesday">Tuesday</option><option value="Wednesday">Wednesday</option><option value="Thursday">Thursday</option><option value="Friday">Friday</option><option value="Saturday">Saturday</option></select></td></tr>';
                    $("#ride_now").html(form1);
                    $("#ride_later").html(form2);
                    $("#surge_price").html(form3);
                });
                $.each(data.vehicle, function( i, value ) {
                    $("#available_days_"+i+"_0").select2();
                });
                    
            },
            error: function (data) {
                console.log('Error:', data);
            }
        });
        
    });

    $(document).on('click',".addrow",function(){
        var ids = $(this).attr('id');
        var count = $(this).parent('td').attr('rowspan');
        count++;
        $(this).parent('td').attr('rowspan',count);
        var datavalue = $(this).attr('data-value');
        var value = $(this).val();
        var text = '<tr><td><input type="text" name="sruge_price['+value+']['+datavalue+']" placeholder="Sruge Price" class="form-control"></td><td><input type="text" name="surge_distance_price['+value+']['+datavalue+']" placeholder="Surge Distance Price" class="form-control"></td><td><input type="time" name="start_time['+value+']['+datavalue+']" class="form-control"></td><td><input type="time" name="end_time['+value+']['+datavalue+']" class="form-control"></td><td><select name="available_days['+value+']['+datavalue+'][]" multiple="multiple" id="available_days_'+value+'_'+datavalue+'" class="form-control"><option value="Sunday">Sunday</option><option value="Monday">Monday</option><option value="Tuesday">Tuesday</option><option value="Wednesday">Wednesday</option><option value="Thursday">Thursday</option><option value="Friday">Friday</option><option value="Saturday">Saturday</option></select></td></tr>';
        $("."+ids).after(text);
        $("#available_days_"+value+"_"+datavalue).select2();
        datavalue = (datavalue*1)+1;
        $(this).attr('data-value',datavalue);

    });

    // $(document).on('click',".deleterow",function(){
    //     var count = $(this).parent('table').attr('rowspan');
    // });

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

    $(document).on('change',"#primary_zone",function(){
        var slug = $(this).val();
        $.ajax({
            url: "{{ url('zone/details') }}/"+slug,
            type: "GET",
            dataType: 'json',
            success: function (data) {
                console.log(data);
                if(data.success){
                    var zone = data.data.map_cooder;
                    var name = data.data.zone_name;
                    polygon(zone,name);
                }
            }
        });
    })
</script>
<script>
    $('#pin_the_location').val("");
    var service_location = $('#map_zone').val();
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

    var map = '';
    var locat = {
                lat: 28.61,
                lng: 77.23
            };
    function initMap() {
        map = new google.maps.Map(document.getElementById('map-canvas'), {
            center: locat,
            zoom: 10
        });
        var pickupPoint = document.getElementById('pin_the_location');
        var pickupPointAutocomplete = new google.maps.places.Autocomplete(pickupPoint);
        pickupPointAutocomplete.addListener('place_changed', function () {
            var pickupPointPlace = pickupPointAutocomplete.getPlace();
            $('#pick_latitude').val(pickupPointPlace.geometry['location'].lat());
            $('#pick_longitude').val(pickupPointPlace.geometry['location'].lng());
            pickupPoint.focus();
            lat = pickupPointPlace.geometry['location'].lat();
            lng = pickupPointPlace.geometry['location'].lng();
            map_reset(lat, lng);
        });
        if (already_has_lat_lng_list) {
            var flightPath = new google.maps.Polygon({
                path: service_location_json,
                fillColor: '#FF0000',
                fillOpacity: 0.35,
                // geodesic: true,
                strokeColor: '#FF0000',
                strokeOpacity: 1.0,
                strokeWeight: 2,
            });
            flightPath.setMap(map);
        }
        drawing_add(map);
    }
    $("#remove_poly").on('click',function(){
        // google.maps.event.addDomListener(document.getElementById('map-canvas'), 'click', function (event) {
            // if (already_has_lat_lng_list == true) {
                already_has_lat_lng_list = false;
                removeShape();
            // }
        // });
        // drawing_add(map);
    })
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
                editable: false
            }
        });
        drawingManager.setMap(map);
        google.maps.event.addListener(drawingManager, 'overlaycomplete', function (event) {
            event.overlay.set('editable', false);
            drawingManager.setMap(null);
            locat.lat = event.overlay.getPath().getArray()[0].lat();
            locat.lng = event.overlay.getPath().getArray()[0].lng();
            var zone = new google.maps.Polygon({
                paths: event.overlay.getPath().getArray(),
                strokeColor: '#FF0000',
                strokeOpacity: 0.8,
                strokeWeight: 3,
                fillColor: '#FF0000',
                fillOpacity: 0.35,
                editable: true
            })
            listOfPolygons.push(zone);
            showArrays(zone);
            listOfPolygons[listOfPolygons.length - 1].setMap(map);
            listOfPolygons[listOfPolygons.length - 1].addListener('click', function() {
                showArrays(zone);
            });
            // listOfPolygons[listOfPolygons.length - 1].addListener('dragstart', function() {
            //     showArrays(zone);
            // });
            // listOfPolygons[listOfPolygons.length - 1].addListener('dragover', function() {
            //     showArrays(zone);
            // });
            // listOfPolygons[listOfPolygons.length - 1].addListener('drop', function() {
            //     showArrays(zone);
            // });
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
        $('#map_zone').val(myJSON);
        $('#pin_the_location').val("");
    }
    function showArrays(event) {
        var vertices = event.getPath();
        console.log(vertices);
        var contentString = '';
        var lat_lng_list = [];
        for (var i = 0; i < vertices.getLength(); i++) {
            var xy = vertices.getAt(i);
            lat_lng_list[i] = {};
            lat_lng_list[i].lat = xy.lat();
            lat_lng_list[i].lng = xy.lng();
        }
        var myJSON = JSON.stringify(lat_lng_list);
        $('#map_zone').val(myJSON);
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
        var drawingManager;
        let area = JSON.parse(data);
        var zones = [];
        var stands = [];
        $('#map_zone').val(data);
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

        // console.log(LatAndLng['lat']);

        return LatAndLng;
    }

</script>

<script src="https://maps.googleapis.com/maps/api/js?key={{settingValue('geo_coder')}}&libraries=drawing,places&callback=initMap" async defer>    
</script>

<!-- <script src="https://apis.mapmyindia.com/advancedmaps/api/{{settingValue('google_map_key')}}/map_sdk?layer=vector&v=2.0&callback=initMap1" defer async></script>
{{ config('app.map_key')}}
    <script src="https://apis.mapmyindia.com/advancedmaps/api/{{settingValue('google_map_token')}}/map_sdk_plugins"></script>
<script>
    var map;
            function initMap1(){
                map = new MapmyIndia.Map('map', {
                    center: [28.61, 77.23],
                    zoomControl: true,
                    location: true,
                    search: false
                    // backgroundColor:"red",
                });

                var placeOptions={
                    location:[28.61, 77.23]
                };
                new MapmyIndia.search(document.getElementById("pin_the_location"),placeOptions,callback);

                var marker;
                function callback(data) { 
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
                        /*Use elocMarker Plugin to add marker*/
                        if(marker) marker.remove();
                        marker=new MapmyIndia.elocMarker({map:map,eloc:eloc,popupHtml:place,popupOptions:{openPopup:true}}).fitbounds();
                    }
                }    

                // map.addListener("click", function(e) {
                //     console.log(e.lngLat);
                // });


        var placeOptions={
            location:[28.61, 77.23]
        };
        new MapmyIndia.search(document.getElementById("pin_the_location"),placeOptions,callback);

        var marker;
        function callback(data) { 
            if(data)
            {
                var dt=data[0];
                if(!dt) return false;
                var eloc=dt.eLoc;
                var place=dt.placeName+", "+dt.placeAddress;
                /*Use elocMarker Plugin to add marker*/
                if(marker) marker.remove();
                marker=new MapmyIndia.elocMarker({map:map,eloc:eloc,popupHtml:place,popupOptions:{openPopup:true}}).fitbounds();
            }

    
            
        </script> -->


<script>
$(document).ready(function() {
    $('#vehicle_type').select2();
    $('#payment_type').select2();
});
</script>
@endsection
