<?php
require_once dirname(__FILE__).'/../../learning_object_form.class.php';
require_once dirname(__FILE__).'/forum.class.php';
/**
 * @package repository.learningobject
 * @subpackage forum
 */
class ForumForm extends LearningObjectForm
{
	
	function create_learning_object()
	{
		$object = new Forum();
		$object->set_locked($this->exportValue(Forum :: PROPERTY_LOCKED));
		$this->set_learning_object($object);
		return parent :: create_learning_object();
	}
	
	function update_learning_object()
	{
		$object = $this->get_learning_object();
		$object->set_locked($this->exportValue(Forum :: PROPERTY_LOCKED));
		//$this->set_learning_object($object);
		return parent :: update_learning_object();
	}
	
	function build_creation_form()
	{
		parent :: build_creation_form();
		$this->addElement('category', Translation :: get(get_class($this) .'Properties'));
		$this->addElement('checkbox','locked', Translation :: get('Locked'));
		$this->addElement('category');
	}
	
	function build_editing_form()
	{
		parent :: build_editing_form();
		$this->addElement('category', Translation :: get(get_class($this) .'Properties'));
		$this->addElement('checkbox','locked', Translation :: get('Locked'));
		$this->addElement('category');
	}
	
	function set_csv_values($valuearray)
	{
		$defaults[LearningObject :: PROPERTY_TITLE] = $valuearray[0];
		$defaults[LearningObject :: PROPERTY_PARENT_ID] = $valuearray[1];
		$defaults[LearningObject :: PROPERTY_DESCRIPTION] = $valuearray[2];
		$defaults[LearningObject :: PROPERTY_LOCKED] = $valuearray[3];	
		parent :: set_values($defaults);			
	}
	
	/*function setDefaults($defaults = array())
	{
		$object = $this->get_learning_object();
		if($object != null){
			$defaults[Forum :: PROPERTY_LOCKED] = $object->get_locked();
		}
		parent :: setDefaults($defaults);
	}*/
	
}
?>
