<?php
/**
 * @file
 * Contains \Drupal\genlyd_maps\Controller\ApiController.
 */

namespace Drupal\genlyd_maps\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Drupal\file\Entity\File;
use Drupal\genlyd_maps\Render\GeoJsonResponse;
use Drupal\image\Entity\ImageStyle;

/**
 * Class ApiController.
 *
 * @package Drupal\grundsalg_maps\Controller
 */
class ApiController extends ControllerBase {

  /**
   * Load all activities.
   *
   * @return \Drupal\genlyd_maps\Render\GeoJsonResponse
   *   The activities as GeoJSON encoded array.
   */
  public function activates() {
    /**
     * @TODO: Filter based on date?
     */
    $storage = \Drupal::entityTypeManager()->getStorage('node');
    $ids = $storage->getQuery()
      ->condition('type', 'activity')
      ->condition('status', 1)
      ->execute();
    $activities = $storage->loadMultiple($ids);

    $response = new GeoJsonResponse();

    // Load geo-coder service and set configuration.
    $geocoder = \Drupal::service('geocoder');
    $plugins = ['geonames', 'googlemaps', 'bingmaps'];
    $options = [
      'geonames' => [],
      'googlemaps' => [],
      'bingmaps' => [],
    ];

    foreach ($activities as $activity) {
      // Load image and use image style.
      $file = File::load($activity->get('field_image')->entity->id());
      $image_uri = ImageStyle::load('activity_teaser')->buildUrl($file->getFileUri());

      // Create metadata, which can be used in the marker popup's.
      $metadata = [
        'title' => $activity->getTitle(),
        'image' => $image_uri,
        'address' => $activity->get('field_address')->value,
        'zipcode' => $activity->get('field_zipcode')->value,
        'area' => $activity->get('field_area')->value,
        'url' => Url::fromRoute('entity.node.canonical', ['node' => $activity->id()], ['absolute' => TRUE])->toString(),
      ];

      // Encode the address to get lat/lng.
      $address = implode([
        $metadata['address'],
        $metadata['zipcode'],
        'Denmark',
      ], ',');
      $addressCollection = $geocoder->geocode($address, $plugins, $options);
      $latitude = $addressCollection->first()->getCoordinates()->getLatitude();
      $longitude = $addressCollection->first()->getCoordinates()->getLongitude();

      // Add the information to the output.
      $response->addPoint($latitude, $longitude, $metadata);
    }

    return $response;
  }
}
