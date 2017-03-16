<?php

namespace Drupal\itk_activity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\user\EntityOwnerInterface;
use Drupal\Core\Entity\EntityChangedInterface;

/**
 * Provides an interface defining a ExternalUser entity.
 * @ingroup content_entity_example
 */
interface ExternalUserInterface extends ContentEntityInterface, EntityOwnerInterface, EntityChangedInterface {

}
