<?php
/**
 * @file
 * Contains key/value storage for ITK Floating Help.
 */

namespace Drupal\itk_floating_help\State;

use Drupal\Core\KeyValueStore\DatabaseStorage;
use Drupal\Component\Serialization\SerializationInterface;
use Drupal\Core\Database\Connection;

class ITKFloatingHelpConfig extends DatabaseStorage {
  /**
   * @param \Drupal\Component\Serialization\SerializationInterface $serializer
   * @param \Drupal\Core\Database\Connection $connection
   */
  public function __construct(SerializationInterface $serializer, Connection $connection) {
    parent::__construct('itk_floating_help.config', $serializer, $connection);
  }
}
