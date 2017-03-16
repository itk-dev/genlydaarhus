<?php
/**
 * @file
 * Defines new CSV response object.
 */

namespace Drupal\genlyd_carto\Render;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * Defines new CSV response object.
 *
 * @see \Symfony\Component\HttpFoundation\Response
 */
class CsvResponse extends Response {

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
    $this->headers->set('Content-Type', 'application/CSV');

    if (!empty($content)) {
      if (empty($content['header']) || empty($content['rows'])) {
        throw new \UnexpectedValueException('The content do not have the right format (header and rows keys missing).');
      }

      $this->content = $content;
    }
    else {
      $this->content = [
        'header' => [],
        'rows' => [],
      ];
    }
  }

  /**
   * Add "description/header" row to the CSV output.
   *
   * @param array $header
   *   The headers as a string array.
   *
   * @return $this
   */
  public function setHeaderRow(array $header) {
    $this->content['header'] = $header;

    return $this;
  }

  /**
   * Adds data row to the CSV output.
   *
   * @param array $row
   *   String array with the values.
   *
   * @return $this
   */
  public function addRow(array $row) {
    array_push($this->content['rows'], $row);

    return $this;
  }

  /**
   * Sends content for the current web response.
   *
   * @return $this
   */
  public function sendContent() {
    if (empty($this->content['header']) || empty($this->content['rows'])) {
      throw new \UnexpectedValueException('The CSV content is not filled in correctly');
    }

    $output = '"' . implode($this->content['header'], '","') . '"' . "\r\n";
    foreach ($this->content['rows'] as $row) {
      $output .= '"' . implode($row, '","') . '"' . "\r\n";
    }

    echo $output;

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
