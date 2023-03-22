@extends('layouts.app')

@section('content')
<style>
    #map {
    height: 500px;
        width:100%;
        margin-top:10px !important;
        overflow: inherit !important;
    }

    html, body {
    height: 100%;
    margin: 0;
    padding: 0;
    }


    #legend {
        font-family: Arial, sans-serif;
        background: #fff;/*transparent;*/
        padding: 5px;
        margin: 5px;
        border: 3px solid #000;
        width:120px;
        font-size: 8px;
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

    .pac-controls label {
    font-family: Roboto;
    font-size: 13px;
    font-weight: 300;
    }

    #pac-input {
        background-color: #f7f7f7;
        font-size: 15px;
        font-weight: 300;
        margin-top: 10px;
        padding: 0 11px 0 13px;
        text-overflow: ellipsis;
        height: 25px;
        width: 400px;
        border: 1px solid #c7c7c7;
        border-bottom: none;
        border-radius: 10px;
    }

    #pac-input:focus {
        border-color: #4d90fe;
        margin-top:50px;
        transition: 1s ease all;

    }
    :-webkit-input-placeholder { /* WebKit, Blink, Edge */
        color:    #909;
    }
    :-moz-placeholder { /* Mozilla Firefox 4 to 18 */
        color:    #909;
        opacity:  1;
    }
    ::-moz-placeholder { /* Mozilla Firefox 19+ */
        color:    #909;
        opacity:  1;
    }
    :-ms-input-placeholder { /* Internet Explorer 10-11 */
        color:    #909;
    }
    ::-ms-input-placeholder { /* Microsoft Edge */
        color:    #909;
    }
</style>

<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
   
      

      <div class="col-12">
        <div id="map"></div>

        <div id="legend"><h5> @lang('view.legend') </h5></div>
      </div>

    </div>
  </div>



  <script src="https://maps.google.com/maps/api/js?key={{settingValue('geo_coder')}}&sensor=false&libraries=places"></script>


