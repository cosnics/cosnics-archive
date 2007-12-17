<?php
require_once dirname(__FILE__).'/../../learningobjectform.class.php';
require_once dirname(__FILE__).'/learning_path.class.php';
/**
 * @package repository.learningobject
 * @subpackage learning_path
 */
class LearningPathForm extends LearningObjectForm
{
	const TOTAL_PROPERTIES = 2;
	function setCsvValues($valuearray)
	{
		if(count($valuearray) == self :: TOTAL_PROPERTIES)
		{
			$defaults[LearningObject :: PROPERTY_TITLE] = $valuearray[0];
			$defaults[LearningObject :: PROPERTY_DESCRIPTION] = $valuearray[1];	
			parent :: setValues($defaults);
			return true;
		}
		return false;	
	}
	function create_learning_object()
	{
		$object = new LearningPath();
		$this->set_learning_object($object);
		return parent :: create_learning_object();
	}
}
?>
