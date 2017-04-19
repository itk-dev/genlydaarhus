<?php
/**
 * @file
 * Configuration form for the Cookie message module.
 */

namespace Drupal\itk_cookie_message\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Language\LanguageInterface;

/**
 * Configuration form for the Cookie message module.
 */
class ItkCookieMessageConfigForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'itk_cookie_message_admin_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return array('itk_cookie_message.settings');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $settings = $this->config('itk_cookie_message.settings');

    $form['tabs'] = array(
      '#type' => 'vertical_tabs',
      '#weight' => 99,
    );

    $form['general'] = array(
      '#type' => 'details',
      '#title' => t('General settings'),
      '#group' => 'tabs',
    );

    $form['general'][$this->getKey('cookie_name')] = array(
      '#type' => 'textfield',
      '#title' => t('Cookie name'),
      '#required' => TRUE,
      '#default_value' => $settings->get('cookie_name'),
    );

    $form['general'][$this->getKey('cookie_lifetime')] = array(
      '#type' => 'select',
      '#title' => t('Cookie lifetime'),
      '#options' => array(
        30 * 24 * 60 * 60 => t('One month'),
        365 * 24 * 60 * 60 => t('One year'),
      ),
      '#default_value' => $settings->get('cookie_lifetime'),
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Get configuration.
    $config = $this->config('itk_cookie_message.settings');

    // Set cookie values.
    $config->set('cookie_name', $form_state->getValue($this->getKey('cookie_name')))
      ->set('cookie_lifetime', $form_state->getValue($this->getKey('cookie_lifetime')));

    $config->save();

    parent::submitForm($form, $form_state);
  }

  /**
   * Get a form key.
   *
   * @param string $key
   *   The key.
   * @param LanguageInterface $language
   *   The optional language.
   *
   * @return string
   *   The form key.
   */
  private function getKey($key, LanguageInterface $language = NULL) {
    return 'itk_cookie_message' . ($language ? '_' . $language->getId() : '') . '_' . $key;
  }

}
