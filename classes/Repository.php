<?php

/**
 * Created by Gorlum 10.02.2017 0:07
 */

/**
 * Class Repository
 *
 * Holds current entity objects
 */
class Repository {

  /**
   * @var \Common\ContainerPlus $repository
   */
  protected $repository;

  /**
   * @param TextModel  $model
   * @param int|string $id
   *
   * @return TextEntity
   */
  public function getById($model, $id) {

    // TODO - is_object()

    $entityIndex = get_class($model) . '\\' . $id;
    if (!isset($this->repository[$entityIndex])) {
      $entity = $model->loadById($id);
      if ($entity && !$entity->isEmpty()) {
        $this->repository[$entityIndex] = $entity;
      }
    } else {
      $entity = $this->repository[$entityIndex];
    }

    return $entity;
  }

}
