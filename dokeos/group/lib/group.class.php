<?php

require_once dirname(__FILE__).'/group_data_manager.class.php';

/**
 * @package users
 */
/**
 *	@author Hans de Bisschop
 *	@author Dieter De Neef
 *  @author Sven Vanpoucke
 */

class Group
{
	const CLASS_NAME = __CLASS__;
	
	const PROPERTY_ID = 'id';
	const PROPERTY_NAME = 'name';
	const PROPERTY_DESCRIPTION = 'description';
	const PROPERTY_SORT = 'sort';
	const PROPERTY_PARENT = 'parent';
	
	/**
	 * Default properties of the group object, stored in an associative
	 * array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new group object.
	 * @param int $id The numeric ID of the group object. May be omitted
	 *                if creating a new object.
	 * @param array $defaultProperties The default properties of the group
	 *                                 object. Associative array.
	 */
	function Group($id = 0, $defaultProperties = array ())
	{
		$this->defaultProperties = $defaultProperties;
	}
	
	/**
	 * Gets a default property of this group object by name.
	 * @param string $name The name of the property.
	 */
	function get_default_property($name)
	{
		return $this->defaultProperties[$name];
	}
	
	/**
	 * Gets the default properties of this group.
	 * @return array An associative array containing the properties.
	 */
	function get_default_properties()
	{
		return $this->defaultProperties;
	}
	
	function set_default_properties($defaultProperties)
	{
		$this->defaultProperties = $defaultProperties;
	}
	
	/**
	 * Get the default properties of all groups.
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_ID, self :: PROPERTY_NAME, self :: PROPERTY_DESCRIPTION, self :: PROPERTY_SORT, self :: PROPERTY_PARENT);
	}
		
	/**
	 * Sets a default property of this group by name.
	 * @param string $name The name of the property.
	 * @param mixed $value The new value for the property.
	 */
	function set_default_property($name, $value)
	{
		$this->defaultProperties[$name] = $value;
	}
	
	/**
	 * Checks if the given identifier is the name of a default group
	 * property.
	 * @param string $name The identifier.
	 * @return boolean True if the identifier is a property name, false
	 *                 otherwise.
	 */
	static function is_default_property_name($name)
	{
		return in_array($name, self :: get_default_property_names());
	}

	/**
	 * Returns the id of this group.
	 * @return int The id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}
	
	/**
	 * Returns the name of this group.
	 * @return String The name
	 */
	function get_name()
	{
		return $this->get_default_property(self :: PROPERTY_NAME);
	}
	
	/**
	 * Returns the description of this group.
	 * @return String The description
	 */
	function get_description()
	{
		return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
	}
	
	function get_sort()
	{
		return $this->get_default_property(self :: PROPERTY_SORT);
	}
	
	/**
	 * Sets the group_id of this group.
	 * @param int $group_id The group_id.
	 */
	function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
	}		
	
	/**
	 * Sets the name of this group.
	 * @param String $name the name.
	 */
	function set_name($name)
	{
		$this->set_default_property(self :: PROPERTY_NAME, $name);
	}
	
	/**
	 * Sets the description of this group.
	 * @param String $description the description.
	 */
	function set_description($description)
	{
		$this->set_default_property(self :: PROPERTY_DESCRIPTION, $description);
	}
	
	function set_sort($sort)
	{
		$this->set_default_property(self :: PROPERTY_SORT, $sort);
	}
	
	function get_parent()
	{
		return $this->get_default_property(self :: PROPERTY_PARENT);
	}
	
	function set_parent($parent)
	{
		$this->set_default_property(self :: PROPERTY_PARENT, $parent);
	}
	
	/**
	 * Instructs the Datamanager to delete this group.
	 * @return boolean True if success, false otherwise.
	 */
	function delete()
	{
		return GroupDataManager :: get_instance()->delete_group($this);
	}
	
	function truncate()
	{
		return GroupDataManager :: get_instance()->truncate_group($this);
	}
	
	function create()
	{
		$gdm = GroupDataManager :: get_instance();
		$this->set_id($gdm->get_next_group_id());
		return $gdm->create_group($this);
	}
	
	function update() 
	{
		$gdm = GroupDataManager :: get_instance();
		$success = $gdm->update_group($this);
		if (!$success)
		{
			return false;
		}

		return true;	
	}
	
	static function get_table_name()
	{
		return DokeosUtilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
}
?>