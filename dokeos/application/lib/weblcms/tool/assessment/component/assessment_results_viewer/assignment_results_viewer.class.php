<?php
require_once dirname(__FILE__).'/results_viewer.class.php';

class AssignmentResultsViewer extends ResultsViewer
{
	function build()
	{
		$assessment = parent :: get_assessment();
		$assessment_id = $assessment->get_id();

//		$this->addElement('html', '<div class="learning_object" style="background-image: url('. Theme :: get_common_image_path(). 'learning_object/' .$assessment->get_icon_name().'.png);">');
//		$this->addElement('html', '<div class="title" style="font-size: 14px">');
		$this->addElement('html', '<h3>' . Translation :: get('ViewExerciseResults').': '.$assessment->get_title() . '</h3>');
//		$this->addElement('html', '</div>');
//		$this->addElement('html', '<div class="description">');
		$this->addElement('html', $assessment->get_description());
//		$this->addElement('html', '</div>');
//		$this->addElement('html', '</div>');
		$count = 1;
		$uaid = parent :: get_user_assessment()->get_id();
		$dm = RepositoryDataManager :: get_instance();
		$db = WeblcmsDataManager :: get_instance();
		$user_assessment = parent :: get_user_assessment();
		
		//dump($assessment);
		//dump($user_assessment);
		if (get_class($user_assessment) == 'WeblcmsAssessmentAttemptsTracker')
		{
			$publication = WeblcmsDataManager :: get_instance()->retrieve_learning_object_publication(parent :: get_user_assessment()->get_assessment_id());
			$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_PARENT, $publication->get_learning_object()->get_id());
		}
		else
		{
			$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_PARENT, $user_assessment->get_assessment_id());
		}
		$clo_questions = $dm->retrieve_complex_learning_object_items($condition);
		while($clo_question = $clo_questions->next_result())
		{
			$question = $dm->retrieve_learning_object($clo_question->get_ref());
			
			if (get_class($user_assessment) == 'WeblcmsAssessmentAttemptsTracker')
			{
				$track = new WeblcmsQuestionAttemptsTracker();
				$condition_ass = new EqualityCondition(WeblcmsQuestionAttemptsTracker :: PROPERTY_ASSESSMENT_ATTEMPT_ID, $this->get_user_assessment()->get_id());
				$condition_question = new EqualityCondition(WeblcmsQuestionAttemptsTracker :: PROPERTY_QUESTION_ID, $clo_question->get_id());
				$condition = new AndCondition(array($condition_ass, $condition_question));		
			}
			else
			{
				$track = new WeblcmsLearningPathQuestionAttemptsTracker();
				$condition_ass = new EqualityCondition(WeblcmsLearningPathQuestionAttemptsTracker :: PROPERTY_LEARNING_PATH_ASSESSMENT_ATTEMPT_ID, $this->get_user_assessment()->get_id());
				$condition_question = new EqualityCondition(WeblcmsLearningPathQuestionAttemptsTracker :: PROPERTY_QUESTION_ID, $clo_question->get_id());
				$condition = new AndCondition(array($condition_ass, $condition_question));	
				//dump($condition);
			}
			
			
			//$track = new WeblcmsQuestionAttemptsTracker();
			//$condition_ass = new EqualityCondition(WeblcmsQuestionAttemptsTracker :: PROPERTY_ASSESSMENT_ATTEMPT_ID, $this->get_user_assessment()->get_id());
			//$condition_question = new EqualityCondition(WeblcmsQuestionAttemptsTracker :: PROPERTY_QUESTION_ID, $clo_question->get_id());
			//$condition = new AndCondition(array($condition_ass, $condition_question));
			$q_results = $track->retrieve_tracker_items($condition);
			$question_result = QuestionResult :: create_question_result($this, $clo_question, $q_results, $this->get_edit_rights(), $count, parent :: get_user_assessment()->get_id());
			$count++;
			$question_result->display_exercise();
		}
		if (Request :: get(AssessmentTool :: PARAM_ADD_FEEDBACK) == '1')
		{
			$buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Save'), array('class' => 'positive'));
			$buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

			$this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
		}
		$max_total_score = $assessment->get_maximum_score();
		$score = round(parent :: get_user_assessment()->get_total_score());
		$pct_score = round(($score / $max_total_score) * 100, 2);
		$this->addElement('html', '<h3>'.Translation :: get('TotalScore').': '.$score."/".$max_total_score.' ('.$pct_score.'%)</h3><br />');
		
	}
}
?>