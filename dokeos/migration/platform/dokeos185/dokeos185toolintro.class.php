<?php 
/**
 * migration.lib.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../../lib/import/importtoolintro.class.php';

/**
 * This class presents a Dokeos185 tool_intro
 *
 * @author Sven Vanpoucke
 */
class Dokeos185ToolIntro extends ImportToolIntro
{
	private static $mgdm;
	
	/**
	 * Dokeos185ToolIntro properties
	 */
	const PROPERTY_ID = 'id';
	const PROPERTY_INTRO_TEXT = 'intro_text';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Dokeos185ToolIntro object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185ToolIntro($defaultProperties = array ())
	{
		$this->defaultProperties = $defaultProperties;
	}

	/**
	 * Gets a default property by name.
	 * @param string $name The name of the property.
	 */
	function get_default_property($name)
	{
		return $this->defaultProperties[$name];
	}

	/**
	 * Gets the default properties
	 * @return array An associative array containing the properties.
	 */
	function get_default_properties()
	{
		return $this->defaultProperties;
	}

	/**
	 * Get the default properties
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_ID, self :: PROPERTY_INTRO_TEXT);
	}

	/**
	 * Sets a default property by name.
	 * @param string $name The name of the property.
	 * @param mixed $value The new value for the property.
	 */
	function set_default_property($name, $value)
	{
		$this->defaultProperties[$name] = $value;
	}

	/**
	 * Sets the default properties of this class
	 */
	function set_default_properties($defaultProperties)
	{
		$this->defaultProperties = $defaultProperties;
	}

	/**
	 * Returns the id of this Dokeos185ToolIntro.
	 * @return the id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}

	/**
	 * Returns the intro_text of this Dokeos185ToolIntro.
	 * @return the intro_text.
	 */
	function get_intro_text()
	{
		return $this->get_default_property(self :: PROPERTY_INTRO_TEXT);
	}

	function is_valid($array)
	{
		$course = $array['course'];
	}
	
	function convert_to_lcms($array)
	{	
		$course = $array['course'];
	}
	
	static function get_all($parameters = array())
	{
		self :: $mgdm = $parameters['mgdm'];
		
		$db = $parameters['course'];
		$tablename = 'tool_intro';
		$classname = 'Dokeos185ToolIntro';
			
		return self :: $mgdm->get_all($db, $tablename, $classname, $tool_name);	
	}

}

?>