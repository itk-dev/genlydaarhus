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
    $config = \Drupal::getContainer()->get('itkore_admin.itkore_config')->getAll();

    $footer_title = $config['footer_title'];
    $footer_text = check_markup($config['footer_text'], 'filtered_html');

    return array (
      '#type' => 'markup',
      '#theme' => 'itkore_footer_block',
      '#cache' => array(
        'max-age' => 0,
      ),
      '#footer_title' => $footer_title,
      '#footer_text' => $footer_text,
    );
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);

    return $form;
  }
}
?>