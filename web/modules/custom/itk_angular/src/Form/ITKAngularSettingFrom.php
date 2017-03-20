<?php
/**
 * @file
 * Handles configuration and conditions for using Angular.
 */

namespace Drupal\itk_angular\Form;

use Drupal\Component\Plugin\Factory\FactoryInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use \Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ITKAngularSettingFrom.
 *
 * The administration configuration/settings form.
 *
 * @package Drupal\itk_angular\Form
 */
class ITKAngularSettingFrom extends ConfigFormBase {

  protected $conditionPath;
  protected $conditionRole;
  protected $conditionNodeType;

  /**
   * The condition plugin manager.
   *
   * @var \Drupal\Core\Condition\ConditionManager
   */
  protected $manager;

  /**
   * ITKAngularSettingFrom constructor.
   */
  public function __construct(ConfigFactoryInterface $config_factory, FactoryInterface $plugin_factory) {
    parent::__construct($config_factory);
    $this->manager = $plugin_factory;
    $this->conditionPath = $plugin_factory->createInstance('request_path');
    $this->conditionRole = $plugin_factory->createInstance('user_role');
    $this->conditionNodeType = $plugin_factory->createInstance('node_type');
  }

  /**
   * {@inheritdoc}
   *
   * Inject the two services used with conditional plugins.
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('plugin.manager.condition')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'itk_angular_admin_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'itk_angular.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('itk_angular.settings');

    // Set the default condition configuration.
    $this->conditionPath->setConfiguration($config->get('request_path') ? $config->get('request_path') : []);
    $this->conditionRole->setConfiguration($config->get('user_role') ? $config->get('user_role') : []);
    $this->conditionNodeType->setConfiguration($config->get('node_type') ? $config->get('node_type') : []);

    $form['app_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Angular app name'),
      '#description' => $this->t('Enter the name for the angular application.'),
      '#default_value' => $config->get('appName') ? $config->get('appName') : 'ITKAngular',
    ];

    $form['visibility'] = [
      '#type' => 'details',
      '#title' => $this->t('Visibility'),
      '#description' => $this->t('Control where Angular sould be injected into the site.'),
      '#open' => TRUE,
    ];

    $form['visibility']['paths'] = [
      '#type' => 'details',
      '#title' => $this->t('Pages'),
      '#open' => FALSE,
    ];
    $form['visibility']['paths'] += $this->conditionPath->buildConfigurationForm([], $form_state);

    $form['visibility']['node_type'] = [
      '#type' => 'details',
      '#title' => $this->t('Content types'),
      '#open' => FALSE,
    ];
    $form['visibility']['node_type'] += $this->conditionNodeType->buildConfigurationForm([], $form_state);

    $form['visibility']['roles'] = [
      '#type' => 'details',
      '#title' => $this->t('Roles'),
      '#open' => FALSE,
    ];
    $form['visibility']['roles'] += $this->conditionRole->buildConfigurationForm([], $form_state);

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('itk_angular.settings');

    // Validate the conditions.
    $this->conditionPath->submitConfigurationForm($form, $form_state);
    $this->conditionNodeType->submitConfigurationForm($form, $form_state);
    $this->conditionRole->submitConfigurationForm($form, $form_state);

    $config->set('appName', $form_state->getValue('app_name'));
    $config->set('request_path', $this->conditionPath->getConfiguration());
    $config->set('user_role', $this->conditionRole->getConfiguration());
    $config->set('node_type', $this->conditionNodeType->getConfiguration());
    $config->save();

    parent::submitForm($form, $form_state);
  }
}