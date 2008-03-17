<?php 
/**
 * migration.lib.platform.dokeos185
 */

/**
 * This class presents a dokeos185 track_e_open
 *
 * @author Sven Vanpoucke
 */
class Dokeos185TrackEOpen
{
	/**
	 * Dokeos185TrackEOpen properties
	 */
	const PROPERTY_OPEN_ID = 'open_id';
	const PROPERTY_OPEN_REMOTE_HOST = 'open_remote_host';
	const PROPERTY_OPEN_AGENT = 'open_agent';
	const PROPERTY_OPEN_REFERER = 'open_referer';
	const PROPERTY_OPEN_DATE = 'open_date';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Dokeos185TrackEOpen object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185TrackEOpen($defaultProperties = array ())
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
		return array (SELF :: PROPERTY_OPEN_ID, SELF :: PROPERTY_OPEN_REMOTE_HOST, SELF :: PROPERTY_OPEN_AGENT, SELF :: PROPERTY_OPEN_REFERER, SELF :: PROPERTY_OPEN_DATE);
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
	 * Returns the open_id of this Dokeos185TrackEOpen.
	 * @return the open_id.
	 */
	function get_open_id()
	{
		return $this->get_default_property(self :: PROPERTY_OPEN_ID);
	}

	/**
	 * Sets the open_id of this Dokeos185TrackEOpen.
	 * @param open_id
	 */
	function set_open_id($open_id)
	{
		$this->set_default_property(self :: PROPERTY_OPEN_ID, $open_id);
	}
	/**
	 * Returns the open_remote_host of this Dokeos185TrackEOpen.
	 * @return the open_remote_host.
	 */
	function get_open_remote_host()
	{
		return $this->get_default_property(self :: PROPERTY_OPEN_REMOTE_HOST);
	}

	/**
	 * Sets the open_remote_host of this Dokeos185TrackEOpen.
	 * @param open_remote_host
	 */
	function set_open_remote_host($open_remote_host)
	{
		$this->set_default_property(self :: PROPERTY_OPEN_REMOTE_HOST, $open_remote_host);
	}
	/**
	 * Returns the open_agent of this Dokeos185TrackEOpen.
	 * @return the open_agent.
	 */
	function get_open_agent()
	{
		return $this->get_default_property(self :: PROPERTY_OPEN_AGENT);
	}

	/**
	 * Sets the open_agent of this Dokeos185TrackEOpen.
	 * @param open_agent
	 */
	function set_open_agent($open_agent)
	{
		$this->set_default_property(self :: PROPERTY_OPEN_AGENT, $open_agent);
	}
	/**
	 * Returns the open_referer of this Dokeos185TrackEOpen.
	 * @return the open_referer.
	 */
	function get_open_referer()
	{
		return $this->get_default_property(self :: PROPERTY_OPEN_REFERER);
	}

	/**
	 * Sets the open_referer of this Dokeos185TrackEOpen.
	 * @param open_referer
	 */
	function set_open_referer($open_referer)
	{
		$this->set_default_property(self :: PROPERTY_OPEN_REFERER, $open_referer);
	}
	/**
	 * Returns the open_date of this Dokeos185TrackEOpen.
	 * @return the open_date.
	 */
	function get_open_date()
	{
		return $this->get_default_property(self :: PROPERTY_OPEN_DATE);
	}

	/**
	 * Sets the open_date of this Dokeos185TrackEOpen.
	 * @param open_date
	 */
	function set_open_date($open_date)
	{
		$this->set_default_property(self :: PROPERTY_OPEN_DATE, $open_date);
	}

}

?>