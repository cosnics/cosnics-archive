<?php 
/**
 * migration.lib.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../../lib/import/importpermissiongroup.class.php';

/**
 * This class presents a Dokeos185 permission_group
 *
 * @author Sven Vanpoucke
 */
class Dokeos185PermissionGroup extends ImportPermissionGroup
{
	private static $mgdm;
	
	/**
	 * Dokeos185PermissionGroup properties
	 */
	const PROPERTY_ID = 'id';
	const PROPERTY_GROUP_ID = 'group_id';
	const PROPERTY_TOOL = 'tool';
	const PROPERTY_ACTION = 'action';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Dokeos185PermissionGroup object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185PermissionGroup($defaultProperties = array ())
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
		return array (self :: PROPERTY_ID, self :: PROPERTY_GROUP_ID, self :: PROPERTY_TOOL, self :: PROPERTY_ACTION);
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
	 * Returns the id of this Dokeos185PermissionGroup.
	 * @return the id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}

	/**
	 * Returns the group_id of this Dokeos185PermissionGroup.
	 * @return the group_id.
	 */
	function get_group_id()
	{
		return $this->get_default_property(self :: PROPERTY_GROUP_ID);
	}

	/**
	 * Returns the tool of this Dokeos185PermissionGroup.
	 * @return the tool.
	 */
	function get_tool()
	{
		return $this->get_default_property(self :: PROPERTY_TOOL);
	}

	/**
	 * Returns the action of this Dokeos185PermissionGroup.
	 * @return the action.
	 */
	function get_action()
	{
		return $this->get_default_property(self :: PROPERTY_ACTION);
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
		$tablename = 'permission_group';
		$classname = 'Dokeos185PermissionGroup';
			
		return self :: $mgdm->get_all($db, $tablename, $classname, $tool_name);	
	}

}

?>