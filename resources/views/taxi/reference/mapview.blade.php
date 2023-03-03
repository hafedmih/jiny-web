@extends('layouts.dispatcher-layout')

@section('content')
    <style>
      html,
      body {
        height: 100%;
        margin: 0;
        padding: 0;
      }
      #map {
        height: 100%;
      }
      #floating-panel {
        position: absolute;
        top: 10px;
        left: 25%;
        z-index: 5;
        background-color: #fff;
        padding: 5px;
        border: 1px solid #999;
        text-align: center;
        font-family: 'Roboto', 'sans-serif';
        line-height: 30px;
        padding-left: 10px;
      }

      #floating-panel {
        background-color: #fff;
        border: 1px solid #999;
        left: 25%;
        padding: 5px;
        position: absolute;
        top: 10px;
        z-index: 5;
      }
    </style>


  <body>
    <div id="floating-panel">
      <button onclick="toggleHeatmap()">Toggle Heatmap</button>
      <button onclick="changeGradient()">Change gradient</button>
      <button onclick="changeRadius()">Change radius</button>
      <button onclick="changeOpacity()">Change opacity</button>
    </div>
    <div id="map"></div>
    @foreach($point as $value)
    
      <input type="hidden" name="pick_lat[]" id="pick_lat_{{$value->id}}" value="{{$value->pick_lat}}" />
      <input type="hidden" name="pick_lng[]" id="pick_lng_{{$value->id}}" value="{{$value->pick_lng}}" />

      <input type="hidden" name="drop_lat[]" id="drop_lat_{{$value->id}}" value="{{$value->drop_lat}}" />
      <input type="hidden" name="drop_lng[]" id="drop_lng_{{$value->id}}" value="{{$value->drop_lng}}" />
    @endforeach
    <script>
      var map, heatmap;
      var heatmapData = [];
      var pickLat = [];
      var pickLng = [];
      var default_lat = '11.0176052';
      var default_lng = '76.9586527';
     
      var heat_lat =  document.getElementById('pick_lat').value;
     
      var heat_lng =  document.getElementById('pick_lng').value;
      

      function initMap() {
        // map = new google.maps.Map(document.getElementById('map'), {
        //   zoom: 17,
        //   center: { lat: 21.7679, lng: -76.9558 },
        //   mapTypeId: 'roadmap',
        // });
        map = new google.maps.Map(document.getElementById('map'), {
            center: new google.maps.LatLng(default_lat, default_lng),
            zoom: 9,
            mapTypeId: 'roadmap'
        });

        heatmap = new google.maps.visualization.HeatmapLayer({
          data: getPoints(),
          map: map,
        });
      }

      function toggleHeatmap() {
        heatmap.setMap(heatmap.getMap() ? null : map);
      }

      function changeGradient() {
        var gradient = [
          'rgba(0, 255, 255, 0)',
          'rgba(0, 255, 255, 1)',
          'rgba(0, 191, 255, 1)',
          'rgba(0, 127, 255, 1)',
          'rgba(0, 63, 255, 1)',
          'rgba(0, 0, 255, 1)',
          'rgba(0, 0, 223, 1)',
          'rgba(0, 0, 191, 1)',
          'rgba(0, 0, 159, 1)',
          'rgba(0, 0, 127, 1)',
          'rgba(63, 0, 91, 1)',
          'rgba(127, 0, 63, 1)',
          'rgba(191, 0, 31, 1)',
          'rgba(255, 0, 0, 1)',
        ];
        heatmap.set('gradient', heatmap.get('gradient') ? null : gradient);
      }

      function changeRadius() {
        heatmap.set('radius', heatmap.get('radius') ? null : 20);
      }

      function changeOpacity() {
        heatmap.set('opacity', heatmap.get('opacity') ? null : 0.2);
      }

      // Heatmap data: 500 Points
      function getPoints( ) {
        var arr = $('input[name="pick_lat[]"]').map(function () {
          return this.value; // $(this).val()
        }).get();
        var arr1 = $('input[name="pick_lng[]"]').map(function () {
          return this.value; // $(this).val()
        }).get();
        var array = [];
        for (let index = 0; index < arr.length; index++) {
          array.push(new google.maps.LatLng(arr[index], arr1[index]))
          
        }
        return array;
      }
      // function getPoints( ) {
      //   var arr = $('input[name="drop_lat[]"]').map(function () {
      //     return this.value; // $(this).val()
      //   }).get();
      //   var arr1 = $('input[name="drop_lng[]"]').map(function () {
      //     return this.value; // $(this).val()
      //   }).get();
      //   var array = [];
      //   for (let index = 0; index < arr.length; index++) {
      //     array.push(new google.maps.LatLng(arr[index], arr1[index]))
          
      //   }
      //   return array;
      // }
    </script>
    <script
      async
      defer
      src="https://maps.googleapis.com/maps/api/js?key={{settingValue('google_map_key')}}&signed_in=true&libraries=visualization&callback=initMap"
    ></script>
  </body>
@endsection
AIzaSyAFimWZwIvDnYDZS0pKqz25yCBY10DTzm4
