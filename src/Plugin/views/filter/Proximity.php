<?php

/**
 * @file
 *   Definition of Drupal\search\Plugin\views\filter\Search.
 */

namespace Drupal\geolocation\Plugin\views\filter;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\geolocation\GeolocationCore;
use Drupal\views\Plugin\views\display\DisplayPluginBase;
use Drupal\views\Plugin\views\filter\NumericFilter;
use Drupal\views\Plugin\views\query\Sql;
use Drupal\views\ViewExecutable;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Filter handler for search keywords.
 *
 * @ingroup views_filter_handlers
 *
 * @ViewsFilter("geolocation_filter_proximity")
 */
class Proximity extends NumericFilter implements ContainerFactoryPluginInterface {

  /**
   * The field alias.
   *
   * @var string
   */
  protected $field_alias;

  /**
   * The geolocaiton core service.
   *
   * @var \Drupal\geolocation\GeolocationCore
   */
  protected $geolocation_core;

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
   * @param \Drupal\geolocation\GeolocationCore
   *   The geolocation core helper.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, GeolocationCore $geolocation_core) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->geolocation_core = $geolocation_core;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('geolocation.core')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function init(ViewExecutable $view, DisplayPluginBase $display, array &$options = NULL) {
    parent::init($view, $display, $options);

    // Set the field alias.
    $this->field_alias = $this->options['id'] . '_filter';
  }

  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    $options = parent::defineOptions();

    // value is already set up properly, we're just adding our new field to it.
    $options['value']['contains']['lat']['default'] = '';
    $options['value']['contains']['lng']['default'] = '';

    return $options;
  }

  /**
   * {@inheritdoc}
   */
  protected function valueForm(&$form, FormStateInterface $form_state) {
    $form['value']['lat'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Latitude'),
      '#default_value' => !empty($this->value['lat']) ? $this->value['lat'] : '',
    ];
    $form['value']['lng'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Longitude'),
      '#default_value' => !empty($this->value['lng']) ? $this->value['lng'] : '',
    ];
    parent::valueForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function query() {
    /** @var Sql $query */
    $query = $this->query;
    $table_name = $this->ensureMyTable();
    $field_id = str_replace('_proximity', '', $this->realField);
    $lat = $this->value['lat'];
    $lgn = $this->value['lng'];

    $expression = $this->geolocation_core->getQueryFragment($table_name, $field_id, $lat, $lgn);

    // We use having to be able to reuse the query on field handlers
    $query->addField(NULL, $expression, $this->field_alias);

    $info = $this->operators();
    $placeholder = $this->placeholder();
    if (!empty($info[$this->operator]['method'])) {
      $this->{$info[$this->operator]['method']}($placeholder, $expression);
    }
  }

  /**
   * {@inheritdoc}
   */
  protected function opBetween($placeholder, $expression) {
    if ($this->operator == 'between') {
      $this->query->addWhereExpression($this->options['group'], "{$expression} BETWEEN {$placeholder}_min AND {$placeholder}_max",
        [
          $placeholder . '_min' => $this->value['min'],
          $placeholder . '_max' => $this->value['max']
        ]
      );
    }
    else {
      $this->query->addWhereExpression($this->options['group'], "{$expression} <= {$placeholder}_min OR {$field} >= {$placeholder}_max",
        [
          $placeholder . '_min' => $this->value['min'],
          $placeholder . '_max' => $this->value['max']
        ]
      );
    }
  }

  /**
   * {@inheritdoc}
   */
  protected function opSimple($placeholder, $expression) {
    $this->query->addWhereExpression($this->options['group'], "{$expression} {$this->operator} {$placeholder}",
      [
        $placeholder => $this->value['value']
      ]
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function opEmpty($placeholder, $expression) {
    if ($this->operator == 'empty') {
      $operator = "IS NULL";
    }
    else {
      $operator = "IS NOT NULL";
    }

    $this->query->addWhereExpression($this->options['group'], "{$expression} {$operator}");
  }

  /**
   * @inheritdoc
   */
  protected function opRegex($placeholder, $expression) {
    $this->query->addWhereExpression($this->options['group'], "{$expression} 'REGEXP' {$placeholder}",
      [
        $placeholder => $this->value['value']
      ]
    );
  }

}
