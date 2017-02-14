<?php
/**
 * @file
 * Contains Drupal\itk_floating_help\Form\ITKFloatingHelpContentForm.
 */

namespace Drupal\itk_floating_help\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class ITKFloatingHelpContentForm
 *
 * @package Drupal\itk_floating_help\Form
 */
class ITKFloatingHelpContentForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'itk_floating_help';
  }

  /**
   * Get key/value storage for base config.
   *
   * @return object
   */
  private function getBaseConfig() {
    return \Drupal::getContainer()->get('itk_floating_help.config');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->getBaseConfig();

    $form['intro_wrapper'] = array(
      '#title' => $this->t('ITK Floating Help'),
      '#type' => 'item',
      '#description' => $this->t('Configure the content of the floating help module.'),
      '#weight' => '1',
      '#open' => TRUE,
    );

    $form['floating_help_title'] = array(
      '#title' => $this->t('Title'),
      '#type' => 'textfield',
      '#default_value' => $config->get('floating_help_title'),
      '#required' => true,
      '#weight' => '1',
    );

    $form['floating_help_text'] = array(
      '#title' => $this->t('Text'),
      '#type' => 'textfield',
      '#default_value' => $config->get('floating_help_text'),
      '#required' => true,
      '#weight' => '2',
    );

    $form['floating_help_contact'] = array(
      '#title' => $this->t('Contact'),
      '#type' => 'textfield',
      '#default_value' => $config->get('floating_help_contact'),
      '#weight' => '3',
    );

    $form['floating_help_phone'] = array(
      '#title' => $this->t('Phone'),
      '#type' => 'textfield',
      '#default_value' => $config->get('floating_help_phone'),
      '#weight' => '4',
    );

    $form['floating_help_email'] = array(
      '#title' => $this->t('Email'),
      '#type' => 'textfield',
      '#default_value' => $config->get('floating_help_email'),
      '#weight' => '4',
    );

    $form['submit'] = array(
      '#type' => 'submit',
      '#value' => t('Save changes'),
      '#weight' => '6',
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    drupal_set_message('Settings saved');

    // Set the rest of the configuration values.
    $this->getBaseConfig()->setMultiple(array(
      'floating_help_title' => $form_state->getValue('floating_help_title'),
      'floating_help_text' => $form_state->getValue('floating_help_text'),
      'floating_help_contact' => $form_state->getValue('floating_help_contact'),
      'floating_help_phone' => $form_state->getValue('floating_help_phone'),
      'floating_help_email' => $form_state->getValue('floating_help_email'),
    ));

    drupal_flush_all_caches();
  }
}
