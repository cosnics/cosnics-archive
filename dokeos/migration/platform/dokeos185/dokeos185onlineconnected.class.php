<?php 
/**
 * migration.lib.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../../lib/import/importonlineconnected.class.php';

/**
 * This class presents a Dokeos185 online_connected
 *
 * @author Sven Vanpoucke
 */
class Dokeos185OnlineConnected extends ImportOnlineConnected
{
	private static $mgdm;
	
	/**
	 * Dokeos185OnlineConnected properties
	 */
	const PROPERTY_USER_ID = 'user_id';
	const PROPERTY_LAST_CONNECTION = 'last_connection';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Dokeos185OnlineConnected object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185OnlineConnected($defaultProperties = array ())
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
		return array (self :: PROPERTY_USER_ID, self :: PROPERTY_LAST_CONNECTION);
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
	 * Returns the user_id of this Dokeos185OnlineConnected.
	 * @return the user_id.
	 */
	function get_user_id()
	{
		return $this->get_default_property(self :: PROPERTY_USER_ID);
	}

	/**
	 * Returns the last_connection of this Dokeos185OnlineConnected.
	 * @return the last_connection.
	 */
	function get_last_connection()
	{
		return $this->get_default_property(self :: PROPERTY_LAST_CONNECTION);
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
		$tablename = 'online_connected';
		$classname = 'Dokeos185OnlineConnected';
			
		return self :: $mgdm->get_all($db, $tablename, $classname, $tool_name);	
	}

}

?>