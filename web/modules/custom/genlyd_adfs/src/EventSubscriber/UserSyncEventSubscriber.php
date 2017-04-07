<?php

namespace Drupal\genlyd_adfs\EventSubscriber;

use Drupal\samlauth\Event\SamlauthEvents;
use Drupal\samlauth\Event\SamlauthUserSyncEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Event subscriber that synchronizes user properties on a user_sync event.
 */
class UserSyncEventSubscriber implements EventSubscriberInterface {
  /**
   * A logger instance.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * Construct a new SamlauthUserSyncSubscriber.
   *
   * @param \Psr\Log\LoggerInterface $logger
   *   A logger instance.
   */
  public function __construct(LoggerInterface $logger) {
    $this->logger = $logger;
  }
  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      SamlauthEvents::USER_SYNC => ['onUserSync'],
    ];
  }

  /**
   * Performs actions to synchronize users with Factory data on login.
   *
   * @param \Drupal\samlauth\Event\SamlauthUserSyncEvent $event
   *   The event.
   */
  public function onUserSync(SamlauthUserSyncEvent $event) {
    $account = $event->getAccount();
    $firstname = $this->getAttribute('http://schemas.xmlsoap.org/ws/2005/05/identity/claims/givenname', $event);
    $surname = $this->getAttribute('http://schemas.xmlsoap.org/ws/2005/05/identity/claims/surname', $event);
    $account->set('field_first_name', $firstname ?: 'first_name');
    $account->set('field_surname', $surname ?: 'surname');
    $event->markAccountChanged();
  }

  /**
   * @param string $config_key
   *   A key in the module's configuration, containing the name of a SAML
   *   attribute.
   * @param \Drupal\samlauth\Event\SamlauthUserSyncEvent $event
   *   The event, which holds the attributes from the SAML response.
   *
   * @return mixed|null
   *   The SAML attribute value; NULL if the attribute value was not found.
   */
  public function getAttribute($name, SamlauthUserSyncEvent $event) {
    $attributes = $event->getAttributes();
    return $name && !empty($attributes[$name][0]) ? $attributes[$name][0] : NULL;
  }

}
