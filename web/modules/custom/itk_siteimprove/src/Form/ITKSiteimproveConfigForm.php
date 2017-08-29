<?php
/**
 * @file
 * Configuration form for the siteimprove module.
 */

namespace Drupal\itk_siteimprove\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configuration form for the siteimprove module.
 */
class ITKSiteimproveConfigForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'itk_siteimprove_admin_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['itk_siteimprove.config'];
  }

  /**
   * Get key/value storage for base config.
   *
   * @return object
   */
  private function getSettings() {
    return \Drupal::getContainer()->get('itk_siteimprove.settings');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('itk_siteimprove.config');
    $settings = $this->getSettings();

    $form['tabs'] = [
      '#type' => 'vertical_tabs',
      '#weight' => 99,
    ];

    $form['general'] = [
      '#type' => 'details',
      '#title' => t('General settings'),
      '#group' => 'tabs',
    ];

    $form['general']['key'] = [
      '#type' => 'textfield',
      '#title' => t('Siteimprove key'),
      '#required' => TRUE,
      '#default_value' => $settings->get('key'),
    ];

    $form['general']['exclude_admin'] = [
      '#type' => 'checkbox',
      '#title' => t('Exclude admin pages'),
      '#required' => FALSE,
      '#default_value' => $config->get('exclude_admin'),
    ];

    $form['general']['excludes'] = [
      '#type' => 'textarea',
      '#title' => t('Path excludes'),
      '#description' => t(
        'Regular expressions of paths to exclude. One per line. ' .
        'For example (to exclude admin pages): /^\/admin(.)*/'),
      '#required' => FALSE,
      '#default_value' => $config->get('excludes'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Save to config.
    $config = $this->config('itk_siteimprove.config');
    $config->set('exclude_admin', $form_state->getValue('exclude_admin'));
    $config->set('excludes', $form_state->getValue('excludes'));

    $formStateExcludes = $form_state->getValue('excludes');

    $excludePatterns = $formStateExcludes ? explode("\r\n",
      $formStateExcludes) : [];

    if ($form_state->getValue('exclude_admin') &&
      !in_array('/^\/admin(.)*/', $excludePatterns)
    ) {
      $excludePatterns[] = '/^\/admin(.)*/';
    }

    $config->set('exclude_patterns', $excludePatterns);
    $config->save();

    // Save key to database.
    $settings = $this->getSettings();
    $settings->set('key', $form_state->getValue('key'));
    drupal_flush_all_caches();

    parent::submitForm($form, $form_state);
  }

}
