<?php
/**
 * @file
 * Contains Drupal\itk_info_section\Form\ITKInfoSectionContentForm.
 */

namespace Drupal\itk_info_section\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class ITKHeroForm
 *
 * @package Drupal\itk_hero\Form
 */
class ITKInfoSectionContentForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'itk_hero_settings';
  }

  /**
   * Get key/value storage for base config.
   *
   * @return object
   */
  private function getBaseConfig() {
    return \Drupal::getContainer()->get('itk_info_section.config');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->getBaseConfig();

    // Block 1
    $form['block1'] = [
      '#title' => $this->t('Block 1'),
      '#type' => 'details',
      '#weight' => '1',
      '#open' => TRUE,
    ];

    $form['block1']['block1_title'] = array(
      '#type' => 'textfield',
      '#title' => t('Title'),
      '#default_value' => $config->get('block1_title'),
    );

    $form['block1']['block1_subtitle'] = array(
      '#type' => 'textfield',
      '#title' => t('Subtitle'),
      '#default_value' => $config->get('block1_subtitle'),
    );

    $form['block1']['block1_question_1'] = array(
      '#type' => 'textfield',
      '#title' => t('Question 1'),
      '#default_value' => $config->get('block1_question_1'),
    );

    $form['block1']['block1_answer_1'] = array(
      '#type' => 'textfield',
      '#title' => t('Answer 1'),
      '#default_value' => $config->get('block1_answer_1'),
    );

    $form['block1']['block1_question_2'] = array(
      '#type' => 'textfield',
      '#title' => t('Question 2'),
      '#default_value' => $config->get('block1_question_2'),
    );

    $form['block1']['block1_answer_2'] = array(
      '#type' => 'textfield',
      '#title' => t('Answer 2'),
      '#default_value' => $config->get('block1_answer_2'),
    );

    $form['block1']['block1_question_3'] = array(
      '#type' => 'textfield',
      '#title' => t('Question 3'),
      '#default_value' => $config->get('block1_question_3'),
    );

    $form['block1']['block1_answer_3'] = array(
      '#type' => 'textfield',
      '#title' => t('Answer 3'),
      '#default_value' => $config->get('block1_answer_3'),
    );

    $form['block1']['block1_call_to_action_text'] = array(
      '#type' => 'textfield',
      '#title' => t('Call to action text'),
      '#default_value' => $config->get('block1_call_to_action_text'),
    );

    $form['block1']['block1_call_to_action_button_text'] = array(
      '#type' => 'textfield',
      '#title' => t('Call to action button text'),
      '#default_value' => $config->get('block1_call_to_action_button_text'),
    );

    $form['block1']['block1_call_to_action_button_link'] = array(
      '#type' => 'textfield',
      '#title' => t('Call to action button link'),
      '#default_value' => $config->get('block1_call_to_action_button_link'),
    );

    // Block 2
    $form['block2'] = [
      '#title' => $this->t('Block 2'),
      '#type' => 'details',
      '#weight' => '2',
      '#open' => TRUE,
    ];

    $form['block2']['block2_title'] = array(
      '#type' => 'textfield',
      '#title' => t('Title'),
      '#default_value' => $config->get('block2_title'),
    );

    $form['block2']['block2_subtitle'] = array(
      '#type' => 'textfield',
      '#title' => t('Subtitle'),
      '#default_value' => $config->get('block2_subtitle'),
    );

    $form['block2']['block2_question_1'] = array(
      '#type' => 'textfield',
      '#title' => t('Question 1'),
      '#default_value' => $config->get('block2_question_1'),
    );

    $form['block2']['block2_answer_1'] = array(
      '#type' => 'textfield',
      '#title' => t('Answer 1'),
      '#default_value' => $config->get('block2_answer_1'),
    );

    $form['block2']['block2_question_2'] = array(
      '#type' => 'textfield',
      '#title' => t('Question 2'),
      '#default_value' => $config->get('block2_question_2'),
    );

    $form['block2']['block2_answer_2'] = array(
      '#type' => 'textfield',
      '#title' => t('Answer 2'),
      '#default_value' => $config->get('block2_answer_2'),
    );

    $form['block2']['block2_question_3'] = array(
      '#type' => 'textfield',
      '#title' => t('Question 3'),
      '#default_value' => $config->get('block2_question_3'),
    );

    $form['block2']['block2_answer_3'] = array(
      '#type' => 'textfield',
      '#title' => t('Answer 3'),
      '#default_value' => $config->get('block2_answer_3'),
    );

    $form['block2']['block2_call_to_action_text'] = array(
      '#type' => 'textfield',
      '#title' => t('Call to action text'),
      '#default_value' => $config->get('block2_call_to_action_text'),
    );

    $form['block2']['block2_call_to_action_button_text'] = array(
      '#type' => 'textfield',
      '#title' => t('Call to action button text'),
      '#default_value' => $config->get('block2_call_to_action_button_text'),
    );

    $form['block2']['block2_call_to_action_button_link'] = array(
      '#type' => 'textfield',
      '#title' => t('Call to action button link'),
      '#default_value' => $config->get('block2_call_to_action_button_link'),
    );

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
    // Set the rest of the configuration values.
    $this->getBaseConfig()->setMultiple([
      'block1_title' => $form_state->getValue('block1_title'),
      'block1_subtitle' => $form_state->getValue('block1_subtitle'),
      'block1_question_1' => $form_state->getValue('block1_question_1'),
      'block1_answer_1' => $form_state->getValue('block1_answer_1'),
      'block1_question_2' => $form_state->getValue('block1_question_2'),
      'block1_answer_2' => $form_state->getValue('block1_answer_2'),
      'block1_question_3' => $form_state->getValue('block1_question_3'),
      'block1_answer_3' => $form_state->getValue('block1_answer_3'),
      'block1_call_to_action_text' => $form_state->getValue('block1_call_to_action_text'),
      'block1_call_to_action_button_text' => $form_state->getValue('block1_call_to_action_button_text'),
      'block1_call_to_action_button_link' => $form_state->getValue('block1_call_to_action_button_link'),

      'block2_title' => $form_state->getValue('block2_title'),
      'block2_subtitle' => $form_state->getValue('block2_subtitle'),
      'block2_question_1' => $form_state->getValue('block2_question_1'),
      'block2_answer_1' => $form_state->getValue('block2_answer_1'),
      'block2_question_2' => $form_state->getValue('block2_question_2'),
      'block2_answer_2' => $form_state->getValue('block2_answer_2'),
      'block2_question_3' => $form_state->getValue('block2_question_3'),
      'block2_answer_3' => $form_state->getValue('block2_answer_3'),
      'block2_call_to_action_text' => $form_state->getValue('block2_call_to_action_text'),
      'block2_call_to_action_button_text' => $form_state->getValue('block2_call_to_action_button_text'),
      'block2_call_to_action_button_link' => $form_state->getValue('block2_call_to_action_button_link'),
    ]);

    drupal_set_message('Settings saved');
    drupal_flush_all_caches();
  }
}
