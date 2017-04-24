<?php

namespace Drupal\itk_floating_help\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides floating help
 *
 * @Block(
 *   id = "itk_floating_help",
 *   admin_label = @Translation("ITK Floating help"),
 * )
 */
class ITKFloatingHelp extends BlockBase {
  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = \Drupal::getContainer()->get('itk_floating_help.config')->getAll();
    $form = \Drupal::formBuilder()->getForm('Drupal\itk_floating_help\Form\ITKFloatingHelpContentForm');

    $floating_help_button_label_closed = $config['floating_help_button_label_closed'];
    $floating_help_button_label_open = $config['floating_help_button_label_open'];
    $floating_help_text = $config['floating_help_text'];
    $floating_help_contact = $config['floating_help_contact'];
    $floating_help_phone = $config['floating_help_phone'];
    $floating_help_email = $config['floating_help_email'];

    $floating_help_contact_label = $form['contact_information_wrapper']['floating_help_contact']['#title'];
    $floating_help_phone_label = $form['contact_information_wrapper']['floating_help_phone']['#title'];
    $floating_help_email_label = $form['contact_information_wrapper']['floating_help_email']['#title'];

    return [
      '#theme' => 'itk_floating_help_block',
      '#floating_help_contact_label' => $floating_help_contact_label,
      '#floating_help_phone_label' => $floating_help_phone_label,
      '#floating_help_email_label' => $floating_help_email_label,
      '#floating_help_button_label_closed' => $floating_help_button_label_closed,
      '#floating_help_button_label_open' => $floating_help_button_label_open,
      '#floating_help_text' => $floating_help_text,
      '#floating_help_contact' => $floating_help_contact,
      '#floating_help_phone' => $floating_help_phone,
      '#floating_help_email' => $floating_help_email,
      '#floating_help_email_title' => t('Send email to @title', [ '@title' => $floating_help_email ]),
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
