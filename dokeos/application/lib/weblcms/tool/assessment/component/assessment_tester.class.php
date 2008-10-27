<?php
/**
 * @package application.weblcms.tool.exercise.component
 */
require_once dirname(__FILE__).'/assessment_tester_form/assessment_tester_form.class.php';

class AssessmentToolTesterComponent extends AssessmentToolComponent
{
	function run()
	{
		$datamanager = WeblcmsDataManager :: get_instance();
		
		$pid = $_GET[Tool :: PARAM_PUBLICATION_ID];
		$pub = $datamanager->retrieve_learning_object_publication($pid);
		$visible = !$pub->is_hidden() && $pub->is_visible_for_target_users();
		
		if (!$this->is_allowed(VIEW_RIGHT) || !$visible)
		{
			Display :: display_not_allowed();
			return;
		}
		$trail = new BreadcrumbTrail();
		$assessment = $pub->get_learning_object();
		
		$this->display_header($trail);
		
		$tester_form = new AssessmentTesterForm($assessment, $this->get_url(array(Tool :: PARAM_ACTION => AssessmentTool :: ACTION_TAKE_ASSESSMENT, Tool :: PARAM_PUBLICATION_ID => $pid)));
		
		$this->check($tester_form, $assessment, $datamanager);
		
		$this->display_footer();
	}
	
	function check($tester_form, $assessment, $datamanager)
	{
	if ($tester_form->validate())
		{
			$values = $tester_form->exportValues();
			$user_assessment = new UserAssessment();
			$user_assessment->set_assessment_id($assessment->get_id());
			$user_assessment->set_user_id(1);
			$user_assessment->set_id($this->get_next_user_assessment_id());
			
			if($datamanager->create_user_assessment($user_assessment)) {
				foreach($values as $key => $value)
				{
					$answer = new UserAnswer();
					$answer->set_user_test_id($user_assessment->get_id());
					//print_r ('\n'.$key.';'.$value);
					$parts = split("_", $key);
					$answer->set_question_id($parts[0]);
					$answer->set_answer_id($parts[1]);
					$answer->set_extra($value);
					//$user_answers[] = $answer;
					print_r ('\n'.$answer);
					$datamanager->create_user_answer($user_answer);
				}
			}
		} else {
			echo $tester_form->toHtml();
		}
	}
}
?>