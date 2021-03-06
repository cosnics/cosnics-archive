<?php
require_once dirname(__FILE__) . '/../../content_object.class.php';
/**
 * @package repository.learningobject
 * @subpackage learning_path
 */
class LearningPathItem extends ContentObject
{
	const PROPERTY_REFERENCE = 'reference_id';
	const PROPERTY_MAX_ATTEMPTS = 'max_attempts';
	const PROPERTY_MASTERY_SCORE = 'mastery_score';
	
	static function get_additional_property_names()
	{
		return array (self :: PROPERTY_REFERENCE, self :: PROPERTY_MAX_ATTEMPTS, self :: PROPERTY_MASTERY_SCORE);
	}
	
	function get_reference()
	{
		return $this->get_additional_property(self :: PROPERTY_REFERENCE);
	}
	
	function set_reference($reference)
	{
		$this->set_additional_property(self :: PROPERTY_REFERENCE, $reference);
	}
	
	function get_max_attempts()
	{
		return $this->get_additional_property(self :: PROPERTY_MAX_ATTEMPTS);
	}
	
	function set_max_attempts($max_attempts)
	{
		$this->set_additional_property(self :: PROPERTY_MAX_ATTEMPTS, $max_attempts);
	}
	
	function get_mastery_score()
	{
		return $this->get_additional_property(self :: PROPERTY_MASTERY_SCORE);
	}
	
	function set_mastery_score($mastery_score)
	{
		$this->set_additional_property(self :: PROPERTY_MASTERY_SCORE, $mastery_score);
	}
}
?>