<?php
/**
 * @file
 * Contains key/value storage for itk_siteimprove settings.
 */

namespace Drupal\itk_siteimprove\State;

use Drupal\Core\KeyValueStore\DatabaseStorage;
use Drupal\Component\Serialization\SerializationInterface;
use Drupal\Core\Database\Connection;

class ITKSiteimproveSettings extends DatabaseStorage {
  /**
   * @param \Drupal\Component\Serialization\SerializationInterface $serializer
   * @param \Drupal\Core\Database\Connection $connection
   */
  public function __construct(SerializationInterface $serializer, Connection $connection) {
    parent::__construct('itk_siteimprove.settings', $serializer, $connection);
  }
}
