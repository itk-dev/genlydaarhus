<?php

/**
 * @file
 * Contains \Drupal\itk_activity\Form\Multistep\MultistepFormBase.
 */

namespace Drupal\itk_activity\Form\Multistep;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Session\SessionManagerInterface;
use Drupal\user\PrivateTempStoreFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\node\Entity\Node;
use Drupal\file\Entity\File;

/**
 * Class MultistepFormBase.
 *
 * The abstract class the other forms inherit from. Manages data and final submission.
 *
 * @package Drupal\itk_activity\Form\Multistep
 */
abstract class MultistepFormBase extends FormBase {

  /**
   * @var \Drupal\user\PrivateTempStoreFactory
   */
  protected $tempStoreFactory;

  /**
   * @var \Drupal\Core\Session\SessionManagerInterface
   */
  private $sessionManager;

  /**
   * @var \Drupal\Core\Session\AccountInterface
   */
  private $currentUser;

  /**
   * @var \Drupal\user\PrivateTempStore
   */
  protected $store;

  /**
   * Constructs a \Drupal\demo\Form\Multistep\MultistepFormBase.
   *
   * @param \Drupal\user\PrivateTempStoreFactory $temp_store_factory
   * @param \Drupal\Core\Session\SessionManagerInterface $session_manager
   * @param \Drupal\Core\Session\AccountInterface $current_user
   */
  public function __construct(PrivateTempStoreFactory $temp_store_factory, SessionManagerInterface $session_manager, AccountInterface $current_user) {
    $this->tempStoreFactory = $temp_store_factory;
    $this->sessionManager = $session_manager;
    $this->currentUser = $current_user;

    $this->store = $this->tempStoreFactory->get('multistep_data');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('user.private_tempstore'),
      $container->get('session_manager'),
      $container->get('current_user')
    );
  }

  /**
   * {@inheritdoc}.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // If multistep session has not been created, create it.
    if (!isset($_SESSION['multistep_form_holds_session'])) {
      $_SESSION['multistep_form_holds_session'] = TRUE;
      $this->sessionManager->start();
    }

    $form = array();
    $form['actions']['#type'] = 'actions';
    $form['actions']['submitStep'] = [
      [
        '#type' => 'submit',
        '#value' => '1. ' . t('About activity'),
        '#attributes' => [
          'class' => ['progress-bar--submit'],
        ],
        '#step' => 'about',
        '#name' => 'submit-step-0',
        '#submit' => ['::submitStep'],
      ],
      [
        '#type' => 'submit',
        '#value' => '2. ' . t('Information'),
        '#attributes' => [
          'class' => ['progress-bar--submit'],
        ],
        '#step' => 'information',
        '#name' => 'submit-step-1',
        '#submit' => ['::submitStep'],
      ],
      [
        '#type' => 'submit',
        '#value' => '3. ' . t('Categories'),
        '#attributes' => [
          'class' => ['progress-bar--submit'],
        ],
        '#step' => 'categories',
        '#name' => 'submit-step-2',
        '#submit' => ['::submitStep'],
      ],
      [
        '#type' => 'submit',
        '#value' => '4. ' . t('Image'),
        '#attributes' => [
          'class' => ['progress-bar--submit'],
        ],
        '#step' => 'image',
        '#name' => 'submit-step-3',
        '#submit' => ['::submitStep'],
      ],
      [
        '#type' => 'submit',
        '#value' => '5. ' . t('Details'),
        '#attributes' => [
          'class' => ['progress-bar--submit'],
        ],
        '#step' => 'details',
        '#name' => 'submit-step-4',
        '#submit' => ['::submitStep'],
      ],
      [
        '#type' => 'submit',
        '#value' => '6. ' . t('Confirm'),
        '#attributes' => [
          'class' => ['progress-bar--submit'],
        ],
        '#step' => 'confirm',
        '#name' => 'submit-step-5',
        '#submit' => ['::submitStep'],
      ],

    ];
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => t('Submit'),
      '#button_type' => 'primary',
      '#weight' => 10,
    );

    return $form;
  }

  /**
   * Commit the current step to the store.
   *
   * To be overridden in inheritance.
   *
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   */
  protected function commitStep(FormStateInterface $form_state) {}

  /**
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   */
  public function submitStep(array &$form, FormStateInterface $form_state) {
    $this->commitStep($form_state);

    $element = $form_state->getTriggeringElement();
    $step = $element['#step'];

    $form_state->setRedirect('itk_activity.multistep_' . $step);
  }

  /**
   * Get all data from store.
   */
  protected function getData() {
    return [
      'type' => 'activity',
      'title' => $this->store->get('title'),
      'body' => $this->store->get('body'),
      'field_address' => $this->store->get('field_address'),
      'field_area' => $this->store->get('field_area'),
      'field_categories' => $this->store->get('field_categories'),
      'field_date' => $this->store->get('field_date'),
      'field_entry_requirements' => $this->store->get('field_entry_requirements'),
      'field_help_needed' => $this->store->get('field_help_needed'),
      'field_image' => $this->store->get('field_image'),
      'field_maximum_participants' => $this->store->get('field_maximum_participants'),
      'field_physical_requirements' => $this->store->get('field_physical_requirements'),
      'field_price' => $this->store->get('field_price'),
      'field_signup_required' => $this->store->get('field_signup_required'),
      'field_time_end' => $this->store->get('field_time_end'),
      'field_time_start' => $this->store->get('field_time_start'),
      'field_zipcode' => $this->store->get('field_zipcode'),
    ];
  }

  /**
   * Get the progress bar array.
   *
   * @param string active
   *   The active step.
   *
   * @return array
   */
  protected function getProgressBar($active) {
    return [
      'items' => [
        [
          '#title' => t('About activity'),
          '#open' => TRUE,
          '#active' => $active == 'about',
        ],
        [
          '#title' => t('Information'),
          '#open' => $this->store->get('step_information'),
          '#active' => $active == 'information',
        ],
        [
          '#title' => t('Categories'),
          '#open' => $this->store->get('step_categories'),
          '#active' => $active == 'categories',
        ],
        [
          '#title' => t('Image'),
          '#open' => $this->store->get('step_image'),
          '#active' => $active == 'image',
        ],
        [
          '#title' => t('Details'),
          '#open' => $this->store->get('step_details'),
          '#active' => $active == 'details',
        ],
        [
          '#title' => t('Confirm'),
          '#open' => $this->store->get('step_confirm'),
          '#active' => $active == 'confirm',
        ],
      ],
    ];
  }

  /**
   * Accept a step in the form.
   *
   * Opens up the link to the step.
   *
   * @param $step
   */
  protected function acceptStep($step) {
    $this->store->set('step_' . $step, TRUE);
  }

  /**
   * Saves the data from the multistep form.
   */
  protected function saveData() {
    $data = $this->getData();

    // Check that image has not been removed after being added.
    $image = array_key_exists(0, $data['field_image']) ? File::load($data['field_image'][0]) : NULL;
    if (is_null($image)) {
      $data['field_image'] = [];
    }

    // Create the activity.
    $activity = Node::create($data);
    $activity->save();

    $this->deleteStore();

    drupal_set_message(t('The form has been saved.'));

    return $activity->id();
  }

  /**
   * Helper method that removes all the keys from the store collection used for
   * the multistep form.
   */
  protected function deleteStore() {
    $keys = [
      'title',
      'body',
      'field_address',
      'field_area',
      'field_categories',
      'field_date',
      'field_entry_requirements',
      'field_help_needed',
      'field_image',
      'field_maximum_participants',
      'field_physical_requirements',
      'field_price',
      'field_signup_required',
      'field_time_end',
      'field_time_start',
      'field_zipcode',
      'step_information',
      'step_categories',
      'step_image',
      'step_details',
      'step_confirm',
    ];
    foreach ($keys as $key) {
      $this->store->delete($key);
    }
  }
}