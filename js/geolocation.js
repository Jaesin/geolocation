/**
 * @file
 *   Javascript for the geocoder module.
 */
(function ($, _, Drupal, settings) {
  // Ensure the geolocation object without overwriting it.
  var geolocation = Drupal.geolocation = Drupal.geolocation || {};

  /**
   * Gets the default settings for the google map.
   *
   * @returns {{scrollwheel: boolean, panControl: boolean, mapTypeControl: boolean, scaleControl: boolean, streetViewControl: boolean, overviewMapControl: boolean, zoomControl: boolean, zoomControlOptions: {style: *, position: *}, mapTypeId: *, zoom: number}}
   */
  geolocation.default_settings = function() {
    return {
      scrollwheel: false,
      panControl: false,
      mapTypeControl: true,
      scaleControl: false,
      streetViewControl: false,
      overviewMapControl: false,
      zoomControl: true,
      zoomControlOptions: {
        style: google.maps.ZoomControlStyle.SMALL,
        position: google.maps.ControlPosition.LEFT_BOTTOM
      },
      mapTypeId: google.maps.MapTypeId.ROADMAP,
      zoom: 2
    };
  };

  /**
   * Provides the callback that is called when maps loads.
   */
  geolocation.google_callback = function() {
    // Ensure callbacks array;
    geolocation.google_load_callbacks = geolocation.google_load_callbacks || [];

    // Wait until the window load event to try to use the maps library.
    $(window).load(function() {
      _.invoke(settings.geolocation.google_load_callbacks, "callback");
      geolocation.google_load_callbacks = [];
    });
  };

  /**
   * Adds a callback that will be called once the maps library is loaded.
   *
   * @param callback
   */
  geolocation.add_callback = function(callback) {
    settings.geolocation.google_load_callbacks = geolocation.google_load_callbacks || [];
    settings.geolocation.google_load_callbacks.push({callback: callback});
  };

  /**
   * Load google maps and set a callback to run when it's ready.
   *
   * @param callback
   */
  geolocation.load_google = function(callback) {
    // Add the callback.
    geolocation.add_callback(callback);
    // Check for google maps.
    if (typeof google == 'undefined' || typeof google.maps == 'undefined') {
      // google maps isn't loaded so lazy load google maps.
      $.getScript("//maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&callback=Drupal.geolocation.google_callback");
    } else {
      // Google maps loaded. Run callback.
      geolocation.google_callback();
    }
  };

  /**
   * Load google maps and set a callback to run when it's ready.
   *
   * @param callback
   */
  geolocation.add_map = function(map) {
    // Get the container for the map.
    var container = document.getElementById(map.id);
    // make sure this map hasn,t already been processed.
    if(!$(container).hasClass('geolocation-processed')
      && typeof google !== 'undefined'
      && typeof google.maps !== 'undefined'){

      // Set the container size.
      $(container).css({
        height: map.settings.height,
        width: map.settings.width
      });

      // Get the center point.
      var center = new google.maps.LatLng(map.lat, map.lng);

      // Create the map object and assign it to the map.
      map.google_map = new google.maps.Map(container, {
        zoom: parseInt(map.settings.zoom),
        center: center,
        mapTypeId: google.maps.MapTypeId[map.settings.type]
      });
      // Add the marker to the map.
      map.marker = new google.maps.Marker({
        position: center,
        map: map.google_map
      });
      // Set the already processed flag.
      $(container).addClass('geolocation-processed');
    }
  };
})(jQuery, _, Drupal, drupalSettings);
