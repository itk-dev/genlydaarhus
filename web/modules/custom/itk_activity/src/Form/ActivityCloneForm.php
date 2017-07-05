<?php
/**
 * Clone node form.
 */

namespace Drupal\itk_activity\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Url;

/**
 * Class ActivityCloneForm
 * @package Drupal\itk_activity\Form
 */
class ActivityCloneForm extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'itk_activity_clone_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $nid = NULL) {
    $node = \Drupal::entityTypeManager()->getStorage('node')->load($nid);

    // Check the it is an activity that is attempted cloned.
    if (!isset($node) || $node->getType() != 'activity') {
      drupal_set_message(t('Activity does not exist.'));

      // Redirect to user page.
      return new RedirectResponse(Url::fromRoute('user.page')->toString());
    }

    // Check the owner is the current user.
    if ($node->getOwner()->id() != \Drupal::currentUser()->id()) {
      drupal_set_message(t('Activity not owned by user.'));

      // Redirect to user page.
      return new RedirectResponse(Url::fromRoute('user.page')->toString());
    }

    // Set node for later processing.
    $form_state->set('node', $node);

    // Default to start and end time from original activity.
    $timeStartDefault = $node->get('field_time_start')->value;
    $timeEndDefault = $node->get('field_time_end')->value;

    $form['#tree'] = TRUE;
    $form['occurrences'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('You are creating new occurrences of the activity "@title"', ['@title' => $node->title->value]),
      'actions' => [
        '#type' => 'actions',
      ],
    ];

    // Get previous occurrences.
    $occurrences = $form_state->get('occurrences') ? $form_state->get('occurrences') : [];

    // If not occurrences set, add one.
    if (empty($occurrences)) {
      $occurrences[] = [];
      $form_state->set('occurrences', $occurrences);
    }

    foreach ($occurrences as $i => $occurrence) {
      $form['occurrences'][$i] = [
        '#prefix' => $this->t('Occurrence @nr', ['@nr' => $i])
      ];

      $form['occurrences'][$i]['field_date'] = array(
        '#type' => 'date',
        '#title' => t('Date'),
      );

      $form['occurrences'][$i]['field_time_start'] = array(
        '#type' => 'textfield',
        '#max_length' => 5,
        '#attributes' => [
          'title' => t('Must have format HH:mm'),
          'placeholder' => t('Must have format: HH:mm, for example: 12:00'),
          'pattern' => '[0-9]{2}:[0-9]{2}',
          'maxlength' => 5,
          'class' => [ 'js-timepicker-field' ],
        ],
        '#title' => t('Time start'),
        '#default_value' => $timeStartDefault,
      );

      $form['occurrences'][$i]['field_time_end'] = array(
        '#type' => 'textfield',
        '#max_length' => 5,
        '#attributes' => [
          'title' => t('Must have format HH:mm'),
          'placeholder' => t('Must have format: HH:mm, for example: 12:00'),
          'pattern' => '[0-9]{2}:[0-9]{2}',
          'maxlength' => 5,
          'class' => [ 'js-timepicker-field' ],
        ],
        '#title' => t('Time end'),
        '#default_value' => $timeEndDefault,
      );

      $form['occurrences'][$i]['actions']['remove_occurrence'] = [
        '#type' => 'submit',
        '#attributes' => [
          'class' => ['button-delete'],
        ],
        'element_index' => $i,
        '#name' => 'remove-occurrences-' . $i,
        '#value' => t('Remove'),
        '#submit' => ['::removeCallback'],
        '#ajax' => [
          'callback' => '::addmoreCallback',
          'wrapper' => "occurrence-fieldset-wrapper",
        ],
      ];
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
      '#attributes' => [
        'class' => ['button-secondary-dark'],
      ],
      '#value' => t('Add date and time'),
      '#submit' => ['::addOne'],
      '#ajax' => [
        'callback' => '::addmoreCallback',
        'wrapper' => "occurrence-fieldset-wrapper",
      ],
    ];

    $form_state->setCached(FALSE);

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => t('Submit'),
    ];

    // Attach timepickers js.
    $form['#attached']['library'][] = 'itk_activity/timepickers';

    return $form;
  }

  /**
   * Callback for both ajax-enabled buttons.
   *
   * Selects and returns the fieldset with the occurrences in it.
   */
  public function addmoreCallback(array &$form, FormStateInterface $form_state) {
    return $form['occurrences'];
  }

  /**
   * Submit handler for the "add-one-more" button.
   *
   * Increments the max counter and causes a rebuild.
   */
  public function addOne(array &$form, FormStateInterface $form_state) {
    $occurrences = $form_state->get('occurrences');
    $occurrences[] = [
      'field_date' => '',
      'field_time_start' => '',
      'field_time_end' => '',
    ];
    $form_state->set('occurrences', $occurrences);

    $form_state->setRebuild(TRUE);
  }

  /**
   * Submit handler for the "remove one" button.
   *
   * Decrements the max counter and causes a form rebuild.
   */
  public function removeCallback(array &$form, FormStateInterface $form_state) {
    $element = $form_state->getTriggeringElement();
    $index = $element['element_index'];

    $occurrences = $form_state->get('occurrences');

    if (array_key_exists($index, $occurrences)) {
      unset($occurrences[$index]);
    }

    $form_state->set('occurrences', $occurrences);

    $form_state->setRebuild(TRUE);
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
    $values = $form_state->getValues();
    $occurrences = (isset($values['occurrences']) && is_array($values['occurrences'])) ? $values['occurrences'] : [];

    if (empty($occurrences)) {
      drupal_set_message(t('No new occurrences set.'));

      return;
    }

    $clones = \Drupal::service('itk_activity.activity_manager')->cloneActivity($form_state->get('node'), $occurrences);

    // Create link messages.
    $activityLinksMessage = "<ul>";
    foreach ($clones as $activity) {
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

    // Redirect to user page.
    $form_state->setRedirect('user.page');
  }

}
