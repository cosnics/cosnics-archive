<?php
require_once dirname(__FILE__) . '/../../../../../../../../common/global.inc.php';
require_once Path :: get_application_path() . 'lib/weblcms/trackers/weblcms_lpi_attempt_tracker.class.php';
require_once Path :: get_repository_path() . 'lib/repository_data_manager.class.php';

$tracker_id = Request :: post('tracker_id');
$variable = Request :: post('variable');

$condition = new EqualityCondition(WeblcmsLpiAttemptTracker :: PROPERTY_ID, $tracker_id);		
$dummy = new WeblcmsLpiAttemptTracker();
$trackers = $dummy->retrieve_tracker_items($condition);
$tracker = $trackers[0];

$rdm = RepositoryDataManager :: get_instance();
$item = $rdm->retrieve_complex_content_object_item($tracker->get_lp_item_id());

$learning_path_item = $rdm->retrieve_content_object($item->get_ref());
$scorm_item = $rdm->retrieve_content_object($learning_path_item->get_reference());

if(substr($variable, 0, 15) == 'cmi.objectives.')
{
	$left = substr($variable, 15, strlen($variable) - 15);
	$objectives = $scorm_item->get_objectives();
	
	$first_char = substr($left, 0, 1);
	if(is_numeric($first_char))
	{
		$objective = $objectives->get_objective(intval($first_char));
		$left = substr($left, 2, strlen($left) - 2);
	}
	
	switch($left)
	{
		case '_count':
			$value = $objectives ? $objectives->count_objectives() : 0;
			break;
		case 'id':
			$value = $objective->get_id();
	}
}
 // SCORM 1.2 functions
elseif(substr($variable, 0, 9) == 'cmi.core.')
{
	$left = substr($variable, 9);
	
	switch($left)
	{
		case '_children' :
			$value = 'student_id, student_name, lesson_location, credit, lesson_status, entry, score, total_time, exit, session_time';
			break;
		case 'student_id' :
			$value = Session :: get_user_id();
			break;
		case 'student_name' :
			$user_id = Session :: get_user_id();
			$user = UserDataManager :: get_instance()->retrieve_user($user_id);
			$value = $user->get_lastname() . ',' . $user->get_firstname();
			break;
		case 'credit' :
			$value = 'credit';
			break;
		case 'lesson_status' :
			$value = 'not attempted';
			break;
		case 'entry':
			if($tracker->get_status() == 'not attempted')
				$value = 'ab-initio';
			else
				$value = 'resume';		
			break;
		case 'score._children':
			$value = 'raw, min, max';
	}	
}
else
{
	switch($variable)
	{
		case 'cmi.max_time_allowed' : 
			$value = $scorm_item->get_time_limit(); 
			break;
		case 'cmi.scaled_passing_score' : 
			$objectives = $scorm_item->get_objectives();
			if($objectives)
			{
				$primary = $objectives->get_primary_objective();
				if($primary->get_satisfied_by_measure())
					$value = $primary->get_minimum_satisfied_measure();
			}
			break;
		
	}
}

echo $value;
?>