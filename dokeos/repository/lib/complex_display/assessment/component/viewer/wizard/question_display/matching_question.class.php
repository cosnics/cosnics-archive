<?php

require_once dirname(__FILE__).'/../question_display.class.php';

class MatchingQuestionDisplay extends QuestionDisplay
{
	function add_to($formvalidator)
	{
		$formvalidator->addElement('html', parent :: display_header());
		$clo_question = $this->get_clo_question();
		$question = RepositoryDataManager :: get_instance()->retrieve_learning_object($clo_question->get_ref());
		$answers = $question->get_options();
		$dbmatches = $question->get_matches();
		
		//$formvalidator->addElement('html', '<p><b>'.Translation :: get('Answers').' :</b>');
		$this->shuffle_with_keys($dbmatches);
		$i = 0;
		foreach ($dbmatches as $num => $match)
		{
			$matches[$num] = ($i+1);
			$matchcontents[$i] = $match;
			$i++;
		}
		//dump($matches);
		$formvalidator->addElement('html', '<div style="position: relative; top: 0px; left: 0%; width: 50%">');
		foreach($answers as $i => $answer)
		{
			$name = $this->get_clo_question()->get_id().'_'.$i;
			//$this->shuffle_with_keys($matches);
			$formvalidator->addElement('select', $name, $answer->get_value(), $matches);
		}
		$formvalidator->addElement('html', '</div>');
		
		$formvalidator->addElement('html', '<div style="position: relative; top: 0px; left: 50%; width: 50%">'.Translation :: get('Matches').': <br/><ol>');
		foreach ($matchcontents as $match)
		{
			$formvalidator->addElement('html', '<li>'.$match.'</li>');
		}
		$formvalidator->addElement('html', '</ol></div>');
		
		//$formvalidator->addElement('html', '</p>');
		$formvalidator->addElement('html', $this->display_footer());
	}
	
	function shuffle_with_keys(&$array) {
	    /* Auxiliary array to hold the new order */
	    $aux = array();
	    /* We work with an array of the keys */
	    $keys = array_keys($array);
	    /* We shuffle the keys */
	    shuffle($keys);
	    /* We iterate thru' the new order of the keys */
	    foreach($keys as $key) {
	      /* We insert the key, value pair in its new order */
	      $aux[$key] = $array[$key];
	      /* We remove the element from the old array to save memory */
	      unset($array[$key]);
	    }
	    /* The auxiliary array with the new order overwrites the old variable */
	    $array = $aux;
  	}
}
?>