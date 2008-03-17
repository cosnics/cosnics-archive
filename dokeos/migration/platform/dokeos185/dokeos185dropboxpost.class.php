<?php 
/**
 * migration.lib.platform.dokeos185
 */

/**
 * This class presents a dokeos185 dropbox_post
 *
 * @author Sven Vanpoucke
 */
class Dokeos185DropboxPost
{
	/**
	 * Dokeos185DropboxPost properties
	 */
	const PROPERTY_FILE_ID = 'file_id';
	const PROPERTY_DEST_USER_ID = 'dest_user_id';
	const PROPERTY_FEEDBACK_DATE = 'feedback_date';
	const PROPERTY_FEEDBACK = 'feedback';
	const PROPERTY_CAT_ID = 'cat_id';
	const PROPERTY_SESSION_ID = 'session_id';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Dokeos185DropboxPost object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185DropboxPost($defaultProperties = array ())
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
		return array (SELF :: PROPERTY_FILE_ID, SELF :: PROPERTY_DEST_USER_ID, SELF :: PROPERTY_FEEDBACK_DATE, SELF :: PROPERTY_FEEDBACK, SELF :: PROPERTY_CAT_ID, SELF :: PROPERTY_SESSION_ID);
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
	 * Returns the file_id of this Dokeos185DropboxPost.
	 * @return the file_id.
	 */
	function get_file_id()
	{
		return $this->get_default_property(self :: PROPERTY_FILE_ID);
	}

	/**
	 * Sets the file_id of this Dokeos185DropboxPost.
	 * @param file_id
	 */
	function set_file_id($file_id)
	{
		$this->set_default_property(self :: PROPERTY_FILE_ID, $file_id);
	}
	/**
	 * Returns the dest_user_id of this Dokeos185DropboxPost.
	 * @return the dest_user_id.
	 */
	function get_dest_user_id()
	{
		return $this->get_default_property(self :: PROPERTY_DEST_USER_ID);
	}

	/**
	 * Sets the dest_user_id of this Dokeos185DropboxPost.
	 * @param dest_user_id
	 */
	function set_dest_user_id($dest_user_id)
	{
		$this->set_default_property(self :: PROPERTY_DEST_USER_ID, $dest_user_id);
	}
	/**
	 * Returns the feedback_date of this Dokeos185DropboxPost.
	 * @return the feedback_date.
	 */
	function get_feedback_date()
	{
		return $this->get_default_property(self :: PROPERTY_FEEDBACK_DATE);
	}

	/**
	 * Sets the feedback_date of this Dokeos185DropboxPost.
	 * @param feedback_date
	 */
	function set_feedback_date($feedback_date)
	{
		$this->set_default_property(self :: PROPERTY_FEEDBACK_DATE, $feedback_date);
	}
	/**
	 * Returns the feedback of this Dokeos185DropboxPost.
	 * @return the feedback.
	 */
	function get_feedback()
	{
		return $this->get_default_property(self :: PROPERTY_FEEDBACK);
	}

	/**
	 * Sets the feedback of this Dokeos185DropboxPost.
	 * @param feedback
	 */
	function set_feedback($feedback)
	{
		$this->set_default_property(self :: PROPERTY_FEEDBACK, $feedback);
	}
	/**
	 * Returns the cat_id of this Dokeos185DropboxPost.
	 * @return the cat_id.
	 */
	function get_cat_id()
	{
		return $this->get_default_property(self :: PROPERTY_CAT_ID);
	}

	/**
	 * Sets the cat_id of this Dokeos185DropboxPost.
	 * @param cat_id
	 */
	function set_cat_id($cat_id)
	{
		$this->set_default_property(self :: PROPERTY_CAT_ID, $cat_id);
	}
	/**
	 * Returns the session_id of this Dokeos185DropboxPost.
	 * @return the session_id.
	 */
	function get_session_id()
	{
		return $this->get_default_property(self :: PROPERTY_SESSION_ID);
	}

	/**
	 * Sets the session_id of this Dokeos185DropboxPost.
	 * @param session_id
	 */
	function set_session_id($session_id)
	{
		$this->set_default_property(self :: PROPERTY_SESSION_ID, $session_id);
	}

}

?>