<!-- The core Firebase JS SDK is always required and must be listed first -->
<script src="http://www.gstatic.com/firebasejs/7.19.0/firebase-app.js"></script>
<script src="http://www.gstatic.com/firebasejs/7.19.0/firebase-database.js"></script>
<!-- TODO: Add SDKs for Firebase products that you want to use https://firebase.google.com/docs/web/setup#available-libraries -->
<script src="http://www.gstatic.com/firebasejs/7.19.0/firebase-analytics.js"></script>
{{-- <script type="module" src="{{ asset('js/geofire.js') }}"></script> --}}
<script>


    var fire_drivers = [];
    const RIDENOW = 1;
    const RIDELATER = 2;
    const INSTANTTRIP = 3;
    const FULLLOAD = 4;
    const HALFLOAD = 5;
    const CORPORATE = 6;
    var heatmapData = [];
    var pickLat = [];
    var pickLng = [];
    var Lat = "{{ 11.0023559 }}"; //'51.1657';  // Have to change this based on admin location
    var Lng = "{{ 76.9652617 }}"; //'10.4515';  // Have to change this based on admin location
    var mapZoom = 7;
    let map, heatmap;
    let $tripDistance,$tripDuration = 0;

    google.maps.event.addDomListener(window, 'load', initialize);

    var directionsService = new google.maps.DirectionsService();
    var directionsRenderer = new google.maps.DirectionsRenderer({suppressMarkers: true});
    var marker = [];
    var vehicleMarker = [];
    var pickUpMarker,dropMarker;
    var pickUpMarkerData = [];
    var dropOffMarkerData = [];
    var pickUpLocation,dropLocation;
    var pickUpLat, pickUpLng, dropLat, dropLng;
    var $timer,$toast;

    var iconBase = '{{ asset("backend") }}';
    var icons = {
        pickup: {
            name: 'Pickup',
            icon: iconBase + '/on-trip.png',
            mapicon: {
                    url: iconBase + '/pickup.png',
                    scaledSize: new google.maps.Size(30, 35), // scaled size
                    origin: new google.maps.Point(0,0), // origin
                    anchor: new google.maps.Point(10, 26) // anchor
                },
        },
        drop: {
            name: 'Drop',
            icon: iconBase + '/on-trip.png',
            mapicon: {
                    url: iconBase + '/drop.png',
                    scaledSize: new google.maps.Size(30, 35), // scaled size
                    origin: new google.maps.Point(0,0), // origin
                    anchor: new google.maps.Point(10, 26) // anchor
                },
        },
        available: {
            name: 'Available',
            icon: iconBase + '/on-trip.png',
        },
        ontrip: {
            name: 'On Trip',
            icon: iconBase + '/on-trip.png',
        },
    };


    // Your web app's Firebase configuration
    // For Firebase JS SDK v7.20.0 and later, measurementId is optional
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

    var driverRef = firebase.database().ref('drivers');

    
    driverRef.on('value', async function(snapshot) {
        var data = snapshot.val();
        console.log(data);
        await loadDriverIcons(data);
    });
    
    
    // function getDrivers(lat,lng,type){
    //     // var url = "{{ env('NODE_APP_URL') }}:{{ env('NODE_APP_PORT') }}/"+lat+'/'+lng+'/'+type
        
    //     // fetch(url)
    //     // .then(response => response.json())
    //     // .then(result => {
    //     //     console.log(result);
    //     // });
    //     // Create a GeoFire index
    //     var geoFire = new GeoFire(driverRef);
    //     var geoQuery = geoFire.query({
    //         center: [lat, lng],
    //         radius: 10
    //     });
    //     geoQuery.on("key_entered", function(key, location, distance) {
    //         driverRef.child(key).on('value', function(snap) {
    //             let driver = snap.val();

    //             let date = new Date();
    //             let timestamp = date.getTime();
    //             let conditional_timestamp = new Date(timestamp - 5 * 60000);

    //             if (conditional_timestamp < driver.updated_at) {
    //                 if (driver.is_active == 1 & driver.is_available == 1 & driver.type == vehicle_type) {
    //                     fire_drivers.push(driver);
    //                 }
    //             }
    //         });
    //     });
    // }
    

    // function loadDriverIcons(data){
    //     deleteAllMarkers();
    //     Object.entries(data).forEach(([key, val]) => {
    //         if(typeof val.l != 'undefined'){
    //             var contentString = `<div class="p-2">
    //                                 <h6>Name : ${val.first_name ?? '-' } </h6>
    //                                 <h6>Status : ${val.is_available ? 'Available' : 'OnTrip' } </h6>
    //                             </div>`;

    //             var infowindow = new google.maps.InfoWindow({
    //                 content: contentString
    //             });

    //             var iconImg = '';
                
    //             if(val.is_available == true){
    //                 iconImg = icons['available'].icon;
    //             }else{
    //                 iconImg = icons['ontrip'].icon;
    //             }
    //             // console.log(iconImg);

    //             // var carIcon = new google.maps.Marker({
    //             //     position: new google.maps.LatLng(val.l[0],val.l[1]),
    //             //     icon: iconImg,
    //             //     map: map
    //             // });

    //             let todayDate = new Date();
    //             let beforeFiveMins = Date.parse(todayDate) - 300000;
               
    //             if (val.updated_at > beforeFiveMins) {
                  
    //                 var carIcon = new google.maps.Marker({
    //                     position: new google.maps.LatLng(val.l[0],val.l[1]),
    //                     icon: iconImg,
    //                     map: map
    //                 });
                    
    //                 carIcon.addListener('click', function() {
    //                     infowindow.open(map, carIcon);
    //                 });

    //                 marker.push(carIcon);
    //                 carIcon.setMap(map);    
    //             }
                
    //             // carIcon.addListener('click', function() {
    //             //     infowindow.open(map, carIcon);
    //             // });

    //             // marker.push(carIcon);
    //             // carIcon.setMap(map);
    //         }
    //     });
    // }

    // function deleteAllMarkers() {
    //     for(var i=0;i<marker.length;i++){
    //         marker[i].setMap(null);
    //     }
    // }

    // Common function to draw marker on map - [pickup | drop]
    function placeLocationMarker(markerData,type){
        var locationVar,markerVar,getId,lat,lng;

        if(type == 'pickup'){
            markerVar = pickUpMarker
            getId = document.getElementById('pickup');
        }else if(type == 'drop'){
            dropLocation = locationVar
            markerVar = dropMarker
            getId = document.getElementById('drop');
        }

        var location = new google.maps.places.Autocomplete(getId)

        location.setComponentRestrictions({'country': ['ETH','IN']});
        
        location.addListener('place_changed', function() {
            var place = location.getPlace();

            if (!place.geometry) {
                return;
            }

            removeMarkers(markerData);
            lat = place.geometry.location.lat();
            lng = place.geometry.location.lng();

            locationVar = new google.maps.LatLng(lat,lng);

            if(type == 'pickup'){
                pickUpLat = lat
                pickUpLng = lng
                pickUpLocation = locationVar

                // loadDriverIcons();
            }else if(type == 'drop'){
                dropLat = lat
                dropLng = lng
                dropLocation = locationVar
            }


            markerVar = new google.maps.Marker({
                position: locationVar,
                icon: icons[type].mapicon,
                map,
                anchorPoint: new google.maps.Point(0, -29)
            });

            markerData.push(markerVar)

            // If the place has a geometry, then present it on a map.
            if (place.geometry.viewport) {
                map.fitBounds(place.geometry.viewport);
            } else {
                map.setCenter(place.geometry.location);
                map.setZoom(17);
            }
        
            markerVar.setPosition(place.geometry.location);
            markerVar.setVisible(true);

            if(type == 'pickup'){
                pickUpMarker = markerVar
            }else{
                dropMarker = markerVar
            }

            if(pickUpLocation && dropLocation)
                calcRoute(pickUpLocation,dropLocation)

            bindDataToForm(place.formatted_address,lat,lng,type);
        });   
    }

    // Initialize google maps
    function initialize(){
        var pickup = document.getElementById('pickup');
        var drop = document.getElementById('drop');
        var centerLatLng = new google.maps.LatLng(Lat, Lng);

        map = new google.maps.Map(document.getElementById('map'), {
            center: centerLatLng,
            zoom: mapZoom,
            mapTypeId: 'roadmap',
            mapTypeControl: true,
            mapTypeControlOptions: {
                style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR,
                position: google.maps.ControlPosition.TOP_CENTER,
            },
            zoomControl: true,
            zoomControlOptions: {
                position: google.maps.ControlPosition.RIGHT_BOTTOM,
            },
            scaleControl: true,
            streetViewControl: false,
            fullscreenControl: true,
        });

        directionsRenderer.setMap(map);

        placeLocationMarker(pickUpMarkerData,'pickup');
        placeLocationMarker(dropOffMarkerData,'drop');


        var legend = document.getElementById('legend');

        for (var key in icons) {
            var type = icons[key];
            if(type.name != 'Pickup' && type.name != 'Drop'){
                var name = type.name;
                var icon = type.icon;
                var div = document.createElement('div');
                div.innerHTML = '<img src="' + icon + '?legends"> ' +
                    `<span class="text">${name}</span>`;
                legend.appendChild(div);
            }
        }

        map.controls[google.maps.ControlPosition.RIGHT_CENTER].push(legend);
    }


    // Draw path from pickup to drop - map api
    function calcRoute(pickup,drop) {
        getVehicleTypes()
        var request = {
            origin: pickup,
            destination: drop,
            travelMode: google.maps.TravelMode['DRIVING']
        };
        directionsService.route(request, function(response, status) {
            if (status == 'OK') {
                var directionsData = response.routes[0].legs[0]
                $tripDistance = directionsData.distance.value / 1000
                $tripDuration = directionsData.duration.value / 60

                directionsRenderer.setDirections(response);
            }
        });
    }

    // // Add pick and drop address,Lat and Lng
    // function bindDataToForm(address,lat,lng,loc){
    //     document.getElementById(loc).value = address;
    //     document.getElementById(loc+'_lat').value = lat;
    //     document.getElementById(loc+'_lng').value = lng;
    // }

    // Remove markers already drawn on map
    function removeMarkers(markers){
        for(i=0; i < markers.length; i++){
            markers[i].setMap(null);
        }
    }

    // // Show and hide date time
    // $(document).on('click','.tripType',function(){
    //     let type = $(this).attr('id');
    //     getVehicleTypes();
    //     if(type == 'ridenow'){
    //         $('#ridenow').prop('checked',true);
    //         $('#ridelater').prop('checked',false);
    //         $('.datetimeCol').addClass('d-none');
    //     }else{
    //         $('#ridelater').prop('checked',true);
    //         $('#ridenow').prop('checked',false);
    //         $('.datetimeCol').removeClass('d-none');
    //     }
    // })

    // // Select Service type and show load
    // $(document).on('click','.serviceType',function(){
    //     let type = $(this).attr('id');
        
    //     if(type == 'taxi'){
    //         $('#taxi').prop('checked',true);
    //         $('#delivery').prop('checked',false);
    //         $('.deliveryLoadType').addClass('d-none');
    //     }else{
    //         $('#delivery').prop('checked',true);
    //         $('#taxi').prop('checked',false);
    //         $('.deliveryLoadType').removeClass('d-none');
    //     }
    // })

    // $(document).on('click','.serviceType',function(){
    //     getVehicleTypes();
    // })

    // $(document).on('click','.loadType',function(){
    //     getVehicleTypes();
    // })

    function getVehicleTypes(){
        // var vehicle_type = document.getElementById('vehicle_type').value;
        var price = document.getElementsByClassName('etaprice');
        var time = document.getElementsByClassName('etatime');
        var distance = document.getElementsByClassName('etadistance');
        var pickup = document.getElementById('pickup').value;
        var drop = document.getElementById('drop').value;

        var etaData = {
            'pick_lat':pickUpLat,
            'pick_lng':pickUpLng,
            'trip_type': RIDENOW,
            'trip': $('.serviceType:checked').attr('id'),
            'id': 0 // User id
        };
        
        if($('#ridelater').prop('checked') == true){
            etaData.trip_type = RIDELATER
        }

        if($('.serviceType:checked').attr('id') == 'delivery'){
            if($('#half_load').prop('checked') == true){
                etaData.trip_type = HALFLOAD
            }
            if($('#full_load').prop('checked') == true){
                etaData.trip_type = FULLLOAD
            }
        }
        

        if(pickup && drop){
            var etaUrl = "{{ url('dispatcher/get/types') }}"
            fetch(etaUrl,{
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json;charset=utf-8',
                    "X-CSRF-Token": "{{ csrf_token() }}"
                },
                body: JSON.stringify(etaData)
            })
            .then(response => response.json())
            .then(result => {
                console.log(result);
                if(result.success){
                    var drivers = result.type_list;
                    var driverList = '';
                    if (drivers.length > 0) {
                        $('.driverList').removeClass('d-none')
                        drivers.forEach(driver => {
                            var tripFares = calculateTripFare(driver.type_price[0]);
                            driverList += `<div class="card p-10 bg-light-info mb-2 driverDetail" style="cursor:pointer;" data-vehicle="${driver.id}">
                                                <div class="row">
                                                    <div class="col-8 mt-2">
                                                        <strong class="font-weight-bolder">${driver.name}</strong><br>
                                                    </div>
                
                                                    <div class="col-4 mt-2">
                                                        <div style="float: right">
                                                            <div class="avatar avatar-m">
                                                                <img src="${driver.icon}" alt="">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <span>${driver.currency} ${tripFares.min} - ${tripFares.max}</span>
                                                </div>
                                            </div>`;
                            });
                    }else{
                        $('.driverList').addClass('d-none')
                        driverList += `<div class="card p-10 bg-light-danger text-center noDrivers d-none">
                                        <div class="row">
                                            <div class="col-12 p-2">
                                                <span class="text-danger text-bold">No Drivers Found</span>
                                            </div>
                                        </div>
                                    </div>`;
                    }
                    
                    $('.driverList').html(driverList);
                }
            })
            .catch((error) => {
                console.log(error)
            });
        }
    }

    // // create trip button click event
    // $(document).on('click','.create-trip',function(){
    //     let validation = validateTripData()
    //     if(validation == 0){
    //         // $(this).prop('disabled','disabled');
    //         createTripRequest();
    //     }
    // });

    // function createTripRequest(){
    //     var typeId = $('#vehicle_type').val();
    //     var pickAdd = $('#pickup').val();
    //     var dropAdd = $('#drop').val();
    //     var driverNotes = $('#driver_notes').val();
    //     var userDetails = {
    //         'name' : $('#customer_name').val(),
    //         'address' : $('#customer_address').val(),
    //         'mobile' : $('#customer_mobile').val(),
    //     }
    //     var vehicle = $('.selectedDriver').attr('data-vehicle');

    //     var tripData = {
    //         'paymentOpt': "1", // Cash
    //         'platitude': pickUpLat,
    //         'plongitude': pickUpLng,
    //         'dlatitude': dropLat,
    //         'dlongitude': dropLng,
    //         'plocation': pickAdd,
    //         'dlocation': dropAdd,
    //         'customer': userDetails,
    //         'driver_notes': driverNotes,
    //         'dispatch_method': 'automatic',
    //         'is_share': 0,
    //         'type': vehicle
    //     }

    //     if($('.serviceType:checked').attr('id') == 'delivery'){
    //         if($('#half_load').prop('checked') == true){
    //             tripData.load = HALFLOAD
    //         }
    //         if($('#full_load').prop('checked') == true){
    //             tripData.load = FULLLOAD
    //         }
    //     }

    //     if($('#ridelater').prop('checked') == true){
    //         var date = $('#date').val();

    //         tripData.is_later = true;
    //         tripData.trip_start_time = date;
    //     }


    //     var tripUrl = "{{ url('dispatcher/request/create') }}"
        
    //     fetch(tripUrl, {
    //         method: 'POST',
    //         headers: {
    //         'Content-Type': 'application/json;charset=utf-8',
    //         "X-CSRF-Token": "{{ csrf_token() }}"
    //         },
    //         body: JSON.stringify(tripData)
    //     })
    //     .then(response => response.json())
    //     .then(result => {
    //         console.log(result);
    //         if (result.success == false) {
    //             $('.create-trip').prop('disabled',false);
    //             showStatusToast(result.message,5000,'center','#f73b56');
    //             $('.create-trip').prop('disabled',false);
    //             return false;
    //         }
    //         if (result.success == true) {
    //             var data = result.data
    //             console.log(data)
    //             if(data.later == 1 || result.message == "searching_for_drivers"){
    //                 var mes = "All drivers are busy, A driver will be assigned soon";
    //                 if(data.later) mes = "Your order is confirmed. A driver will be assigned soon"
                    
    //                 showStatusToast(mes,5000,'center','#2aa34b');
    //                 resetFormData();

    //                 setTimeout(() => {
    //                     window.location.href = "{{ url('dispatcher/dashboard') }}"; 
    //                 }, 3000);
                    
    //             }else{
    //                 var text = `<div class="avatar avatar-lg" style="align-items: center;">
    //                             <img src="{{ asset("dispatcher/assets/img/loader-preview.svg") }}">
    //                             <h5 style="padding-left: 10px;padding-top: 10px;">Searching for driver, please wait for driver respond</h5>
    //                         </div>`;

    //                 showStatusToast(text,5000,'center','#2aa34b');
    //                 resetFormData();
    //                 $timer = setInterval(() => {
    //                     updateTripStatus(data.request.id)
    //                 }, 3000);
    //             }
    //         }
    //     });
    // }

    // // Fetch trip status and show toast notification
    // function updateTripStatus(tripId){
    //     var tripUrl = "{{ url('dispatcher/request/status') }}/"+tripId
        
    //     fetch(tripUrl)
    //         .then(response => response.json())
    //         .then(result => {
    //             if(result.success == true){
    //                 // remove toast already active
    //                 $toast.toastElement.classList.remove('on')
    //                 if(result.tripStatus == "REQUESTED"){
    //                     var text = `<div class="avatar avatar-lg" style="align-items: center;">
    //                                 <img src="{{ asset("dispatcher/assets/img/loader-preview.svg") }}">
    //                                 <h5 style="padding-left: 10px;padding-top: 10px;">Searching for driver, please wait for driver respond</h5>
    //                             </div>`;
    //                     showStatusToast(text,5000,'center','#2aa34b')
    //                 }else if(result.tripStatus == "DRIVING_TO_PICKUP"){
    //                     var text = `Your request has been accepted by driver`;
    //                     showStatusToast(text,5000,'center','#2aa34b')
    //                     setTimeout(() => {
    //                         window.location.href = "{{ url('dispatcher/history') }}/"+tripId; 
    //                     }, 3000);
    //                     clearInterval($timer);
    //                 }else if(result.tripStatus == "CAR_NOT_AVAILABLE"){
    //                     var text = `No drivers available at this moment, please try again some time`;
    //                     showStatusToast(text,5000,'center','#f73b56')
    //                     clearInterval($timer);
    //                 }else if(result.tripStatus == "CANCELLED"){
    //                     var text = `No drivers available at this moment, please try again some time`;
    //                     showStatusToast(text,5000,'center','#f73b56')
    //                     clearInterval($timer);
    //                 }
    //             }
    //         });
    // }

    // // Calculate trip fare
    // function calculateTripFare(fare){
    //     var tripDis = $tripDistance - fare ? fare.base_distance : 0
    //     var disPrice = tripDis * fare ? fare.price_per_distance : 0
    //     var timePrice = $tripDuration * fare ? fare.price_per_time : 0

    //     var price = fare ? fare.base_price : 0 + disPrice + timePrice;
    //     var perForMax = parseFloat(price * 8 / 100);
        
    //     var minPrice = parseFloat(price).toFixed(2)
    //     var maxPrice = parseFloat(price + perForMax).toFixed(2);

    //     return {'min':minPrice, 'max':maxPrice}
    // }

    // // To select driver on manual dispatch option
    // $(document).on('click','.driverDetail',function(){
    //     $('.driverDetail').removeClass('selectedDriver');
    //     $(this).addClass('selectedDriver')
    //     var type = $(this).attr('data-vehicle')

    //     // getDrivers(pickUpLat,dropLat,type);
    // });


    // // To show trip status toast notification
    // function showStatusToast(text,duration,position,color){
    //     $toast = Toastify({
    //         text: text,
    //         duration: duration,
    //         close:false,
    //         gravity:"top",
    //         position: position,
    //         backgroundColor: color,
    //     }).showToast();
    // }

    // // To Validate trip form data
    // function validateTripData(){
    //     let columns = ['customer_mobile','customer_name','pickup','drop'];//,'driver_info','dispatcher_info'
    //     var j = 0;
    //     var errCount = 0;
    //     columns.forEach((element,i) => {
    //         if($('#'+element).val() == ''){
    //             errCount++
    //             if(i == j){
    //                 $('#'+element).focus();
    //             }
    //             $('#'+element).parent().find('span').text('The Field is required');
    //             return false;
    //         }
    //         j++;
    //     });

    //     var a = $("#customer_mobile").val();
    //     var filter = /^[+][0-9]{7,15}$/;
    //     if (!filter.test(a)) {
    //         $(".mobile-error").text("Please country code in phone number");
    //         return false;
    //     }
    //     else{
    //         $(".mobile-error").text("");
    //     }

    //     if($('.selectedDriver').length == 0){
    //         showStatusToast('Please select vehicle type to continue',5000,'right','#f73b56')
    //         errCount++;
    //     }

    //     if($('#ridelater').prop('checked') == true){
    //         var prescheduledTime = 60;
    //         var date = $('#date').val();
    //         var currentDate = new Date();
    //         var orderDate = new Date(date);

    //         if (date) {
    //             if(orderDate > currentDate){
    //                 var diff = diff_minutes(currentDate,orderDate);
    //                 if(diff <= prescheduledTime){
    //                     $('#date').parent().find('span.err').text('Date time has to be '+prescheduledTime+' Mins in advance');
    //                     errCount++;
    //                 }
    //             }else{
    //                 $('#date').parent().find('span.err').text('Date time must be greater than current time');
    //                 errCount++;
    //             }    
    //         }else{
    //             $('#date').parent().find('span.err').text('Date time Field is required');
    //             errCount++;
    //         }
            
    //     }

    //     setTimeout(() => {
    //         removeErrMsg()
    //     }, 5000);

    //     return errCount;
    // }

    // // To reset the whole form and map after create trip
    // function resetFormData(){
    //     let columns = ['customer_mobile','customer_name','customer_address','pickup','drop','driver_notes'];
    //     var li = '<li>-</li>';

    //     columns.forEach((element,i) => {
    //         $('#'+element).val('');
    //     });

    //     // getCapacity($('#vehicle_type'));
    //     $('.completedTrip').html('-')
    //     $('.cancelledTrip').html('-')
    //     $('.history').html(li);
    //     $('.pickupSuggestion').html(li)
    //     $('.dropSuggestion').html(li)
    //     $('.driverList').addClass('d-none')
    //     $('.driverDetail').removeClass('selectedDriver');

    //     $('#ridelater').prop('checked',false);
    //     $('#ridenow').prop('checked',true);
    //     $('.datetimeCol').addClass('d-none');
    //     $('#taxi').prop('checked',true);
    //     $('#delivery').prop('checked',false);
    //     $('#half_load').prop('checked',true);
    //     $('#full_load').prop('checked',false);

    //     if(pickUpMarker || dropMarker){
    //         directionsRenderer.setMap(null);
    //         if (pickUpMarker) pickUpMarker.setMap(null)
    //         if (dropMarker) dropMarker.setMap(null)
    //     }

    // }

    // function diff_minutes(dt2, dt1) {
    //     var diff =(dt2.getTime() - dt1.getTime()) / 1000;
    //     diff /= 60;

    //     return Math.abs(Math.round(diff));
    // }

    // // Hide form error message from settimeout
    // function removeErrMsg(){
    //     $('.err').text('');
    // }

    // // Blur event on user mobile 
    // $(document).on('blur','#customer_mobile',function(){
    //     let mobile = $(this).val();

    //     getUserInfo(mobile);
    // })


    // // Fetch user details trip count and suggestions based on mobile
    // function getUserInfo(mobile){
    //     let completedTrip = 0
    //     let cancelledTrip = 0
    //     let requestNumber = '';
    //     let pickSug = '';
    //     let dropSug = '';

    //     var getUserInfoUrl = "{{ url('dispatcher/getuser') }}/"+mobile
    //     fetch(getUserInfoUrl)
    //     .then(response => response.json())
    //     .then(result => {
    //         if(result.length != 0){
    //             var name = result[0].name
    //             var addr = result[0].address
    //             result.forEach((element,i) => {
    //                 var tripDet = element.request;

    //                 if(tripDet){
    //                     if(tripDet.is_completed == 1){
    //                         completedTrip += 1;
    //                     }else if(tripDet.is_cancelled == 1){
    //                         cancelledTrip += 1
    //                     }
    //                 }

    //                 if(i <= 2){
    //                     var historyUrl = "{{ url('dispatcher/history') }}/" + tripDet.id;
    //                     requestNumber += `<li class="text-sm"> <a href="${historyUrl}" target="_blank">${tripDet.request_id}</a></li>`;
    //                     pickSug += `<li class="pickingSug" data-lat="${tripDet.request_place.pick_latitude}" data-lng="${tripDet.request_place.pick_longitude}">${tripDet.request_place.pick_location}</li>`;
    //                     if (tripDet.request_place.drop_location) {
    //                         dropSug += `<li class="dropingSug" data-lat="${tripDet.request_place.drop_latitude}" data-lng="${tripDet.request_place.drop_longitude}">${tripDet.request_place.drop_location}</li>`;
    //                     }
    //                 }
    //             });
    //             $('#customer_name').val(name)
    //             $('#customer_address').val(addr)
    //         }else{
    //             requestNumber += '<span class="text-danger">No History Found</span>';
    //             pickSug += '<span class="text-danger">No History Found</span>';
    //             dropSug += '<span class="text-danger">No History Found</span>';
    //         }

    //         $('.completedTrip').html(completedTrip)
    //         $('.cancelledTrip').html(cancelledTrip)
    //         $('.history').html(requestNumber)
    //         $('.pickupSuggestion').html(pickSug)
    //         $('.dropSuggestion').html(dropSug)
    //     });
    // }


    // // Place marker on map based on suggestions
    // $(document).on('click','.pickingSug,.dropingSug',function(){
    //     let addr = $(this).text();
    //     let lat = $(this).attr('data-lat');
    //     let lng = $(this).attr('data-lng');

    //     // For pickup
    //     if($(this).hasClass('pickingSug')){
    //         $('.pickingSug').removeClass('sug_active');
    //         $(this).addClass('sug_active')
    //         // var id = document.getElementById('pickup');
    //         // var location = new google.maps.places.Autocomplete(id)
    //         // location.addListener('place_changed',placeLocationMarker(pickUpMarkerData,'pickup'));
    //         bindDataToForm(addr,lat,lng,'pickup')
    //         $('#pickup').focus();
    //         placeLocationMarker(pickUpMarkerData,'pickup');
    //     }else{
    //         $('.dropingSug').removeClass('sug_active');
    //         $(this).addClass('sug_active')
    //         bindDataToForm(addr,lat,lng,'drop')
    //         $('#drop').focus();
    //         placeLocationMarker(dropOffMarkerData,'drop');
    //     }
    // });
</script>
@endsection
