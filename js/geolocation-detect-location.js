/**
 * @file
 *   Javascript for the user location detect.
 */
(function ($, Drupal, navigator) {
  "use strict";

  function createCookie(name, value, hours) {
    if (hours) {
      var date = new Date();
      date.setTime(date.getTime() + hours * 60 * 50);
      var expires = "; expires=" + date.toGMTString();
    }
    else {
      var expires = "";
    }
    document.cookie = name + "=" + value + expires + "; path=/";
  }

  function readCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i = 0; i < ca.length; i++) {
      var c = ca[i];
      while (c.charAt(0) == ' ') c = c.substring(1, c.length);
      if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
  }

  function eraseCookie(name) {
    createCookie(name, "", -1);
  }

  Drupal.behaviors.geolocationDetectLocation = {
    attach: function(context, settings) {

      var latCookie = readCookie('lat');
      var lngCookie = readCookie('lng');

      // If the browser supports W3C Geolocation API.
      if (!latCookie || !lngCookie) {
        if (navigator.geolocation) {

          // Get the geolocation from the browser.
          navigator.geolocation.getCurrentPosition(

            // Success handler for getCurrentPosition()
            function (position) {
              var lat = position.coords.latitude;
              var lng = position.coords.longitude;
              var accuracy = position.coords.accuracy / 1000;
              createCookie('lat', lat, 1);
              createCookie('lng', lng, 1);
              $.get(settings.path.baseUrl + "geolocation/detect-user-location/" + lat + "/" + lng);
            },

            function(error) {},
            {
              enableHighAccuracy: true,
              timeout: 3600000,
              maximumAge: 3600000
            }
          );

        }
      }

    }
  };

})(jQuery, Drupal, navigator);
