<?php

/**
 * @file
 * Contains \Drupal\itk_activity\Form\Multistep\MultistepFormImage.
 */

namespace Drupal\itk_activity\Form\Multistep;

use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;
use Drupal\Core\Url;

/**
 * Class MultistepFormImage.
 *
 * @package Drupal\itk_activity\Form\Multistep
 */
class MultistepFormImage extends MultistepFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'multistep_form_four';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);

    // Set progress bar data.
    $progressBar = $this->getProgressBar();
    $progressBar['items'][3]['active'] = TRUE;
    $form['data']['progressBar'] = $progressBar;

    $form['field_image'] = [
      '#title' => $this->t('Image'),
      '#type' => 'managed_file',
      '#default_value' => $this->store->get('field_image'),
      '#upload_location' => 'public://',
      '#theme' => 'image_widget',
      '#upload_validators' => [
        'file_validate_extensions' => [ 'jpg png jpeg' ],
        'file_validate_size' => [ 10 * 1024 * 1024, ],
      ],
      '#required' => FALSE,
    ];

    // Load image preview, if image is already set.
    $fileId = $this->store->get('field_image')[0];
    if (isset($fileId)) {
      // Get the file.
      $file = File::load($fileId);

      if ($file) {
        // Get the image.
        $image = \Drupal::service('image.factory')->get($file->getFileUri());

        // Set dimensions.
        if ($image->isValid()) {
          $variables['width'] = $image->getWidth();
          $variables['height'] = $image->getHeight();
        }
        else {
          $variables['width'] = $variables['height'] = NULL;
        }

        // Set preview entry.
        $form['field_image']['preview'] = array(
          '#weight' => -10,
          '#theme' => 'image_style',
          '#width' => $variables['width'],
          '#height' => $variables['height'],
          '#style_name' => 'large',
          '#uri' => $file->getFileUri(),
        );
      }
    }

    $form['actions']['submit']['#value'] = $this->t('Next');
    $form['actions']['back'] = [
      'href' => Url::fromRoute('itk_activity.multistep_categories')->toString(),
      'title' => \Drupal::translation()->translate('Back'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $fileId = $form_state->getValue('field_image');

    // Set values in storage.
    $this->store->set('field_image', $fileId);

    $this->acceptStep(4);

    // Redirect to next step.
    $form_state->setRedirect('itk_activity.multistep_details');
  }

}
