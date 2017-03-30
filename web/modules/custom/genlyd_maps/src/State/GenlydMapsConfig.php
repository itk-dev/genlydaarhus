<?php
/**
 * @file
 * Contains key/value storage for genlyd maps base config.
 */

namespace Drupal\genlyd_maps\State;

use Drupal\Core\KeyValueStore\DatabaseStorage;
use Drupal\Component\Serialization\SerializationInterface;
use Drupal\Core\Database\Connection;

class GenlydMapsConfig extends DatabaseStorage {
  /**
   * @param \Drupal\Component\Serialization\SerializationInterface $serializer
   * @param \Drupal\Core\Database\Connection $connection
   */
  public function __construct(SerializationInterface $serializer, Connection $connection) {
    parent::__construct('genlyd_maps.config', $serializer, $connection);
  }
}