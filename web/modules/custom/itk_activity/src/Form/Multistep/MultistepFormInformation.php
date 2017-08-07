<?php

/**
 * @file
 * Contains \Drupal\itk_activity\Form\Multistep\MultistepFormInformation.
 */

namespace Drupal\itk_activity\Form\Multistep;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Class MultistepFormInformation.
 *
 * @package Drupal\itk_activity\Form\Multistep
 */
class MultistepFormInformation extends MultistepFormBase {

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

    $form['data']['progressBar'] = $this->getProgressBar('information');

    // Load entry_requirements.
    $entryRequirements = \Drupal::service('entity_type.manager')
      ->getStorage("taxonomy_term")
      ->loadTree('entry_requirements', 0, NULL, FALSE);

    // Setup entry_requirements options array.
    $entryRequirementOptions = [];
    foreach ($entryRequirements as $requirement) {
      $entryRequirementOptions[$requirement->tid] = $requirement->name;
    }

    $form['field_entry_requirements'] = [
      '#type' => 'radios',
      '#required' => TRUE,
      '#title' => t('Entry requirements'),
      '#default_value' => $this->store->get('field_entry_requirements') ? $this->store->get('field_entry_requirements') : NULL,
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

    $form['field_physical_requirements'] = [
      '#type' => 'radios',
      '#required' => TRUE,
      '#title' => t('Physical requirements'),
      '#default_value' => $this->store->get('field_physical_requirements') ? $this->store->get('field_physical_requirements') : NULL,
      '#options' => $physicalRequirementOptions,
    ];


    $form['field_maximum_participants'] = [
      '#type' => 'number',
      '#title' => t('Maximum participants'),
      '#default_value' => $this->store->get('field_maximum_participants') ? $this->store->get('field_maximum_participants') : NULL,
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

    $form['field_help_needed'] = [
      '#type' => 'radios',
      '#required' => TRUE,
      '#title' => t('Help needed'),
      '#default_value' => $this->store->get('field_help_needed') ? $this->store->get('field_help_needed') : NULL,
      '#options' => $helpNeededOptions,
    ];
    
    $form['actions']['submit']['#value'] = t('Next');
    $form['actions']['back'] = [
      '#href' => Url::fromRoute('itk_activity.multistep_about')->toString(),
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
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Set values in storage.
    $this->store->set('field_entry_requirements', $form_state->getValue('field_entry_requirements'));
    $this->store->set('field_physical_requirements', $form_state->getValue('field_physical_requirements'));
    $this->store->set('field_maximum_participants', $form_state->getValue('field_maximum_participants'));
    $this->store->set('field_help_needed', $form_state->getValue('field_help_needed'));

    $this->acceptStep('categories');

    // Redirect to next step.
    $form_state->setRedirect('itk_activity.multistep_categories');
  }

}
