<?php

namespace Drupal\niv\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Implements an example form.
 */
class adminSuggestionsForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'admin_suggestions_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state,$extra= null) {

					$connection = \Drupal::database();
					$query = $connection->select('niv_suggestion_mapping', 'n');
					$query->condition('n.profile_id',$extra['profile_id'] );
					$query->condition('n.submission_id', $extra['submission_id']);
					$query->fields('n', ['id', 'suggestion_id']);
					$results = $query->execute()->fetchAllKeyed(0,1);
					
					
					
    foreach($extra['suggestions'] as $key=>$suggestion){


      $sectionId = $suggestion->get('field_section')->referencedEntities()[0]->get('tid')->value;
      $sectionName = $suggestion->get('field_section')->referencedEntities()[0]->get('name')->value;

      $elements[$sectionName][$key]['name'] = $sectionName;
      $elements[$sectionName][$key]['suggestion_name'] = $suggestion->getTitle();

    }
   // dump($elements);
    foreach($elements as $key1=>$element){
    $form['section'][$key1][] = array(
      '#type' => 'markup',
      '#markup' => '<h6>'.$this->t($key1).'</h6>',
    );
    foreach($element as $key=>$value){

		if(in_array($key,$results)){
			$default = $key;
		
		}
      $form['section'][$key1][]['suggestion'] = array(
        '#type' => 'checkbox',
        '#title' => $this->t($value['suggestion_name']),
        '#name'=>'suggestions[]',
        '#return_value'=>$key,
		'#default_value' => $default,
      );
    }
    
  }

  $form['profile_id'] = array(

    '#type' => 'hidden',
   
    '#name'=>'profile_id',
    '#value'=>$extra['profile_id'],
  );
  $form['submission_id'] = array(

    '#type' => 'hidden',
   
    '#name'=>'submission_id',
    '#value'=>$extra['submission_id'],
  );

  //dump($form);
     // $sectionId = 
      //$node->get($field)->referencedEntities();
      //$form['section'][]
      
   /* $form['suggestion'][$key] = array(
      '#type' => 'checkbox',
      '#title' => $this->t($suggestion->getTitle()),
      '#name'=>'suggestions[]',
      '#return_value'=>$key
    );*/
  
  //dump($elements);
    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
      '#button_type' => 'primary',
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  /*public function validateForm(array &$form, FormStateInterface $form_state) {
    if (strlen($form_state->getValue('phone_number')) < 3) {
      $form_state->setErrorByName('phone_number', $this->t('The phone number is too short. Please enter a full phone number.'));
    }
  }*/

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

   
    $selectedSuggestions = $form_state->getUserInput()['suggestions'];
    $profile_id = $form_state->getUserInput()['profile_id'];
    $submission_id = $form_state->getUserInput()['submission_id'];
    
    if(!empty($selectedSuggestions)){
      $connection = \Drupal\Core\Database\Database::getConnection() ;
      foreach($selectedSuggestions as $suggestion){

       
        $connection->insert('niv_suggestion_mapping')->fields([ 'profile_id' => $profile_id, 'submission_id' => $submission_id, 'suggestion_id' => $suggestion ]) ->execute();

      }
    }
   
    $this->messenger()->addStatus($this->t('Suggestions added successfully'));
    $url = Url::fromRoute('entity.node.canonical', ['node' => $profile_id ]);
    $form_state->setRedirectUrl($url);
    
  }

}
