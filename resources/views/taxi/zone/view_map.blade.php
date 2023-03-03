@extends('layouts.app')

@section('content')
<style>

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
            <h5 class="card-title">{{ __('view-map-zone') }}</h5>
            <div class="header-elements">
                <div class="list-icons">
                    <a href="{{ route('zone') }}" id="add_new_btn" class="btn bg-purple btn-sm legitRipple"><i class="icon-menu3 mr-2"></i> List</a>
                </div>
            </div>
        </div>
    </div>
    <div class="card">  
        <div class="card-header bg-white header-elements-inline">
            <h6 class="card-title">{{ __('view-map-zone') }}</h6>
            <div class="header-elements">
                <div class="list-icons">
                    <a class="list-icons-item" data-action="collapse"></a>
                    <a class="list-icons-item" data-action="reload"></a>
                    <a class="list-icons-item" data-action="remove"></a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div id="map-canvas" style="width:100%;height:400px;"></div>
            <input type="hidden" id="bounds" name="bounds" value='{{$zone->map_cooder}}'>
        </div>
        

    </div>
    



</div>


<script>

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
                lat: 52.3993534,
                lng: 4.9421651
            },
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
  }


    
</script>

<script src="https://maps.googleapis.com/maps/api/js?key={{settingValue('geo_coder')}}&libraries=drawing,places&callback=initMap" async defer>    
</script>
  
</script>
@endsection
