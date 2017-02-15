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
    $settings = itk_cookie_message_get_settings();

    $form['tabs'] = array(
      '#type' => 'vertical_tabs',
      '#weight' => 99,
    );

    $form['general'] = array(
      '#type' => 'details',
      '#title' => $this->t('General settings'),
      '#group' => 'tabs',
    );

    $key = 'cookie_name';
    $form['general'][$this->getKey($key)] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Cookie name'),
      '#required' => TRUE,
      '#default_value' => $settings[$key],
    );

    $key = 'cookie_lifetime';
    $form['general'][$this->getKey($key)] = array(
      '#type' => 'select',
      '#title' => $this->t('Cookie lifetime'),
      '#options' => array(
        1 * 30 * 24 * 60 * 60 => $this->t('One month'),
        1 * 365 * 24 * 60 * 60 => $this->t('One year'),
      ),
      '#default_value' => $settings[$key],
    );

    $languages = \Drupal::languageManager()->getLanguages();

    foreach ($languages as $language) {
      $language_id = $language->getId();
      $settings = itk_cookie_message_get_settings($language);

      $form[$language_id] = array(
        '#type' => 'details',
        '#title' => $this->t($language->getName()),
        '#group' => 'tabs',
      );

      $key = 'text';
      $form[$language_id][$this->getKey($key, $language)] = array(
        '#type' => 'textfield',
        '#title' => $this->t('Message'),
        '#description' => $this->t('Message to display in the dialog show to the users.'),
        '#required' => TRUE,
        '#default_value' => $settings[$key],
      );

      $key = 'read_more_url';
      $form[$language_id][$this->getKey($key, $language)] = array(
        '#type' => 'textfield',
        '#title' => $this->t('Read more url'),
        '#default_value' => $settings[$key],
      );

      $key = 'read_more_text';
      $form[$language_id][$this->getKey($key, $language)] = array(
        '#type' => 'textfield',
        '#title' => $this->t('Read more text'),
        '#default_value' => $settings[$key],
      );

      $key = 'accept_button_text';
      $form[$language_id][$this->getKey($key, $language)] = array(
        '#type' => 'textfield',
        '#title' => $this->t('Accept button text'),
        '#required' => TRUE,
        '#default_value' => $settings[$key],
      );
    }

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('itk_cookie_message.settings')
      ->set('cookie_name', $form_state->getValue($this->getKey('cookie_name')))
      ->set('cookie_lifetime', $form_state->getValue($this->getKey('cookie_lifetime')));

    $languages = \Drupal::languageManager()->getLanguages();
    foreach ($languages as $language) {
      $language_id = $language->getId();

      $config->set($language_id, array(
        'text' => $form_state->getValue($this->getKey('text', $language)),
        'read_more_url' => $form_state->getValue($this->getKey('read_more_url', $language)),
        'read_more_text' => $form_state->getValue($this->getKey('read_more_text', $language)),
        'accept_button_text' => $form_state->getValue($this->getKey('accept_button_text', $language)),
      ));
    }

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
