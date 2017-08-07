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
use Drupal\Core\Url;
use DateTime;

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
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => t('Submit'),
      '#button_type' => 'primary',
      '#weight' => 10,
    );

    return $form;
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
      'field_entry_requirements' => $this->store->get('field_entry_requirements'),
      'field_help_needed' => $this->store->get('field_help_needed'),
      'field_image' => $this->store->get('field_image'),
      'field_maximum_participants' => $this->store->get('field_maximum_participants'),
      'field_physical_requirements' => $this->store->get('field_physical_requirements'),
      'field_price' => $this->store->get('field_price'),
      'field_signup_required' => $this->store->get('field_signup_required'),
      'field_zipcode' => $this->store->get('field_zipcode'),
      'occurrences' => $this->store->get('occurrences'),
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
          '#title' => t("About activity"),
          '#href' => Url::fromRoute('itk_activity.multistep_about')->toString(),
          '#open' => TRUE,
          '#active' => $active == 'about',
        ],
        [
          '#title' => t("Information"),
          '#href' => Url::fromRoute('itk_activity.multistep_information')->toString(),
          '#open' => $this->store->get('step_information'),
          '#active' => $active == 'information',
        ],
        [
          '#title' => t("Categories"),
          '#href' => Url::fromRoute('itk_activity.multistep_categories')->toString(),
          '#open' => $this->store->get('step_categories'),
          '#active' => $active == 'categories',
        ],
        [
          '#title' => t("Image"),
          '#href' => Url::fromRoute('itk_activity.multistep_image')->toString(),
          '#open' => $this->store->get('step_image'),
          '#active' => $active == 'image',
        ],
        [
          '#title' => t("Details"),
          '#href' => Url::fromRoute('itk_activity.multistep_details')->toString(),
          '#open' => $this->store->get('step_details'),
          '#active' => $active == 'details',
        ],
        [
          '#title' => t("Confirm"),
          '#href' => Url::fromRoute('itk_activity.multistep_confirm')->toString(),
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

    $occurrences = $data['occurrences'];
    unset($data['occurrences']);

    $createdActivities = [];

    foreach ($occurrences as $occurrence) {
      if (isset($occurrence['field_date']) &&
          isset($occurrence['field_time_start']) &&
          isset($occurrence['field_time_end'])) {

        $data['field_date'] = $occurrence['field_date'];
        $data['field_time_start'] = $occurrence['field_time_start'];
        $data['field_time_end'] = $occurrence['field_time_end'];

        // Create the activity.
        $activity = Node::create($data);
        $activity->save();

        $createdActivities[] = $activity;
      }
    }

    // Create link messages.
    $activityLinksMessage = "<ul>";
    foreach ($createdActivities as $activity) {
      $url = \Drupal\Core\Url::fromRoute('entity.node.canonical', ['node' => $activity->id()], ['absolute' => TRUE])->toString();

      $activityLinksMessage .=
        implode('', [
          '<li>',
          \Drupal::service('date.formatter')->format((new DateTime($activity->field_date->value))->getTimestamp(), 'date_medium'),
          ' - ',
          '<a href="',
          $url,
          '">',
          $url,
          '</a>',
          "</li>"
        ]);
    }
    $activityLinksMessage .= "</ul>";

    if (!empty($createdActivities)) {
      $message = \Drupal::translation()->formatPlural(
        count($createdActivities),
        '<p>The activity with title "' . $createdActivities[0]->title->value . '" has been created.</p>' . $activityLinksMessage,
        '<p>The @count activities with title "' . $createdActivities[0]->title->value . '" have been created.</p>' . $activityLinksMessage
      );
    }
    else {
      $message = 'No activity was created.';
    }

    drupal_set_message($message);

    // Clear stored data.
    $this->deleteStore();
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
      'field_entry_requirements',
      'field_help_needed',
      'field_image',
      'field_maximum_participants',
      'field_physical_requirements',
      'field_price',
      'field_signup_required',
      'field_zipcode',
      'occurrences',
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