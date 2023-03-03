<link href="{{ asset('backend/assets/css/jquery.multiselect.css') }}" rel="stylesheet" type="text/css">
<style>
    .autocompletes{
        z-index: 9999;
        position: absolute;
        background: #fff;
        border: solid 1px #000;
        height:auto;
        max-height: 250px;
        overflow: hidden;
        overflow-y: scroll;
    }
    .autocompletes ul{
        padding: 0px;
    }
    .autocompletes ul li{
        list-style: none;
        padding: 10px;
        font-size: 15px;
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



</style>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Share Trip Information</title>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>
<body>
  
<div class="container">
  <h1><img src="{{ asset('backend/RODA_logo.png') }}"  alt=""></h1><hr>
  <div class="container-fluid">
  
 <div class="row">
  <div class="col-sm-5" style="border: 2px solid black; border-radius: 15px; box-shadow: 15px 15px grey;">
    <div class="card">
      <div class="card-body">
      <h4 style="color:#FFD60B;">Trip Information</h4>
        <h5 class="card-title"><b>Invoice :</b> #{{$requests->request_number}}</h5>
        <p class="card-text"><b>Vehicle Name :</b> {{$requests->driverDetail->driver->vehicletype->vehicle_name}}</p>
        <p class="card-text"><b>Car Number :</b> {{$requests->driverDetail->driver->car_number}}</p>
        <p class="card-text"><b>Car Model :</b> {{$requests->driverDetail->driver->car_model}}</p>

       

      </div>
      <br>
    </div>
  </div>
  <div class="col-sm-2">
    <div class="card">
      <div class="card-body">
     
      </div>
      <br>
    </div>
  </div>
  <div class="col-sm-5"  style="border: 2px solid black; border-radius: 15px; box-shadow: 15px 15px grey;">
    <div class="card">
      <div class="card-body">
        <!--<h4 style="color:#FFD60B;">Driver Information</h4><h4 style="float:right; margin-top:-20px;"><img src="{{$requests->driverDetail->profile_pic ? $requests->driverDetail->profile_pic : asset('backend/global_assets/images/demo/users/face6.jpg') }}" width="72" height="72" alt=""></h4>-->

        <h5 class="card-title"><b>Driver Name :</b> {{$requests->driverDetail->firstname}} {{$requests->driverDetail->lastname}}</h5>
        <p class="card-text"><b>Phone Number :</b> {{$requests->driverDetail->phone_number}}</p>
        <p class="card-text"><b>Email :</b> {{$requests->driverDetail->email ? $requests->driverDetail->email : ' -'}}</p>

      </div>
    </div>
     <br>
  </div>
  
</div>

<br><br>

<div class="col-md-12" >
<div id="map-canvas" style="width:100%;height:100%;"></div>
</div>



</body>
</html>









<script src="https://maps.googleapis.com/maps/api/js?key={{settingValue('geo_coder')}}"></script>


<script type="text/javascript">
    $(function () {
    "use strict";
    
    $(".popup img").click(function () {
        var $src = $(this).attr("src");
        $(".show").fadeIn();
        $(".img-show img").attr("src", $src);
    });

    $(".popup1 img").click(function () {
        var $src = $(this).attr("src");
        $(".show1").fadeIn();
        $(".img-show1 img").attr("src", $src);
    });
    
    $("span, .overlay1").click(function () {
        $(".show1").fadeOut();
    });
    $("span, .overlay").click(function () {
        $(".show").fadeOut();
    });
    
});


    function initialize() {
    var map = new google.maps.Map(
        document.getElementById("map_canvas"), {
        center: new google.maps.LatLng(51.276092, 1.028938),
        zoom: 13,
        mapTypeId: google.maps.MapTypeId.ROADMAP
        });
    var directionsService = new google.maps.DirectionsService();
    var directionsDisplay = new google.maps.DirectionsRenderer({
        map: map,
        preserveViewport: true
    });
    directionsService.route({
        origin: new google.maps.LatLng(51.269776, 1.061326),
        destination: new google.maps.LatLng(51.30118, 0.926486),
        waypoints: [{
        stopover: false,
        location: new google.maps.LatLng(51.263439, 1.03489)
        }],
        travelMode: google.maps.TravelMode.DRIVING
    }, function(response, status) {
        if (status === google.maps.DirectionsStatus.OK) {
        // directionsDisplay.setDirections(response);
        var polyline = new google.maps.Polyline({
            path: [],
            strokeColor: '#0000FF',
            strokeWeight: 3
        });
        var bounds = new google.maps.LatLngBounds();


        var legs = response.routes[0].legs;
        for (i = 0; i < legs.length; i++) {
            var steps = legs[i].steps;
            for (j = 0; j < steps.length; j++) {
            var nextSegment = steps[j].path;
            for (k = 0; k < nextSegment.length; k++) {
                polyline.getPath().push(nextSegment[k]);
                bounds.extend(nextSegment[k]);
            }
            }
        }

        polyline.setMap(map);
        } else {
        window.alert('Directions request failed due to ' + status);
        }
    });
    }
    google.maps.event.addDomListener(window, "load", initialize);


</script>

<script type="text/javascript">
    $(".autocompletes").hide();
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

    function driverSearch(url){
        // console.log(url);
        $.ajax({
            url: url,
            type: "GET",
            dataType: 'json',
            success: function (data) {
                // console.log(data);
                if(data.success){
                    if(data.data.hold_status){
                        swal({
                            title: "{{ __('errors') }}",
                            text: data.message,
                            icon: "error",
                        }).then((value) => {        
                            // window.location.href = "../driver-document/"+$('#driver_id').val();
                        });
                    }
                    else{
                        swal({
                            title: "{{ __('success') }}",
                            text: data.message,
                            icon: "success",
                        }).then((value) => {        
                            // window.location.href = "../driver-document/"+$('#driver_id').val();
                        });
                    }
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                var err = eval("(" + xhr.responseText + ")");
                console.log(err);
                console.log('2');
            }
        });
    }

    function myFunction() {
        var value = $("#customer_number").val();
        var text = "";
        $.ajax({
            url: "{{ url('get-driver-detail') }}/{{$requests->getZonePrice ? $requests->getZonePrice->type_id : '1'}}/"+value,
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
                
            }
        });
    }

    $(document).on('click',".getCustomer",function(){
        var id = $(this).attr('id');
        $.ajax({
            url: "{{ url('get-customer-detail') }}/"+id,
            type: "GET",
            dataType: 'json',
            success: function (data) {
                $("#customer_number").val(data.data.phone_number);
                $("#driver_id").val(data.data.id);
                $(".autocompletes").hide();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                
            }
        });
    })

    $(document).on('click','#assign',function(){
        var slug = $("#driver_id").val();

        $.ajax({
            url: "{{ url('assign-driver-trip') }}/{{$requests->id}}/"+slug,
            type: "GET",
            dataType: 'json',
            success: function (data) {
                if(data.success){
                    location.reload();
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                
            }
        });
    })

</script>
<script>
    var pick_place_id = '';
    var drop_place_id = '';
    var stopPlaceId = [];
    var map = '';
    @if($requests->requestPlace)
    function initMap() {

        map = new google.maps.Map(document.getElementById('map-canvas'), {
            center: {
                lat: {{$requests->requestPlace->pick_lat}},
                lng: {{$requests->requestPlace->pick_lng}}
            },
            zoom: 13
        });
        travelMode = google.maps.TravelMode.WALKING;
        directionsService = new google.maps.DirectionsService();
        directionsRenderer = new google.maps.DirectionsRenderer();
        directionsRenderer.setMap(map);

        // const image = "https://developers.google.com/maps/documentation/javascript/examples/full/images/beachflag.png";
        // const beachMarker = new google.maps.Marker({
        //         position: { lat: {{$requests->requestPlace->pick_lat}}, lng: {{$requests->requestPlace->pick_lng}} },
        //         map,
        //         // icon: image,
        //         title: "{{$requests->requestPlace->pick_address}}",
        //     });
        
        var geocoder = new google.maps.Geocoder;
        var latlng = { lat: {{$requests->requestPlace->pick_lat}}, lng: {{$requests->requestPlace->pick_lng}} };

        geocoder.geocode({'location': latlng}, function(results, status) {
            if (status === google.maps.GeocoderStatus.OK) {
            if (results[1]) {
                pick_place_id = results[1].place_id;
                this.route();
            } else {
                window.alert('No results found');
            }
            } else {
            window.alert('Geocoder failed due to: ' + status);
            }
        });
        var geocoder = new google.maps.Geocoder;
        var latlng = { lat: {{$requests->requestPlace->drop_lat}}, lng: {{$requests->requestPlace->drop_lng}} };

        geocoder.geocode({'location': latlng}, function(results, status) {
            if (status === google.maps.GeocoderStatus.OK) {
            if (results[1]) {
                drop_place_id = results[1].place_id;
                this.route();
            } else {
                window.alert('No results found');
            }
            } else {
            window.alert('Geocoder failed due to: ' + status);
            }
        });
    }

    // class AutocompleteDirectionsHandler {
        function route() {
            if (!pick_place_id || !drop_place_id) {
                return;
            }
            var stop_address = "{{$requests->requestPlace->stop_address}}";
            stopPlaceId.push({
                    location: stop_address,
                    stopover: true,
                });

            const me = this;
            if(stop_address != ""){
                var request = {
                    origin: { placeId: pick_place_id },
                    destination: { placeId: drop_place_id },
                    waypoints: stopPlaceId,
                    travelMode: travelMode,
                };
            }
            else{
                var request = {
                    origin: { placeId: pick_place_id },
                    destination: { placeId: drop_place_id },
                    travelMode: travelMode,
                };
            }
            
            directionsService.route(request,
                (response, status) => {
                    if (status === "OK") {
                    me.directionsRenderer.setDirections(response);
                    } else {
                    window.alert("Directions request failed due to " + status);
                    }
                });
        }
        @endif
    // }

    
</script>

<script src="https://maps.googleapis.com/maps/api/js?key={{settingValue('geo_coder')}}&libraries=drawing,places&callback=initMap" async defer>    
</script>
<script src="https://www.gstatic.com/firebasejs/7.19.0/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/7.19.0/firebase-database.js"></script>
<script src="https://www.gstatic.com/firebasejs/7.19.0/firebase-analytics.js"></script>


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
        var tripRef = firebase.database().ref('requests');
        tripRef.on('value', async function(snapshot) {
            var data = snapshot.val();
            await loadDriverIcons(data);
        });

        var iconBase = '{{ asset("backend") }}';
        var icons = {
          available: {
            name: 'Available',
            key: 'free',
            icon: iconBase + '/car.png'
          },
          ontrip: {
            name: 'OnTrip',
            key: 'ontrip',
            icon: iconBase + '/car.png'
          }
        };


        function loadDriverIcons(data){

            deleteAllMarkers();
            // console.log(data);
            // var result = Object.entries(data);
            Object.entries(data).forEach(([key, val]) => {  
                // var infowindow = new google.maps.InfoWindow({
                //     content: contentString
                // });
                console.log(val.request_id);
                if(val.request_id == "{{$requests->id}}" && val.driver_trip_status < 3){
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

                // var phone = '+91 7200704057';


                var contentString = '<div id="content">' +
                '<div id="siteNotice">' +
                '</div>' +
                '<h3 id="firstHeading" class="firstHeading"> <i class="fa fa-id-card" aria-hidden="true"></i> &nbsp&nbsp'+ firstName +'&nbsp&nbsp</h3>' +
                '<h4 id="bodyContent" style="color:#4d5051"> <i class="fa fa-phone" aria-hidden="true"></i> &nbsp&nbsp&nbsp&nbsp' + phone +'&nbsp&nbsp'+
                '</h4>' +
                '</div>';

                var infowindow = new google.maps.InfoWindow({
                content: contentString
                });

                // if( typeof val.lat_lng_array !=  'undefined'  ) {
                var iconImg = '';


                if(val.is_available == true){
                    iconImg = icons['available'].icon;
                }else{
                    iconImg = icons['ontrip'].icon;
                }

                var date = new Date();
                var timestamp = date.getTime();
                var currentTime = +new Date(timestamp - 1 * 60000);
                
                //   console.log(val);

                // if(val.is_available == true && showFreeDrivers == true && val.is_active == true ) {

                    var carIcon = new google.maps.Marker({
                        position: new google.maps.LatLng(val.lat,val.lng),
                        icon : {
                            url: iconImg, // url
                            origin: new google.maps.Point(0, 0),
                            anchor: new google.maps.Point(17, 34),
                            scaledSize: new google.maps.Size(40, 40)

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
                    carIcon.setMap(map);

                // }else if(val.is_available == false && showUnAvailableDrivers == true ) {


                //     var carIcon = new google.maps.Marker({
                //         position: new google.maps.LatLng(val.l[0],val.l[1]),
                //         icon : {
                //             url: iconImg, // url
                //             origin: new google.maps.Point(0, 0),
                //             anchor: new google.maps.Point(17, 34),
                //             scaledSize: new google.maps.Size(45, 30)
                //             // scaledSize: new google.maps.Size(40, 40), // scaled size
                //             // origin: new google.maps.Point(0,0), // origin
                //             // anchor: new google.maps.Point(0, 0) // anchor
                //         },
                //         map: map,
                    
                //     });

                //     carIcon.addListener('click', function() {
                //         infowindow.open(map, carIcon);
                //     });

                //     // deleteAllMarkers();
                    
                //     marker.push(carIcon);
                //     carIcon.setMap(map);

                // }

                
                // }
            }


            });
        }
        function deleteAllMarkers() {
            for(var i=0;i<marker.length;i++){
                marker[i].setMap(null);
            }
        }

</script>