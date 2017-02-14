<?php

namespace Drupal\itk_footer\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Menu\MenuTreeParameters;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides footer content
 *
 * @Block(
 *   id = "itk_footer",
 *   admin_label = @Translation("ITK footer"),
 * )
 */
class ITKFooter extends BlockBase {
  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = \Drupal::getContainer()->get('itk_footer.config')->getAll();

    $footer_title = $config['footer_title'];
    $footer_text = check_markup($config['footer_text'], 'filtered_html');

    return array(
      '#type' => 'markup',
      '#theme' => 'itk_footer_block',
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