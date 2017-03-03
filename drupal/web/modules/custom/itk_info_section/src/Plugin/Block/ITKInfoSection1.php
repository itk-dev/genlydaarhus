<?php

namespace Drupal\itk_info_section\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a 'ITK Info Section 1' block.
 *
 * @Block(
 *   id = "itk_info_section_block1",
 *   admin_label = @Translation("ITK Info Section 1"),
 * )
 */
class ITKInfoSection1 extends BlockBase {
  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = \Drupal::getContainer()->get('itk_info_section.config')->getAll();

    return [
      '#theme' => 'itk_info_section_block',
      '#title' => $config['block1_title'],
      '#subtitle' => $config['block1_subtitle'],
      '#question_1' => $config['block1_question_1'],
      '#answer_1' => $config['block1_answer_1'],
      '#question_2' => $config['block1_question_2'],
      '#answer_2' => $config['block1_answer_2'],
      '#question_3' => $config['block1_question_3'],
      '#answer_3' => $config['block1_answer_3'],
      '#call_to_action_text' => $config['block1_call_to_action_text'],
      '#call_to_action_button_text' => $config['block1_call_to_action_button_text'],
      '#call_to_action_button_link' => $config['block1_call_to_action_button_link'],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);

    return $form;
  }
}