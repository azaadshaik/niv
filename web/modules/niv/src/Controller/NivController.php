<?php

namespace Drupal\niv\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Entity\webform;
use Drupal\Core\Site\Settings;
use Drupal\node\Entity\Node;
use Symfony\Component\Yaml\Yaml;
use Drupal\user\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;

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


  public function loadAssessmentReport($profileId,$submissionId){

    $values = ['entity_id'=>$profileId,'sid'=>$submissionId];
    $submission = \Drupal::entityTypeManager()
->getStorage('webform_submission')
->loadByProperties($values);

$vision_raw_score = 0;
$hearing_raw_score =0;
$touch_raw_score=0;
$taste_and_smell_raw_score=0;
$body_awareness_raw_score=0;
$balance_and_motion_raw_score=0;
$planning_and_ideas_raw_score=0;
$social_participation_raw_score=0;

//dump($submission);
//dump( $submission[$submissionId]->getData());
$data = $submission[$submissionId]->getData();

foreach($data as $key=>$value){

  
  if((int) $key>=1 && (int) $key<=10){
      $vision_raw_score = $vision_raw_score+ (int) $value;
  }
  elseif((int) $key>=11 && (int) $key<=20){
    $hearing_raw_score = $hearing_raw_score+ (int) $value;
}
if((int) $key>=21 && (int) $key<=30){
  $touch_raw_score = $touch_raw_score+ (int) $value;
}
if((int) $key>=31 && (int) $key<=40){
  $taste_and_smell_raw_score = $taste_and_smell_raw_score+ (int) $value;
}
if((int) $key>=41 && (int) $key<=50){
  $body_awareness_raw_score = $body_awareness_raw_score+ (int) $value;
}
if((int) $key>=51 && (int) $key<=60){
  $balance_and_motion_raw_score = $balance_and_motion_raw_score+ (int) $value;
}
if((int) $key>=61 && (int) $key<=70){
  $planning_and_ideas_raw_score = $planning_and_ideas_raw_score+ (int) $value;
}
if((int) $key>=71 && (int) $key<=80){
  $social_participation_raw_score = $social_participation_raw_score+ (int) $value;
}
  


}
$total_raw_score = $vision_raw_score + $hearing_raw_score + $touch_raw_score + $taste_and_smell_raw_score + $body_awareness_raw_score + $balance_and_motion_raw_score;

// echo 'Vision Raw Score:'.$vision_raw_score."<br>";
// echo 'Hearing Raw Score:'.$hearing_raw_score."<br>";
// echo 'Touch Raw Score:'.$touch_raw_score."<br>";
// echo 'Taste And Smell Raw Score:'.$taste_and_smell_raw_score."<br>";
// echo 'Body Awareness Raw Score:'.$body_awareness_raw_score."<br>";
// echo 'Balance and Motion Raw Score:'.$balance_and_motion_raw_score."<br>";
// echo 'Planning nad Ideas Raw Score:'.$planning_and_ideas_raw_score."<br>";
// echo 'Social Participation Raw Score:'.$social_participation_raw_score."<br>";

// echo 'Total raw Scores ( except Social Participation and Planning and Ideas):'. $total_raw_score."<br>";

//dump(Settings::get('t-score-matrix'));

$tscore_matrix = Settings::get('t_score_home_matrix');
$nivscore_matrix = Settings::get('t_score_niv_score_matrix');
$niv_constants = Settings::get('niv_constants');
$ei_constants = $niv_constants['engagement_index'];
$att_constants = $niv_constants['attention_index'];


$vision_tscore = $tscore_matrix['vision_raw_score'][$vision_raw_score];
$hearing_tscore = $tscore_matrix['hearing_raw_score'][$hearing_raw_score];
$touch_tscore = $tscore_matrix['touch_raw_score'][$touch_raw_score];
$taste_tscore = $tscore_matrix['taste_raw_score'][$taste_and_smell_raw_score];
$body_tscore = $tscore_matrix['body_raw_score'][$body_awareness_raw_score];
$balance_tscore = $tscore_matrix['balance_raw_score'][$balance_and_motion_raw_score];
$planning_tscore = $tscore_matrix['planning_raw_score'][$planning_and_ideas_raw_score];
$social_tscore = $tscore_matrix['social_raw_score'][$social_participation_raw_score];

// echo 'social tscore: '.$social_tscore."<br>";

$vision_niv_score = $nivscore_matrix[$vision_tscore];
$hearing_niv_score = $nivscore_matrix[$hearing_tscore];
$touch_niv_score = $nivscore_matrix[$touch_tscore];
$taste_niv_score = $nivscore_matrix[$taste_tscore];
$body_niv_score = $nivscore_matrix[$body_tscore];
$balance_niv_score = $nivscore_matrix[$balance_tscore];
$planning_niv_sscore = $nivscore_matrix[$planning_tscore];
$social_niv_score = $nivscore_matrix[$social_tscore];

//echo 'social niv score: '.$social_niv_score."<br>";

$ei_social = ($ei_constants['social'] * $social_niv_score) / 100;
$ei_visual = ($ei_constants['vision'] * $vision_niv_score) / 100;
$ei_hearing = ($ei_constants['hearing'] * $hearing_niv_score) / 100;
$ei_body = ($ei_constants['body'] * $body_niv_score) / 100;
$ei_touch = ($ei_constants['touch'] * $touch_niv_score) / 100;
$ei_balance = ($ei_constants['balance'] * $balance_niv_score) / 100;
$ei_planning = ($ei_constants['planning'] * $planning_niv_sscore) / 100;


$engagementIndex = $ei_social + $ei_visual + $ei_hearing + $ei_touch + $ei_body + $ei_balance + $ei_planning;


$att_social = ($att_constants['social'] * $social_niv_score) / 100;
$att_visual = ($att_constants['vision'] * $vision_niv_score) / 100;
$att_hearing = ($att_constants['hearing'] * $hearing_niv_score) / 100;
$att_body = ($att_constants['body'] * $body_niv_score) / 100;
$att_touch = ($att_constants['touch'] * $touch_niv_score) / 100;
$att_balance = ($att_constants['balance'] * $balance_niv_score) / 100;
$att_planning = ($att_constants['planning'] * $planning_niv_sscore) / 100;

$attentionIndex = $att_social + $att_visual + $att_hearing + $att_touch + $att_body + $att_balance + $att_planning;

$performanceIndex = ($engagementIndex + $attentionIndex) /2 ;

// echo 'Engagement Index :' .$engagementIndex."<br>";
// echo 'Attention Index :' .$attentionIndex."<br>";
// echo 'Performance Index :' .$performanceIndex."<br>";
// echo "I am in";
// die;



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
  
 
    $build['content'] = [
      '#type' => 'webform',
      '#webform' => $webform,
      ];


    return $build;
  }

  function nivAdminDashboard(){


    $query = \Drupal::entityQuery('node')
    ->condition('type', 'organizations')
  	->condition('field_active', 1, '=');
	 $nids = $query->execute();
  $orgs = Node::loadMultiple($nids);
  $orgCount = count($orgs);

  $query = \Drupal::entityQuery('node')
    ->condition('type', 'profile');
  	
	 $nids = $query->execute();
  $profiles = Node::loadMultiple($nids);

  $profilesCount = count($profiles);


  $build = [
    '#theme' => 'nivadmin_dashboard',
    '#counts' => ['orgCount'=>$orgCount,'proCount' => $profilesCount],
    
   
  ];

    return $build;

  }


  function addSuggestions($profileId,$submissionId){

    $query = \Drupal::entityQuery('node')
    ->condition('type', 'suggestions');
  	$nids = $query->execute();
  $suggestions = Node::loadMultiple($nids);
  $profile = Node::load($profileId);
  $name = $profile->title->value;
	 $dob = $profile->field_date_of_birth->value;
	 $dobYear = date('Y',strtotime($dob));
	 $currentYear = date('Y');
	 $age = $currentYear - $dobYear;
	 $gender = $profile->field_gender->value;
  $extra = ['submission_id'=>$submissionId,'profile_id'=>$profileId,'suggestions'=>$suggestions];
  $form = \Drupal::formBuilder()->getForm('Drupal\niv\Form\adminSuggestionsForm',$extra);
  

  $build = [
    '#theme' => 'nivadmin_addsuggestions',
    '#data' => ['suggestions_form'=>$form,'profile_data' => ['name'=>$name,'age'=>$age,'gender'=>$gender]],
    
   
  ];

  return $build;



  }

  function addNewSuggestions($profileId,$submissionId){

    $profile = Node::load($profileId);
    $name = $profile->title->value;
    $dob = $profile->field_date_of_birth->value;
    $dobYear = date('Y',strtotime($dob));
    $currentYear = date('Y');
    $age = $currentYear - $dobYear;
    $gender = $profile->field_gender->value;
    $user = User::load($profile->getOwnerId());  
    $authorRoles = $user->getRoles();
    
   $form_type = ($authorRoles[1]=='parent')?'parent':'school';

    $query = \Drupal::entityQuery('node')
    ->condition('type', 'new_suggestions')
    ->condition('field_form_type', $form_type, '=');
   

    
  	$nids = $query->execute();
  $suggestions = Node::loadMultiple($nids);
  

  $extra = ['submission_id'=>$submissionId,'profile_id'=>$profileId,'suggestions'=>$suggestions];
  $form = \Drupal::formBuilder()->getForm('Drupal\niv\Form\adminNewSuggestionsForm',$extra);
  

  $build = [
    '#theme' => 'nivadmin_add_new_suggestions',
    '#data' => ['suggestions_form'=>$form,'profile_data' => ['name'=>$name,'age'=>$age,'gender'=>$gender]],
    
   
  ];

  return $build;



  }

