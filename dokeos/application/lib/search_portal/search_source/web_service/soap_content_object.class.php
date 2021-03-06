<?php
/**
 * @package application.searchportal
 * @subpackage webservice
 */
require_once Path :: get_repository_path(). 'lib/accessible_content_object.class.php';

class SoapContentObject implements AccessibleContentObject
{
	public $Type;

	public $Title;

	public $Description;

	public $Created;

	public $Modified;

	public $URL;

	function SoapContentObject($Type, $Title, $Description, $Created, $Modified, $URL)
	{
		$this->Type = $Type;
		$this->Title = $Title;
		$this->Description = $Description;
		$this->Created = $Created;
		$this->Modified = $Modified;
		$this->URL = $URL;
	}

	static function from_standard_object($std_object)
	{
		return new SoapContentObject($std_object->Type, $std_object->Title, $std_object->Description, $std_object->Created, $std_object->Modified, $std_object->URL);
	}

	function get_type()
	{
		return $this->Type;
	}

	function get_title()
	{
		return $this->Title;
	}

	function get_description()
	{
		return $this->Description;
	}

	function get_creation_date()
	{
		return strtotime($this->Created);
	}

	function get_modification_date()
	{
		return strtotime($this->Modified);
	}

	function get_view_url()
	{
		return $this->URL;
	}
}
?>