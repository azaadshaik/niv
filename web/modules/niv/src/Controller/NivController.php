<?php

namespace Drupal\niv\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Returns responses for niv routes.
 */
class NivController extends ControllerBase {

  /**
   * Builds the response.
   */
  public function build() {

    $build['content'] = [
      '#type' => 'item',
      '#markup' => $this->t('It works!'),
    ];

    return $build;
  }

}
