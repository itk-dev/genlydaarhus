<?php
/**
 * @file
 * Contains Drupal\itkore_frontpage_header\Form\ItkoreFrontpageHeaderSettingsForm.
 */

namespace Drupal\itk_floating_help\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\system\Entity\Menu;
use Drupal\Core\Session;

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
    return \Drupal::getContainer()->get('itkore_admin.itkore_config');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->getBaseConfig();

    $form['footer_title'] = array(
      '#title' => $this->t('Title'),
      '#type' => 'textfield',
      '#default_value' => $config->get('footer_title'),
      '#weight' => '1',
    );

    $form['footer_text'] = array(
      '#title' => $this->t('Text'),
      '#type' => 'text_format',
      '#format' => 'filtered_html',
      '#default_value' => $config->get('footer_text'),
      '#weight' => '2',
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
      'footer_title' => $form_state->getValue('footer_title'),
      'footer_text' => $form_state->getValue('footer_text')['value'],
    ));

    drupal_flush_all_caches();
  }
}

