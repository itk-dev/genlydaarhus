<?php
/**
 * @file
 * Contains ITKCookieMessage block.
 */

namespace Drupal\itk_cookie_message\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides cookie message block.
 *
 * @Block(
 *   id = "itk_cookie_message",
 *   admin_label = @Translation("ITK cookie message"),
 * )
 */
class ITKCookieMessage extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $settings = _itk_cookie_message_get_settings();

    return [
      '#type' => 'markup',
      '#theme' => 'itk_cookie_message_block',
      '#variables' => [
        'text' => $settings['text'],
        'read_more_url' => $settings['read_more_url'],
        'read_more_text' => $settings['read_more_text'],
        'accept_button_text' => $settings['accept_button_text'],
      ],
      '#attached' => array(
        'library' => array(
          'itk_cookie_message/itk-cookie-message',
        ),
        'drupalSettings' => array(
          'itk_cookie_message' => array(
            'cookie_name' => $settings['cookie_name'],
            'cookie_lifetime' => $settings['cookie_lifetime'],
          ),
        ),
      ),
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
