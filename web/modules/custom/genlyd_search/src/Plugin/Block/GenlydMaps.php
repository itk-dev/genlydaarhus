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
      '#theme' => 'genlyd_search_map_block',
      '#attached' => [
        'library' => [
          'genlyd_search/openlayers',
          'genlyd_search/maps',
        ],
        'drupalSettings' => [
          'genlyd_search' => [
            'path' => '/' . drupal_get_path('module', 'genlyd_search'),
            'marker' => '/images/marker.png',
            'template' => '/js/templates/popup.html.twig',
          ],
        ],
      ],
    ];
  }
}
