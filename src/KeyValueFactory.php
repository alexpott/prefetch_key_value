<?php

namespace Drupal\prefetch_key_value;

use Drupal\Core\KeyValueStore\KeyValueFactoryInterface;

class KeyValueFactory implements KeyValueFactoryInterface {

  /**
   * @var \Drupal\Core\KeyValueStore\KeyValueFactoryInterface
   */
  protected $factory;

  protected $prefetchKeys = [
    // Tour
    //'routes.*.routes_name:node.add',
    'state' => [
      'system.maintenance_mode',
      'twig_extension_hash_prefix',
      'system.private_key',
      'system.css_js_query_string',
      'system.cron_last',
    ],
//    'config.entity.key_store.block' => [
//      'theme:seven',
//    ],
    'entity.definitions.installed' => [
      'user.entity_type',
      'user.field_storage_definitions',
      'node.entity_type',
      'node.field_storage_definitions',
      'taxonomy_term.entity_type',
      'taxonomy_term.field_storage_definitions',
      'comment.entity_type',
      'comment.field_storage_definitions',
      'shortcut.entity_type',
      'shortcut.field_storage_definitions',
    ],
  ];

  public function __construct(KeyValueFactoryInterface $factory) {
    $this->factory = $factory;
  }

  /**
   * {@inheritdoc}
   */
  public function get($collection) {
    $storage = $this->factory->get($collection);
    if (isset($this->prefetchKeys[$collection])) {
      return new PrefetchStorage($storage, $this->prefetchKeys[$collection]);
    }
    return $storage;
  }

}
