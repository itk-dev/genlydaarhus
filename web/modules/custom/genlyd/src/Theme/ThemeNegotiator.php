<?php
/**
 * @file
 * Contains \Drupal\genlyd\Theme\ThemeNegotiator.
 */

namespace Drupal\genlyd\Theme;

use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Theme\ThemeNegotiatorInterface;

/**
 * Class ThemeNegotiator
 *
 * Controllers which theme is applies to a given path.
 *
 * @package Drupal\genlyd\Theme
 */
class ThemeNegotiator implements ThemeNegotiatorInterface {

  /**
   * Renegotiate paths from administration theme to genlyd_aarhus theme.
   *
   * @param \Drupal\Core\Routing\RouteMatchInterface $route
   * @return bool
   */
  public function applies(RouteMatchInterface $route) {
    switch ($route->getRouteName()) {
      case 'entity.user.edit_form':
        // /user/{user}/edit
        return true;
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
