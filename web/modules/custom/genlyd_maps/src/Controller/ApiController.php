<?php
/**
 * @file
 * Contains \Drupal\genlyd_maps\Controller\ApiController.
 */

namespace Drupal\grundsalg_maps\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\genlyd_maps\Render\GeoJsonResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Core\Url;

/**
 * Class ApiController.
 *
 * @package Drupal\grundsalg_maps\Controller
 */
class ApiController extends ControllerBase {

  public function activates() {
    $storage = \Drupal::entityTypeManager()->getStorage('node');
    $ids = $storage->getQuery()
      ->condition('type', 'activity')
      ->condition('status', 1)
      ->execute();
    $activities = $storage->loadMultiple($ids);

    $response = new GeoJsonResponse();


    return $response;

  }
}
