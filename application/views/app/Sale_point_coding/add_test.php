<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google Maps Drawing Example</title>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCJPePs39ubzYGmfpcKbPV6k404GvXcL7s&libraries=drawing,geometry,places"></script>
    <style>
        #map {
            height: 400px;
            width: 100%;
        }
        /* Your existing styles here... */
    </style>
</head>
<body>

<input type="text" class="controls" id="area_name" placeholder="Area name will appear here" />

<input id="pac-input" class="controls" type="text" placeholder="Search Box" />
<div id="map"></div>

<script>
    let map;
    let drawingManager;
    let selectedShape;
    let geocoder;
    const pakistanBounds = new google.maps.LatLngBounds(
        new google.maps.LatLng(23.6345, 60.8718),
        new google.maps.LatLng(37.0841, 77.0861)
    );

    function initMap() {
        map = new google.maps.Map(document.getElementById('map'), {
            center: { lat: 30.3753, lng: 69.3451 },
            zoom: 4,
            restriction: {
                latLngBounds: pakistanBounds,
                strictBounds: true
            }
        });

        geocoder = new google.maps.Geocoder();

        drawingManager = new google.maps.drawing.DrawingManager({
            drawingMode: google.maps.drawing.OverlayType.POLYGON,
            drawingControl: true,
            drawingControlOptions: {
                position: google.maps.ControlPosition.TOP_CENTER,
                drawingModes: ['polygon']
            },
            polygonOptions: {
                editable: true,
                draggable: true,
            }
        });

        drawingManager.setMap(map);

        google.maps.event.addListener(drawingManager, 'polygoncomplete', (polygon) => {
            if (selectedShape) {
                selectedShape.setMap(null);
            }
            selectedShape = polygon;

            const area = getPolygonArea(polygon);
            const center = getPolygonCenter(polygon);
            reverseGeocode(center, area);
        });

        // Initialize Places Search Box
        initAutocomplete();
    }

    function getPolygonArea(polygon) {
        const paths = polygon.getPath();
        return google.maps.geometry.spherical.computeArea(paths);
    }

    function getPolygonCenter(polygon) {
        const paths = polygon.getPath();
        let latSum = 0;
        let lngSum = 0;
        const len = paths.getLength();

        for (let i = 0; i < len; i++) {
            latSum += paths.getAt(i).lat();
            lngSum += paths.getAt(i).lng();
        }

        return { lat: latSum / len, lng: lngSum / len };
    }

    function reverseGeocode(location, area) {
        geocoder.geocode({ location: location }, (results, status) => {
            const area_nameInput = document.getElementById('area_name');
            if (status === 'OK' && results[0]) {
                const addressComponents = results[0].address_components;
                const streetComponents = addressComponents.filter(component =>
                    component.types.includes('route') ||
                    component.types.includes('sublocality') ||
                    component.types.includes('locality')
                );
                const longNames = streetComponents.map(component => component.long_name).join(', ');
                area_nameInput.value = longNames;
            } else {
                area_nameInput.value = `Area Size: ${Math.round(area)} m²`;
            }
        });
    }

    function initAutocomplete() {
        const input = document.getElementById("pac-input");
        const searchBox = new google.maps.places.SearchBox(input);
        map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

        map.addListener("bounds_changed", () => {
            searchBox.setBounds(map.getBounds());
        });

        let markers = [];
        searchBox.addListener("places_changed", () => {
            const places = searchBox.getPlaces();
            if (places.length == 0) return;

            markers.forEach((marker) => {
                marker.setMap(null);
            });
            markers = [];

            const bounds = new google.maps.LatLngBounds();
            places.forEach((place) => {
                if (!place.geometry || !place.geometry.location) {
                    console.log("Returned place contains no geometry");
                    return;
                }

                markers.push(new google.maps.Marker({
                    map,
                    title: place.name,
                    position: place.geometry.location,
                }));

                if (place.geometry.viewport) {
                    bounds.union(place.geometry.viewport);
                } else {
                    bounds.extend(place.geometry.location);
                }
            });
            map.fitBounds(bounds);
        });
    }

    window.onload = initMap;
</script>

</body>
</html>
