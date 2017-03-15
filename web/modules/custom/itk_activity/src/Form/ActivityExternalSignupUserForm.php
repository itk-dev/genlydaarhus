<?php
/**
 * @file
 * Contains \Drupal\itk_activity\Form\ActivityExternalSignupUserForm.
 */

namespace Drupal\itk_activity\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Url;

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

    $form['name'] = array(
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => $this->t('Name'),
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

    $form['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Sign up user'),
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
    $name = $form_state->getValue('name');
    $email = $form_state->getValue('email');
    $phone = $form_state->getValue('phone');

    $node = $form_state->get('node');

    if (isset($node)) {
      $node->field_external_signed_up_users[] = $name . " - " . $email . " - " . $phone;

      $node->save();
    }

    // Add message.
    drupal_set_message(t($name . ' is registered to activity.'));

    // Redirect to node.
    $form_state->setRedirect('entity.node.canonical', ['node' => $node->id()]);
  }

}
