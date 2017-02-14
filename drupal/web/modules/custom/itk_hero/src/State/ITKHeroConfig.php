<?php
/**
 * @file
 * Contains key/value storage for itk_hero base config.
 */

namespace Drupal\itk_hero\State;

use Drupal\Core\KeyValueStore\DatabaseStorage;
use Drupal\Component\Serialization\SerializationInterface;
use Drupal\Core\Database\Connection;

class ITKHeroConfig extends DatabaseStorage {
  /**
   * @param \Drupal\Component\Serialization\SerializationInterface $serializer
   * @param \Drupal\Core\Database\Connection $connection
   */
  public function __construct(SerializationInterface $serializer, Connection $connection) {
    parent::__construct('itk_hero.config', $serializer, $connection);
  }
}