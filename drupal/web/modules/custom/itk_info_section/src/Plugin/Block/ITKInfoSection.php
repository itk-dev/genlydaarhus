<?php

namespace Drupal\itk_info_section\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a 'ITK Info Section' block.
 *
 * @Block(
 *   id = "itk_info_section_block",
 *   admin_label = @Translation("ITK Info Section"),
 * )
 */
class ITKInfoSection extends BlockBase {
  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = $this->getConfiguration();

    return [
      '#type' => 'markup',
      '#theme' => 'itk_info_section_block',
      '#variables' => $config,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);

    // Retrieve existing configuration for this block.
    $config = $this->getConfiguration();

    // Add a form field to the existing block configuration form.
    $form['title'] = array(
      '#type' => 'textfield',
      '#title' => t('Title'),
      '#default_value' => isset($config['title']) ? $config['title'] : '',
    );

    $form['subtitle'] = array(
      '#type' => 'textfield',
      '#title' => t('Subtitle'),
      '#default_value' => isset($config['subtitle']) ? $config['subtitle'] : '',
    );

    $form['question_1'] = array(
      '#type' => 'textfield',
      "#multiple" => TRUE,
      '#title' => t('Question 1'),
      '#default_value' => isset($config['question_1']) ? $config['question_1'] : '',
    );

    $form['answer_1'] = array(
      '#type' => 'textfield',
      "#multiple" => TRUE,
      '#title' => t('Answer 1'),
      '#default_value' => isset($config['answer_1']) ? $config['answer_1'] : '',
    );

    $form['question_2'] = array(
      '#type' => 'textfield',
      "#multiple" => TRUE,
      '#title' => t('Question 2'),
      '#default_value' => isset($config['question_2']) ? $config['question_2'] : '',
    );

    $form['answer_2'] = array(
      '#type' => 'textfield',
      "#multiple" => TRUE,
      '#title' => t('Answer 2'),
      '#default_value' => isset($config['answer_2']) ? $config['answer_2'] : '',
    );

    $form['question_3'] = array(
      '#type' => 'textfield',
      "#multiple" => TRUE,
      '#title' => t('Question 3'),
      '#default_value' => isset($config['question_3']) ? $config['question_3'] : '',
    );

    $form['answer_3'] = array(
      '#type' => 'textfield',
      "#multiple" => TRUE,
      '#title' => t('Answer 3'),
      '#default_value' => isset($config['answer_3']) ? $config['answer_3'] : '',
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    // Save our custom settings when the form is submitted.
    $this->setConfigurationValue('title', $form_state->getValue('title'));
    $this->setConfigurationValue('subtitle', $form_state->getValue('subtitle'));
    $this->setConfigurationValue('question_1', $form_state->getValue('question_1'));
    $this->setConfigurationValue('answer_1', $form_state->getValue('answer_1'));
    $this->setConfigurationValue('question_2', $form_state->getValue('question_2'));
    $this->setConfigurationValue('answer_2', $form_state->getValue('answer_2'));
    $this->setConfigurationValue('question_3', $form_state->getValue('question_3'));
    $this->setConfigurationValue('answer_3', $form_state->getValue('answer_3'));
  }

  /**
   * {@inheritdoc}
   */
  public function blockValidate($form, FormStateInterface $form_state) {}
}