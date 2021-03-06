<?php
/**
 *  @author Sven Vanpoucke
 */

abstract class DataClass
{
	const PROPERTY_ID = 'id';
	const NO_UID = -1;
	
	/**
	 * Default properties of the data class object, stored in an associative
	 * array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new data class object.
	 * @param array $defaultProperties The default properties of the data class
	 *                                 object. Associative array.
	 */
	function DataClass($defaultProperties = array ())
	{
		$this->defaultProperties = $defaultProperties;
	}
	
	/**
	 * Gets a default property of this data class object by name.
	 * @param string $name The name of the property.
	 */
	function get_default_property($name)
	{
		return $this->defaultProperties[$name];
	}
	
	/**
	 * Gets the default properties of this data class.
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
	 * Get the default properties of all data classes.
	 * @return array The property names.
	 */
	static function get_default_property_names($extended_property_names = array())
	{
		$extended_property_names[] = self :: PROPERTY_ID;
		return $extended_property_names;
	}
		
	/**
	 * Sets a default property of this data class by name.
	 * @param string $name The name of the property.
	 * @param mixed $value The new value for the property.
	 */
	function set_default_property($name, $value)
	{
		$this->defaultProperties[$name] = $value;
	}
	
	/**
	 * Checks if the given identifier is the name of a default data class
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
	 * Returns the id of this data class
	 * @return int The id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}
	
	/**
	 * Sets id of the data class
	 * @param int $id
	 */
	function set_id($id)
	{
	    if(isset($id) && strlen($id) > 0)
	    {
	        $this->set_default_property(self :: PROPERTY_ID, $id);
	    }
	}	

	function is_identified()
	{
	    $id = $this->get_id();
	    if(isset($id) && strlen($id) > 0 && $id != self :: NO_UID)
	    {
	        return true;
	    }
	    else
	    {
	        return false;
	    }
	}
	
	function save()
	{
	    if($this->is_identified())
	    {
	        return $this->update();
	    }
	    else
	    {
	        return $this->create();
	    }
	}
	
	function get_object_name()
	{
		$class = get_class($this);
		return DokeosUtilities :: camelcase_to_underscores($class);
	}
	
	function create()
	{
		$dm = $this->get_data_manager();
		$class_name = $this->get_object_name();
		
		$func = 'get_next_' . $class_name . '_id';
		$id = call_user_func(array($dm, $func));
		$this->set_id($id);
	
		$func = 'create_' . $class_name;
		return call_user_func(array($dm, $func), $this);
	}
	
	function update()
	{
		$dm = $this->get_data_manager();
		$class_name = $this->get_object_name();

		$func = 'update_' . $class_name;
		return call_user_func(array($dm, $func), $this);
	}
	
	function delete()
	{
		$dm = $this->get_data_manager();
		$class_name = $this->get_object_name();

		$func = 'delete_' . $class_name;
		return call_user_func(array($dm, $func), $this);
	}
	
	abstract function get_data_manager();

}
?>