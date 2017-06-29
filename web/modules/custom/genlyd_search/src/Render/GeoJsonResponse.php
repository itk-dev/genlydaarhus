<?php
/**
 * @file
 * Defines new CSV response object.
 */

namespace Drupal\genlyd_search\Render;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * Defines new CSV response object.
 *
 * @see \Symfony\Component\HttpFoundation\Response
 */
class GeoJsonResponse extends Response {

  /**
   * Constructor.
   *
   * @param mixed $content
   *   The response content, see setContent()
   * @param int $status
   *   The response status code
   * @param array $headers
   *   An array of response headers
   *
   * @throws \InvalidArgumentException When the HTTP status code is not valid
   */
  public function __construct($content = array(), $status = 200, $headers = array()) {
    $this->headers = new ResponseHeaderBag($headers);
    $this->setStatusCode($status);
    $this->setProtocolVersion('1.0');
    $this->headers->set('Content-Type', 'application/json');

    if (!empty($content)) {
      if (empty($content['features']) || !isset($content['type']) || !isset($content['crs'])) {
        throw new \UnexpectedValueException('The content do not have the right format.');
      }

      $this->content = $content;
    }
    else {
      $this->content = [
        'type' => 'FeatureCollection',
        'features' => [],
      ];
    }
  }

  /**
   * Add GeoJson "Point" feature.
   *
   * @param float $lat
   *   The latitude for the feature.
   * @param float $lng
   *   The longitude for the feature.
   * @param array $metadata
   *   Metadata to display in popups.
   *
   * @return $this
   */
  public function addPoint(float $lat, float $lng, array $metadata) {
    array_push($this->content['features'], [
      'type' => 'Feature',
      'geometry' => [
        'type' => 'Point',
        'coordinates' => [$lng, $lat],
      ],
      'properties' => $metadata,
    ]);

    return $this;
  }

  /**
   * Sends content for the current web response.
   *
   * @return $this
   */
  public function sendContent() {
    echo json_encode($this->content);

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setContent($content) {
    if (null !== $content && !is_array($content)) {
      throw new \UnexpectedValueException(sprintf('The Response content must be a string or object implementing __toString(), "%s" given.', gettype($content)));
    }

    $this->content = $content;

    return $this;
  }
}
