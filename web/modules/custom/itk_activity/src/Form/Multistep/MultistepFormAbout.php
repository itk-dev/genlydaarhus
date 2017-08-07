<?php
/**
 * @file
 * Contains \Drupal\itk_activity\Form\Multistep\MultistepFormAbout.
 */

namespace Drupal\itk_activity\Form\Multistep;

use Drupal\Core\Form\FormStateInterface;

/**
 * Class MultistepFormAbout.
 *
 * @package Drupal\itk_activity\Form\Multistep
 */
class MultistepFormAbout extends MultistepFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'multistep_form_one';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);

    $form['data']['progressBar'] = $this->getProgressBar('about');

    $form['title'] = array(
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => t('Title'),
      '#default_value' => $this->store->get('title') ? $this->store->get('title') : '',
    );

    $form['body'] = array(
      '#type' => 'textarea',
      '#required' => TRUE,
      '#title' => t('Description'),
      '#default_value' => $this->store->get('body') ? $this->store->get('body') : '',
    );

    $form['field_signup_required'] = array(
      '#type' => 'radios',
      '#required' => TRUE,
      '#default_value' => $this->store->get('field_signup_required'),
      '#options' => [
        1 => t('Sign up required'),
        0 => t('Sign up not required'),
      ],
    );

    $form['actions']['submit']['#value'] = t('Next');

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
  protected function commitStep(FormStateInterface $form_state) {
    // Set values in storage.
    $this->store->set('title', $form_state->getValue('title'));
    $this->store->set('body', $form_state->getValue('body'));
    $this->store->set('field_signup_required', $form_state->getValue('field_signup_required'));
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->commitStep($form_state);
    $this->acceptStep('information');

    // Redirect to next step.
    $form_state->setRedirect('itk_activity.multistep_information');
  }

}
