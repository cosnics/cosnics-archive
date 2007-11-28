<?php
/**
 * @package classgroup.install
 */
require_once dirname(__FILE__).'/../lib/classgroupdatamanager.class.php';
/**
 * This installer can be used to create the storage structure for the
 * classgroup application.
 */
class ClassGroupInstaller {
	/**
	 * Constructor
	 */
    function ClassGroupInstaller() {
    }
	/**
	 * Runs the install-script.
	 * @todo This function now uses the function of the RepositoryInstaller
	 * class. These shared functions should be available in a common base class.
	 */
	function install()
	{
		$sqlfiles = array();
		//Todo: Use FileSystem::get_directory_content to get xml files
		$dir = dirname(__FILE__);
		$handle = opendir($dir);
		while (false !== ($type = readdir($handle)))
		{
			$path = $dir.'/'.$type;
			if (file_exists($path) && (substr($path, -3) == 'xml'))
			{
				$this->parse_xml_file($path);
			}
			elseif (file_exists($path) && (substr($path, -3) == 'sql'))
			{
				$sqlfiles[] = $type;
			}
		}
		for ($i = 0; $i < count($sqlfiles); $i++)
		{
			$this->parse_sql_file($dir , $sqlfiles[$i]);
		}
		closedir($handle);
	}

	/**
	 * Parses an sql file and sends the request to the database manager
	 * @param String $directory
	 * @param String $filename
	 */
	function parse_sql_file($directory, $sqlfilename)
	{
		$udm = UsersDataManager :: get_instance();
		$path = $directory.'/'.$sqlfilename;
		$filecontent = fread(fopen($path, 'r'), filesize($path));
		$sqlstring = explode("\n", $filecontent);
		echo '<pre>Executing additional ClassGroup SQL statement(s)</pre>';flush();
		foreach($sqlstring as $sqlstatement)
		{
			$udm->ExecuteQuery($sqlstatement);
		}

	}

	/**
	 * Parses an XML file and sends the request to the database manager
	 * @param String $path
	 */
	function parse_xml_file($path)
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
			 $property_info['default'] = $property->getAttribute('default');
			 $property_info['autoincrement'] = $property->getAttribute('autoincrement');
			 $property_info['fixed'] = $property->getAttribute('fixed');
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
		$udm = ClassGroupDataManager :: get_instance();
		echo '<pre>Creating ClassGroup Storage Unit: '.$name.'</pre>';flush();
		$udm->create_storage_unit($name,$properties,$indexes);
	}
}
?>