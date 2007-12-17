<?php
/**
 * $Id$
 * @package repository.learningobject
 * @subpackage announcement
 */
require_once dirname(__FILE__).'/../../learningobjectform.class.php';
require_once dirname(__FILE__).'/announcement.class.php';
/**
 * This class represents a form to create or update announcements
 */
class AnnouncementForm extends LearningObjectForm
{
	const TOTAL_PROPERTIES = 2;
	// Inherited
	function create_learning_object()
	{
		$object = new Announcement();
		$this->set_learning_object($object);
		return parent :: create_learning_object();
	}

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
}
?>
