<?php

/**
 * @file
 * Contains \Drupal\itk_activity\Form\Multistep\MultistepThreeForm.
 */

namespace Drupal\itk_activity\Form\Multistep;

use Drupal\Core\Form\FormStateInterface;

/**
 * Class MultistepThreeForm.
 *
 * @package Drupal\itk_activity\Form\Multistep
 */
class MultistepThreeForm extends MultistepFormBase {

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
      '#title' => $this->t('Categories'),
      '#default_value' => $this->store->get('field_categories') ? $this->store->get('field_categories') : NULL,
      '#options' => $categoriesOptions,
    ];

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
    // Set values in storage.
    $this->store->set('field_categories', $form_state->getValue('field_categories'));

    // Redirect to next step.
    $form_state->setRedirect('itk_activity.multistep_four');
  }

}
