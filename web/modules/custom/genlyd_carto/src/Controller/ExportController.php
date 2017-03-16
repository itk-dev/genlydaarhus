<?php
/**
 * @file
 * Generates an CSV file based activity entities.
 */

namespace Drupal\genlyd_carto\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Drupal\file\Entity\File;
use Drupal\genlyd_carto\Render\CsvResponse;
use Drupal\image\Entity\ImageStyle;

class ExportController extends ControllerBase {

  /**
   * Route callback to expose activities as a CSV file.
   *
   * @return \Drupal\genlyd_carto\Render\CsvResponse
   *   The activities as CSV file.
   */
  public function csv() {
    $storage = \Drupal::entityTypeManager()->getStorage('node');
    $ids = $storage->getQuery()
      ->condition('type', 'activity')
      ->condition('status', 1)
      ->execute();
    $activities = $storage->loadMultiple($ids);

    $response = new CsvResponse();

    // Set headers in the CSV file.
    $response->setHeaderRow(['Title', 'Image', 'Address', 'Postal code', 'Area', 'Encoded address', 'URL']);

    foreach ($activities as $activity) {
      // Load image and use image style.
      $file = File::load($activity->get('field_image')->entity->id());
      $image_uri = ImageStyle::load('activity_teaser')->buildUrl($file->getFileUri());

      // Add a row to the CVS output.
      $response->addRow([
        $activity->getTitle(),
        $image_uri,
        $activity->get('field_address')->value,
        $activity->get('field_zipcode')->value,
        $activity->get('field_area')->value,
        implode([
          $activity->get('field_address')->value,
          $activity->get('field_zipcode')->value,
          'Denmark',
        ], ','),
        Url::fromRoute('entity.node.canonical', ['node' => $activity->id()], ['absolute' => TRUE])->toString(),
      ]);
    }

    // Set cache time to 5 min.
    $response->setMaxAge(300);

    return $response;
  }
}