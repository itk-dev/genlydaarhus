<?php
/**
 * @file
 * Contains \Drupal\genlyd_search\Controller\ApiController.
 */

namespace Drupal\genlyd_search\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Drupal\file\Entity\File;
use Drupal\genlyd_search\Render\GeoJsonResponse;
use Drupal\image\Entity\ImageStyle;
use Drupal\search_api\Entity\Index;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ApiController.
 *
 * @package Drupal\genlyd_search\Controller
 */
class ApiController extends ControllerBase {
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
      "limit" => 9,
      "page" => 0,
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

    if (isset($config['sort'])) {
      $query->sort($config['sort']);
    }

    $result = $query->execute();
    $items = $result->getResultItems();

    /* @var $item \Drupal\search_api\Item\ItemInterface*/
    $hits = array();
    foreach ($items as $item) {

      $entity_locations = $item->getField('geo_coder_field')->getValues();
      $entity_location = reset($entity_locations);

      // Hardcoded categories is not optimal for facets.
      $categories = $item->getField('categories')->getValues();

      /** @var \Drupal\Core\Entity\EntityInterface $entity */
      $entity = $item->getOriginalObject()->getValue();
      if (!$entity) {
        continue;
      }

      // Render as view modes.
      $view_mode = isset($config['view_mode']) ? $config['view_mode'] : 'search_results';
      $snippet = $this->entityTypeManager()->getViewBuilder($entity->getEntityTypeId())->view($entity, $view_mode);
      $hits[] = [
        "id" => $entity->id(),
        "location" => $entity_location,
        "categories" => $categories,
        "snippet" => render($snippet),
      ];
    }

    $json = [
      'title' => $this->t('Search results'),
      'offset' => !is_null($config['page']) ? $config['page'] * $config['limit'] : 0,
      'no_of_results' => 0,
    ];

    if (!empty($hits)) {
      $json['no_of_results'] = $result->getResultCount();
      $json['hits'] = $hits;
    }

    $response =  new JsonResponse($json);
    return $response;
  }

  public function searchGeoJSON(Request $request) {
    // This condition checks the `Content-type` and makes sure to
    // decode JSON string from the request body into array.
    $config = [
      "limit" => 9999,
      "page" => 0,
      "index" => 'activities',
    ];
    if (0 === strpos($request->headers->get( 'Content-Type' ), 'application/json')) {
      $config = json_decode($request->getContent(), TRUE);
    }

    /* @var $search_api_index \Drupal\search_api\IndexInterface */
    $search_api_index = Index::load($config['index']);

    // Create the query.
    $query = $search_api_index->query([
      'limit' => $config['limit'],
      'offset' => 0,
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

    $response = new GeoJsonResponse();

    /* @var $item \Drupal\search_api\Item\ItemInterface*/
    foreach ($items as $item) {
      /** @var \Drupal\Core\Entity\EntityInterface $entity */
      $entity = $item->getOriginalObject()->getValue();
      if (!$entity) {
        continue;
      }

      $entity_locations = $item->getField('geo_coder_field')->getValues();
      $entity_location = reset($entity_locations);
      if (empty($entity_location)) {
        continue;
      }

      $image_uri = '';
      if (isset($entity->get('field_image')->entity)) {
        $file = File::load($entity->get('field_image')->entity->id());
        $image_uri = ImageStyle::load('activity_teaser')->buildUrl($file->getFileUri());
      }

      // Get the prices for this activity.
      $priceRaw = $entity->field_price->value;
      $price = t('Free');
      if (isset($priceRaw) || $priceRaw > 0) {
        $price = t(':price kr.', [ ':price' => $priceRaw ]);
      }

      // Create metadata, which can be used in the marker popup's.
      $metadata = [
        'title' => [
          'label' => t('Title'),
          'value' => $entity->getTitle(),
        ],
        'image' => $image_uri,
        'date' => [
          'label' => t('Date'),
          'value' => \Drupal::service('date.formatter')->format((new \DateTime($entity->field_date->value))->getTimestamp(), 'date_medium'),
        ],
        'price' => [
          'label' =>  t('Price'),
          'value' => $price,
        ],
        'address' => [
          'label' => t('Address'),
          'value' => $entity->get('field_address')->value,
        ],
        'zipcode' => [
          'label' => t('Zipcode'),
          'value' => $entity->get('field_zipcode')->value,
        ],
        'area' => [
          'label' => t('Area'),
          'value' => $entity->get('field_area')->value,
        ],
        'url' => [
          'label' => t('More information'),
          'value' => Url::fromRoute('entity.node.canonical', ['node' => $entity->id()], ['absolute' => TRUE])->toString(),
        ],
      ];


      list($longitude, $latitude) = explode(',', $entity_location);
      $response->addPoint($latitude, $longitude, $metadata);
    }

    return $response;
  }
}
