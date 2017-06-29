<?php
/**
 * @file
 * Contains override of geocoder service.
 */

namespace Drupal\genlyd_search\Service;

class Geocoder extends \Drupal\geocoder\Geocoder {

  /**
   * Overrides Geocoder log function to avoid the drupal_set_message.
   *
   * Log a message in the Drupal watchdog.
   *
   * @param string $message
   *   The message.
   * @param string $type
   *   The type of message.
   */
  public static function log($message, $type) {
    \Drupal::logger('geocoder')->log($type, $message);
  }
}
