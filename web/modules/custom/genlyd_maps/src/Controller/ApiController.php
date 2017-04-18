<?php
/**
 * @file
 * Contains \Drupal\genlyd_maps\Controller\ApiController.
 */

namespace Drupal\genlyd_maps\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Url;
use Drupal\file\Entity\File;
use Drupal\genlyd_maps\Render\GeoJsonResponse;
use Drupal\image\Entity\ImageStyle;
use Geocoder\Exception\InvalidCredentials;
use Geocoder\Exception\NoResult;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ApiController.
 *
 * @package Drupal\grundsalg_maps\Controller
 */
class ApiController extends ControllerBase {

  /**
   * Load all activities.
   *
   * @param Request $request
   *   The HTTP request.
   * @return \Drupal\genlyd_maps\Render\GeoJsonResponse
   *   The activities as GeoJSON encoded array.
   */
  public function activates(Request $request) {
    // This condition checks the `Content-type` and makes sure to
    // decode JSON string from the request body into array.
    $filters = [];
    if (0 === strpos($request->headers->get( 'Content-Type' ), 'application/json')) {
      $filters = json_decode($request->getContent(), TRUE);
      $request->request->replace(is_array($filters) ? $filters : []);
    }

    // Get the current date and format it correctly to use it in the entity
    // query below.
    $date = new DrupalDateTime();
    $date->setTimezone(new \DateTimezone(DATETIME_STORAGE_TIMEZONE));
    $formatted = $date->format('Y-m-d');

    // Filter activities.
    $storage = \Drupal::entityTypeManager()->getStorage('node');
    $query = $storage->getQuery()
      ->condition('type', 'activity')
      ->condition('status', 1)
      ->condition('field_date.value', $formatted, '>=');

    if (!empty($filters['field_categories'])) {
      $query->condition('field_categories', $filters['field_categories'], 'IN');
    }

    if (!empty($filters['title'])) {
      $query->condition('title', current($filters['title']), 'CONTAINS');
    }

    if (!empty($filters['field_zipcode'])) {
      $query->condition('field_zipcode', current($filters['field_zipcode']));
    }

    $ids = $query->execute();
    $activities = $storage->loadMultiple($ids);

    $config = \Drupal::getContainer()->get('genlyd_maps.config')->getAll();

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

    $response = new GeoJsonResponse();
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

      $t = \Drupal::translation();

      // Create metadata, which can be used in the marker popup's.
      $metadata = [
        'title' => [
          'label' => $t->translate('Title'),
          'value' => $activity->getTitle(),
        ],
        'image' => $image_uri,
        'date' => [
          'label' => $t->translate('Date'),
          'value' => \Drupal::service('date.formatter')->format((new \DateTime($activity->field_date->value))->getTimestamp(), 'date_medium'),
        ],
        'price' => [
          'label' =>  $t->translate('Price'),
          'value' => $price,
        ],
        'address' => [
          'label' => $t->translate('Address'),
          'value' => $activity->get('field_address')->value,
        ],
        'zipcode' => [
          'label' => $t->translate('Zipcode'),
          'value' => $activity->get('field_zipcode')->value,
        ],
        'area' => [
          'label' => $t->translate('Area'),
          'value' => $activity->get('field_area')->value,
        ],
        'url' => [
          'label' => $t->translate('More information'),
          'value' => Url::fromRoute('entity.node.canonical', ['node' => $activity->id()], ['absolute' => TRUE])->toString(),
        ],
      ];

      // Encode the address to get lat/lng.
      $address = implode([
        $metadata['address']['value'],
        $metadata['zipcode']['value'],
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
