<?php
/**
 * @file
 * Contains \Drupal\genlyd_maps\Controller\ApiController.
 */

namespace Drupal\genlyd_search\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Url;
use Drupal\file\Entity\File;
use Drupal\genlyd_maps\Render\GeoJsonResponse;
use Drupal\image\Entity\ImageStyle;
use Drupal\search_api\Entity\Index;
use Geocoder\Exception\InvalidCredentials;
use Geocoder\Exception\NoResult;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ApiController.
 *
 * @package Drupal\genlyd_search\Controller
 */
class ApiController extends ControllerBase {

  /**
   * Load all activities.
   *
   * @param Request $request
   *   The HTTP request.
   *
   * @return \Drupal\genlyd_search\Render\GeoJsonResponse
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

    $config = \Drupal::getContainer()->get('genlyd_search.config')->getAll();

    // Load geo-coder service and set configuration.
    $geocoder = \Drupal::service('geocoder');
    $plugins = ['googlemaps', 'bingmaps'];
    $options = [
      'googlemaps' => [
        'useSsl' => TRUE,
        'apiKey' => $config['genlyd_search_google_api_key'],
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
      $price = t('Free');
      if (isset($priceRaw) || $priceRaw > 0) {
        $price = t(':price kr.', [ ':price' => $priceRaw ]);
      }

      // Create metadata, which can be used in the marker popup's.
      $metadata = [
        'title' => [
          'label' => t('Title'),
          'value' => $activity->getTitle(),
        ],
        'image' => $image_uri,
        'date' => [
          'label' => t('Date'),
          'value' => \Drupal::service('date.formatter')->format((new \DateTime($activity->field_date->value))->getTimestamp(), 'date_medium'),
        ],
        'price' => [
          'label' =>  t('Price'),
          'value' => $price,
        ],
        'address' => [
          'label' => t('Address'),
          'value' => $activity->get('field_address')->value,
        ],
        'zipcode' => [
          'label' => t('Zipcode'),
          'value' => $activity->get('field_zipcode')->value,
        ],
        'area' => [
          'label' => t('Area'),
          'value' => $activity->get('field_area')->value,
        ],
        'url' => [
          'label' => t('More information'),
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

  /**
   * Search callback.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *
   * @return mixed
   */
  public function search(Request $request) {
    // This condition checks the `Content-type` and makes sure to
    // decode JSON string from the request body into array.
    $config = [
      "limit" => 10,
      "page" => 2,
    ];
    if (0 === strpos($request->headers->get( 'Content-Type' ), 'application/json')) {
      $config = json_decode($request->getContent(), TRUE);
    }

    /* @var $search_api_index \Drupal\search_api\IndexInterface */
    $search_api_index = Index::load($config['index']);

    // Create the query.
    $query = $search_api_index->query([
      'limit' => $config['limit'],
      'offset' => !is_null($config['page']) ? $config['page'] * $config['limit'] : 0,
      'search id' => 'search_genlyd_search',
    ]);

    $parse_mode = \Drupal::getContainer()
      ->get('plugin.manager.search_api.parse_mode')
      ->createInstance('direct');
    $query->setParseMode($parse_mode);

    // Search for keys.
    if (!empty($config['keys'])) {
      $query->keys($config['keys']);
    }

    // Index fields.
    $query->setFulltextFields($config['fields']);

    // Filter on facets.
    if (isset($config['facets'])) {
      foreach ($config['facets'] as $facet => $value) {
        if (!empty($value)) {
          $query->addCondition($facet, $value, (is_array($value) ? 'IN' : '='));
        }
      }
    }

    $result = $query->execute();
    $items = $result->getResultItems();

    /* @var $item \Drupal\search_api\Item\ItemInterface*/
    $results = array();
    foreach ($items as $item) {

      $entity_locations = $item->getField('geo_coder_field')->getValues();
      $entity_location = reset($entity_locations);

      $categroies = $item->getField('categories')->getValues();

      /** @var \Drupal\Core\Entity\EntityInterface $entity */
      $entity = $item->getOriginalObject()->getValue();
      if (!$entity) {
        continue;
      }

      // Render as view modes.
      $view_mode = isset($config['view_mode']) ? $config['view_mode'] : 'search_results';
      $results[] = [
        "id" => $entity->id(),
        "location" => $entity_location,
        "categroies" => $categroies,
        "snippet" => render($this->entityTypeManager()->getViewBuilder($entity->getEntityTypeId())->view($entity, $view_mode))
      ];
    }

    $json = [
      'title' => $this->t('Search results'),
      'offset' => !is_null($config['page']) ? $config['page'] * $config['limit'] : 0,
      'no_of_results' => 0,
    ];

    if (!empty($results)) {
      $json['no_of_results'] = $result->getResultCount();
      $json['results'] = $results;
    }


    $response =  new JsonResponse($json);
    return $response;
  }
}
