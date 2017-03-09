<?php

/**
 * @file
 * Contains \Drupal\itk_activity\Form\Multistep\MultistepFourForm.
 */

namespace Drupal\itk_activity\Form\Multistep;

use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;

/**
 * Class MultistepFourForm.
 *
 * @package Drupal\itk_activity\Form\Multistep
 */
class MultistepFourForm extends MultistepFormBase {

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

    $form['field_image'] = [
      '#title' => $this->t('Image'),
      '#type' => 'managed_file',
      '#default_value' => $this->store->get('field_image'),
      '#upload_location' => 'public://',
      '#theme' => 'image_widget',
      '#upload_validators' => array(
        'file_validate_extensions' => array('jpg png jpeg'),
      ),
      '#required' => FALSE,
    ];

    // Load image preview, if image is already set.
    $fileId = $this->store->get('field_image')[0];
    if (isset($fileId)) {
      $file = File::load($fileId);

      if ($file) {
        $image = \Drupal::service('image.factory')->get($file->getFileUri());

        if ($image->isValid()) {
          $variables['width'] = $image->getWidth();
          $variables['height'] = $image->getHeight();
        }
        else {
          $variables['width'] = $variables['height'] = NULL;
        }

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

    $st = $this->store->get('field_image');

    // Redirect to next step.
    $form_state->setRedirect('itk_activity.multistep_five');
  }

}
