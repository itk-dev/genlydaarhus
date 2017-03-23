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
    $config = \Drupal::config('itk_cookie_message.settings');

    $cookieName = $config->get('cookie_name');
    $cookieLifetime = $config->get('cookie_lifetime');

    return [
      '#type' => 'markup',
      '#theme' => 'itk_cookie_message_block',
      '#variables' => [
        'text' => isset($settings['text']) ? trim($settings['text']) : \Drupal::translation()->translate('We use cookies to improve your user experience'),
        'read_more_url' => isset($settings['read_more_url']) ? trim($settings['read_more_url']) : '/information-about-cookies',
        'read_more_text' => isset($settings['read_more_text']) ? trim($settings['read_more_text']) : \Drupal::translation()->translate('Read more about cookies'),
        'accept_button_text' => isset($settings['accept_button_text']) ? trim($settings['accept_button_text']) : \Drupal::translation()->translate('Accept cookies'),
      ],
      '#attached' => array(
        'library' => array(
          'itk_cookie_message/itk-cookie-message',
        ),
        'drupalSettings' => array(
          'itk_cookie_message' => array(
            'cookie_name' => isset($cookieName) ? $cookieName : 'accept_cookies',
            'cookie_lifetime' => isset($cookieLifetime) ? $cookieLifetime : 365 * 24 * 60 * 60,
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
