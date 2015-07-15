<?php

/**
 * @file
 *   Definition of Drupal\geolocation\Plugin\views\Proximity.
 */

namespace Drupal\geolocation\Plugin\views\field;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\field\NumericField;

/**
 * Field handler for geolocaiton field.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("geolocation_field_proximity")
 */
class Proximity extends NumericField {

  /**
   * {@inheritdoc}
   */
  protected function getFieldStorageDefinition() {
    return parent::getFieldStorageDefinition();
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
    $form['proximity_group']['source'] = [
      '#type' => 'select',
      '#title' => t('Select the source type.'),
      '#description' => t('To calculate proximity we need a starting point to compare the field value to. Select where to get the start location.'),
      '#options' => [
        'direct_input' => t('Direct Input'),
        'filter' => t('Filter Settings'),
      ],
    ];
    // Add the Lat/Lng field group for direct input.
//    $form['proximity_group']['lat_lng'] = [
//      '#type' => 'fieldset',
//      '#title' => t('Direct Input'),
//      '#states' => [
//
//      ],
//    ];
    // Add the Latitude field for direct input.
    $form['proximity_group']['lat'] = [
      '#type' => 'textfield',
      '#title' => t('Latitude'),
      '#empty_value' => '',
      '#default_value' => '',
      '#maxlength' => 255,
      '#description' => t('Latitude'),
      '#states' => [
        'visible' => [
          'select[name="options[proximity_group][source]"]' => ['value' => 'direct_input'],
        ],
      ],
    ];
    // Add the Latitude field for direct input.
    $form['proximity_group']['lng'] = [
      '#type' => 'textfield',
      '#title' => t('Longitude'),
      '#empty_value' => '',
      '#default_value' => '',
      '#maxlength' => 255,
      '#description' => t('Longitude'),
      '#states' => [
        'visible' => [
          'select[name="options[proximity_group][source]"]' => ['value' => 'direct_input'],
        ],
      ],
    ];
    $valid_filters = [];

    $handlers = $this->view->getHandlers('filter', $this->view->current_display);
    // Check for valid filters.
    foreach ($this->view->getHandlers('filter', $this->view->current_display) as $delta => $filter) {
      if ($filter['plugin_id'] === 'geolocation_filter_proximity') {
        $valid_filters[$delta] = $filter['id'];
      }
    }
    // Add the Filter selector.
    $form['proximity_group']['filter_selector'] = empty($valid_filters)
      ? ['#markup' => t('There are no proximity filters available in this display.')]
      : [
        '#type' => 'select',
        '#title' => t('Select filter.'),
        '#description' => t('Select the filter to use as the starting point for calculating proximity.'),
        '#options' => $valid_filters,
        '#states' => [
          'visible' => [
            'select[name="options[proximity_group][source]"]' => ['value' => 'filter'],
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
    return NULL;
    $this->ensureMyTable();
    // Add the field.
    $params = $this->options['group_type'] != 'group' ? array('function' => $this->options['group_type']) : array();
    $this->field_alias = $this->query->addField(NULL, $this->realField, NULL, $params);

    // Figure out the starting point.

    // Add the query expression.

//    $this->addAdditionalFields();
  }

}
