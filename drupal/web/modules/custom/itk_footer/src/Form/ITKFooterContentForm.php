<?php
/**
 * @file
 * Contains Drupal\itk_footer\Form\ITKFooterContentForm.
 */

namespace Drupal\itk_footer\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class ITKFooterContentForm
 *
 * @package Drupal\itk_footer\Form
 */
class ITKFooterContentForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'itk_footer_settings';
  }

  /**
   * Get key/value storage for base config.
   *
   * @return object
   */
  private function getBaseConfig() {
    return \Drupal::getContainer()->get('itk_footer.config');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->getBaseConfig();

    $form['footer_text'] = array(
      '#title' => $this->t('Text'),
      '#type' => 'text_format',
      '#format' => 'filtered_html',
      '#default_value' => $config->get('footer_text'),
      '#weight' => '1',
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
      'footer_text' => $form_state->getValue('footer_text')['value'],
    ));

    drupal_flush_all_caches();
  }
}

