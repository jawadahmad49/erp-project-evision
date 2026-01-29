   
      var geocoder;
      var map;
      var infowindow;
      var mapOptions = {
          zoom: 17,
          mapTypeId: google.maps.MapTypeId.StreetViewPanorama
        }
      var marker;
      function initialize() {
        geocoder = new google.maps.Geocoder();
        infowindow = new google.maps.InfoWindow();
        map = new google.maps.Map(document.getElementById('map_canvas'), mapOptions);
        codeAddress();
      }
      function codeAddress() {
        var address = document.getElementById('location_address').value;

        geocoder.geocode({ 'address': address}, function(results, status) {

          if (status == google.maps.GeocoderStatus.OK) {

            $("#result").html('');

              for(var i=0; i<results.length && i<15; i++){
                 var images='marker'+i+'.png';

                 $("#result").append("<table class='table-class'><tr class='formated-location'><td><img src='../assets/map/"+images+"'></td><td>"+results[i].formatted_address+"</td></tr><tr><td>Location:</td><td>"+results[i].geometry.location+"</td></tr><tr><td>Type:</td><td>"+results[i].address_components[0].types+"</td></tr></table>");             
               
            }
              marker = new google.maps.Marker({
                map: map,
                position: results[0].geometry.location,
                draggable: true
            });
       


        map.setCenter(results[0].geometry.location);

         
            google.maps.event.addListener(marker, "dragend", function() {
              document.getElementById('lat').value = marker.getPosition().lat();
              document.getElementById('lng').value = marker.getPosition().lng();

            });
            document.getElementById('lat').value = marker.getPosition().lat();
            document.getElementById('lng').value = marker.getPosition().lng();

          } else {
            alert('Geocode was not successful for the following reason: ' + status);
          }
        
        });

        google.maps.event.addListener(map, 'click', function(event) {
         
              document.getElementById('lat').value = event.latLng.lat();
              document.getElementById('lng').value = event.latLng.lng();

              placeMarker(event.latLng);
              geocodePosition(event.latLng);
         });


        function placeMarker(location) {
            if (marker == undefined){
                marker = new google.maps.Marker({
                    position: location,
                    map: map, 
                    animation: google.maps.Animation.DROP,
                });
            }
            else{
                marker.setPosition(location);
            }
            map.setCenter(location);
        }

        function geocodePosition(pos) {
        geocoder.geocode({
          latLng: pos
        }, function(responses) {
          if (responses && responses.length > 0) {

            $("#result").html('');

              for(var i=0; i<responses.length && i<15; i++){

                 var images='marker'+i+'.png';

                 $("#result").append("<table class='table-class'><tr class='formated-location'><td><img src='../assets/map/"+images+"'></td><td>"+responses[i].formatted_address+"</td></tr><tr><td>Location:</td><td>"+responses[i].geometry.location+"</td></tr><tr><td>Type:</td><td>"+responses[i].address_components[0].types+"</td></tr></table>");             
               }

             document.getElementById('location_address').value=responses[0].formatted_address;
          } else {
             $("#result").append('Cannot determine address at this location.');
          }
        });
      }
  }
      function newAddress(address) {
        geocoder.geocode( { 'address': address}, function(results, status) {

          if (status == google.maps.GeocoderStatus.OK) {
            map.setCenter(results[0].geometry.location);
            if(marker)
              marker.setMap(null);
              marker = new google.maps.Marker({
                map: map,
                position: results[0].geometry.location,
                draggable: true
            });
            google.maps.event.addListener(marker, "dragend", function() {
              document.getElementById('lat').value = marker.getPosition().lat();
              document.getElementById('lng').value = marker.getPosition().lng();

            });
            document.getElementById('lat').value = marker.getPosition().lat();
            document.getElementById('lng').value = marker.getPosition().lng();
          } else {
            alert('Geocode was not successful for the following reason: ' + status);
          }
        });

        google.maps.event.addListener(map, 'click', function(event) {

              document.getElementById('lat').value = event.latLng.lat();
              document.getElementById('lng').value = event.latLng.lng();

              placeMarker(event.latLng);
              geocodePosition(event.latLng);
         });
        function placeMarker(location) {
            if (marker == undefined){
                marker = new google.maps.Marker({
                    position: location,
                    map: map, 
                    animation: google.maps.Animation.DROP,
                });
            }
            else{
                marker.setPosition(location);
            }
            map.setCenter(location);
        }

        function geocodePosition(pos) {
        geocoder.geocode({
          latLng: pos
        }, function(responses) {
          if (responses && responses.length > 0) {
             document.getElementById('location_address').value=responses[0].formatted_address;
          } else {
            updateMarkerAddress('Cannot determine address at this location.');
          }
        });
      }

  }
  
          $(document).ready(function() {

              $("#result").on("click", "table", function(){
              $("#result").find('table').css("background-color","");

                var location= $(this).find('.formated-location').text();
                $('#location_address').val(location);
                newAddress(location);

                $(this).css("background-color","#eef");

              });
          });