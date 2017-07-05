<?php
/**
 * @file
 * Contains Drupal\itk_footer\Form\ITKFooterContentForm.
 */

namespace Drupal\genlyd_search\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\taxonomy\Entity\Vocabulary;

/**
 * Class ITKFooterContentForm
 *
 * @package Drupal\genlyd_search\Form
 */
class GenlydSearchSettingsForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'genlyd_search_settings';
  }

  /**
   * Get key/value storage for base config.
   *
   * @return object
   */
  private function getBaseConfig() {
    return \Drupal::getContainer()->get('genlyd_search.config');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->getBaseConfig();

    $form['wrapper'] = array(
      '#type' => 'details',
      '#title' => t('Maps API keys'),
      '#open' => TRUE,
    );

    $form['wrapper']['google_api_key'] = array(
      '#type' => 'textfield',
      '#title' => t('Google API key'),
      '#default_value' => $config->get('google_api_key'),
      '#required' => TRUE,
    );

    $form['wrapper_search'] = array(
      '#type' => 'details',
      '#title' => t('Search settings'),
      '#open' => TRUE,
    );

    $index_options = array();
    $search_api_indexes = \Drupal::entityManager()->getStorage('search_api_index')->loadMultiple();
    /* @var  $search_api_index \Drupal\search_api\IndexInterface */
    foreach ($search_api_indexes as $search_api_index) {
      $index_options[$search_api_index->id()] = $search_api_index->label();
    }

    $default_index = $config->get('search_index');
    $form['wrapper_search']['search_index'] = array(
      '#type' => 'select',
      '#title' => t('Search index'),
      '#description' => t('The search API index to use.'),
      '#options' => $index_options,
      '#default_value' => isset($default_index) ? $default_index : reset($index_options),
      '#required' => TRUE,
    );

    $default = $config->get('search_limit');
    $form['wrapper_search']['search_limit'] = array(
      '#type' => 'textfield',
      '#title' => t('Limit'),
      '#description' => t('The number of results to show pr. page.'),
      '#default_value' => isset($default) ? $config->get('search_limit') : 10,
      '#required' => TRUE,
    );

    $searched_fields = [];
    $index = reset($search_api_indexes);
    if (isset($default_index)) {
      foreach ($search_api_indexes as $search_api_index) {
        if ($search_api_index->id() == $default_index) {
          $index = $search_api_index;
          break;
        }
      }
    }
    $fields_info = $index->getFields();
    foreach ($index->getFulltextFields() as $field_id) {
      $searched_fields[$field_id] = $fields_info[$field_id]->getPrefixedLabel();
    }

    $form['wrapper_search']['search_fields'] = array(
      '#type' => 'select',
      '#multiple' => TRUE,
      '#options' => $searched_fields,
      '#size' => min(4, count($searched_fields)),
      '#title' => $this->t('Searched fields'),
      '#description' => $this->t('Select the fields that will be searched. If no fields are selected, all available fulltext fields will be searched.'),
      '#default_value' => $config->get('search_fields'),
    );

    $fields_info = $index->getFields();
    $sort_fields = [];
    foreach ($fields_info as $field) {
      $sort_fields[$field->getFieldIdentifier()] = $field->getPrefixedLabel();
    }

    $form['wrapper_search']['sort'] = array(
      '#type' => 'select',
      '#options' => $sort_fields,
      '#title' => $this->t('Sort field'),
      '#description' => $this->t('Select the field that will be sorted on.'),
      '#default_value' => $config->get('search_sort'),
    );

    $vocabulary_options = [];
    $vocabularies = Vocabulary::loadMultiple();
    foreach ($vocabularies as $vocabulary) {
      $vocabulary_options[$vocabulary->id()] = $vocabulary->label();
    }

    $form['wrapper_search']['search_facets'] = array(
      '#type' => 'select',
      '#options' => $vocabulary_options,
      '#title' => $this->t('Searched facets'),
      '#description' => $this->t('Select the vocabulary to use for facet search.'),
      '#default_value' => $config->get('search_facets'),
    );

    $form['actions'] = array('#type' => 'actions');
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#attributes' => ['class' => ['button--primary']],
      '#value' => t('Save content'),
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    drupal_set_message('Settings saved. If you changed the Google API key please re-index all search content.');

    // Set the rest of the configuration values.
    $this->getBaseConfig()->set('google_api_key', $form_state->getValue('google_api_key'));
    $this->getBaseConfig()->set('search_index', $form_state->getValue('search_index'));
    $this->getBaseConfig()->set('search_limit', $form_state->getValue('search_limit'));
    $this->getBaseConfig()->set('search_fields', $form_state->getValue('search_fields'));
    $this->getBaseConfig()->set('search_facets', $form_state->getValue('search_facets'));
    $this->getBaseConfig()->set('search_sort', $form_state->getValue('sort'));
  }
}