function profilesByuser($userId){
  
}

function getDailyProgress($profileId){

   $connection = \Drupal::database();
  $query = $connection->select('niv_suggestion_submissions','nss');
  $query->join('node_field_data', 'nfd', 'nfd.nid = nss.suggestion_id');
  $query->fields('nfd', array('title'));
  $query->fields('nss', array('suggestion_value','submitted_date','id'));
 
  $query->condition('nss.profile_id', $profileId, '=')
  ->orderBy('nss.submitted_date', 'ASC');
  
      
  $result = $query->execute()->fetchAll();
  $suggestions =[];
 
$formattedArray = [];

if($result){
  foreach($result as $record){
  
    $formattedArray[$record->submitted_date][] = ['suggestion'=>$record->title,'value'=>$record->suggestion_value];
    
  
  }
  
}

   foreach ($formattedArray as $key=>$value) {

    $suggestions['suggestions']['#attributes'] = array(
      'class' => array( 'daily-progress'),
    );
  
    $suggestions['suggestions'][$key] = array(
      '#type' => 'table',
      '#caption' => $this
        ->t($key),
      '#header' => array($this->t('#'),$this->t('Suggestion'),$this->t('Submitted Value')),
    );
    $row = [];
    foreach($value as $subkey=>$submission){

        $serial = $subkey+1;
        $suggestion_text = $submission['suggestion'];
        $suggestion_value = $submission['value'];
        $row[] = [$serial,$suggestion_text,$suggestion_value];
    


    }
    $suggestions['suggestions'][$key]['#rows'] = $row;
    $suggestions['suggestions'][$key]['#empty'] = $this->t('No data available');
  
  }

  $profile = Node::load($profileId);
  $name = $profile->title->value;
	 $dob = $profile->field_date_of_birth->value;
	 $dobYear = date('Y',strtotime($dob));
	 $currentYear = date('Y');
	 $age = $currentYear - $dobYear;
	 $gender = $profile->field_gender->value;

  $build['#theme'] = 'nivadmin_daily_progress_view';
  $build['#data'] = array('profile_data'=>['name'=>$name,'age'=>$age,'gender'=>$gender],'suggestions'=>$suggestions);

  return $build;



}


