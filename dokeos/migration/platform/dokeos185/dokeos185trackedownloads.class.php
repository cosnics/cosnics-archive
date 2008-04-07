<?php 
/**
 * migration.lib.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../../lib/import/importtrackedownloads.class.php';

/**
 * This class presents a Dokeos185 track_e_downloads
 *
 * @author Sven Vanpoucke
 */
class Dokeos185TrackEDownloads extends ImportTrackEDownloads
{
	private static $mgdm;

	/**
	 * Dokeos185TrackEDownloads properties
	 */
	const PROPERTY_DOWN_ID = 'down_id';
	const PROPERTY_DOWN_USER_ID = 'down_user_id';
	const PROPERTY_DOWN_DATE = 'down_date';
	const PROPERTY_DOWN_COURS_ID = 'down_cours_id';
	const PROPERTY_DOWN_DOC_PATH = 'down_doc_path';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Dokeos185TrackEDownloads object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185TrackEDownloads($defaultProperties = array ())
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
		return array (self :: PROPERTY_DOWN_ID, self :: PROPERTY_DOWN_USER_ID, self :: PROPERTY_DOWN_DATE, self :: PROPERTY_DOWN_COURS_ID, self :: PROPERTY_DOWN_DOC_PATH);
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
	 * Returns the down_id of this Dokeos185TrackEDownloads.
	 * @return the down_id.
	 */
	function get_down_id()
	{
		return $this->get_default_property(self :: PROPERTY_DOWN_ID);
	}

	/**
	 * Returns the down_user_id of this Dokeos185TrackEDownloads.
	 * @return the down_user_id.
	 */
	function get_down_user_id()
	{
		return $this->get_default_property(self :: PROPERTY_DOWN_USER_ID);
	}

	/**
	 * Returns the down_date of this Dokeos185TrackEDownloads.
	 * @return the down_date.
	 */
	function get_down_date()
	{
		return $this->get_default_property(self :: PROPERTY_DOWN_DATE);
	}

	/**
	 * Returns the down_cours_id of this Dokeos185TrackEDownloads.
	 * @return the down_cours_id.
	 */
	function get_down_cours_id()
	{
		return $this->get_default_property(self :: PROPERTY_DOWN_COURS_ID);
	}

	/**
	 * Returns the down_doc_path of this Dokeos185TrackEDownloads.
	 * @return the down_doc_path.
	 */
	function get_down_doc_path()
	{
		return $this->get_default_property(self :: PROPERTY_DOWN_DOC_PATH);
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
		
		$db = 'statistics_database';
		$tablename = 'track_e_downloads';
		$classname = 'Dokeos185TrackEDownloads';
			
		return self :: $mgdm->get_all($db, $tablename, $classname, $tool_name);	
	}

}

?>