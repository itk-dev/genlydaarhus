<?php
/**
 * @file
 *
 */

namespace Drupal\itk_activity\Service;

use Drupal\node\Entity\Node;

/**
 * Class ActivityManager
 */
class ActivityManager {
  /**
   * Clone an activity to multiple occurrences.
   *
   * @param $activity
   * @param $occurrences
   * @return array
   */
  public function cloneActivity($activity, $occurrences) {
    $createdActivities = [];

    foreach ($occurrences as $occurrence) {
      $newDate = $occurrence['field_date'];
      $newTimeStart = $occurrence['field_time_end'];
      $newTimeEnd = $occurrence['field_time_start'];

      // Reject if any field is not set.
      if (empty($newDate) || empty($newTimeStart) || empty($newTimeEnd)) {
        continue;
      }

      // Create the new activity.
      $newActivity = Node::create([
        'type' => 'activity',
        'title' => $activity->title->value,
        'body' => $activity->body,
        'field_parent_activity' => $activity,
        'field_address' => $activity->field_address,
        'field_area' => $activity->field_area,
        'field_categories' => $activity->field_categories,
        'field_entry_requirements' => $activity->field_entry_requirements,
        'field_help_needed' => $activity->field_help_needed,
        'field_image' => $activity->field_image,
        'field_maximum_participants' => $activity->field_maximum_participants,
        'field_physical_requirements' => $activity->field_physical_requirements,
        'field_price' => $activity->field_price,
        'field_signup_required' => $activity->field_signup_required,
        'field_zipcode' => $activity->field_zipcode,
        'field_date' => $newDate,
        'field_time_end' => $newTimeStart,
        'field_time_start' => $newTimeEnd,
      ]);
      $newActivity->save();

      $createdActivities[] = $newActivity;
    }

    return $createdActivities;
  }

}
