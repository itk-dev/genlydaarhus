<?php

namespace Drupal\itk_hero\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\file\Entity\File;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides hero
 *
 * @Block(
 *   id = "itk_hero",
 *   admin_label = @Translation("ITK hero"),
 * )
 */
class ITKHero extends BlockBase {
  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = \Drupal::getContainer()->get('itk_hero.config')->getAll();

    // Fetch header top file.
    $file = isset($config['frontpage_image']) ? File::load($config['frontpage_image']) : FALSE;
    $config['frontpage_image_url'] = $file ? $file->url() : '';

    return array(
      '#type' => 'markup',
      '#theme' => 'itk_hero_block',
      '#cache' => array(
        'max-age' => 0,
      ),
      '#variables' => $config,
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
