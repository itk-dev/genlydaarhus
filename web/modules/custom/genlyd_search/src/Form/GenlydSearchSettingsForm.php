<?php
/**
 * @file
 * Contains Drupal\itk_footer\Form\ITKFooterContentForm.
 */

namespace Drupal\genlyd_search\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class ITKFooterContentForm
 *
 * @package Drupal\itk_footer\Form
 */
class GenlydSearchSettingsForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'genlyd_search_settings';
  }

  /**
   * Get key/value storage for base config.
   *
   * @return object
   */
  private function getBaseConfig() {
    return \Drupal::getContainer()->get('genlyd_search.config');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->getBaseConfig();

    $form['wrapper'] = array(
      '#title' => t('Maps API keys'),
      '#type' => 'details',
      '#weight' => '1',
      '#open' => TRUE,
    );

    $form['wrapper']['genlyd_search_google_api_key'] = array(
      '#title' => t('Google API key'),
      '#type' => 'textfield',
      '#default_value' => $config->get('genlyd_search_google_api_key'),
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
    $this->getBaseConfig()->set('genlyd_search_google_api_key', $form_state->getValue('genlyd_search_google_api_key'));

    // Clear cache as this will trigger an re-encoding of addresses in the map.
    drupal_flush_all_caches();
  }
}

