<?php
/**
 * @file
 * Contains key/value storage for itk_info_section base config.
 */

namespace Drupal\itk_info_section\State;

use Drupal\Core\KeyValueStore\DatabaseStorage;
use Drupal\Component\Serialization\SerializationInterface;
use Drupal\Core\Database\Connection;

class ITKInfoSectionConfig extends DatabaseStorage {
  /**
   * @param \Drupal\Component\Serialization\SerializationInterface $serializer
   * @param \Drupal\Core\Database\Connection $connection
   */
  public function __construct(SerializationInterface $serializer, Connection $connection) {
    parent::__construct('itk_info_section.config', $serializer, $connection);
  }
}