<?php

/**
 * @file
 * Contains \Drupal\geolocation\Controller\GeolocationPageController.
 */

namespace Drupal\geolocation\Controller;

use Drupal\geolocation\GeolocationCore;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Controller routines for geolocation routes.
 */
class GeolocationPageController extends ControllerBase {

  public function rememberUserLocation($lat, $lng) {
    // Make sure you don't trust the URL to be safe! Always check for exploits.
    if (!is_numeric($lat) || !is_numeric($lng)) {
      // We will just show a standard "access denied" page in this case.
      throw new AccessDeniedHttpException();
    }

    GeolocationCore::setCookie('lat', $lat);
    GeolocationCore::setCookie('lng', $lng);

    return [
      '#markup' => '',
    ];
  }

}
