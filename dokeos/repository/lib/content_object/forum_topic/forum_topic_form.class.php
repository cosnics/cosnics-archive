<?php
require_once dirname(__FILE__).'/../../content_object_form.class.php';
require_once dirname(__FILE__).'/forum_topic.class.php';
/**
 * @package repository.learningobject
 * @subpackage forum
 */
class ForumTopicForm extends ContentObjectForm
{
	function create_content_object()
	{
		$object = new ForumTopic();
		
		$object->set_locked($this->exportValue(ForumTopic :: PROPERTY_LOCKED));
		$this->set_content_object($object);
		
/*		$values-> $this->exportValues();
		$object->set_locked($values[ForumTopic :: PROPERTY_LOCKED]);
		$this->set_content_object($object);
		if ($values['locked']){
			$object->set_locked("1");
		}
		else{
			$object->set_locked("0");
		}*/
		return parent :: create_content_object();
	}
	
	function update_content_object()
	{
		$object = $this->get_content_object();
		$object->set_locked($this->exportValue(ForumTopic :: PROPERTY_LOCKED));
		
		return parent :: update_content_object();
	}
	
	function build_creation_form($default_content_object = null)
	{
		parent :: build_creation_form();
		$this->addElement('category', Translation :: get(get_class($this) .'Properties'));
		$this->addElement('checkbox','locked', Translation :: get('Locked'));
		$this->addElement('category');
	}
	
	function build_editing_form($object)
	{
		parent :: build_editing_form();
		$this->addElement('category', Translation :: get(get_class($this) .'Properties'));
		$this->addElement('checkbox','locked', Translation :: get('Locked'));
		$this->addElement('category');
	}
	
	function set_csv_values($valuearray)
	{
		$defaults[ContentObject :: PROPERTY_TITLE] = $valuearray[0];
		$defaults[ContentObject :: PROPERTY_PARENT_ID] = $valuearray[1];
		$defaults[ContentObject :: PROPERTY_DESCRIPTION] = $valuearray[2];
		$defaults[ForumTopic :: PROPERTY_LOCKED] = $valuearray[3];	
		parent :: set_values($defaults);			
	}
	
	/*function setDefaults($defaults = array())
	{
		$object = $this->get_content_object();
		if($object != null){
			$defaults[ForumTopic :: PROPERTY_LOCKED] = $object->get_locked();
		}
		parent :: setDefaults($defaults);
	}*/
}
?>