<?php
/**
 * @package repository.learningobject
 * @subpackage document
 * 
 *  @author Hans De Bisschop
 *  @author Dieter De Neef
 */
/**
 * This class can be used to get the difference between documents
 */
class DocumentDifference extends ContentObjectDifference
{
	function get_difference()
	{
		$object = $this->get_object();
		$version = $this->get_version();
		
		$object_string = $object->get_filename() . ' (' . number_format($object->get_filesize() / 1024, 2, '.', '') . ' kb)';
        $object_string = explode("\n", strip_tags($object_string));
           	
        $version_string = $version->get_filename() . ' (' . number_format($version->get_filesize() / 1024, 2, '.', '') . ' kb)';;
		$version_string = explode("\n", strip_tags($version_string));
		
		$td = new Difference_Engine($object_string, $version_string);
		
		return array_merge(parent :: get_difference(), $td->getDiff());
	}
}
?>