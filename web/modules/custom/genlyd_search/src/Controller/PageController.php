<?php
/**
 * @file
 * Contains \Drupal\genlyd_search\Controller\PageController.
 */

namespace Drupal\genlyd_search\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\search_api\Entity\Index;
use Drupal\taxonomy\Entity\Vocabulary;
use Drupal\views\Plugin\views\display\Page;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ApiController.
 *
 * @package Drupal\genlyd_search\Controller
 */
class PageController extends ControllerBase {
  /**
   * Search page callback.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *
   * @return mixed
   */
  public function search(Request $request) {

    $config = \Drupal::getContainer()->get('genlyd_search.config')->getAll();

    // Load facets.
    $facets = [];
    $terms = \Drupal::service('entity_type.manager')->getStorage("taxonomy_term")->loadTree($config['search_facets']);
    foreach ($terms as $term) {
      $facets[$term->name] = $term->name;
    }

    return [
      '#theme' => 'genlyd_search_page',
      '#facets' => $facets,
      '#attached' => [
        'library' => [
          'genlyd_search/search',
        ],
        'drupalSettings' => [
          'genlyd_search' => [
            'endpoint' => '/api/search',
            'index' => $config['search_index'],
            'search_facet_index' => $config['search_facets'],
            'search_limit' => $config['search_limit'],
            'search_fields' => array_values($config['search_fields']),
          ]
        ]
      ],
    ];
  }
}