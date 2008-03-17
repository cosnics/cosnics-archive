<?php 
/**
 * migration.lib.platform.dokeos185
 */

/**
 * This class presents a dokeos185 track_e_exercices
 *
 * @author Sven Vanpoucke
 */
class Dokeos185TrackEExercices
{
	/**
	 * Dokeos185TrackEExercices properties
	 */
	const PROPERTY_EXE_ID = 'exe_id';
	const PROPERTY_EXE_USER_ID = 'exe_user_id';
	const PROPERTY_EXE_DATE = 'exe_date';
	const PROPERTY_EXE_COURS_ID = 'exe_cours_id';
	const PROPERTY_EXE_EXO_ID = 'exe_exo_id';
	const PROPERTY_EXE_RESULT = 'exe_result';
	const PROPERTY_EXE_WEIGHTING = 'exe_weighting';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Dokeos185TrackEExercices object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185TrackEExercices($defaultProperties = array ())
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
		return array (SELF :: PROPERTY_EXE_ID, SELF :: PROPERTY_EXE_USER_ID, SELF :: PROPERTY_EXE_DATE, SELF :: PROPERTY_EXE_COURS_ID, SELF :: PROPERTY_EXE_EXO_ID, SELF :: PROPERTY_EXE_RESULT, SELF :: PROPERTY_EXE_WEIGHTING);
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
	 * Returns the exe_id of this Dokeos185TrackEExercices.
	 * @return the exe_id.
	 */
	function get_exe_id()
	{
		return $this->get_default_property(self :: PROPERTY_EXE_ID);
	}

	/**
	 * Sets the exe_id of this Dokeos185TrackEExercices.
	 * @param exe_id
	 */
	function set_exe_id($exe_id)
	{
		$this->set_default_property(self :: PROPERTY_EXE_ID, $exe_id);
	}
	/**
	 * Returns the exe_user_id of this Dokeos185TrackEExercices.
	 * @return the exe_user_id.
	 */
	function get_exe_user_id()
	{
		return $this->get_default_property(self :: PROPERTY_EXE_USER_ID);
	}

	/**
	 * Sets the exe_user_id of this Dokeos185TrackEExercices.
	 * @param exe_user_id
	 */
	function set_exe_user_id($exe_user_id)
	{
		$this->set_default_property(self :: PROPERTY_EXE_USER_ID, $exe_user_id);
	}
	/**
	 * Returns the exe_date of this Dokeos185TrackEExercices.
	 * @return the exe_date.
	 */
	function get_exe_date()
	{
		return $this->get_default_property(self :: PROPERTY_EXE_DATE);
	}

	/**
	 * Sets the exe_date of this Dokeos185TrackEExercices.
	 * @param exe_date
	 */
	function set_exe_date($exe_date)
	{
		$this->set_default_property(self :: PROPERTY_EXE_DATE, $exe_date);
	}
	/**
	 * Returns the exe_cours_id of this Dokeos185TrackEExercices.
	 * @return the exe_cours_id.
	 */
	function get_exe_cours_id()
	{
		return $this->get_default_property(self :: PROPERTY_EXE_COURS_ID);
	}

	/**
	 * Sets the exe_cours_id of this Dokeos185TrackEExercices.
	 * @param exe_cours_id
	 */
	function set_exe_cours_id($exe_cours_id)
	{
		$this->set_default_property(self :: PROPERTY_EXE_COURS_ID, $exe_cours_id);
	}
	/**
	 * Returns the exe_exo_id of this Dokeos185TrackEExercices.
	 * @return the exe_exo_id.
	 */
	function get_exe_exo_id()
	{
		return $this->get_default_property(self :: PROPERTY_EXE_EXO_ID);
	}

	/**
	 * Sets the exe_exo_id of this Dokeos185TrackEExercices.
	 * @param exe_exo_id
	 */
	function set_exe_exo_id($exe_exo_id)
	{
		$this->set_default_property(self :: PROPERTY_EXE_EXO_ID, $exe_exo_id);
	}
	/**
	 * Returns the exe_result of this Dokeos185TrackEExercices.
	 * @return the exe_result.
	 */
	function get_exe_result()
	{
		return $this->get_default_property(self :: PROPERTY_EXE_RESULT);
	}

	/**
	 * Sets the exe_result of this Dokeos185TrackEExercices.
	 * @param exe_result
	 */
	function set_exe_result($exe_result)
	{
		$this->set_default_property(self :: PROPERTY_EXE_RESULT, $exe_result);
	}
	/**
	 * Returns the exe_weighting of this Dokeos185TrackEExercices.
	 * @return the exe_weighting.
	 */
	function get_exe_weighting()
	{
		return $this->get_default_property(self :: PROPERTY_EXE_WEIGHTING);
	}

	/**
	 * Sets the exe_weighting of this Dokeos185TrackEExercices.
	 * @param exe_weighting
	 */
	function set_exe_weighting($exe_weighting)
	{
		$this->set_default_property(self :: PROPERTY_EXE_WEIGHTING, $exe_weighting);
	}

}

?>