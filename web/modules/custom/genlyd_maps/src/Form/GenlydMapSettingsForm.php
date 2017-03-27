<?php
/**
 * @file
 * Contains Drupal\itk_footer\Form\ITKFooterContentForm.
 */

namespace Drupal\genlyd_maps\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class ITKFooterContentForm
 *
 * @package Drupal\itk_footer\Form
 */
class GenlydMapSettingsForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'genlyd_maps_settings';
  }

  /**
   * Get key/value storage for base config.
   *
   * @return object
   */
  private function getBaseConfig() {
    return \Drupal::getContainer()->get('genlyd_maps.config');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->getBaseConfig();

    $form['wrapper'] = array(
      '#title' => $this->t('Maps API keys'),
      '#type' => 'details',
      '#weight' => '1',
      '#open' => TRUE,
    );

    $form['wrapper']['genlyd_maps_google_api_key'] = array(
      '#title' => $this->t('Google API key'),
      '#type' => 'textfield',
      '#default_value' => $config->get('genlyd_maps_google_api_key'),
      '#weight' => '1',
    );

    $form['actions'] = array('#type' => 'actions');
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#attributes' => ['class' => ['button--primary']],
      '#value' => t('Save content'),
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
      'genlyd_maps_google_api_key' => $form_state->getValue('genlyd_maps_google_api_key'),
    ));

    drupal_flush_all_caches();
  }
}

