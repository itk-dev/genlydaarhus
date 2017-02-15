<?php

namespace Drupal\itk_fime_field\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Plugin implementation of the 'time_type' field type.
 *
 * @FieldType(
 *   id = "time_type",
 *   label = @Translation("Time"),
 *   module = "itk_time_field",
 *   description = @Translation("Supplies a time field"),
 *   default_widget = "time_widget",
 *   default_formatter = "time_formatter"
 * )
 */
class TimeType extends FieldItemBase {
  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return array(
      'columns' => array(
        'value' => array(
          'type' => 'datetime',
          'not null' => TRUE,
        ),
      ),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    $value = $this->get('value')->getValue();
    return $value === NULL || $value === '';
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties['value'] = DataDefinition::create('datetime')
      ->setLabel(t('Time'));

    return $properties;
  }
}
