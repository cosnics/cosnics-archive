<?php
require_once dirname(__FILE__) . '/../../content_object.class.php';
/**
 * @package repository.learningobject
 * @subpackage learning_path
 */
class LearningPath extends ContentObject
{
	function get_allowed_types()
	{
		return array('learning_path', 'learning_path_item');
	}
	
	const PROPERTY_CONTROL_MODE = 'control_mode';
	const PROPERTY_VERSION = 'version';
	
	static function get_additional_property_names()
	{
		return array (self :: PROPERTY_CONTROL_MODE, self :: PROPERTY_VERSION);
	}
	
	function get_control_mode()
	{
		return unserialize($this->get_additional_property(self :: PROPERTY_CONTROL_MODE));
	}
	
	function set_control_mode($control_mode)
	{
		if(!is_array($control_mode))
			$control_mode = array($control_mode);
			
		$this->set_additional_property(self :: PROPERTY_CONTROL_MODE, serialize($control_mode));
	}
	
	function get_version()
	{
		return $this->get_additional_property(self :: PROPERTY_VERSION);
	}
	
	function set_version($version)
	{
		$this->set_additional_property(self :: PROPERTY_VERSION, $version);
	}
}
?>