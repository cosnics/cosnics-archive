<?php

require_once dirname(__FILE__).'/../question_display.class.php';

class DocumentQuestionDisplay extends QuestionDisplay
{
	function add_to($formvalidator) 
	{
		$formvalidator->addElement('html', parent :: display_header());
		$name = $this->get_clo_question()->get_ref().'_0';
		
		$formvalidator->addElement('hidden', $name, '');
		$formvalidator->addElement('text', $name.'_name', Translation :: get('Selected document'));
		
		$formvalidator->addElement('submit', 'repoviewer_'.$name, Translation :: get('RepoViewer'));
		$formvalidator->addElement('html', '<br/>');
		$formvalidator->addElement('html', $this->display_footer());
	}
}
?>