<?php

/**
 * @file
 * Contains \Drupal\itk_activity\Form\Multistep\MultistepFormConfirm.
 */

namespace Drupal\itk_activity\Form\Multistep;

use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;
use Drupal\taxonomy\Entity\Term;

/**
 * Class MultistepFormConfirm.
 *
 * @package Drupal\itk_activity\Form\Multistep
 */
class MultistepFormConfirm extends MultistepFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'multistep_form_six';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);

    $categorySelections = $this->store->get('field_categories');

    // Setup help_needed options array.
    $categories = '';
    foreach ($categorySelections as $key => $value) {
      if ($value) {
        if ($categories != '') {
          $categories .= ', ';
        }
        $categories .= Term::load($key)->name->value;
      }
    }

    $form['data'] = [
      'title' => [
        'label' => $this->t('Title'),
        'value' => $this->store->get('title'),
      ],
      'body' => [
        'label' => $this->t('Description'),
        'value' => $this->store->get('body'),
      ],
      'address' => [
        'label' => $this->t('Address'),
        'value' => $this->store->get('field_address'),
      ],
      'area' => [
        'label' => $this->t('Area'),
        'value' => $this->store->get('field_area'),
      ],
      'categories' => [
        'label' => $this->t('Categories'),
        'value' => $categories,
      ],
      'date' => [
        'label' => $this->t('Date'),
        'value' => $this->store->get('field_date'),
      ],
      'entryRequirements' => [
        'label' => $this->t('What level is required to participate?'),
        'value' => $this->store->get('field_entry_requirements'),
      ],
      'helpNeeded' => [
        'label' => $this->t('Do you need help?'),
        'value' => $this->store->get('field_help_needed'),
      ],
      'image' => [
        'src' => isset($this->store->get('field_image')[0]) ? File::load($this->store->get('field_image')[0])->url() : '',
      ],
      'maximumParticipants' => [
        'label' => $this->t('How many can participate?'),
        'value' => $this->store->get('field_maximum_participants'),
      ],
      'physicalRequirements' => [
        'label' => $this->t('What are the physical requirements?'),
        'value' => $this->store->get('field_physical_requirements'),
      ],
      'price' => [
        'label' => $this->t('Price'),
        'value' => $this->store->get('field_price'),
      ],
      'signupRequired' => [
        'label' => $this->t('Sign up required'),
        'value' => $this->store->get('field_signup_required'),
      ],

      'timeEnd' => [
        'label' => $this->t('End time'),
        'value' => $this->store->get('field_time_end'),
      ],
      'timeStart' => [
        'label' => $this->t('Start time'),
        'value' => $this->store->get('field_time_start'),
      ],
      'zipcode' => [
        'label' => $this->t('Zipcode'),
        'value' => $this->store->get('field_zipcode'),
      ],
    ];

    $form['actions']['submit']['#value'] = $this->t('Create activity');

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
    $activityId = $this->saveData();

    // Redirect to the created activity.
    $form_state->setRedirect('entity.node.canonical', ['node' => $activityId]);
  }

}
