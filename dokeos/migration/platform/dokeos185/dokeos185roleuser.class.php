<?php 
/**
 * migration.lib.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../../lib/import/importroleuser.class.php';

/**
 * This class presents a Dokeos185 role_user
 *
 * @author Sven Vanpoucke
 */
class Dokeos185RoleUser extends ImportRoleUser
{
	private static $mgdm;
	
	/**
	 * Dokeos185RoleUser properties
	 */
	const PROPERTY_ROLE_ID = 'role_id';
	const PROPERTY_SCOPE = 'scope';
	const PROPERTY_USER_ID = 'user_id';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Dokeos185RoleUser object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185RoleUser($defaultProperties = array ())
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
		return array (self :: PROPERTY_ROLE_ID, self :: PROPERTY_SCOPE, self :: PROPERTY_USER_ID);
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
	 * Returns the role_id of this Dokeos185RoleUser.
	 * @return the role_id.
	 */
	function get_role_id()
	{
		return $this->get_default_property(self :: PROPERTY_ROLE_ID);
	}

	/**
	 * Returns the scope of this Dokeos185RoleUser.
	 * @return the scope.
	 */
	function get_scope()
	{
		return $this->get_default_property(self :: PROPERTY_SCOPE);
	}

	/**
	 * Returns the user_id of this Dokeos185RoleUser.
	 * @return the user_id.
	 */
	function get_user_id()
	{
		return $this->get_default_property(self :: PROPERTY_USER_ID);
	}
	
	/**
	 * Checks if a role user is valid
	 * @param Array $array
	 * @return Boolean
	 */
	function is_valid($array)
	{
		$course = $array['course'];
	}
	
	/**
	 * migrate role user, sets category
	 * @param Array $array
	 * @return
	 */
	function convert_to_lcms($array)
	{	
		$course = $array['course'];
	}
	
	/**
	 * Gets all the role user of a course
	 * @param Array $array
	 * @return Array of dokeos185roleuser
	 */
	static function get_all($parameters)
	{
		self :: $mgdm = $parameters['mgdm'];
		
		$db = $parameters['course'];
		$tablename = 'role_user';
		$classname = 'Dokeos185RoleUser';
			
		return self :: $mgdm->get_all($db, $tablename, $classname, $tool_name);	
	}

}

?>