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

alert(10);
            // Set the values of hidden form inputs.
            $thisButton.siblings(".geolocation-hidden-lat").val(lat);
            $thisButton.siblings(".geolocation-hidden-lng").val(lng);

            // Hide the default text.
            $('.default', $thisButton).hide();

            // Display a success message.
            var locationString = Drupal.t('Browser location: @lat,@lng Accuracy: @accuracy m', {'@lat': lat, '@lng': lng, '@accuracy': accuracy});
            $('.location', $thisButton).html(locationString);

            // Disable the button.
            $thisButton.addClass('disabled');

            // Show the clear icon.
            $('.clear', $thisButton).show();
          },

          // Error handler for getCurrentPosition()
          function(error) {

            // Alert with error message.
            switch(error.code) {
              case error.PERMISSION_DENIED:
                alert(Drupal.t('No location data found. Reason: PERMISSION_DENIED.'));
                break;
              case error.POSITION_UNAVAILABLE:
                alert(Drupal.t('No location data found. Reason: POSITION_UNAVAILABLE.'));
                break;
              case error.TIMEOUT:
                alert(Drupal.t('No location data found. Reason: TIMEOUT.'));
                break;
              default:
                alert(Drupal.t('No location data found. Reason: Unknown error.'));
                break;
            }
          },

          // Options for getCurrentPosition()
          {
            enableHighAccuracy: true,
            timeout: 5000,
            maximumAge: 6000
          }
        );

      }
      else {
        alert(Drupal.t('No location data found. Your browser does not support the W3C Geolocation API.'));
      }

    }
  };

})(jQuery, Drupal, navigator);
