<?php

/**
 * @file
 *   Definition of Drupal\search\Plugin\views\argument\Proximity.
 */

namespace Drupal\geolocation\Plugin\views\argument;

use Drupal\geolocation\GeoCoreInjectionTrait;
use Drupal\geolocation\GeolocationCore;
use Drupal\views\Plugin\views\argument\Formula;

/**
 * Argument handler for geolocation proximity.
 *
 * Argument format should be in the following format:
 * "37.7749295,-122.41941550000001<=5miles" (defaults to km).
 *
 * @ingroup views_argument_handlers
 *
 * @ViewsArgument("geolocation_argument_proximity")
 */
class ProximityArgument extends Formula {

  use GeoCoreInjectionTrait;

  protected $operator = '<';

  /**
   * @{inheritdoc}
   */
  public function getFormula() {
    $value = $this->getValue();
    // Get the earth radius from the units.
    $earth_radius = strpos(strtolower($value), 'mile') > -1 ? GeolocationCore::EARTH_RADIUS_MILE : GeolocationCore::EARTH_RADIUS_KM;
    $this->operator = preg_replace('/[^<>=]/', '', $value);
    // Split the values into numeric values.
    $values = preg_split("/[a-zA-Z,<>= ]+/", $this->getValue());
    $this->argument = !empty($values[2]) ? (int) preg_replace('/(^[0-9]+).*$/', '$1', $values[2]) : NULL;
    $formula = !empty($earth_radius) && !empty($values[0]) && !empty($values[1]) && !empty($values[2])
      ? $this->geolocation_core->getQueryFragment($this->tableAlias, $this->realField, $values[0], $values[1], $earth_radius ) : FALSE;
    return !empty($formula) ? str_replace('***table***', $this->tableAlias, $formula) : '';
  }

  /**
   * @{inheritdoc}
   */
  public function query($group_by = FALSE) {
    $this->ensureMyTable();
    // Now that our table is secure, get our formula.
    $placeholder = $this->placeholder();
    $formula = $this->getFormula() .' ' . $this->operator . ' ' . $placeholder;
    $placeholders = array(
      $placeholder => $this->argument,
    );
    $this->query->addWhere(0, $formula, $placeholders, 'formula');
  }
}
