<?php
/**
 * @file
 * Contains \Drupal\itk_activity\Form\ActivityExternalSignupUserForm.
 */

namespace Drupal\itk_activity\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\itk_activity\Entity\ExternalUser;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Url;
use Drupal\node\Entity\Node;

/**
 * ActivityExternalSignupUserForm.
 */
class ActivityExternalSignupUserForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'itk_activity_external_signup_form';
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
      '#title' => t('First name'),
    );

    $form['surname'] = array(
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => t('Surname'),
    );

    $form['email'] = array(
      '#type' => 'email',
      '#required' => TRUE,
      '#title' => t('Email'),
    );

    $form['phone'] = array(
      '#type' => 'tel',
      '#required' => TRUE,
      '#title' => t('Phone'),
    );

    $form['submit'] = array(
      '#type' => 'submit',
      '#value' => t('Sign up user'),
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
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

      // Create new external user entity.
      $externalUser = ExternalUser::create([
        'type' => 'itk_activity_external_user',
        'first_name' => $firstName,
        'surname' => $surname,
        'email' => $email,
        'phone' => $phone,
      ]);
      $externalUser->save();

      // Update activity node.
      $node->field_external_signed_up_users[] = $externalUser;
      $node->save();

      // Add message.
      drupal_set_message(t(':first_name is registered to activity.', [
        ':first_name' => $firstName,
      ]));
    }
    else {
      drupal_set_message(t('Activity is not set.'));
    }

    // Redirect to node.
    $form_state->setRedirect('entity.node.canonical', ['node' => $node->id()]);
  }

}
