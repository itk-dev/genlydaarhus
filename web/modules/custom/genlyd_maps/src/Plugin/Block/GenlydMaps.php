<?php

namespace Drupal\genlyd_maps\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Menu\MenuTreeParameters;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides itk_footer content
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
