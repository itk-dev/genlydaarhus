<?php
/**
 * @file
 * New search API processer for geo-coding content.
 */

namespace Drupal\genlyd_search\Plugin\search_api\processor;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\search_api\Datasource\DatasourceInterface;
use Drupal\search_api\Item\ItemInterface;
use Drupal\search_api\Processor\ProcessorPluginBase;
use Drupal\search_api\Utility\Utility;
use Drupal\genlyd_search\Plugin\search_api\processor\Property\GeoCoderFieldProperty;

/**
 * Adds geo-encoding to the search index.
 *
 * @SearchApiProcessor(
 *   id = "geo_coder_field",
 *   label = @Translation("GeoCoder field"),
 *   description = @Translation("Add geo information based on fields."),
 *   stages = {
 *     "add_properties" = 20,
 *   },
 *   locked = true,
 *   hidden = true,
 * )
 */
class GeoCoderField extends ProcessorPluginBase {

  /**
   * {@inheritdoc}
   */
  public function getPropertyDefinitions(DatasourceInterface $datasource = NULL) {
    $properties = [];

    if (!$datasource) {
      $definition = [
        'label' => $this->t('GeoCoder field'),
        'description' => $this->t('An aggregation of multiple other fields into lat/lon.'),
        'type' => 'string',
        'processor_id' => $this->getPluginId(),
      ];
      $properties['geo_coder_field'] = new GeoCoderFieldProperty($definition);
    }

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public function addFieldValues(ItemInterface $item) {
    $fields = $this->index->getFields();

    $geo_coder_fields = $this->getFieldsHelper()
      ->filterForPropertyPath($fields, NULL, 'geo_coder_field');

    $required_properties_by_datasource = [
      NULL => [],
      $item->getDatasourceId() => [],
    ];

    foreach ($geo_coder_fields as $field) {
      foreach ($field->getConfiguration()['fields'] as $combined_id) {
        list($datasource_id, $property_path) = Utility::splitCombinedId($combined_id);
        $required_properties_by_datasource[$datasource_id][$property_path] = $combined_id;
      }
    }

    $property_values = $this->getFieldsHelper()
      ->extractItemValues([$item], $required_properties_by_datasource)[0];

    $geo_coder_fields = $this->getFieldsHelper()
      ->filterForPropertyPath($item->getFields(), NULL, 'geo_coder_field');

    foreach ($geo_coder_fields as $geo_coder_field) {
      $values = [];
      $configuration = $geo_coder_field->getConfiguration();
      foreach ($configuration['fields'] as $combined_id) {
        if (!empty($property_values[$combined_id])) {
          $values = array_merge($values, $property_values[$combined_id]);
        }
      }

      // Default value.
      $value = '';

      // Use cache as this is a processed field that will be called both on
      // indexing and searching.
      $cid = md5(implode('', $values));
      $cache = \Drupal::cache()->get($cid);
      if ($cache) {
        $value = $cache->data;
      }
      else {
        // Geo-encode fields.
        $config = \Drupal::getContainer()->get('genlyd_search.config')->getAll();

        // Load geo-coder service and set configuration.
        $geocoder = \Drupal::service('geocoder');
        $plugins = ['googlemaps', 'bingmaps'];
        $options = [
          'googlemaps' => [
            'useSsl' => TRUE,
            'apiKey' => $config['google_api_key'],
          ],
          'bingsearch' => [],
        ];
        $addressCollection = $geocoder->geocode(implode($values, ','), $plugins, $options);
        if ($addressCollection) {
          $latitude = $addressCollection->first()
            ->getCoordinates()
            ->getLatitude();
          $longitude = $addressCollection->first()
            ->getCoordinates()
            ->getLongitude();

          $value = $longitude . ',' . $latitude;
        }

        \Drupal::cache()->set($cid, $value, CacheBackendInterface::CACHE_PERMANENT, array('search_api', 'geocoder'));
      }

      $geo_coder_field->addValue($value);
    }
  }

}
