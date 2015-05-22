<?php

/**
 * @file
 *   Definition of Drupal\search\Plugin\views\argument\Proximity.
 */

namespace Drupal\geolocation\Plugin\views\argument;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\geolocation\GeolocationCore;
use Drupal\views\Plugin\views\argument\Formula;
use Drupal\views\Plugin\views\query\Sql;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Argument handler for geolocation proximity.
 *
 * @ingroup views_argument_handlers
 *
 * @ViewsArgument("geolocation_argument_proximity")
 */
class Proximity extends Formula implements ContainerFactoryPluginInterface {

  var $formula = '';

  /**
   * The route match.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * Constructs a new Date instance.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   *
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The route match.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, RouteMatchInterface $route_match) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->routeMatch = $route_match;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_route_match')
    );
  }
//
//  /**
//   * Build the query based upon the formula
//   */
//  public function query($group_by = FALSE) {
//    $this->ensureMyTable();
//    // Now that our table is secure, get our formula.
//    $placeholder = $this->placeholder();
//
//    $placeholders = array(
//      $placeholder => $this->argument,
//    );
//    /** @var Sql $query */
//    $query = $this->query;
//    $query->addWhereExpression(0, $this->getFormula(), $placeholders);
//  }
//
//  /**
//   * @inheritdoc
//   */
//  public function getFormula() {
//    /** @var GeolocationCore $geo_core */
//    $geo_core = \Drupal::service('geolocation.core');
////    $this->query->addWhereExpression($geo_core->generateQueryFragment($this->argument));
//
////    $this->query->addWhere(0, "$this->tableAlias.$this->realField", $this->argument);
//    $formula = $geo_core->generateQueryFragment()str_replace('***table***', $this->tableAlias, $this->formula);
//    return $formula;
//  }
}
