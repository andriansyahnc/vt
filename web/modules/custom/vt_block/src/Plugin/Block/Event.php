<?php

namespace Drupal\vt_block\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Link;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'Event' block.
 *
 * @Block(
 *  id = "event",
 *  admin_label = @Translation("Event"),
 * )
 */
class Event extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Drupal\Core\Entity\EntityTypeManagerInterface definition.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = new static($configuration, $plugin_id, $plugin_definition);
    $instance->entityTypeManager = $container->get('entity_type.manager');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $build['#theme'] = 'event';

    $node_storage = $this->entityTypeManager->getStorage('node');
    $node_query = $node_storage->getQuery();

    $now = $node_query->andConditionGroup();
    $now->condition('event_date.value', date('Y-m-d'), '<=');
    $now->condition('event_date.end_value', date('Y-m-d'), '>=');

    $date_condition = $node_query->orConditionGroup();
    $date_condition->condition('event_date.value', date('Y-m-d'), '>=');

    $date_condition->condition($now);

    $node_query->condition($date_condition);
    $node_query->range(0, 2);

    $nids = $node_query->execute();

    /** @var \Drupal\node\NodeInterface[] $nodes */
    $nodes = $node_storage->loadMultiple($nids);

    foreach ($nodes as $node) {
      $event_date = $node->get('event_date')->value . ' - ' . $node->get('event_date')->end_value;
      /** @var \Drupal\file\FileInterface $file */
      $file = $node->get('event_image')->entity;
      $event_image = file_create_url($file->getFileUri());
      $build['content'][$node->id()] = [
        'event_title' => $node->getTitle(),
        'event_date' => $event_date,
        'event_description' => $node->get('event_description')->value,
        'event_image' => $event_image,
        'link' => Link::createFromRoute('entity.node.canonical', ['node' => $node->id()]),
      ];
    }

    return $build;
  }

}
