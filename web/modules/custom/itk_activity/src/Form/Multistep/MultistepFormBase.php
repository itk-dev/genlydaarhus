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

/**
 * Class MultistepFormBase.
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
      '#value' => $this->t('Submit'),
      '#button_type' => 'primary',
      '#weight' => 10,
    );

    return $form;
  }

  /**
   * Saves the data from the multistep form.
   */
  protected function saveData() {
    // @TODO: Logic for saving data goes here...

    // Setup activity, that data should be saved to.
    $activity =  Node::create([
      'type' => 'activity',
    ]);

    $this->deleteStore();
    drupal_set_message($this->t('The form has been saved.'));
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
      'field_signed_up_users',
      'field_signup_required',
      'field_time_end',
      'field_time_start',
      'field_zipcode',
    ];
    foreach ($keys as $key) {
      $this->store->delete($key);
    }
  }
}