<?php

/**
 * Created by Gorlum 09.02.2017 23:27
 */
class TextModel {

  /**
   * @var Repository $repository
   */
  protected $repository;

  /**
   * @var Storage $storage
   */
  protected $storage;

  /**
   * @var TextRecordDescription $textRecordDescription
   */
  protected $textRecordDescription;

  protected $entityClass = 'TextEntity';

  public function __construct() {
    $this->textRecordDescription = new TextRecordDescription();
    $this->repository = classSupernova::$gc->repository;
    $this->storage = classSupernova::$gc->storage;
  }


  /**
   * Gets text entity by id
   *
   * @param int|string $textId
   *
   * @return TextEntity
   */
  public function getById($textId) {
    $entity = $this->repository->getById($this, $textId);

    return $entity;
  }

  /**
   * Load from Storage
   *
   * Operates with arrays from different tables
   *
   * @param int|string $textId
   *
   * @return TextEntity
   */
  public function loadById($textId) {
    $array = $this->storage->loadById($this->textRecordDescription, $textId);

    /**
     * @var TextEntity $text
     */
    $text = new $this->entityClass();
    if (is_array($array) && !empty($array)) {
      $this->parseArray($text, $array);
    }

    return $text;
  }


  /**
   * @param TextEntity $entity
   * @param array      $array
   */
  public function parseArray($entity, $array) {
    // Basic parsing
    foreach ($array as $key => $value) {
      $entity->$key = $value;
    }
  }


  /**
   * Get next text in chain with resolving alternate next
   *
   * @param int|string $textId
   *
   * @return TextEntity
   */
  public function next($textId) {
    $currentText = classSupernova::$gc->textModel->getById($textId);
    if (!$currentText->isEmpty() && $currentText->next) {
      $next = classSupernova::$gc->textModel->getById($currentText->next);
      if ($next->isEmpty() && $currentText->next_alt) {
        $next = classSupernova::$gc->textModel->getById($currentText->next_alt);
      }
    }

    if(!isset($next)) {
      $next = new TextEntity();
    }

    return $next;
  }

  /**
   * @param int|string $textId
   *
   * @return TextEntity
   */
  public function prev($textId) {
    $currentText = classSupernova::$gc->textModel->getById($textId);
    if (!$currentText->isEmpty() && $currentText->prev) {
      $prev = classSupernova::$gc->textModel->getById($currentText->prev);
    }

    if(!isset($prev)) {
      $prev = new TextEntity();
    }

    return $prev;
  }

}
