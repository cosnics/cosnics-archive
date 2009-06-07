<?php

require_once dirname(__FILE__) . '/question_display.class.php';

class QuestionsAssessmentViewerWizardPage extends AssessmentViewerWizardPage
{
	private $page_number;
	private $questions;
	
	function QuestionsAssessmentViewerWizardPage($name, $parent, $number, $questions)
	{
		parent :: AssessmentViewerWizardPage($name, $parent);
		
		$this->page_number = $number;
		$this->questions = $questions;
	}

	function buildForm()
	{
		$this->_formBuilt = true;
		
		$i = 1;
		
		while($question = $this->questions->next_result())
		{
			$display = QuestionDisplay :: factory($question, $i);
			$display->add_to($this);
			$i++;	
		}
	}
}
?>