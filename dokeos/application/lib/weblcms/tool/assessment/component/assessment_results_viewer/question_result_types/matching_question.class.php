<?php

require_once dirname(__FILE__).'/../question_result.class.php';

class MatchingQuestionResult extends QuestionResult
{
	function display_exercise()
	{
		$this->display_question_header();
		
		$rdm = RepositoryDataManager :: get_instance();
		$results = parent :: get_results();
		
		//$clo_answers = parent :: get_clo_answers();
		$answers = parent :: get_question()->get_options();
		$matches = parent :: get_question()->get_matches();
		foreach ($answers as $answer)
		{
			$total_div += $answer->get_weight();
		}
		
		foreach ($results as $result)
		{
			$answer = $matches[$result->get_answer()];
			$ans_match = $answers[$result->get_answer_index()];
			$correct = $matches[$ans_match->get_match()];
			$answers_arr[] = array('answer' => $answer, 'match' => $ans_match->get_value(), 'correct' => $correct, 'score' => $result->get_score());
			$total_score += $result->get_score();
		}
		
		$total_score = $total_score / $total_div * $this->get_clo_question()->get_weight();
		$total_div = $this->get_clo_question()->get_weight();
		$score_line = Translation :: get('Score').': '.$total_score.'/'.$total_div;
		$this->display_score($score_line);
		
		foreach ($answers_arr as $answer)
		{
			$line = $answer['match'].' '.Translation :: get('LinkedTo').' '.$answer['answer'].' ('.Translation :: get('Score').': '.$answer['score'].')';
			if ($answer['score'] == 0)
			{
				//$link = $this->get_link($answer['answer']->get_id());
				$line .= ' '.Translation :: get('CorrectAnswer').': '.$answer['correct'];
			}
			$answer_lines[] = $line;
		}
		$this->display_answers($answer_lines);
		if ($this->get_edit_rights() == 1 && $feedback = $_GET[AssessmentTool :: PARAM_ADD_FEEDBACK] == '1')
			$this->add_feedback_controls();
			
		$this->display_feedback();
		$this->display_footer();
	}
	
	function display_survey()
	{
		$this->display_question_header();
		$rdm = RepositoryDataManager :: get_instance();
		$user_answers = parent :: get_user_answers();

		foreach ($user_answers as $user_answer)
		{
			$answer = $rdm->retrieve_learning_object($user_answer->get_answer_id());
			$link = $rdm->retrieve_learning_object($user_answer->get_extra());
			$answers[] = array('answer' => $answer, 'link' => $link);
		}
		
		foreach ($answers as $answer)
		{
			$line = $answer['answer']->get_title().' '.Translation :: get('LinkedTo').' '.$answer['link']->get_title();
			$answer_lines[] = $line;
		}
		$this->display_answers($answer_lines);
		$this->display_footer();
	}
	
	function display_assignment()
	{
		$this->display_question();
		//return implode('<br/>', $html);
	}

	function get_link($answer_id)
	{
		$dm = RepositoryDataManager :: get_instance();
		$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_PARENT, $answer_id);
		$clo_answers = $dm->retrieve_complex_learning_object_items($condition);
		
		$clo_answer = $clo_answers->next_result();
		return array('answer' => $dm->retrieve_learning_object($clo_answer->get_ref(), 'answer'), 'score' => $clo_answer->get_score(), 'display_order' => $clo_answer->get_display_order());
	}
}
?>