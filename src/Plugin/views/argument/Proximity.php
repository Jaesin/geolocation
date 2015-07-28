<?php

/**
 * @file
 *   Definition of Drupal\search\Plugin\views\argument\Proximity.
 */

namespace Drupal\geolocation\Plugin\views\argument;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\CacheablePluginInterface;
use Drupal\geolocation\GeolocationCore;
use Drupal\views\Plugin\views\argument\ArgumentPluginBase;

/**
 * Argument handler for geolocation proximity.
 *
 * @ingroup views_argument_handlers
 *
 * @ViewsArgument("geolocation_argument_proximity")
 */
class Proximity extends ArgumentPluginBase implements CacheablePluginInterface {

  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    $options = parent::defineOptions();
    $options['location']['contains'] = 
    [
      'lat'      => ['default' => 37.7752393],
      'lng'      => ['default' => -122.4593581],
      'operator' => ['default' => '<='],
      'distance' => ['default' => 5],
    ];
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);
    $form['location'] = [
      '#type'  => 'fieldset',
      '#title' => $this->t('Geolocation Proximity'),
    ];
    $form['location']['lat'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Latitude'),
      '#default_value' => $this->options['location']['lat'],
    ];
    $form['location']['lng'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Longitude'),
      '#default_value' => $this->options['location']['lng'],
    ];
    $form['location']['operator'] = [
      '#type' => 'select',
      '#title' => $this->t('Operator'),
      '#options' => [
        '<',
        '<=',
        '>',
        '>=',
        '=',
        '!=',
      ],
      '#default_value' => $this->options['location']['operator'],
    ];
    $form['location']['distance'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Distance'),
      '#default_value' => $this->options['location']['distance'],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function query($group_by = FALSE) {

    $query = $this->query;
    $table_name = $this->ensureMyTable();

    $field_id = str_replace('_proximity', '', $this->realField);

    // Default to San Francisco
    $lat = $this->options['location']['lat'];
    $lgn = $this->options['location']['lng'];
    $operator = $this->options['location']['operator'];
    $distance = $this->options['location']['distance'];

    $expression = GeolocationCore::getQueryFragment($table_name, $field_id, $lat, $lgn);
    $placeholder = $this->placeholder();

    // We use having to be able to reuse the query on field handlers
    $query->addField(NULL, $expression, $this->field_alias);

    $this->query->addWhereExpression(NULL, "$expression {$operator} {$placeholder}_distance",
      [
        $placeholder . '_distance' => $distance
      ]
    );

  }

}
