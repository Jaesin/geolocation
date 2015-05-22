<?php

/**
 * @file
 *   Definition of Drupal\geolocation\Plugin\views\Proximity.
 */

namespace Drupal\geolocation\Plugin\views\field;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\field\Numeric;

/**
 * Field handler for geolocaiton field.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("geolocation_field_proximity")
 */
class Proximity extends Numeric {

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
    parent::buildOptionsForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function query() {
    $this->ensureMyTable();
    // Add the field.
    $params = $this->options['group_type'] != 'group' ? array('function' => $this->options['group_type']) : array();
    $this->field_alias = $this->query->addField(NULL, $this->realField, NULL, $params);

//    $this->addAdditionalFields();
  }

}
