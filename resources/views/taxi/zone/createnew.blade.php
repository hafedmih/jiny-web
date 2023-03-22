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
      <form class="wizard-form steps-validation" id="form_id" action="{{ route('saveZone') }}" data-fouc autocomplete="off" method="POST" enctype="multipart/form-data">
         @csrf
         <h6>Service Location</h6>
         <fieldset>
            <div class="row">
               <div class="col-md-6">
                  <div class="form-group">
                     <label class="col-form-label col-md-4"><b>Select Country</b><span class="text-danger">*</span></label>
                     <div class="form-group col-md-8">
                        <select name="country" id="country" class="form-control required" required>
                           <option value="" selected disabled>Select Country</option>
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
               <div class="col-md-6">
                  <div class="form-group">
                     <label class="col-form-label col-sm-6"><b>Admin commission type</b><span class="text-danger">*</span></label>
                     <div class="col-sm-8">
                        <select class="form-control" name="admin_commission_type" id="admin_commission_type" required>
                        <option value="1" {{(old('admin_commission_type',1) == 1 )?'selected':''}}>
                        Percentage</option>
                        <option value="0" {{(old('admin_commission_type',1) == 0 )?'selected':''}}>
                        Fixed </option>
                        </select>
                     </div>
                  </div>
               </div>
               <div class="col-md-6">
                  <div class="form-group">
                     <input type="hidden" id="bounds" name="bounds" value="">
                     <label class="col-form-label col-md-4"><b>Admin commission</b><span class="text-danger">*</span></label>
                     <div class="col-md-8">
                        <input id="admin_commission" name="admin_commission" placeholder="Admin commission" type="text" class="form-control" value="" required>
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
                        <option value="KM">Kilo Meter</option>
                        <option value="MILES">Miles</option>
                     </select>
                  </div>
               </div>
               <div class="col-md-6">
                  <br><br>
                  <div class="form-check mb-0">
                     <label class="form-check-label">
                     <input type="checkbox" id="remember_me" name="non_service_zone" class="form-input-styled" data-fouc>
                     Non Service Zone
                     </label>
                  </div>
               </div>
            </div>
         </fieldset>
         <h6>Ride Now</h6>
         <fieldset>
            <table class="table" id="ride_now">
               <tr>
                  <td>
                     <h1>Loading...</h1>
                  </td>
               </tr>
            </table>
         </fieldset>
         <h6>Ride Later</h6>
         <fieldset>
            <table class="table" id="ride_later">
               <tr>
                  <td>
                     <h1>Loading...</h1>
                  </td>
               </tr>
            </table>
         </fieldset>
         <h6>Surge Price</h6>
         <fieldset>
            <table class="table" id="surge_price">
               <tr>
                  <td>
                     <h1>Loading...</h1>
                  </td>
               </tr>
            </table>
         </fieldset>
         <button type="submit" id="submit"></button>
      </form>
   </div>
</div>

