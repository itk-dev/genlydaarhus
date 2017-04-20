<?php
/**
 * @file
 * Contains Drupal\itk_cookie_message\Form\ITKCookieMessageContentForm.
 */

namespace Drupal\itk_cookie_message\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Language\LanguageInterface;

/**
 * Class ITKCookieMessageContentForm
 *
 * @package Drupal\itk_cookie_message\Form
 */
class ITKCookieMessageContentForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'itk_cookie_message_content_form';
  }

  /**
   * Get key/value storage for base config.
   *
   * @return object
   */
  private function getBaseConfig() {
    return \Drupal::getContainer()->get('itk_cookie_message.content');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->getBaseConfig();

    $form['tabs'] = array(
      '#type' => 'vertical_tabs',
      '#weight' => 99,
    );

    $languages = \Drupal::languageManager()->getLanguages();

    foreach ($languages as $language) {
      $language_id = $language->getId();

      $languageSpecificSettings = $config->get($language->getId());

      $form[$language_id] = array(
        '#type' => 'details',
        '#title' => t($language->getName()),
        '#group' => 'tabs',
      );

      $form[$language_id][$this->getKey('text', $language)] = array(
        '#type' => 'textfield',
        '#title' => t('Message'),
        '#description' => t('Message to display in the dialog show to the users.'),
        '#required' => TRUE,
        '#default_value' => $languageSpecificSettings['text'],
      );

      $form[$language_id][$this->getKey('read_more_url', $language)] = array(
        '#type' => 'textfield',
        '#title' => t('Read more url'),
        '#default_value' => $languageSpecificSettings['read_more_url'],
      );

      $form[$language_id][$this->getKey('read_more_text', $language)] = array(
        '#type' => 'textfield',
        '#title' => t('Read more text'),
        '#default_value' => $languageSpecificSettings['read_more_text'],
      );

      $form[$language_id][$this->getKey('accept_button_text', $language)] = array(
        '#type' => 'textfield',
        '#title' => t('Accept button text'),
        '#required' => TRUE,
        '#default_value' => $languageSpecificSettings['accept_button_text'],
      );
    }

    $form['actions'] = ['#type' => 'actions'];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#attributes' => ['class' => ['button--primary']],
      '#value' => t('Save content'),
      '#weight' => '6',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    drupal_set_message('Settings saved');

    // Set language specific values.
    $languages = \Drupal::languageManager()->getLanguages();
    foreach ($languages as $language) {
      $language_id = $language->getId();

      $this->getBaseConfig()->set($language_id, [
        'text' => $form_state->getValue($this->getKey('text', $language)),
        'read_more_url' => $form_state->getValue($this->getKey('read_more_url', $language)),
        'read_more_text' => $form_state->getValue($this->getKey('read_more_text', $language)),
        'accept_button_text' => $form_state->getValue($this->getKey('accept_button_text', $language)),
      ]);
    }

    drupal_flush_all_caches();
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
