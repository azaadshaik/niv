<?php

namespace Drupal\niv\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\node\Entity\Node;

/**
 * Implements an example form.
 */
class profileSuggestionsForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'profile_suggestions_form';
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
					
					 $form['date'] = array(
				  '#type' => 'date',
				  '#title' => $this
					->t('Date'),
				  
				  '#required' => TRUE,
				  
				);
					
    if(!empty($results)){    
    foreach($results as $suggestion_id){

      
	
	$suggestion = Node::load($suggestion_id);
  
 
	 $form[$suggestion_id] = array(
				  '#type' => 'radios',
				  '#title' => $this
					->t($suggestion->get('title')->value),
				  '#name' => 'suggestions[]', 	
				  
				  '#required' => TRUE,
				  '#options' => array(
					1 => $this
					  ->t('1'),
					2 => $this
					  ->t('2'),
					3 => $this
					  ->t('3'),
					4 => $this
					  ->t('4'),
					5 => $this
					  ->t('5'),
				  ),
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

   
  
   $values= $form_state->getValues();
   //dump($values);
   //die;
   $connection = \Drupal::database();
					$query = $connection->select('niv_suggestion_mapping', 'n');
					$query->condition('n.profile_id',$values['profile_id'] );
					$query->condition('n.submission_id', $values['submission_id']);
					$query->fields('n', ['id', 'suggestion_id']);
					$results = $query->execute()->fetchAllKeyed(0,1);
	
	 
   		$profile_id = 	$values['profile_id'];
		$submission_id = 	$values['submission_id'];
		$date = $values['date'];
    foreach($results as $suggestion_id){
		
	
       $suggestion_value = $values[$suggestion_id];
	           $connection->insert('niv_suggestion_submissions')->fields([ 'profile_id' => $profile_id, 'submitted_date' => $date, 'suggestion_id' => $suggestion_id, 'suggestion_value' => $suggestion_value]) ->execute();

   
   
    
  }
  
   $this->messenger()->addStatus($this->t('Suggestions response submitted successfully'));
    $url = Url::fromRoute('entity.node.canonical', ['node' => $profile_id ]);
    $form_state->setRedirectUrl($url);
  
  }

}
