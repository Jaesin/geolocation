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
    $options['lat'] = [
      'default' => ''
    ];
    $options['lng'] = [
      'default' => ''
    ];
    $options['current_location'] = [
      'default' => FALSE
    ];
    $options['distance'] = [
      'default' => 10
    ];
    return $options;
  }

  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    // @todo Find out how to store data in sessions
    // @todo Add menu path for AJAX callback and store user location in session variables.
    // @todo Use session variables in $this->query
    $form['current_location'] = [
      '#type' => 'checkbox',
      '#title' => $this->t("Use user's current location"),
      '#default_value' => $this->options['current_location'],
    ];
    $form['location'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Default location'),
      '#states' => [
        'visible' => [
          ':input[name="options[current_location]"]' => [
            'checked' => FALSE
          ],
        ]
      ]
    ];
    $form['location']['lat'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Latitude'),
      '#default_value' => $this->options['lat'],
    ];
    $form['location']['lng'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Longitude'),
      '#default_value' => $this->options['lng'],
    ];
    $form['distance'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Distance'),
      '#default_value' => $this->options['distance'],
    ];
  }

  /**
   * Return the default argument.
   */
  public function getArgument() {
    return [
      $this->options['current_location'],
      $this->options['lat'],
      $this->options['lng']
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function query($group_by = FALSE) {

    $this->ensureMyTable();
    $query = $this->query;
    $table_name = $this->ensureMyTable();

    $field_id = str_replace('_proximity', '', $this->realField);

    if (!$this->options['current_location']) {
      $lat = $this->options['lat'];
      $lgn = $this->options['lng'];
    }

    $expression = GeolocationCore::getQueryFragment($table_name, $field_id, $lat, $lgn);

    // We use having to be able to reuse the query on field handlers
    $query->addField(NULL, $expression, $this->field_alias);

    $this->query->addHavingExpression($this->options['group'], "$this->tableAlias.$this->realField <= {$placeholder}_distance",
      [
        $placeholder . '_distance' => $this->argument
      ]
    );

  }

}
