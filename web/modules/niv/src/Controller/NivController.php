<?php

namespace Drupal\niv\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Entity\webform;

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

  public function loadAssessmentForm($profileId,$webformId) {



    $entity = \Drupal::entityTypeManager()->getStorage('node')->load($profileId);
    
$webform = \Drupal::entityTypeManager()->getStorage('webform')->load($webformId);

 $draft = \Drupal::entityTypeManager()
  ->getStorage('webform_submission')
  ->loadDraft($webform,$entity);
  //dump($draft);
  if(!empty($draft)){
    $webform = $draft->getWebform();

  }
  
  //dump($webform);

  //die;



// $submissions = \Drupal::entityTypeManager()
//   ->getStorage('webform_submission')
//   ->loadFromToken('wi5zi2QrYIlfQVQAGRG0EiQ6kLdw0905VglSxZv05NA',$webform);
//   $webform = $submissions->getWebform();
  
   
    //$webform = \Drupal\webform\Entity\Webform::load('WEBFORM_ID');
    $build['content'] = [
      '#type' => 'webform',
      '#webform' => $webform,
      ];


    return $build;
  }



}
