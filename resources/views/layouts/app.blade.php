<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Neighborbood') }}</title>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    
    @stack('tail')

</head>
<body>
    <div id="app">
        <nav class="navbar navbar-default navbar-static-top">
            <div class="container">
                <div class="navbar-header">

                    <!-- Collapsed Hamburger -->
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                        <span class="sr-only">Toggle Navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>

                    <!-- Branding Image -->
                    <a class="navbar-brand" href="{{ url('/') }}">
                        {{ config('app.name', 'Neighborhood') }}
                    </a>
                </div>

                <div class="collapse navbar-collapse" id="app-navbar-collapse">
                    <!-- Left Side Of Navbar -->
                    <ul class="nav navbar-nav">
                        &nbsp;
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="nav navbar-nav navbar-right">
                        <!-- Authentication Links -->
                        @if (Auth::guest())
                            <li><a href="{{ route('login') }}">Login</a></li>
                            <li><a href="{{ route('register') }}">Register</a></li>
                        @else
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                    {{ Auth::user()->name}}&nbsp;({{Auth::user()->email}}) <span class="caret"></span>
                                </a>

                                <ul class="dropdown-menu" role="menu">
                                    <li>
                                        <a href="{{ route('logout') }}"
                                            onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                            Logout
                                        </a>

                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                            {{ csrf_field() }}
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </nav>
        @yield('content')
    </div>


    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/jquery.js') }}"></script>
    <script src="{{ asset('js/UI/jquery.paginate.js') }}"></script>
    <link rel="stylesheet" type="text/css" href="{{ asset('css/jquery.paginate.css') }}">
    <!-- Semantic UI -->
    <link rel="stylesheet" type="text/css" href="{{ asset('../semantic/dist/semantic.min.css') }}">
    <script src="{{ asset('../semantic/dist/semantic.min.js') }}"></script>

    <script>
    $( document ).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
    });

    var triggered = false;
    function mapTrigger(...block){
        console.log("triggered:", triggered);
        console.log(block)
        if (!triggered) {
            triggered = true;
            loadBlockMap(block);
        }
    }

    function loadBlockMap(...block) {
        //, swlat, swlng, nelat, nelng
        // alert(block.length)
        if (block.length == 1)  block = block[0]        // very important, throught triggered it's wrapped by multi para
        var swpoint = {lat: block[1], lng: block[2]};
        var nepoint = {lat: block[3], lng: block[4]};
        // alert(block);
        var map = new google.maps.Map(document.getElementById('map'+block[0]), {
            zoom: 18,
            center: swpoint,
            mapTypeId: 'terrain'
        });
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
            // bound: {sw: swpoint, ne: nepoint}
        });
        var marker = new google.maps.Marker({position: swpoint, map: map});
    }
    </script>
@stack('head')
</body>
</html>
