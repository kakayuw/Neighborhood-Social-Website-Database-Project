@extends('layouts.app')

@section('content')
<el-container>
    <el-container style="height: 500px;">
    <sidebar></sidebar>
    <el-main>
    <div class="container-fluid">
    <el-button-group>
        <el-button type="primary" id="toblock">To my block</el-button>
        <el-button type="primary" id="tomsg">To my messages</el-button>
    </el-button-group>
        <div style="height:450px; width:900px;">
            <div id="mapf" style="height:450px;width:900px;"></div>
        </div>
    </div>
    </el-main>
    </el-container>
</el-container>
@endsection

@push('head')


<script>
var mypos = null;

$( document ).ready(function() {
    $('#allblockpage').paginate({
        scope: $('div'), // targets all div elements
    });

    getMyLocation();

});

function getMyLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
          var pos = {
            lat: position.coords.latitude,
            lng: position.coords.longitude
          };
          mypos = pos;
        }, function() {
          console.log("cannot load")
        });
      } else {
        // Browser doesn't support Geolocation
        vuemethod.greet("Browser doesn't support!");
      }
}

function initMap() {
    var myLatlng = mypos?mypos:new google.maps.LatLng(40.678177, -73.944160);
    var mapOptions = {
        zoom: 18,
        center: myLatlng
    };
    var map = new google.maps.Map(document.getElementById("mapf"), mapOptions);
    // Place a draggable marker on the map
    var iconBase = 'https://developers.google.com/maps/documentation/javascript/examples/full/images/';

    const features = [
        @foreach($recentgroup as $m)
        {position: new google.maps.LatLng({{$m->lat}}, {{$m->lng}}),  type: 'info', title:'{{$m->title}}', text:'{{$m->text}}', sender:'{{$m->uname}}', time:'{{$m->mtimestamp}}'},
        @endforeach
    ];
    var icons = {
        parking: {
            icon: iconBase + 'parking_lot_maps.png'
        },
        library: {
            icon: iconBase + 'library_maps.png'
        },
        info: {
            icon: iconBase + 'info-i_maps.png'
        }
    };

    var infowindow = new google.maps.InfoWindow({
        });

    // Create markers.
    for (var i = 0; i < features.length; i++) {
        const  marker = new google.maps.Marker({
            position: features[i].position,
            icon: icons[features[i].type].icon,
            map: map
        });
        const contentString = '<div id="siteNotice">'+
            '</div>'+
            '<h3 id="firstHeading" class="firstHeading">' + features[i].title + '</h3>'+
            '<div id="bodyContent">'+
            '<p>' + features[i].text + '</p>'+
            '<hr><p><b>' + features[i].sender + '</b>&nbsp;@&nbsp;' + features[i].time +  '</p>'+
            '</div>'+
            '</div>';
        marker.addListener('click', function() {
            infowindow.close(); // Close previously opened infowindow
            infowindow.setContent(contentString);
            infowindow.open(map, marker);
        });
    }

    // creat block rectangle
    var block = ['f', {{$myblock->swlat}}, {{$myblock->swlng}}, {{$myblock->nelat}}, {{$myblock->nelng}}];
    var swpoint = {lat: block[1], lng: block[2]};
    var nepoint = {lat: block[3], lng: block[4]};
    // alert(block);
    var rectangle = new google.maps.Rectangle({
        strokeColor: '#FF0000',
        strokeOpacity: 0.8,
        strokeWeight: 2,
        fillColor: '#FF0000',
        fillOpacity: 0.35,
        map: map,
        bounds: {
            north: Math.max(swpoint.lat, nepoint.lat),
            south: Math.min(swpoint.lat, nepoint.lat),
            east: Math.max(swpoint.lng, nepoint.lng),
            west: Math.min(swpoint.lng, nepoint.lng)
        }
    });
    var markerrec = new google.maps.Marker({position: swpoint, map: map});

    // add click events
    $("#toblock").click(function() {
        map.setCenter(swpoint);
    });

    var count = 0;
    $("#tomsg").click(function() {
        map.setCenter(features[count].position);
        count = count + 1;
        if (count >= features.length)   count = 0;
    });
}

const vuemethod = new Vue({
    methods: {
        greet: function(event) {
            this.$message({showClose: true,  message: event});
        }
    }
});

</script>

<script src='https://maps.googleapis.com/maps/api/js?key=YOURGOOGLEAPIKEY&language=en-US&callback=initMap'>
</script>
@endpush

