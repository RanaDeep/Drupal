<?php

namespace Drupal\food_delivery_node_controller\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Controller for Categories page.
 */
class CatalogCategoriesController extends ControllerBase {

  /**
   * Returns a node for Catalog Categories view.
   *
   * @param string $category
   *   Category from URL.
   *
   * @return array
   *   Returns render array.
   */
  public function getContent(string $category) {
    try {
      /** @var \Drupal\Core\Url $url_object */
      $url_object = \Drupal::service('path.validator')->getUrlIfValid('/categories');
      $route_parameters = $url_object->getrouteParameters();
      $nid = $route_parameters['node'];
      $entity_type = 'node';
      $view_builder = \Drupal::entityTypeManager()->getViewBuilder($entity_type);
      $storage = \Drupal::entityTypeManager()->getStorage($entity_type);
      $node = $storage->load($nid);
      $build = $view_builder->view($node);
      $output = render($build);
      $build['node'] = [
        '#type' => 'markup',
        '#markup' => $output,
      ];
      return $build;
    }
    catch (\Exception $e) {
      return [];
    }

  }

}
