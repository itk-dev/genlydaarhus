<?php

/**
 * @file
 * Contains \Drupal\itk_activity\Form\Multistep\MultistepFormDetails.
 */

namespace Drupal\itk_activity\Form\Multistep;

use Drupal\Core\Form\FormStateInterface;

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

    $form['field_date'] = array(
      '#type' => 'date',
      '#required' => TRUE,
      '#title' => $this->t('Date'),
      '#default_value' => $this->store->get('field_date') ? $this->store->get('field_date') : '',
    );

    $form['field_time_start'] = array(
      '#type' => 'textfield',
      '#max_length' => 5,
      '#attributes' => [
        'title' => $this->t('Must have format HH:mm'),
        'placeholder' => $this->t('Must have format: HH:mm, for example: 12:00'),
        'pattern' => '[0-9]{2}:[0-9]{2}',
        'maxlength' => 5,
      ],
      '#required' => TRUE,
      '#title' => $this->t('Time start'),
      '#default_value' => $this->store->get('field_time_start') ? $this->store->get('field_time_start') : NULL,
    );

    $form['field_time_end'] = array(
      '#type' => 'textfield',
      '#max_length' => 5,
      '#attributes' => [
        'title' => $this->t('Must have format HH:mm'),
        'placeholder' => $this->t('Must have format: HH:mm, for example: 12:00'),
        'pattern' => '[0-9]{2}:[0-9]{2}',
        'maxlength' => 5,
      ],
      '#required' => TRUE,
      '#title' => $this->t('Time end'),
      '#default_value' => $this->store->get('field_time_end') ? $this->store->get('field_time_end') : NULL,
    );

    $form['field_price'] = array(
      '#type' => 'number',
      '#required' => FALSE,
      '#attributes' => [
        'min' => 0,
      ],
      '#title' => $this->t('Price (if any)'),
      '#default_value' => $this->store->get('field_price') ? $this->store->get('field_price') : NULL,
    );

    $form['field_zipcode'] = array(
      '#type' => 'number',
      '#max_length' => 4,
      '#attributes' => [
        'min' => 0,
        'max' => 9999,
        'class' => [ 'js-field-zipcode', ],
      ],
      '#required' => TRUE,
      '#title' => $this->t('Zipcode'),
      '#default_value' => $this->store->get('field_zipcode') ? $this->store->get('field_zipcode') : NULL,
    );

    // Attach zipcode js, that sets area from zipcode.
    $form['#attached']['library'][] = 'itk_activity/zipcode';

    $form['field_area'] = array(
      '#type' => 'textfield',
      '#attributes' => [
        'class' => [ 'js-field-area', ],
      ],
      '#title' => $this->t('Area'),
      '#default_value' => $this->store->get('field_area') ? $this->store->get('field_area') : NULL,
    );

    $form['field_address'] = array(
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => $this->t('Address'),
      '#default_value' => $this->store->get('field_address') ? $this->store->get('field_address') : NULL,
    );

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
    $this->store->set('field_date', $form_state->getValue('field_date'));
    $this->store->set('field_time_start', $form_state->getValue('field_time_start'));
    $this->store->set('field_time_end', $form_state->getValue('field_time_end'));
    $this->store->set('field_price', $form_state->getValue('field_price'));
    $this->store->set('field_zipcode', $form_state->getValue('field_zipcode'));
    $this->store->set('field_area', $form_state->getValue('field_area'));
    $this->store->set('field_address', $form_state->getValue('field_address'));

    // Redirect to next step.
    $form_state->setRedirect('itk_activity.multistep_confirm');
  }

}