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
    $config = \Drupal::getContainer()->get('genlyd_maps.config')->getAll();

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
    $plugins = ['googlemaps', 'bingmaps'];
    $options = [
      'googlemaps' => [
        'useSsl' => TRUE,
        'apiKey' => $config['genlyd_maps_google_api_key'],
      ],
      'bingmaps' => [],
    ];

    foreach ($activities as $activity) {
      // Load image and use image style.
      if (isset($activity->get('field_image')->entity)) {
        $file = File::load($activity->get('field_image')->entity->id());
        $image_uri = ImageStyle::load('activity_teaser')->buildUrl($file->getFileUri());
      }

      // Get the prices for this activity.
      $priceRaw = $activity->field_price->value;
      $price = \Drupal::translation()->translate('Free');
      if (isset($priceRaw) || $priceRaw > 0) {
        $price = \Drupal::translation()->translate(':price kr.', [ ':price' => $priceRaw ]);
      }

      // Create metadata, which can be used in the marker popup's.
      $metadata = [
        'title' => $activity->getTitle(),
        'image' => $image_uri,
        'date' => \Drupal::service('date.formatter')->format((new \DateTime($activity->field_date->value))->getTimestamp(), 'date_medium'),
        'price' => $price,
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

      // Returns false if address does not exist.
      $addressCollection = $geocoder->geocode($address, $plugins, $options);

      if ($addressCollection) {
        $latitude = $addressCollection->first()->getCoordinates()->getLatitude();
        $longitude = $addressCollection->first()->getCoordinates()->getLongitude();

        // Add the information to the output.
        $response->addPoint($latitude, $longitude, $metadata);
      }
    }

    return $response;
  }
}