public function loadAssessmentResult($profileId,$submissionId) {



  $entity = \Drupal::entityTypeManager()->getStorage('node')->load($profileId);
      
  $values = ['entity_id'=>$profileId,'sid'=>$submissionId];
    $submission = \Drupal::entityTypeManager()
->getStorage('webform_submission')
->loadByProperties($values);
$data = $submission[$submissionId]->getData();
/********************************************************888 */
$form_config = \Drupal::config('webform.webform.form_a_parent')->get('elements');
ksort($data);

$parsed_elements = Yaml::parse($form_config);
$i = 1;
$fieldsets = [];
$tables = [];
$rows[] =[0,0,0,0,0];

$header = array($this->t('#'),$this->t('Question'),$this->t('Attribute'),$this->t('Answer'),$this->t('Score'));
foreach($parsed_elements as $elekey=>$element){

  //fieldset here
  
  //table here
  $eleKeyArr = explode('_',$elekey);
  $element_fieldset = 'fieldset';
  for($i=1;$i<=count($eleKeyArr);$i++){
    $element_fieldset.='_'.$eleKeyArr[$i];
  }
  $element_fieldset = rtrim($element_fieldset,'_');
 $fieldsets[] = $element[$element_fieldset]['#title'];

 $output[] = array(
  'fieldset' => array(
    '#type' => 'details',
    '#title' => t( $element[$element_fieldset]['#title']),
    '#attributes' => array('class' => array('collapsible', 'collapsed','result_table_admin_view'),'id'=>'result_table_admin_view','open'=>'open'),
    'content' => [

      array(
        '#type' => 'table',
        '#caption' => '',
        '#header' => $header,
        '#attributes' => array('class' => array('table', 'table-striped')),
      )
    ],
  ),

);


 foreach($data as $key=>$dataValue){
  
  if(isset($element[$element_fieldset][$key])){

    //echo $key;
    $attribute = isset($element[$element_fieldset][$key]['#attributes']['attribute'])?$element[$element_fieldset][$key]['#attributes']['attribute']:'';
    $rows[] = [$key,$element[$element_fieldset][$key]['#title'],$attribute,$element[$element_fieldset][$key]['#options'][$dataValue],$dataValue];
  }

 // table rows here
 }
 

}

array_shift($rows);
 $chunks = array_chunk($rows,10,true);
//dump($chunks);
//die;

foreach($chunks as $chunkKey=>$chunk){

  //sort the rows by value before assigning to #row
  //dump($chunk);
  
  $valuesArr = array_column($chunk, 4);
  array_multisort($valuesArr, SORT_DESC, $chunk);

  $formattedChunk = [];
  foreach($chunk as $cKey => $chunkVal){
    $chunkVal[0] = $cKey+1;
    $formattedChunk[] = $chunkVal;
  }
 //$chunk = array_map('array_values',$chunk);
 //dump($chunk);
  $output[$chunkKey]['fieldset']['content'][0]['#rows'] = $formattedChunk;
}
//dump($output);
//die;

//$output['fieldset']['content']['#rows'] = $rows;
return $output;

    }


