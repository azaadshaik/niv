<?php

namespace Drupal\niv\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Implements an example form.
 */
class adminNewSuggestionsForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'admin_new_suggestions_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state,$extra= null) {

					$connection = \Drupal::database();
					$query = $connection->select('niv_suggestion_mapping', 'n');
					$query->condition('n.profile_id',$extra['profile_id'] );
					$query->condition('n.submission_id', $extra['submission_id']);
                    $query->condition('n.suggestion_status', 1, '=');
					$query->fields('n', ['id', 'suggestion_id']);
					$results = $query->execute()->fetchAllKeyed(0,1);
					
					//dump($extra);
					
    foreach($extra['suggestions'] as $key=>$suggestion){

      //$label = $node->field_my_field->getSetting('allowed_values')[$node->field_my_field->value];  
      //dump($suggestion->field_suggestion_section->getSetting('allowed_values'));
      //dump($suggestion->field_suggestion_section->getString());

      //die;
      $sectionName = $suggestion->field_suggestion_section->getSetting('allowed_values')[$suggestion->field_suggestion_section->getString()];
      $attributeName = $suggestion->field_section_attribute->getSetting('allowed_values')[$suggestion->field_section_attribute->getString()];

      $elements[$sectionName][$key]['name'] = $sectionName;
      $elements[$sectionName][$key]['attribute'] = $attributeName;
      $elements[$sectionName][$key]['suggestion'] = $suggestion->field_new_suggestion->getString();
      $elements[$sectionName][$key]['attribute_key'] = $suggestion->field_section_attribute->getString();

    }

    //dump($elements);
    
    foreach($elements as $key1=>$element){
       
    $form['section'][$key1] = array(
      '#type' => 'details',
      '#title' => $this->t($key1),
    );
    
    foreach($element as $key2=>$ele){

        
        $form['section'][$key1][$ele['attribute_key']] = array(

            '#type' => 'markup',
            '#markup' => '<h6 class="question-attribute">'.$this->t($ele['attribute']).'</h6>',
        );
        
    }
         
  }
  foreach($elements as $key1=>$element){

   
    // dump($form);
    // echo $key1;
    // dump($element);
     foreach($element as $k=>$v){

        if(in_array($k,$results)){
            $default = $k;
        
        }
  $form['section'][$key1][$v['attribute_key']][$k] = array(
    '#type' => 'checkbox',
    '#title' => $this->t($v['suggestion']),
    '#name'=>'suggestions[]',
    '#attributes'=> ['class'=>['actual-question']],
    '#return_value'=>$k,
    '#default_value' => $default,
  );
}
}



    // dump($form);
    // dump($elements);
    // die;

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

    $connection = \Drupal::database();
    $query = $connection->select('niv_suggestion_mapping', 'n');
    $query->condition('n.profile_id',$profile_id );
    $query->condition('n.submission_id', $submission_id);
    $query->fields('n', ['id', 'suggestion_id']);
    $existingSuggestions = $query->execute()->fetchAllKeyed(0,1);
    $existingSuggestionIds = array_values($existingSuggestions);
    
    if(!empty($existingSuggestionIds)){
    $query = $connection->update('niv_suggestion_mapping');
    $query->fields(['suggestion_status'=>0 ]);
    $query->condition('suggestion_id',$existingSuggestionIds,'IN' );
    $query->condition('profile_id',$profile_id,'=' );
    $query->execute();
    
    
  }


     
    if(!empty($selectedSuggestions)){


      //$connection = \Drupal\Core\Database\Database::getConnection() ;
      foreach($selectedSuggestions as $suggestion){
        $connection->insert('niv_suggestion_log')->fields([ 'profile_id' => $profile_id, 'submission_id' => $submission_id, 'suggestion_id' => $suggestion ]) ->execute();
    

       if(in_array($suggestion,$existingSuggestionIds)){
        $query = $connection->update('niv_suggestion_mapping')->fields(['suggestion_status'=>1 ])
        ->condition('suggestion_id',$suggestion,'=' )
        ->condition('profile_id',$profile_id,'=' )
        ->execute();
       }
       else{
        //dump([ 'profile_id' => $profile_id, 'submission_id' => $submission_id, 'suggestion_id' => $suggestion ]);
        //die;
        $connection->insert('niv_suggestion_mapping')->fields([ 'profile_id' => $profile_id, 'submission_id' => $submission_id, 'suggestion_id' => $suggestion ]) ->execute();
       }
      }
    }
   
    $this->messenger()->addStatus($this->t('Suggestions added successfully'));
    $url = Url::fromRoute('entity.node.canonical', ['node' => $profile_id ]);
    $form_state->setRedirectUrl($url);
    
  }

}
