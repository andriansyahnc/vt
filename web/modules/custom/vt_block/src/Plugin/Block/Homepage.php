<?php

namespace Drupal\vt_block\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'Homepage' block.
 *
 * @Block(
 *  id = "homepage",
 *  admin_label = @Translation("Homepage"),
 * )
 */
class Homepage extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * @inheritDoc
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $plugin = new static($configuration, $plugin_id, $plugin_definition);
    $plugin->setEntityTypeManager($container->get('entity_type.manager'));
    return $plugin;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'title' => 'Welcome to Vox Teneo',
      'subtitle' => 'We create effective solutions',
    ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form['title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Title'),
      '#default_value' => $this->configuration['title'],
      '#maxlength' => 64,
      '#size' => 64,
      '#weight' => '0',
    ];
    $form['subtitle'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Subtitle'),
      '#default_value' => $this->configuration['subtitle'],
      '#maxlength' => 64,
      '#size' => 64,
      '#weight' => '1',
    ];
    $form['image'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Image'),
      '#default_value' => $this->configuration['image'],
      '#weight' => '0',
      '#upload_location' => 'public://homepage_block/',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['title'] = $form_state->getValue('title');
    $this->configuration['subtitle'] = $form_state->getValue('subtitle');
    $this->configuration['image'] = $form_state->getValue('image');
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $build['#theme'] = 'homepage';
    $build['#content']['title'] = $this->configuration['title'];
    $build['#content']['subtitle'] = $this->configuration['subtitle'];

    if (isset($this->configuration['image'][0])) {
      $file_storage = $this->entityTypeManager->getStorage('file');
      /** @var \Drupal\file\FileInterface $file */
      $file = $file_storage->load($this->configuration['image']['0']);
      $build['#content']['image'] = file_create_url($file->getFileUri());
    }

    return $build;
  }

  /**
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *
   * @return $this
   */
  public function setEntityTypeManager(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
    return $this;
  }

}
