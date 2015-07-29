/**
 * @file
 *   Javascript for the user location detect.
 */
(function ($, Drupal, navigator) {
  "use strict";

  Drupal.behaviors.geolocationHTML5 = {
    attach: function(context, settings) {

      // If the browser supports W3C Geolocation API.
      if (navigator.geolocation) {

        // Get the geolocation from the browser.
        navigator.geolocation.getCurrentPosition(

          // Success handler for getCurrentPosition()
          function (position) {
            var lat = position.coords.latitude;
            var lng = position.coords.longitude;
            var accuracy = position.coords.accuracy / 1000;
            $.get( "/geolocation/detect-user-location/" + lat + "/" + lng);
          },

          function(error) {},
          {
            //enableHighAccuracy: true,
            //timeout: 5000,
            //maximumAge: 6000
          }
        );

      }

    }
  };

})(jQuery, Drupal, navigator);
