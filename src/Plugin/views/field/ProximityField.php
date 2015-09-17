<?php

/**
 * @file
 *   Definition of Drupal\geolocation\Plugin\views\Proximity.
 */

namespace Drupal\geolocation\Plugin\views\field;

use Drupal\Core\Form\FormStateInterface;
use Drupal\geolocation\GeoCoreInjectionTrait;
use Drupal\geolocation\GeolocationCore;
use Drupal\views\Plugin\views\display\DisplayPluginBase;
use Drupal\views\Plugin\views\field\NumericField;
use Drupal\views\Plugin\views\query\Sql;
use Drupal\views\ResultRow;
use Drupal\views\ViewExecutable;

/**
 * Field handler for geolocaiton field.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("geolocation_field_proximity")
 */
class ProximityField extends NumericField {

  // Inject GeolocationCore.
  use GeoCoreInjectionTrait;

  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    // Add source, lat, lng and filter.
    return [
      'proximity_source' => ['default' => 'direct_input'],
      'proximity_lat' => ['default' => ''],
      'proximity_lng' => ['default' => ''],
      'units' => ['default' => 'km'],
      'proximity_filter' => ['default' => ''],
    ] + parent::defineOptions();
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    // Add a start point selector.
    // Add the proximity field group.
    $form['proximity_group'] = [
      '#type' => 'fieldset',
      '#title' => t('Proximity Settings'),
    ];
    // Add the source selector.
    $form['proximity_source'] = [
      '#type' => 'select',
      '#title' => t('Select the source type.'),
      '#description' => t('To calculate proximity we need a starting point to compare the field value to. Select where to get the start location.'),
      '#default_value' => $this->options['proximity_source'],
      '#fieldset' => 'proximity_group',
      '#options' => [
        'direct_input' => t('Direct Input'),
        'filter' => t('Filter Settings'),
      ],
    ];
    // Add the Latitude field for direct input.
    $form['proximity_lat'] = [
      '#type' => 'textfield',
      '#title' => t('Latitude'),
      '#empty_value' => '',
      '#default_value' => $this->options['proximity_lat'],
      '#maxlength' => 255,
      '#fieldset' => 'proximity_group',
      '#states' => [
        'visible' => [
          'select[name="options[proximity_source]"]' => ['value' => 'direct_input'],
        ],
      ],
    ];
    // Add the Latitude field for direct input.
    $form['proximity_lng'] = [
      '#type' => 'textfield',
      '#title' => t('Longitude'),
      '#empty_value' => '',
      '#default_value' => $this->options['proximity_lng'],
      '#maxlength' => 255,
      '#fieldset' => 'proximity_group',
      '#states' => [
        'visible' => [
          'select[name="options[proximity_source]"]' => ['value' => 'direct_input'],
        ],
      ],
    ];
    $form['units'] = [
      '#type' => 'select',
      '#title' => $this->t('Units'),
      '#default_value' => !empty($this->options['units']) ? $this->options['units'] : '',
      '#weight' => 40,
      '#fieldset' => 'proximity_group',
      '#options' => [
        'mile' => $this->t('Miles'),
        'km' => $this->t('Kilometers'),
      ],
      '#states' => [
        'visible' => [
          'select[name="options[proximity_source]"]' => ['value' => 'direct_input'],
        ],
      ],
    ];

    // Buffer available  filters.
    $valid_filters = [];

    // Check for valid filters.
    foreach ($this->view->getHandlers('filter', $this->view->current_display) as $delta => $filter) {
      if ($filter['plugin_id'] === 'geolocation_filter_proximity') {
        $valid_filters[$delta] = $filter['id'];
      }
    }
    // Add the Filter selector.
    $form['proximity_filter'] = empty($valid_filters)
      ? ['#markup' => t('There are no proximity filters available in this display.')]
      : [
        '#type' => 'select',
        '#title' => t('Select filter.'),
        '#description' => t('Select the filter to use as the starting point for calculating proximity.'),
        '#options' => $valid_filters,
      ];
    $form['proximity_filter'] += [
      '#fieldset' => 'proximity_group',
      '#states' => [
        'visible' => [
          'select[name="options[proximity_source]"]' => ['value' => 'filter'],
        ],
      ],
    ];

    // Add the Drupal\views\Plugin\views\field\Numeric settings to the form.
    parent::buildOptionsForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function query() {
    /** @var Sql $query */
    $query = $this->query;
    $table_name = $this->ensureMyTable();
    if ($this->options['proximity_source'] === 'filter' && $this->view->filter[$this->options['proximity_filter']]) {
      $filter = $this->view->filter[$this->options['proximity_filter']];
      $lat = $filter->value['lat'];
      $lgn = $filter->value['lng'];
      $units = $filter->value['units'];
    } else {
      $lat = $this->options['proximity_lat'];
      $lgn = $this->options['proximity_lng'];
      $units = $this->options['units'];
    }
    // Get the earth radius from the units.
    $earth_radius = $units === 'mile' ? GeolocationCore::EARTH_RADIUS_MILE : GeolocationCore::EARTH_RADIUS_KM;

    // Build the query expression.
    $expression = $this->geolocation_core->getQueryFragment($table_name, $this->realField, $lat, $lgn, $earth_radius);

    // Get a placeholder for this query and save the field_alias for it.
    $placeholder = $this->placeholder();
    $this->field_alias = substr($placeholder, 1);
    // We use having to be able to reuse the query on field handlers
    $query->addField(NULL, $expression, $this->field_alias);
  }
}