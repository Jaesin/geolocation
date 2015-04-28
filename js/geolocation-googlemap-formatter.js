/**
 * @file
 * Javascript for the geocoder Google map formatter.
 */
(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.geolocationGoogleMap = {
    attach: function (context, settings) {
      var geolocationSettings = drupalSettings.geolocation;
      var fieldMaps = geolocationSettings.googleMaps;
      $.each(fieldMaps, function(delta, fieldMap) {
        var mapCanvas = document.getElementById('map-canvas-' + delta);
        var latLng = new google.maps.LatLng(fieldMap.lat, fieldMap.lng);
        var mapOptions = {
          center: latLng,
          zoom: parseInt(geolocationSettings.fieldSettings.zoom),
          mapTypeId: google.maps.MapTypeId[geolocationSettings.fieldSettings.type]
        }
        var map = new google.maps.Map(mapCanvas, mapOptions);
        var marker = new google.maps.Marker({
          position: latLng,
          map: map
        });
        // To add the marker to the map, call setMap();
        marker.setMap(map);
        google.maps.event.addListenerOnce(map, 'idle', function(){
          // Set map dimensions.
          $(mapCanvas).css({
            height: geolocationSettings.fieldSettings.height,
            width: geolocationSettings.fieldSettings.width
          });
          // Fix for themes which set the default max-width of an image default to 100%.
          $(mapCanvas).find('img').css('max-width', 'inherit');
        });
      });
    }
  }
})(jQuery, Drupal, drupalSettings);