<script src="https://maps.googleapis.com/maps/api/js?key={{settingValue('geo_coder')}}&libraries=drawing,places&callback=initMap" async defer>    </script>
<script>


   $("#submit").hide();
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
       $(document).on('blur','#vehicle_type',function(){
           var a = $('#vehicle_type').val();
           var form1 = "";
           var form2 = "";
           var form3 = "";
           for(var i = 0; i < a.length; i++){
               form1 += '<tr><td><label class="text-uppercase font-size-sm font-weight-bold">'+a[i]+'</label></td><td><div class="row"><div class="col-md-6"><div class="form-group"><label><b class="base_price">Base Price (Kilometer)</b><span class="text-danger">*</span>:</label><input type="text" name="ridenow_base_price['+i+']" placeholder="Base Price" class="form-control" required></div></div><div class="col-md-6"><div class="form-group"><label><b>Base Distance</b><span class="text-danger">*</span>:</label><select name="ridenow_base_distance['+i+']" id="ridenow_base_distance" class="form-control" required><option value="" selected disabled>Select Base Distance</option>@for ($i = 1; $i < 15; $i++)<option value="{{$i}}" >{{$i}}</option>@endfor</select></div></div></div><div class="row"><div class="col-md-6"><div class="form-group"><label><b class="price_per_distance">Price per Distance (Kilometer)</b><span class="text-danger">*</span>:</label><input type="text" name="ridenow_price_per_distance['+i+']" placeholder="Price per Distance" class="form-control" required></div></div><div class="col-md-6"><div class="form-group"><label><b>Price per time</b><span class="text-danger">*</span>:</label><input type="text" name="ridenow_price_per_time['+i+']" placeholder="Price per time" class="form-control" required></div></div></div><div class="row"><div class="col-md-4"><div class="form-group"><label><b>Free Waiting time (Minutes)</b><span class="text-danger">*</span>:</label><input type="text" name="ridenow_free_waiting_time['+i+']" placeholder="Free Waiting time" class="form-control" required></div></div><div class="col-md-4"><div class="form-group"><label><b>Waiting Change</b><span class="text-danger">*</span>:</label><input type="text" name="ridenow_waiting_charge['+i+']" placeholder="Waiting Change" class="form-control" required></div></div><div class="col-md-4"><div class="form-group"><label><b>Cancellation fee</b><span class="text-danger">*</span>:</label><input type="text" name="ridenow_cancellation_fee['+i+']" placeholder="Cancellation Fee" class="form-control" required></div></div></div></td><tr>';
   
               form2 += '<tr><td><label class="text-uppercase font-size-sm font-weight-bold">'+a[i]+'</label></td><td><div class="row"><div class="col-md-6"><div class="form-group"><label><b class="base_price">Base Price (Kilometer)</b><span class="text-danger">*</span>:</label><input type="text" name="ridelater_base_price['+i+']" placeholder="Base Price" class="form-control" required></div></div><div class="col-md-6"><div class="form-group"><label><b>Base Distance</b><span class="text-danger">*</span>:</label><select name="ridelater_base_distance['+i+']" id="ridelater_base_distance" class="form-control" required><option value="" selected disabled>Select Base Distance</option>@for ($i = 1; $i < 15; $i++)<option value="{{$i}}" >{{$i}}</option>@endfor</select></div></div></div><div class="row"><div class="col-md-6"><div class="form-group"><label><b>Price per time</b><span class="text-danger">*</span>:</label><input type="text" name="ridelater_price_per_time['+i+']" placeholder="Price per time" class="form-control" required></div></div><div class="col-md-6"><div class="form-group"><label><b class="price_per_distance">Price per Distance (Kilometer)</b><span class="text-danger">*</span>:</label><input type="text" name="ridelater_price_per_distance['+i+']" placeholder="Price per Distance" class="form-control" required></div></div></div><div class="row"><div class="col-md-4"><div class="form-group"><label><b>Free Waiting time (Minutes)</b>:</label><input type="text" name="ridelater_free_waiting_time['+i+']" placeholder="Free Waiting time" class="form-control" required></div></div><div class="col-md-4"><div class="form-group"><label><b>Waiting Change</b><span class="text-danger">*</span>:</label><input type="text" name="ridelater_waiting_charge['+i+']" placeholder="Waiting Change" class="form-control" required></div></div><div class="col-md-4"><div class="form-group"><label><b>Cancellation fee</b><span class="text-danger">*</span>:</label><input type="text" name="ridelater_cancellation_fee['+i+']" placeholder="Cancellation Fee" class="form-control" required></div></div></div></td><tr>';
   
               form3 += '<tr><td><label class="text-uppercase font-size-sm font-weight-bold">'+a[i]+'</label><br><button type="button" class="btn bg-green-400 btn-icon rounded-round legitRipple addrow" id="'+a[i]+'" value="'+i+'" data-popup="tooltip" data-value="1" title="" data-placement="bottom" data-original-title="Add"> <i class="icon-plus3"></i> </button></td><td class="'+a[i]+'"><div class="row"><div class="col-md-6"><div class="form-group"><label><b>Surge Price</b>:</label><input type="text" name="sruge_price['+i+'][0]" placeholder="Sruge Price" class="form-control"></div></div><div class="col-md-6"><div class="form-group"><label><b>Start Time</b>:</label><input type="time" name="start_time['+i+'][0]" class="form-control"></div></div><div class="col-md-6"><div class="form-group"><label><b>End Time</b>:</label><input type="time" name="end_time['+i+'][0]" class="form-control"></div></div><div class="col-md-6"><div class="form-group"><label><b>Available Days</b>:</label><select name="available_days['+i+'][0][]" multiple="multiple" class="form-control"><option value="Sunday">Sunday</option><option value="Monday">Monday</option><option value="Tuesday">Tuesday</option><option value="Wednesday">Wednesday</option><option value="Thursday">Thursday</option><option value="Friday">Friday</option><option value="Saturday">Saturday</option></select></div></div></div></td><tr>';
           }
           $("#ride_now").html(form1);
           $("#ride_later").html(form2);
           $("#surge_price").html(form3);
       });
   
       $(document).on('click',".addrow",function(){
           var ids = $(this).attr('id');
           var datavalue = $(this).attr('data-value');
           var value = $(this).val();
           var text = '<hr><div class="row"><div class="col-md-6"><div class="form-group"><label><b>Surge Price</b>:</label><input type="text" name="sruge_price['+value+']['+datavalue+']" placeholder="Sruge Price" class="form-control"></div></div><div class="col-md-6"><div class="form-group"><label><b>Start Time</b>:</label><input type="time" name="start_time['+value+']['+datavalue+']" class="form-control"></div></div><div class="col-md-6"><div class="form-group"><label><b>End Time</b>:</label><input type="time" name="end_time['+value+']['+datavalue+']" class="form-control"></div></div><div class="col-md-6"><div class="form-group"><label><b>Available Days</b>:</label><select name="available_days['+value+']['+datavalue+'][]" multiple="multiple" class="form-control"><option value="Sunday">Sunday</option><option value="Monday">Monday</option><option value="Tuesday">Tuesday</option><option value="Wednesday">Wednesday</option><option value="Thursday">Thursday</option><option value="Friday">Friday</option><option value="Saturday">Saturday</option></select></div></div></div>';
           $("."+ids).append(text);
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
        
        var map = new google.maps.Map(document.getElementById('map-canvas'), {
            center: {
                lat: 11.0469218,
                lng: 76.981734
            },
            zoom: 8
        });
        
        var pickupPoint = document.getElementById('pin_the_location');
        var pickupPointAutocomplete = new google.maps.places.Autocomplete(pickupPoint);
        
        pickupPointAutocomplete.addListener('place_changed', function () {
            var pickupPointPlace = pickupPointAutocomplete.getPlace();
            // place variable will have all the information you are looking for.
            
            $('#pick_latitude').val(pickupPointPlace.geometry['location'].lat());
            $('#pick_longitude').val(pickupPointPlace.geometry['location'].lng());
            pickupPoint.focus();

            lat = pickupPointPlace.geometry['location'].lat();
            lng = pickupPointPlace.geometry['location'].lng();

            //console.log(lat, lng);

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


    function drawing_add(map) {
        console.log(map);
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

<script>
   $(document).ready(function() {
       $('#vehicle_type').select2();
       $('#payment_type').select2();
   });
   
</script>

@endsection