/**
 * @file
 *   Javascript for the Google geocoder widget.
 */

(function ($, Drupal, drupalSettings) {
  "use strict";

  // Ensure and add shortcut to the geolocation object.
  var geolocation = Drupal.geolocation = Drupal.geolocation || {};

  Drupal.behaviors.geolocationGooglemaps = {
    attach: function (context, settings) {
      // Ensure itterables.
      settings.geolocation = settings.geolocation || {widget_maps: []};
      // Make sure the lazy loader is available.
      if (typeof geolocation.load_google === 'function') {
        // First load the library from google.
        geolocation.load_google(function(){
          // This won't fire until window load.
          initialize(settings);
        });
      }
    }
  };

  /**
   * Adds the click listeners to the map.
   * @param map
   */
  geolocation.add_click_listener = function(map) {
    google.maps.event.addListener(map.google_map, 'click', function(e) {
      // Create 500ms timeout to wait for double click.
      var singleClick = setTimeout(function() {
        geolocation.codeLatLng(e.latLng, map, 'marker');
        geolocation.setMapMarker(e.latLng, map);
      }, 500);
    });
    google.maps.event.addListener(map.google_map, 'dblclick', function(e) {
      clearTimeout(singleClick);
    });
  };

  /**
  * Set the latitude and longitude values to the input fields
  * And optionally update the address field
  *
  * @param latLng
  *   a location (latLng) object from google maps api
  * @param map
  *   The settings object that contains all of the necessary metadata for this map.
  * @param op
  *   the op that was performed
  */
  geolocation.codeLatLng = function(latLng, map, op) {
    // Update the lat and lng input fields
    $('.geolocation-hidden-lat.for-'+map.id).attr('value', latLng.lat());
    $('.geolocation-hidden-lng.for-'+map.id).attr('value', latLng.lng());
  };

  /**
  * Set/Update a marker on a map
  *
  * @param latLng
  *   a location (latLng) object from google maps api
   * @param map
   *   The settings object that contains all of the necessary metadata for this map.
  */
  geolocation.setMapMarker = function(latLng, map) {
    // make sure the marker exists.
    if (typeof map.marker !== 'undefined') {
      map.marker.setPosition(latLng);
    } else {
      // Add the marker to the map.
      map.marker = new google.maps.Marker({
        position: latLng,
        map: map.google_map
      });
    }
  };

  /**
   * Runs after the google maps api is available
   *
   * @param settings
   */
  function initialize(settings) {

    // Process drupalSettings for every Google map present on the current page.
    $.each(settings.geolocation.widget_maps, function(widget_id, map) {

      // Add any missing settings.
      map.settings = $.extend(geolocation.default_settings(), map.settings);

      // Add the map by ID with settings.
      geolocation.add_map(map);

      // Add the click responders ffor setting the value.
      geolocation.add_click_listener(map);
    });
  }

})(jQuery, Drupal, drupalSettings);
