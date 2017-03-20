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

/**
 * ActivityController.
 */
class ActivityController extends ControllerBase {

  /**
   * Sign up to activity.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   * @param $nid
   * @param $uid
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function signup(Request $request, $nid, $uid) {
    $activity = $this->entityTypeManager()->getStorage('node')->load($nid);

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

}
