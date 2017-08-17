<?php

/**
 * @file
 * Contains \Drupal\itk_activity\Form\Multistep\MultistepFormCategories.
 */

namespace Drupal\itk_activity\Form\Multistep;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Class MultistepFormCategories.
 *
 * @package Drupal\itk_activity\Form\Multistep
 */
class MultistepFormCategories extends MultistepFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'multistep_form_three';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);

    $form['data']['progressBar'] = $this->getProgressBar('categories');

    // Load categories.
    $categories = \Drupal::service('entity_type.manager')
      ->getStorage("taxonomy_term")
      ->loadTree('categories', 0, NULL, FALSE);

    // Setup categories options array.
    $categoriesOptions = [];
    foreach ($categories as $category) {
      $categoriesOptions[$category->tid] = $category->name;
    }

    $form['field_categories'] = [
      '#type' => 'checkboxes',
      '#title' => t('Categories'),
      '#default_value' => $this->store->get('field_categories') ? $this->store->get('field_categories') : array(),
      '#options' => $categoriesOptions,
    ];

    $form['actions']['submit']['#value'] = t('Next');
    $form['actions']['back'] = [
      '#href' => Url::fromRoute('itk_activity.multistep_information')->toString(),
      '#title' => t('Back'),
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
  protected function commitStep(FormStateInterface $form_state) {
    $categories = $form_state->getValue('field_categories');

    // Filter 0 set options away.
    foreach ($categories as $key => $value) {
      if ($value == 0) {
        unset($categories[$key]);
      }
    }

    // Set values in storage.
    $this->store->set('field_categories', $categories);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->commitStep($form_state);
    $this->acceptStep('image');

    // Redirect to next step.
    $form_state->setRedirect('itk_activity.multistep_image');
  }

}
