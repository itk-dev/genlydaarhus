<?php

/**
 * @file
 * Contains \Drupal\itk_activity\Form\Multistep\MultistepFormDetails.
 */

namespace Drupal\itk_activity\Form\Multistep;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Class MultistepFormDetails.
 *
 * @package Drupal\itk_activity\Form\Multistep
 */
class MultistepFormDetails extends MultistepFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'multistep_form_five';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);

    $form['data']['progressBar'] = $this->getProgressBar('details');

    $form['#tree'] = TRUE;
    $form['occurrences'] = [
      '#prefix' => '<div id="occurences-placeholder">', // Id to be replaced by submit output.
      '#suffix' => '</div>',
      '#title' => $this->t('Occurences'),
      'actions' => [
        '#type' => 'actions',
      ],
    ];

    // Get previous occurrences.
    $occurrences = $this->store->get('occurrences') ? $this->store->get('occurrences') : [];

    // If not occurrences set, add one.
    if (empty($occurrences)) {
      $occurrences[] = [];
      $this->store->set('occurrences', $occurrences);
    }

    // Each row of occurences
    foreach ($occurrences as $i => $occurrence) {
      $form['occurrences'][$i] = [
        '#prefix' => '<div class="grid is-fourths"><div class="grid--inner">',
        '#suffix' => '</div></div>',
        '#title' => $this->t('Occurrence @nr', ['@nr' => $i]),
        '#type' => 'html_tag',
        '#tag' => 'div',
      ];

      $form['occurrences'][$i]['field_date'] = array(
        '#type' => 'date',
        '#title' => t('Date'),
        '#required' => TRUE,
        '#default_value' => isset($occurrence['field_date']) ? $occurrence['field_date'] : '',
      );

      $form['occurrences'][$i]['field_time_start'] = array(
        '#type' => 'textfield',
        '#max_length' => 5,
        '#required' => TRUE,
        '#attributes' => [
          'title' => t('Must have format HH:mm'),
          'placeholder' => t('Must have format: HH:mm, for example: 12:00'),
          'pattern' => '[0-9]{2}:[0-9]{2}',
          'maxlength' => 5,
          'class' => [ 'js-timepicker-field' ],
        ],
        '#title' => t('Time start'),
        '#default_value' => isset($occurrence['field_time_start']) ? $occurrence['field_time_start'] : '',
      );

      $form['occurrences'][$i]['field_time_end'] = array(
        '#type' => 'textfield',
        '#max_length' => 5,
        '#required' => TRUE,
        '#attributes' => [
          'title' => t('Must have format HH:mm'),
          'placeholder' => t('Must have format: HH:mm, for example: 12:00'),
          'pattern' => '[0-9]{2}:[0-9]{2}',
          'maxlength' => 5,
          'class' => [ 'js-timepicker-field' ],
        ],
        '#title' => t('Time end'),
        '#default_value' => isset($occurrence['field_time_end']) ? $occurrence['field_time_end'] : '',
      );

      if ($i > 0) {
        $form['occurrences'][$i]['actions']['remove_occurrence'] = [
          '#type' => 'submit',
          '#attributes' => [
            'class' => ['button-delete'],
            'data-grid-type' => ['grid-inner'],
            'style' => ['margin-top: 44px']
          ],
          '#element_index' => $i,
          '#name' => 'button-delete-' . $i,   // Name used to identify which button is clicked
          '#value' => t('Remove'),
          '#submit' => ['::removeLine'],
          '#limit_validation_errors' => array(),  // Ignore all validation errors for submit to run
          '#ajax' => [
            'callback' => '::removeCallback',
            'wrapper' => "occurences-placeholder", // The HTML id to replace with ajax
          ],
        ];
      }
    }
    $form['occurrences_actions'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Occurrence actions'),
     ];

    $form['occurrences_actions']['actions'] = [
      '#type' => 'actions',
    ];

    $form['occurrences_actions']['actions']['add_occurrence'] = [
      '#type' => 'submit',
      '#name' => 'button-add',
      '#attributes' => [
        'class' => ['button-secondary-dark']
      ],
      '#value' => t('Add date and time'),
      '#submit' => array('::addOne'),
      '#limit_validation_errors' => array(),  // Ignore all validation errors for submit to run
      '#ajax' => [
        'callback' => '::addmoreCallback',
        'wrapper' => "occurences-placeholder", // The HTML id to replace with ajax
      ],
    ];

    // Attach timepickers js.
    $form['#attached']['library'][] = 'itk_activity/timepickers';

    $form['field_price'] = array(
      '#type' => 'number',
      '#required' => FALSE,
      '#attributes' => [
        'min' => 0,
      ],
      '#title' => t('Price (if any)'),
      '#default_value' => $this->store->get('field_price') ? $this->store->get('field_price') : NULL,
    );

    $form['field_zipcode'] = array(
      '#type' => 'number',
      '#max_length' => 4,
      '#attributes' => [
        'min' => 0,
        'max' => 9999,
        'class' => [ 'js-field-zipcode' ],
      ],
      '#required' => TRUE,
      '#title' => t('Zipcode'),
      '#default_value' => $this->store->get('field_zipcode') ? $this->store->get('field_zipcode') : NULL,
    );

    // Attach zipcode js, that sets area from zipcode.
    $form['#attached']['library'][] = 'itk_activity/zipcode';

    $form['field_area'] = array(
      '#type' => 'textfield',
      '#attributes' => [
        'class' => [ 'js-field-area' ],
      ],
      '#title' => t('Area'),
      '#default_value' => $this->store->get('field_area') ? $this->store->get('field_area') : NULL,
    );

    $form['field_address'] = array(
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => t('Address'),
      '#default_value' => $this->store->get('field_address') ? $this->store->get('field_address') : NULL,
    );

    $form['actions']['submit']['#value'] = t('Next');
    $form['actions']['back'] = [
      '#href' => Url::fromRoute('itk_activity.multistep_image')->toString(),
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
   * Submit function for add new occurence.
   *
   * All data modification should be part of a submit function.
   * Submit function is executed before each ajax callback.
   *
   */
  public function addOne(array &$form, FormStateInterface $form_state) {
    $occurrences = $this->store->get('occurrences');
    $occurrences[] = [
      'field_date' => '',
      'field_time_start' => '',
      'field_time_end' => '',
    ];
    $this->store->set('occurrences', $occurrences);
    $form_state->setRebuild(TRUE);

    return $form['occurrences'];
  }

  /**
   * Submit function for remove occurence.
   *
   * All data modification should be part of a submit function.
   * Submit function is executed before each ajax callback.
   */
  public function removeLine(array &$form, FormStateInterface $form_state) {
    $element = $form_state->getTriggeringElement();
    $index = $element['#element_index'];

    $occurrences = $this->store->get('occurrences');

    if(array_key_exists($index, $occurrences)) {
      unset($occurrences[$index]);
    }

    $this->store->set('occurrences', $occurrences);

    $form_state->setRebuild(TRUE);

    return $form['occurrences'];
  }

  /**
   * Callback for addMore button.
   *
   * The occurences have changed during submit so we can just return the altered form.
   */
  public function addmoreCallback(array &$form, FormStateInterface $form_state) {
    return $form['occurrences'];
  }

  /**
   * Submit handler for the "remove occurence" button.
   *
   * The occurences have changed during submit so we can just return the altered form.
   */
  public function removeCallback(array &$form, FormStateInterface $form_state) {
    return $form['occurrences'];
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->store->set('occurrences', $form_state->getValue('occurrences'));
    $this->store->set('field_price', $form_state->getValue('field_price'));
    $this->store->set('field_zipcode', $form_state->getValue('field_zipcode'));
    $this->store->set('field_area', $form_state->getValue('field_area'));
    $this->store->set('field_address', $form_state->getValue('field_address'));

    $this->acceptStep('confirm');

    // Redirect to next step.
    $form_state->setRedirect('itk_activity.multistep_confirm');
  }

}
