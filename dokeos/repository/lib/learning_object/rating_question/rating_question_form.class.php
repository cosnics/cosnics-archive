<?php
/**
 * $Id: announcement_form.class.php 9191 2006-09-01 11:48:41Z bmol $
 * @package repository.learningobject
 * @subpackage exercise
 */
require_once dirname(__FILE__).'/../../learning_object_form.class.php';
require_once dirname(__FILE__).'/rating_question.class.php';
/**
 * This class represents a form to create or update open questions
 */
class RatingQuestionForm extends LearningObjectForm
{
	function set_csv_values($valuearray)
	{
		$defaults[LearningObject :: PROPERTY_TITLE] = $valuearray[0];
		$defaults[LearningObject :: PROPERTY_PARENT_ID] = $valuearray[1];
		$defaults[LearningObject :: PROPERTY_DESCRIPTION] = $valuearray[2];	
		parent :: set_values($defaults);			
	}
	
	function setDefaults($defaults = array ())
	{
		$object = $this->get_learning_object();
		if ($object != null) {
			$defaults[RatingQuestion :: PROPERTY_LOW] = $object->get_low();
			$defaults[RatingQuestion :: PROPERTY_HIGH] = $object->get_high();
			$defaults[RatingQuestion :: PROPERTY_CORRECT] = $object->get_correct();
			
			if ($object->get_low() == 0 && $object->get_high() == 100)
			{
				$defaults['type'] = 0;
			}
			else
			{
				$defaults['type'] = 1;
			}
		}
		
		parent :: setDefaults($defaults);
	}
	
	protected function build_creation_form()
	{
		parent :: build_creation_form();
		$this->addElement('category', Translation :: get(get_class($this) .'Properties'));
		
		$elem[] = $this->createElement('radio', 'ratingtype', null, Translation :: get('Percentage'), 1, array ('onclick' => 'javascript:hide_controls(\'buttons\')'));
		$elem[] = $this->createElement('radio', 'ratingtype', null, Translation :: get('Rating'), 0, array ('onclick' => 'javascript:show_controls(\'buttons\')'));
		$this->addGroup($elem,'type',Translation :: get('type'),'<br />',false);
		
		$this->addElement('html', '<div style="margin-left:25px;display:block;" id="buttons">');
		$this->add_textfield(RatingQuestion :: PROPERTY_LOW, Translation :: get ('LowValue'), false);
		$this->add_textfield(RatingQuestion :: PROPERTY_HIGH, Translation :: get('HighValue'), false);
		
		$this->addElement('html', '</div>');
		$this->add_textfield(RatingQuestion :: PROPERTY_CORRECT, Translation :: get('CorrectValue'), false);
		$this->addElement('html',"<script type=\"text/javascript\">
			/* <![CDATA[ */
			hide_controls('buttons');
			function show_controls(elem) {
				el = document.getElementById(elem);
				el.style.display='';
			}
			function hide_controls(elem) {
				el = document.getElementById(elem);
				el.style.display='none';
			}
			/* ]]> */
				</script>\n");
		$this->addElement('category');
		
		$this->addRule(RatingQuestion :: PROPERTY_LOW, Translation :: get('ValueShouldBeNumeric'), 'numeric');
		$this->addRule(RatingQuestion :: PROPERTY_HIGH, Translation :: get('ValueShouldBeNumeric'), 'numeric');
		$this->addRule(RatingQuestion :: PROPERTY_CORRECT, Translation :: get('ValueShouldBeNumeric'), 'numeric');
	}
	protected function build_editing_form()
	{
		parent :: build_editing_form();
		$this->addElement('category', Translation :: get(get_class($this) .'Properties'));
		$elem[] = $this->createElement('radio', null, null, Translation :: get('Percentage'), 1, array ('onclick' => 'javascript:hide_controls(\'buttons\')'));
		$elem[] = $this->createElement('radio', null, null, Translation :: get('Rating'), 0, array ('onclick' => 'javascript:show_controls(\'buttons\')'));
		$this->addGroup($elem, 'type', 'test');
		$this->addElement('html', '<div style="margin-left:25px;display:block;" id="buttons">');
		$this->add_textfield(RatingQuestion :: PROPERTY_LOW, Translation :: get ('LowValue'), false);
		$this->add_textfield(RatingQuestion :: PROPERTY_HIGH, Translation :: get('HighValue'), false);
		$this->add_textfield(RatingQuestion :: PROPERTY_CORRECT, Translation :: get('CorrectValue'));
		
		$this->addRule(RatingQuestion :: PROPERTY_LOW, Translation :: get('ValueShouldBeNumeric'), 'numeric');
		$this->addRule(RatingQuestion :: PROPERTY_HIGH, Translation :: get('ValueShouldBeNumeric'), 'numeric');
		$this->addRule(RatingQuestion :: PROPERTY_CORRECT, Translation :: get('ValueShouldBeNumeric'), 'numeric');
		
		$this->addElement('html', '</div>');
		
		$this->addElement('html',"<script type=\"text/javascript\">
			/* <![CDATA[ */
			function show_controls(elem) {
				el = document.getElementById(elem);
				el.style.display='';
			}
			function hide_controls(elem) {
				el = document.getElementById(elem);
				el.style.display='none';
			}
			/* ]]> */
				</script>\n");
		$this->addElement('category');
	}
	
	function create_learning_object()
	{
		$values = $this->exportValues();
		$object = new RatingQuestion();
		
		if (isset($values[RatingQuestion :: PROPERTY_LOW]) && $values[RatingQuestion :: PROPERTY_LOW] != '')
			$object->set_low($values[RatingQuestion :: PROPERTY_LOW]);
		else
			$object->set_low(0);
		
		if (isset($values[RatingQuestion :: PROPERTY_HIGH]) && $values[RatingQuestion :: PROPERTY_HIGH] != '')
			$object->set_high($values[RatingQuestion :: PROPERTY_HIGH]);
		else
			$object->set_high(100);
			
		if (isset($values[RatingQuestion :: PROPERTY_CORRECT]))
			$object->set_correct($values[RatingQuestion :: PROPERTY_CORRECT]);
			
		$this->set_learning_object($object);
		return parent :: create_learning_object();
	}
	
	function update_learning_object()
	{
		$values = $this->exportValues();
		$object = parent :: get_learning_object();

		if (isset($values[RatingQuestion :: PROPERTY_LOW]) && $values[RatingQuestion :: PROPERTY_LOW] != '')
			$object->set_low($values[RatingQuestion :: PROPERTY_LOW]);
		else
			$object->set_low(0);
		
		if (isset($values[RatingQuestion :: PROPERTY_HIGH]) && $values[RatingQuestion :: PROPERTY_HIGH] != '')
			$object->set_high($values[RatingQuestion :: PROPERTY_HIGH]);
		else
			$object->set_high(100);
			
		if (isset($values[RatingQuestion :: PROPERTY_CORRECT]))
			$object->set_correct($values[RatingQuestion :: PROPERTY_CORRECT]);
		else
			$object->set_correct(null);

		$this->set_learning_object($object);
		return parent :: update_learning_object();
	}
}
?>