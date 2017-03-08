<?php

/**
 * @file
 * Contains \Drupal\itk_activity\Form\Multistep\MultistepOneForm.
 */

namespace Drupal\itk_activity\Form\Multistep;

use Drupal\Core\Form\FormStateInterface;

/**
 * Class MultistepTwoForm.
 *
 * @package Drupal\itk_activity\Form\Multistep
 */
class MultistepTwoForm extends MultistepFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'multistep_form_two';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);

    // Load entry_requirements.
    $entryRequirements = \Drupal::service('entity_type.manager')
      ->getStorage("taxonomy_term")
      ->loadTree('entry_requirements', 0, NULL, FALSE);

    // Setup entry_requirements options array.
    $entryRequirementOptions = [];
    foreach ($entryRequirements as $requirement) {
      $entryRequirementOptions[$requirement->tid] = $requirement->name;
    }

    $form['entry_requirements'] = [
      '#type' => 'radios',
      '#required' => TRUE,
      '#title' => $this->t('Entry requirements'),
      '#default_value' => NULL,
      '#options' => $entryRequirementOptions,
    ];

    // Load physical_requirements.
    $physicalRequirements = \Drupal::service('entity_type.manager')
      ->getStorage("taxonomy_term")
      ->loadTree('physical_requirements', 0, NULL, FALSE);

    // Setup physical_requirements options array.
    $physicalRequirementOptions = [];
    foreach ($physicalRequirements as $requirement) {
      $physicalRequirementOptions[$requirement->tid] = $requirement->name;
    }

    $form['physical_requirements'] = [
      '#type' => 'radios',
      '#required' => TRUE,
      '#title' => $this->t('Physical requirements'),
      '#default_value' => NULL,
      '#options' => $physicalRequirementOptions,
    ];

    // Load help_needed.
    $helpNeeded = \Drupal::service('entity_type.manager')
      ->getStorage("taxonomy_term")
      ->loadTree('help_needed', 0, NULL, FALSE);

    // Setup help_needed options array.
    $helpNeededOptions = [];
    foreach ($helpNeeded as $requirement) {
      $helpNeededOptions[$requirement->tid] = $requirement->name;
    }

    $form['help_needed'] = [
      '#type' => 'radios',
      '#required' => TRUE,
      '#title' => $this->t('Help needed'),
      '#default_value' => NULL,
      '#options' => $helpNeededOptions,
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
    $this->store->set('entry_requirements', $form_state->getValue('entry_requirements'));
    $this->store->set('physical_requirements', $form_state->getValue('physical_requirements'));
    $this->store->set('help_needed', $form_state->getValue('help_needed'));

    // Redirect to next step.
    $form_state->setRedirect('itk_activity.multistep_three');
  }

}
