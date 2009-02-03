<?php
require_once dirname(__FILE__).'/../score.class.php';

class FillInBlanksScore extends Score
{
	
	function get_score()
	{
		$answers = $this->get_question()->get_answers();
		$descr = $answers[$this->get_answer_num()];
		$answer = $this->get_answer();
		if ($descr->get_value() == $answer)
		{
			return $descr->get_weight();
		} 
		else
		{
			return 0;
		}
	}
}
?>