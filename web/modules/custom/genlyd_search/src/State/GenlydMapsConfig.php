<?php
/**
 * @file
 * Contains key/value storage for genlyd search base config.
 */

namespace Drupal\genlyd_search\State;

use Drupal\Core\KeyValueStore\DatabaseStorage;
use Drupal\Component\Serialization\SerializationInterface;
use Drupal\Core\Database\Connection;

class GenlydSearchConfig extends DatabaseStorage {
  /**
   * @param \Drupal\Component\Serialization\SerializationInterface $serializer
   * @param \Drupal\Core\Database\Connection $connection
   */
  public function __construct(SerializationInterface $serializer, Connection $connection) {
    parent::__construct('genlyd_search.config', $serializer, $connection);
  }
}