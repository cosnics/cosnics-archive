<?php
/**
 * @package repository.learningobject
 * @subpackage userinfo_def
 * @author Sven Vanpoucke
 */
require_once dirname(__FILE__).'/../../learning_object_form.class.php';
require_once dirname(__FILE__).'/userinfo_def.class.php';
/**
 * This class represents a form to create or update a userinfo definition
 */
class UserinfoDefForm extends LearningObjectForm
{
	// Inherited
	function create_learning_object()
	{
		$object = new UserinfoDef();
		$this->set_learning_object($object);
		return parent :: create_learning_object();
	}

	function set_csv_values($valuearray)
	{
		$defaults[LearningObject :: PROPERTY_TITLE] = $valuearray[0];
		$defaults[LearningObject :: PROPERTY_PARENT_ID] = $valuearray[1];
		$defaults[LearningObject :: PROPERTY_DESCRIPTION] = $valuearray[2];	
		parent :: set_values($defaults);			
	}	
}
?>
