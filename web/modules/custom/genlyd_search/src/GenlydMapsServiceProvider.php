<?php
/**
 * @file
 * Contains service overrides.
 */

namespace Drupal\genlyd_search;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderBase;
use Drupal\genlyd_search\Service\Geocoder;

/**
 * Modifies the language manager service.
 */
class GenlydSearchServiceProvider extends ServiceProviderBase {

  /**
   * {@inheritdoc}
   */
  public function alter(ContainerBuilder $container) {
    // Override geocoder service.
    $definition = $container->getDefinition('geocoder');
    $definition->setClass(Geocoder::class);
  }

}
