<?php
/**
 * @file
 * Contains \Drupal\itk_activity\Controller\ActivityController.
 */

namespace Drupal\itk_activity\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Drupal\node\Entity\Node;
use Drupal\Core\Url;

/**
 * ActivityController.
 */
class ActivityController extends ControllerBase {

  /**
   * Sign up to activity.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   * @param $nid
   *   The activity node id.
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function signup(Request $request, $nid) {
    $activity = $this->entityTypeManager()->getStorage('node')->load($nid);

    $uid = \Drupal::currentUser()->id();

    // Get referenced users.
    $signedUpUsers = $activity->field_signed_up_users->referencedEntities();

    $alreadyAdded = FALSE;

    // Remove user if already added.
    for ($i = 0; $i < count($signedUpUsers); $i++) {
      if ($signedUpUsers[$i]->uid->value == $uid) {
        $alreadyAdded = TRUE;

        unset($signedUpUsers[$i]);
      }
    }

    if ($alreadyAdded) {
      // If already added, set new array.
      $activity->set('field_signed_up_users', $signedUpUsers);
      drupal_set_message(t('Unregistered from activity.'));
    }
    else {
      // If not, add to array.
      $activity->field_signed_up_users[] = $uid;
      drupal_set_message(t('Registered to activity.'));
    }

    // Save activity.
    $activity->save();


    // Get redirect destination.
    $destination = $request->query->get('destination');
    if (isset($destination)) {
      return new RedirectResponse($destination);
    }

    return new Response();
  }

  /**
   * Clone an activity.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   * @param $nid
   * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
   */
  public function cloneActivity(Request $request, $nid) {
    $node = $this->entityTypeManager()->getStorage('node')->load($nid);

    // Check the it is an activity that is attempted cloned.
    if (!isset($node) || $node->getType() != 'activity') {
      drupal_set_message(t('Activity does not exist.'));

      // Redirect to user page.
      return new RedirectResponse(Url::fromRoute('user.page')->toString());
    }

    $currentUser = \Drupal::currentUser();

    $nodeUserid = $node->getOwner()->id();
    $userid = $currentUser->getAccount()->id();

    // Check the owner is the current user.
    if ($nodeUserid != $userid) {
      drupal_set_message(t('Activity not owned by user.'));

      // Redirect to user page.
      return new RedirectResponse(Url::fromRoute('user.page')->toString());
    }

    // Create the new activity.
    $newActivity = Node::create([
      'type' => 'activity',
      'title' => $node->title->value,
      'body' => $node->body,
      'field_address' => $node->field_address,
      'field_area' => $node->field_area,
      'field_categories' => $node->field_categories,
      'field_date' => $node->field_date,
      'field_entry_requirements' => $node->field_entry_requirements,
      'field_help_needed' => $node->field_help_needed,
      'field_image' => $node->field_image,
      'field_maximum_participants' => $node->field_maximum_participants,
      'field_physical_requirements' => $node->field_physical_requirements,
      'field_price' => $node->field_price,
      'field_signup_required' => $node->field_signup_required,
      'field_time_end' => $node->field_time_end,
      'field_time_start' => $node->field_time_start,
      'field_zipcode' => $node->field_zipcode,
    ]);
    $newActivity->save();

    drupal_set_message(t('Activity cloned.'));

    return new RedirectResponse(Url::fromRoute('entity.node.edit_form', ['node' => $newActivity->id()])->toString());
  }
}
