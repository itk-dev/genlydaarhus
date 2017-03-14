<?php
/**
 * @file
 * Contains \Drupal\genlyd\Theme\GenlydThemeNegotiator.
 */

namespace Drupal\genlyd\Theme;

use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Theme\ThemeNegotiatorInterface;

/**
 * Class ThemeNegotiator
 *
 * Controls which theme is applies to a given path.
 *
 * @package Drupal\genlyd\Theme
 */
class GenlydThemeNegotiator implements ThemeNegotiatorInterface {

  /**
   * Renegotiate paths from administration theme to genlyd_aarhus theme.
   *
   * @param \Drupal\Core\Routing\RouteMatchInterface $route
   * @return bool
   */
  public function applies(RouteMatchInterface $route) {
    switch ($route->getRouteName()) {
      case 'entity.user.edit_form':
        return true;
      case 'entity.node.edit_form':
        $node = $route->getParameter('node');

        if ($node->getType() == 'activity') {
          return true;
        }
    }
    return false;
  }

  /**
   * {@inheritdoc}
   */
  public function determineActiveTheme(RouteMatchInterface $route) {
    return 'genlyd_aarhus';
  }
}
