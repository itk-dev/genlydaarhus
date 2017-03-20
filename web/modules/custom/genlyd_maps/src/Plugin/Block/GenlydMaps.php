<?php
/**
 * @file
 * Defines block to display activities map.
 */

namespace Drupal\genlyd_maps\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides activities map block content.
 *
 * @Block(
 *   id = "genlyd_map",
 *   admin_label = @Translation("Genlyd map"),
 * )
 */
class GenlydMaps extends BlockBase {
  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#theme' => 'genlyd_maps_map_block',
      '#attached' => [
        'library' => [
          'genlyd_maps/openlayers',
          'genlyd_maps/maps',
        ],
        'drupalSettings' => [
          'genlyd_maps' => [
            'path' => '/' . drupal_get_path('module', 'genlyd_maps'),
            'marker' => '/images/marker.png',
          ],
        ],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);

    return $form;
  }
}
