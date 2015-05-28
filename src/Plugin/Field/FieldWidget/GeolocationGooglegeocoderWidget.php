<?php

/**
 * @file
 * Contains \Drupal\geolocation\Plugin\Field\FieldWidget\GeolocationGooglegeocoderWidget.
 */

namespace Drupal\geolocation\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'geolocation_googlegeocoder' widget.
 *
 * @FieldWidget(
 *   id = "geolocation_googlegeocoder",
 *   label = @Translation("Geoloaction Google Geocoder"),
 *   field_types = {
 *     "geolocation"
 *   }
 * )
 */
class GeolocationGooglegeocoderWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    // Get this field name and parent.
    $field_name = $this->fieldDefinition->getName();
    $parents = $form['#parents'];
    // Get the field state.
    $field_state = static::getWidgetState($parents, $field_name, $form_state);

    // Create a unique canvas id for each map of each geolocation field instance.
    $field_id = preg_replace('/[^a-zA-Z0-9\-]/', '-', $this->fieldDefinition->getName());
    $canvas_id = !empty($field_state['canvas_ids'][$delta])
      ? $field_state['canvas_ids'][$delta]
      : uniqid("map-canvas-{$field_id}-");

    // Add the canvas id for this field.
    $field_state['canvas_ids'] = isset($field_state['canvas_ids'])
      ?  $field_state['canvas_ids'] + [$delta => $canvas_id]
      : [$delta => $canvas_id];

    // Save the field state for this field.
    static::setWidgetState($parents, $field_name, $form_state, $field_state);

    // Get the geolocation value for this element.
    $lat = $items[$delta]->lat;
    $lng = $items[$delta]->lng;

    // Get the default values for existing field.
    $lat_default_value = isset($lat) ? $lat : NULL;
    $lng_default_value = isset($lng) ? $lng : NULL;

    // Hidden lat,lng input fields.
    $element['lat'] = array(
      '#type' => 'hidden',
      '#default_value' => $lat_default_value,
      '#attributes' => array('class' => array('geolocation-hidden-lat', "for-{$canvas_id}")),
    );
    $element['lng'] = array(
      '#type' => 'hidden',
      '#default_value' => $lng_default_value,
      '#attributes' => array('class' => array('geolocation-hidden-lng', "for-{$canvas_id}")),
    );

    // Add the map container.
    $element['map_canvas'] = array(
      '#markup' => '<div id="' . $canvas_id . '" class="geolocation-map-canvas"></div>',
      '#attached' => [
        'library' => array(
          'geolocation/geolocation.widgets.googlegeocoder',
        ),
        'drupalSettings' => array(
          'geolocation' => [
            'widget_maps' => [
              $canvas_id => [
                'id' => $canvas_id,
                'lat' => (float)$lat_default_value,
                'lng' => (float)$lng_default_value,
                'settings' => [],
              ],
            ],
          ],
        ),
      ],
    );

    // Wrap the whole form in a container.
    $element += array(
      '#type' => 'container',
      '#title' => $element['#title'],
    );

    return $element;
  }
}
