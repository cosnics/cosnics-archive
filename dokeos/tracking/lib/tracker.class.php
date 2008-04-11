<?php 
/**
 * tracking.lib
 */

/**
 * This class presents a tracker
 *
 * @author Sven Vanpoucke
 */
class Tracker
{
	/**
	 * Tracker properties
	 */
	const PROPERTY_ID = 'id';
	const PROPERTY_CLASS = 'class';
	const PROPERTY_PATH = 'path';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Tracker object
	 * @param array $defaultProperties The default properties
	 */
	function Tracker($defaultProperties = array ())
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
		return array (self :: PROPERTY_ID, self :: PROPERTY_CLASS, self :: PROPERTY_PATH);
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
	 * Returns the id of this Tracker.
	 * @return the id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}

	/**
	 * Sets the id of this Tracker.
	 * @param id
	 */
	function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
	}

	/**
	 * Returns the class of this Tracker.
	 * @return the class.
	 */
	function get_class()
	{
		return $this->get_default_property(self :: PROPERTY_CLASS);
	}

	/**
	 * Sets the class of this Tracker.
	 * @param class
	 */
	function set_class($class)
	{
		$this->set_default_property(self :: PROPERTY_CLASS, $class);
	}

	/**
	 * Returns the path of this Tracker.
	 * @return the path.
	 */
	function get_path()
	{
		return $this->get_default_property(self :: PROPERTY_PATH);
	}

	/**
	 * Sets the path of this Tracker.
	 * @param path
	 */
	function set_path($path)
	{
		$this->set_default_property(self :: PROPERTY_PATH, $path);
	}

	/**
	 * Creates this event in the database
	 */
	function create()
	{
		$trkdmg = TrackingDataManager :: get_instance();
		$this->set_id($trkdmg->get_next_id('tracker'));
		$trkdmg->create_tracker($this);
	}

}

?>