function getSuggestionsLog($profileId,$submissionId){

      $connection = \Drupal::database();
     $query = $connection->select('niv_suggestion_log','nsl');
   //  $query->join('node_field_data', 'nfd', 'nfd.nid = nsl.suggestion_id');
     $query->fields('nsl',['suggestion_id','date']);
       
     $query->condition('nsl.profile_id', $profileId, '=');
     $query->condition('nsl.submission_id', $submissionId, '=')
     ->groupBy('nsl.id','nsl.suggestion_id','nsl.date')
     ->orderBy('nsl.date','DESC');

     
         
     $result = $query->distinct()->execute()->fetchAll();
     $header =  array($this->t('#'),$this->t('Form'),$this->t('Suggestion'),$this->t('Section'),$this->t('Attribute'));
     $i = 1;
    
     if(!empty($result)){
     foreach($result as $record){

     

      $build[$record->date]  = array(
        'fieldset' => array(
          '#type' => 'details',
          '#title' => t( $record->date),
          '#attributes' => array('class' => array('collapsible', 'collapsed','result_table_admin_view'),'id'=>'result_table_admin_view'),
          'content' => [
      
            array(
              '#type' => 'table',
              '#caption' => '',
              '#header' => $header,
              '#attributes' => array('class' => array('table', 'table-striped')),
            )
          ],
        ),
      
      );
      $rows =[];
      $i=1;
      foreach($result as $record2){

        if($record2->date == $record->date){

          $node = Node::load($record2->suggestion_id);
         // dump($node->field_new_suggestion->value);
         $section = $node->field_suggestion_section->getSetting('allowed_values')[$node->field_suggestion_section->getString()];
         $attribute = $node->field_section_attribute->getSetting('allowed_values')[$node->field_section_attribute->getString()];
          $rows[] = [$i,$node->field_form_type->value,$node->field_new_suggestion->value,$section,$attribute];
          $i++;

      }
    }
   // dump($rows);
    $build[$record->date]['fieldset']['content'][0]['#rows'] =$rows;
   // $rows = [];

    //$i++;
      
     }
    //  echo "<pre>";
    
     $build[key($build)]['fieldset']['#attributes']['open'] = 'open';
    }
    else{

      $build =  array(
        '#markup' => 'No history yet',
      );
      

    }
   
    return $build;
}


function getSectionAttributes($formType,$section){

  $attributesKey = ($formType=='parent') ? 'home_section_attributes':'school_section_attributes';
  $attributesArray = Settings::get($attributesKey);
  $response = $attributesArray[$section];
  return new JsonResponse($response);

}
   
}
