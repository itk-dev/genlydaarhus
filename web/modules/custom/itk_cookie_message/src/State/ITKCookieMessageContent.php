<?php
/**
 * @file
 * Contains key/value storage for itk_hero base config.
 */

namespace Drupal\itk_cookie_message\State;

use Drupal\Core\KeyValueStore\DatabaseStorage;
use Drupal\Component\Serialization\SerializationInterface;
use Drupal\Core\Database\Connection;

class ITKCookieMessageContent extends DatabaseStorage {
  /**
   * @param \Drupal\Component\Serialization\SerializationInterface $serializer
   * @param \Drupal\Core\Database\Connection $connection
   */
  public function __construct(SerializationInterface $serializer, Connection $connection) {
    parent::__construct('itk_cookie_message.content', $serializer, $connection);
  }
}