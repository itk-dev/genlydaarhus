<?php
/**
 * @file
 * Contains \Drupal\itk_activity\Form\ActivityContactForm.
 */

namespace Drupal\itk_activity\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Url;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpFoundation\Response;
use Drupal\Core\Flood\FloodInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Drupal\user\UserStorageInterface;

/**
 * ActivityContactForm.
 */
class ActivityContactForm extends FormBase {

  /**
   * The flood control mechanism.
   *
   * @var \Drupal\Core\Flood\FloodInterface
   */
  protected $flood;

  /**
   * The user storage.
   *
   * @var \Drupal\user\UserStorageInterface
   */
  protected $userStorage;

  /**
   * Constructs a MessageForm object.
   *
   * @param \Drupal\Core\Flood\FloodInterface $flood
   *   The flood control mechanism.
   * @param \Drupal\user\UserStorageInterface $user_storage
   *   The user storage.
   */
  public function __construct(FloodInterface $flood, UserStorageInterface $user_storage) {
    $this->flood = $flood;
    $this->userStorage = $user_storage;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('flood'),
      $container->get('entity_type.manager')->getStorage('user')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'itk_activity_contact_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $nid = NULL) {
    $node = \Drupal::entityTypeManager()->getStorage('node')->load($nid);

    // If the node does not exist redirect to frontpage.
    if (!isset($node)) {
      drupal_set_message(t('The activity does not exist.'));
      return new RedirectResponse(Url::fromRoute('<front>')->toString());
    }

    // Set node for later processing.
    $form_state->set('node', $node);

    $form['first_name'] = array(
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => $this->t('First name'),
    );

    $form['surname'] = array(
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => $this->t('Surname'),
    );

    $form['email'] = array(
      '#type' => 'email',
      '#required' => TRUE,
      '#title' => $this->t('Email'),
    );

    $form['phone'] = array(
      '#type' => 'tel',
      '#required' => TRUE,
      '#title' => $this->t('Phone'),
    );

    $form['message'] = array(
      '#type' => 'textarea',
      '#required' => TRUE,
      '#title' => $this->t('Message'),
    );

    $form['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Write to owner'),
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    $this->floodControl();
  }


  /**
   * Enforces flood control for the form submit.
   */
  protected function floodControl() {
    $this->flood->register('itk_activity.mail_to_activity_owner');

    $limitAttempts = 3;
    $limitTimeWindow = 900;

    if (!$this->flood->isAllowed('itk_activity.mail_to_activity_owner', $limitAttempts, $limitTimeWindow)) {
      throw new AccessDeniedHttpException('Access is blocked because of IP based flood prevention.', NULL, Response::HTTP_TOO_MANY_REQUESTS);
    }
  }

  /**
   * Gets the login identifier for user login flood control.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The current request.
   * @param string $username
   *   The username supplied in login credentials.
   *
   * @return string
   *   The login identifier or if the user does not exist an empty string.
   */
  protected function getLoginFloodIdentifier(Request $request, $username) {
    $flood_config = $this->config('user.flood');
    $accounts = $this->userStorage->loadByProperties([
      'name' => $username,
      'status' => 1
    ]);
    if ($account = reset($accounts)) {
      if ($flood_config->get('uid_only')) {
        // Register flood events based on the uid only, so they apply for any
        // IP address. This is the most secure option.
        $identifier = $account->id();
      }
      else {
        // The default identifier is a combination of uid and IP address. This
        // is less secure but more resistant to denial-of-service attacks that
        // could lock out all users with public user names.
        $identifier = $account->id() . '-' . $request->getClientIp();
      }
      return $identifier;
    }
    return '';
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $node = $form_state->get('node');

    if (isset($node)) {
      $firstName = $form_state->getValue('first_name');
      $surname = $form_state->getValue('surname');
      $email = $form_state->getValue('email');
      $phone = $form_state->getValue('phone');
      $message = $form_state->getValue('message');

      $mailManager = \Drupal::service('plugin.manager.mail');

      $message = \Drupal::translation()->translate(":firstName :surname (:email, :phone) has sent you a message.\n\n:message", [
        ':firstName' => $firstName,
        ':surname' => $surname,
        ':email' => $email,
        ':phone' => $phone,
        ':message' => $message,
      ]);

      $params = [
        'message' => $message,
        'node_title' => \Drupal::translation()->translate('A user has sent a mail to you about your activity at Genlyd.'),
      ];

      // Send the mail.
      $result = $mailManager->mail(
          'itk_activity',
          'send_mail_to_owner',
          $node->uid->entity->mail->value,
          \Drupal::currentUser()->getPreferredLangcode(),
          $params,
          NULL,
          TRUE);
      if ($result['result'] !== TRUE) {
        drupal_set_message(t('Message not sent to owner.'), 'error');
      }
      else {
        drupal_set_message(t('Message sent to owner.'));
      }
    }

    // Redirect to node.
    $form_state->setRedirect('entity.node.canonical', ['node' => $node->id()]);
  }

}
