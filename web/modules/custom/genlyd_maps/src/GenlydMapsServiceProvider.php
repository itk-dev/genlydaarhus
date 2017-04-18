<?php
/**
 * @file
 * Contains service overrides.
 */

namespace Drupal\genlyd_maps;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderBase;
use Drupal\genlyd_maps\Service\Geocoder;

/**
 * Modifies the language manager service.
 */
class GenlydMapsServiceProvider extends ServiceProviderBase {

  /**
   * {@inheritdoc}
   *
   * @TODO: Remove this when the geocoding is handled in activity create.
   */
  public function alter(ContainerBuilder $container) {
    // Override geocoder service.
    $definition = $container->getDefinition('geocoder');
    $definition->setClass(Geocoder::class);
  }
}