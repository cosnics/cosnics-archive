<?php
/**
 * @package repository.learningobject
 * @subpackage openquestion
 */
/**
 * This class can be used to get the difference between open question
 */
class HotpotatoesDifference extends ContentObjectDifference
{
	function get_difference()
	{
		$object = $this->get_object();
		$version = $this->get_version();
		
		$object_string = $object->get_path();
        $object_string = explode("\n", strip_tags($object_string));
           	
        $version_string = $version->get_path();
		$version_string = explode("\n", strip_tags($version_string));
		
		$td = new Difference_Engine($object_string, $version_string);
		
		return array_merge(parent :: get_difference(), $td->getDiff());
	}
}
?>