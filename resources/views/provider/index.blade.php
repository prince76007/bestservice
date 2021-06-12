@extends('provider.layout.app')
<meta http-equiv=“Content-Security-Policy” content=“default-src ‘self’ gap://ready file://* *; style-src ‘self’ ‘unsafe-inline’; script-src ‘self’ ‘unsafe-inline’ ‘unsafe-eval’”/>
@section('content')

<div class="pro-dashboard-head">
    <div class="container">
        <a href="#" class="pro-head-link active">@lang('main.service') Now</a>
    </div>
</div>
<div class="pro-dashboard-content">
    <div class="container">
        <div class="dash-content" id="trip-container">
            <div class="row no-margin" >

            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript" src="{{asset('asset/js/rating.js')}}"></script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAP_KEY') }}&libraries=places" defer></script>
<script type="text/javascript">
    var map;
    var routeMarkers = {
                source: {
                    lat: 0,
                    lng: 0,
                },
                destination: {
                    lat: 0,
                    lng: 0,
                }
            };
    var zoomLevel = 13;
    var directionsService;
    var directionsDisplay;

    function initMap() {
        // Basic options for a simple Google Map
        var center = new google.maps.LatLng('13', '80');
        
        directionsService = new google.maps.DirectionsService;
        directionsDisplay = new google.maps.DirectionsRenderer;
        // For more options see: https://developers.google.com/maps/documentation/javascript/reference#MapOptions

        var mapOptions = {
            // How zoomed in you want the map to start at (always required)
            zoom: zoomLevel,
            disableDefaultUI: true,
            // The latitude and longitude to center the map (always required)
            center: center,

            // Map styling
            styles: [
                {
                    elementType:"geometry",
                    stylers:[
                        {
                            color:"#f5f5f5"
                        }
                    ]
                },
                {
                    elementType:"labels.icon",
                    stylers:[
                        {
                            visibility:"off"
                        }
                    ]
                },
                {
                    elementType:"labels.text.fill",
                    stylers:[
                        {
                            color:"#616161"
                        }
                    ]
                },
                {
                    elementType:"labels.text.stroke",
                    stylers:[
                        {
                            color:"#f5f5f5"
                        }
                    ]
                },
                {
                    featureType:"administrative.land_parcel",
                    elementType:"labels.text.fill",
                    stylers:[
                        {
                            color:"#bdbdbd"
                        }
                    ]
                },
                {
                    featureType:"poi",
                    elementType:"geometry",
                    stylers:[
                        {
                            color:"#eeeeee"
                        }
                    ]
                },
                {
                    featureType:"poi",
                    elementType:"labels.text.fill",
                    stylers:[
                        {
                            color:"#757575"
                        }
                    ]
                },
                {
                    featureType:"poi.park",
                    elementType:"geometry",
                    stylers:[
                        {
                            color:"#e5e5e5"
                        }
                    ]
                },
                {
                    featureType:"poi.park",
                    elementType:"geometry.fill",
                    stylers:[
                        {
                            color:"#7de843"
                        }
                    ]
                },
                {
                    featureType:"poi.park",
                    elementType:"labels.text.fill",
                    stylers:[
                        {
                            color:"#9e9e9e"
                        }
                    ]
                },
                {
                    featureType:"road",
                    elementType:"geometry",
                    stylers:[
                        {
                            color:"#ffffff"
                        }
                    ]
                },
                {
                    featureType:"road.arterial",
                    elementType:"labels.text.fill",
                    stylers:[
                        {
                            color:"#757575"
                        }
                    ]
                },
                {
                    featureType:"road.highway",
                    elementType:"geometry",
                    stylers:[
                        {
                            color:"#dadada"
                        }
                    ]
                },
                {
                    featureType:"road.highway",
                    elementType:"labels.text.fill",
                    stylers:[
                        {
                            color:"#616161"
                        }
                    ]
                },
                {
                    featureType:"road.local",
                    elementType:"labels.text.fill",
                    stylers:[
                        {
                            color:"#9e9e9e"
                        }
                    ]
                },
                {
                    featureType:"transit.line",
                    elementType:"geometry",
                    stylers:[
                        {
                            color:"#e5e5e5"
                        }
                    ]
                },
                {
                    featureType:"transit.station",
                    elementType:"geometry",
                    stylers:[
                        {
                            color:"#eeeeee"
                        }
                    ]
                },
                {
                    featureType:"water",
                    elementType:"geometry",
                    stylers:[
                        {
                            color:"#c9c9c9"
                        }
                    ]
                },
                {
                    featureType:"water",
                    elementType:"geometry.fill",
                    stylers:[
                        {
                            color:"#9bd0e8"
                        }
                    ]
                },
                {
                    featureType:"water",
                    elementType:"labels.text.fill",
                    stylers:[
                        {
                            color:"#9e9e9e"
                        }
                    ]
                }
            ]
        };

        // Get the HTML DOM element that will contain your map 
        // We are using a div with id="map" seen below in the <body>
        var mapElement = document.getElementById('map');

        // Create the Google Map using out element and options defined above
        map = new google.maps.Map(mapElement, mapOptions);
if(navigator.geolocation){
               // timeout at 60000 milliseconds (60 seconds)
               var options = {timeout:60000};
               navigator.geolocation.getCurrentPosition
               (showLocation, errorHandler, options);
            } else{
               alert("Sorry, browser does not support geolocation!");
            }
//console.log(navigator.geolocation.getCurrentPosition,'++++++++++++');
        navigator.geolocation.getCurrentPosition(function (position) { 
            console.log(position,'++++++++++++');
            console.log(position,'position');
            center = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
            map.setCenter(center);

            var marker = new google.maps.Marker({
                map: map,
                anchorPoint: new google.maps.Point(0, -29),
            });

            marker.setPosition(center);
            marker.setVisible(true);
        });

    }

       function showLocation(position) {
            var latitude = position.coords.latitude;
            var longitude = position.coords.longitude;
            alert("Latitude : " + latitude + " Longitude: " + longitude);
         }
         function errorHandler(err) {
console.log(err,'err');
             if(err.code == 1) {
            //   alert("Error: Access is denied!");
            } else if( err.code == 2) {
             //  alert("Error: Position is unavailable!");
            }
         }
    function updateMap(route) {

        // console.log('updateMap', route, routeMarkers);
        // var markerSecond = new google.maps.Marker({
        //     map: map,
        //     anchorPoint: new google.maps.Point(0, -29)
        // });

        // source = new google.maps.LatLng('13', '80');
        // destination = new google.maps.LatLng('13', '80');

        // marker.setVisible(false);
        // marker.setPosition(source);

        // markerSecond.setVisible(false);
        // markerSecond.setPosition(destination);

        // var bounds = new google.maps.LatLngBounds();
        // bounds.extend(marker.getPosition());
        // bounds.extend(markerSecond.getPosition());
        // map.fitBounds(bounds);

        if(routeMarkers.source.lat == route.source.lat &&
            routeMarkers.source.lng == route.source.lng &&
            routeMarkers.destination.lat == route.destination.lat &&
            routeMarkers.destination.lng == route.destination.lng) {

        } else {

            routeMarkers = route;
            
            directionsDisplay.set('directions', null);
            directionsDisplay.setMap(map);

            directionsService.route({
                origin: route.source,
                destination: route.destination,
                travelMode: google.maps.TravelMode.DRIVING
            }, function(result, status) {
                if (status == google.maps.DirectionsStatus.OK) {
                    directionsDisplay.setDirections(result);
                }
            });
        }

    }
</script>
@endsection