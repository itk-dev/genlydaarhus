<?php

/**
 * @file
 * Contains \Drupal\itk_activity\Form\Multistep\MultistepFormConfirm.
 */

namespace Drupal\itk_activity\Form\Multistep;

use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;
use Drupal\taxonomy\Entity\Term;
use Drupal\Core\Url;

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

    // Get selected categories string.
    $categories = '';
    foreach ($categorySelections as $key => $value) {
      if ($value) {
        if ($categories != '') {
          $categories .= ', ';
        }
        $categories .= Term::load($key)->name->value;
      }
    }

    $signupRequired = $this->store->get('field_signup_required') ? t('Sign up required') : t('Sign up not required');

    // Get term values.
    $entryRequirements = Term::load($this->store->get('field_entry_requirements'))->name->value;
    $helpNeeded = Term::load($this->store->get('field_help_needed'))->name->value;
    $physicalRequirements = Term::load($this->store->get('field_physical_requirements'))->name->value;

    $form['data'] = [
      'title' => [
        '#label' => t('Title'),
        '#value' => $this->store->get('title'),
      ],
      'body' => [
        '#label' => t('Description'),
        '#value' => $this->store->get('body'),
      ],
      'address' => [
        '#label' => t('Address'),
        '#value' => $this->store->get('field_address'),
      ],
      'area' => [
        '#label' => t('Area'),
        '#value' => $this->store->get('field_area'),
      ],
      'categories' => [
        '#label' => t('Categories'),
        '#value' => $categories,
      ],
      'date' => [
        '#label' => t('Date'),
        '#value' => \Drupal::service('date.formatter')->format((new \DateTime($this->store->get('field_date')))->getTimestamp(), 'date_long'),
      ],
      'entryRequirements' => [
        '#label' => t('What level is required to participate?'),
        '#value' => $entryRequirements,
      ],
      'helpNeeded' => [
        '#label' => t('Do you need help?'),
        '#value' => $helpNeeded,
      ],
      'image' => [
        '#src' => isset($this->store->get('field_image')[0]) ? File::load($this->store->get('field_image')[0])->url() : '',
      ],
      'maximumParticipants' => [
        '#label' => t('How many can participate?'),
        '#value' => $this->store->get('field_maximum_participants'),
      ],
      'physicalRequirements' => [
        '#label' => t('What are the physical requirements?'),
        '#value' => $physicalRequirements,
      ],
      'price' => [
        '#label' => t('Price'),
        '#value' => $this->store->get('field_price'),
      ],
      'signupRequired' => [
        '#label' => t('Is sign up required?'),
        '#value' => $signupRequired,
      ],
      'timeEnd' => [
        '#label' => t('End time'),
        '#value' => $this->store->get('field_time_end'),
      ],
      'timeStart' => [
        '#label' => t('Start time'),
        '#value' => $this->store->get('field_time_start'),
      ],
      'zipcode' => [
        '#label' => t('Zipcode'),
        '#value' => $this->store->get('field_zipcode'),
      ],
    ];

    $form['data']['progressBar'] = $this->getProgressBar('confirm');

    $form['actions']['submit']['#value'] = t('Create activity');
    $form['actions']['back'] = [
      '#href' => Url::fromRoute('itk_activity.multistep_details')->toString(),
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
    $activityId = $this->saveData();

    // Redirect to the created activity.
    $form_state->setRedirect('entity.node.canonical', ['node' => $activityId]);
  }

}
