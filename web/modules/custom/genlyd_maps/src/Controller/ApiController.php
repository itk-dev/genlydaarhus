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
use Drupal\search_api\Entity\Index;
use Geocoder\Exception\InvalidCredentials;
use Geocoder\Exception\NoResult;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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

  public function search(Request $request, $keys = '') {
    $limit = 5;

    $perform_search = TRUE;
    if ($perform_search) {

      /* @var $search_api_index \Drupal\search_api\IndexInterface */
      $search_api_index = Index::load('activities');

      // Create the query.
      $query = $search_api_index->query([
        'limit' => $limit,
        'offset' => !is_null($request->get('page')) ? $request->get('page') * $limit : 0,
        'search id' => 'search_genlyd_maps',
      ]);

      $parse_mode = \Drupal::getContainer()
        ->get('plugin.manager.search_api.parse_mode')
        ->createInstance('direct');
      $query->setParseMode($parse_mode);

      // Search for keys.
      if (!empty($keys)) {
        $query->keys($keys);
      }

      // Index fields.
      $query->setFulltextFields(['title']);

      $result = $query->execute();
      $items = $result->getResultItems();

      /* @var $item \Drupal\search_api\Item\ItemInterface*/
      $results = array();
      foreach ($items as $item) {

        /** @var \Drupal\Core\Entity\EntityInterface $entity */
        $entity = $item->getOriginalObject()->getValue();
        if (!$entity) {
          continue;
        }

        // Render as view modes.
        if (TRUE) {
          $view_mode = 'teaser';
          $results[] = $this->entityTypeManager()->getViewBuilder($entity->getEntityTypeId())->view($entity, $view_mode);
        }

        // Render as snippets.
        if (FALSE) {
          $results[] = array(
            '#theme' => 'search_api_page_result',
            '#item' => $item,
            '#entity' => $entity,
          );
        }
      }

      if (!empty($results)) {

        $build['#search_title'] = array(
          '#markup' => $this->t('Search results'),
        );

        $build['#no_of_results'] = array(
          '#markup' => $this->formatPlural($result->getResultCount(), '1 result found', '@count results found'),
        );

        $build['#results'] = $results;

        // Build pager.
        pager_default_initialize($result->getResultCount(), $limit);
        $build['#pager'] = array(
          '#type' => 'pager',
        );
      }
      elseif ($perform_search) {
        $build['#no_results_found'] = array(
          '#markup' => $this->t('Your search yielded no results.'),
        );

        $build['#search_help'] = array(
          '#markup' => $this->t('<ul>
<li>Check if your spelling is correct.</li>
<li>Remove quotes around phrases to search for each word individually. <em>bike shed</em> will often show more results than <em>&quot;bike shed&quot;</em>.</li>
<li>Consider loosening your query with <em>OR</em>. <em>bike OR shed</em> will often show more results than <em>bike shed</em>.</li>
</ul>'),
        );
      }
    }

    $build['#theme'] = 'search_api_page';
    return $build;
  }

}
