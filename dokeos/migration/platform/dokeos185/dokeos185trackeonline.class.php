<?php 
/**
 * migration.lib.platform.dokeos185
 */

/**
 * This class presents a dokeos185 track_e_online
 *
 * @author Sven Vanpoucke
 */
class Dokeos185TrackEOnline
{
	/**
	 * Dokeos185TrackEOnline properties
	 */
	const PROPERTY_LOGIN_ID = 'login_id';
	const PROPERTY_LOGIN_USER_ID = 'login_user_id';
	const PROPERTY_LOGIN_DATE = 'login_date';
	const PROPERTY_LOGIN_IP = 'login_ip';
	const PROPERTY_COURSE = 'course';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Dokeos185TrackEOnline object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185TrackEOnline($defaultProperties = array ())
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
		return array (SELF :: PROPERTY_LOGIN_ID, SELF :: PROPERTY_LOGIN_USER_ID, SELF :: PROPERTY_LOGIN_DATE, SELF :: PROPERTY_LOGIN_IP, SELF :: PROPERTY_COURSE);
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
	 * Returns the login_id of this Dokeos185TrackEOnline.
	 * @return the login_id.
	 */
	function get_login_id()
	{
		return $this->get_default_property(self :: PROPERTY_LOGIN_ID);
	}

	/**
	 * Sets the login_id of this Dokeos185TrackEOnline.
	 * @param login_id
	 */
	function set_login_id($login_id)
	{
		$this->set_default_property(self :: PROPERTY_LOGIN_ID, $login_id);
	}
	/**
	 * Returns the login_user_id of this Dokeos185TrackEOnline.
	 * @return the login_user_id.
	 */
	function get_login_user_id()
	{
		return $this->get_default_property(self :: PROPERTY_LOGIN_USER_ID);
	}

	/**
	 * Sets the login_user_id of this Dokeos185TrackEOnline.
	 * @param login_user_id
	 */
	function set_login_user_id($login_user_id)
	{
		$this->set_default_property(self :: PROPERTY_LOGIN_USER_ID, $login_user_id);
	}
	/**
	 * Returns the login_date of this Dokeos185TrackEOnline.
	 * @return the login_date.
	 */
	function get_login_date()
	{
		return $this->get_default_property(self :: PROPERTY_LOGIN_DATE);
	}

	/**
	 * Sets the login_date of this Dokeos185TrackEOnline.
	 * @param login_date
	 */
	function set_login_date($login_date)
	{
		$this->set_default_property(self :: PROPERTY_LOGIN_DATE, $login_date);
	}
	/**
	 * Returns the login_ip of this Dokeos185TrackEOnline.
	 * @return the login_ip.
	 */
	function get_login_ip()
	{
		return $this->get_default_property(self :: PROPERTY_LOGIN_IP);
	}

	/**
	 * Sets the login_ip of this Dokeos185TrackEOnline.
	 * @param login_ip
	 */
	function set_login_ip($login_ip)
	{
		$this->set_default_property(self :: PROPERTY_LOGIN_IP, $login_ip);
	}
	/**
	 * Returns the course of this Dokeos185TrackEOnline.
	 * @return the course.
	 */
	function get_course()
	{
		return $this->get_default_property(self :: PROPERTY_COURSE);
	}

	/**
	 * Sets the course of this Dokeos185TrackEOnline.
	 * @param course
	 */
	function set_course($course)
	{
		$this->set_default_property(self :: PROPERTY_COURSE, $course);
	}

}

?>