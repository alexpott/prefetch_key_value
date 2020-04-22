<?php


namespace Drupal\prefetch_key_value;

use Drupal\Core\KeyValueStore\KeyValueStoreInterface;
use Drupal\Core\KeyValueStore\StorageBase;

class PrefetchStorage extends StorageBase {

  /**
   * @var \Drupal\Core\KeyValueStore\KeyValueStoreInterface
   */
  protected $storage;

  protected $cache;

  protected $prefetchKeys = [];

  public function __construct(KeyValueStoreInterface $storage, $prefetch_keys) {
    parent::__construct($storage->getCollectionName());
    $this->storage = $storage;
    $this->prefetchKeys = $prefetch_keys;
  }

  /**
   * {@inheritdoc}
   */
  public function has($key) {
    return $this->storage->has($key);
  }

  /**
   * {@inheritdoc}
   */
  public function getMultiple(array $keys) {
    $common_keys = array_intersect($keys, $this->prefetchKeys);
    if ($common_keys) {
      $values = array_intersect_key($this->getCommonKeys(), array_flip($common_keys));
      $keys_to_get = array_diff($keys, $this->prefetchKeys);
    }
    else {
      $keys_to_get = $keys;
      $values = [];
    }

    if (!empty($keys_to_get)) {
      $values = $values + $this->storage->getMultiple($keys_to_get);
    }

    return array_replace(array_flip($keys), $values);
  }

  /**
   * {@inheritdoc}
   */
  public function getAll() {
    return $this->storage->getAll();
  }

  /**
   * {@inheritdoc}
   */
  public function set($key, $value) {
    return $this->storage->set($key, $value);
  }

  /**
   * {@inheritdoc}
   */
  public function setIfNotExists($key, $value) {
    return $this->storage->setIfNotExists($key, $value);
  }

  /**
   * {@inheritdoc}
   */
  public function rename($key, $new_key) {
    return $this->storage->rename($key, $new_key);
  }

  /**
   * {@inheritdoc}
   */
  public function deleteMultiple(array $keys) {
    return $this->storage->deleteMultiple($keys);
  }

  /**
   * {@inheritdoc}
   */
  public function deleteAll() {
    return $this->storage->deleteAll();
  }

  protected function getCommonKeys() {
    if (!$this->cache) {
      $this->cache = $this->storage->getMultiple($this->prefetchKeys);
    }
    return $this->cache;
  }
}
