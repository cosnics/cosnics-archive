<?php
/**
 * $Id: repositorydatamanager.class.php 9176 2006-08-30 09:08:17Z bmol $
 * @package repository
 */
require_once dirname(__FILE__).'/../../main/inc/claro_init_global.inc.php';
error_reporting(E_ALL);
require_once dirname(__FILE__).'/../../repository/lib/repository_manager/repositorymanager.class.php';
/**
 *	This	 installer can be used to create the storage structure for the
 *repository.
 */
class RepositoryInstaller
{
	/**
	 * Constructor
	 */
	function RepositoryInstaller()
	{
	}
	/**
	 * Runs the install-script. After creating the necessary tables to store the
	 * common learning object information, this function will scan the
	 * directories of all learning object types. When an XML-file describing a
	 * storage unit is found, this function will parse the file and create the
	 * storage unit.
	 */
	function install()
	{
		$this->parse_xml_file('learning_object.xml');
		$this->parse_xml_file('learning_object_attachment.xml');
		$dir = dirname(__FILE__).'/../lib/learning_object';
		$handle = opendir($dir);
		while (false !== ($type = readdir($handle)))
		{
			$path = $dir.'/'.$type.'/'.$type.'.xml';
			if (file_exists($path))
			{
				$this->parse_xml_file($path);
			}
		}
	}
	/**
	 * Parses an XML-file in which a storage unit is described. After parsing,
	 * the create_storage_unit function of the RepositoryDataManager is used to
	 * create the actual storage unit depending on the implementation of the
	 * datamanager.
	 * @param string $path The path to the XML-file to parse
	 * @todo This function should be moved to an upper class
	 */
	private function parse_xml_file($path)
	{
		$doc = new DOMDocument();
		$doc->load($path);
		$object = $doc->getElementsByTagname('object')->item(0);
		$name = $object->getAttribute('name');
		$xml_properties = $doc->getElementsByTagname('property');
		foreach($xml_properties as $index => $property)
		{
			 $property_info = array();
			 $property_info['type'] = $property->getAttribute('type');
			 $property_info['length'] = $property->getAttribute('length');
			 $property_info['unsigned'] = $property->getAttribute('unsigned');
			 $property_info['notnull'] = $property->getAttribute('notnull');
			 $properties[$property->getAttribute('name')] = $property_info;
		}
		$xml_indexes = $doc->getElementsByTagname('index');
		foreach($xml_indexes as $key => $index)
		{
			 $index_info = array();
			 $index_info['type'] = $index->getAttribute('type');
			 $index_properties = $index->getElementsByTagname('indexproperty');
			 foreach($index_properties as $subkey => $index_property)
			 {
			 	$index_info['fields'][$index_property->getAttribute('name')] = array();
			 }
			 $indexes[$index->getAttribute('name')] = $index_info;
		}
		$dm = RepositoryDataManager :: get_instance();
		$dm->create_storage_unit($name,$properties,$indexes);
	}
}
// @todo remove next 2 lines once everything is up and running
$installer = new RepositoryInstaller();
$installer->install();
?>