<?php 
/**
 * migration.lib.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../../lib/import/import_survey_invitation.class.php';

/**
 * This class presents a Dokeos185 survey_invitation
 *
 * @author Sven Vanpoucke
 */
class Dokeos185SurveyInvitation extends ImportSurveyInvitation
{
	private static $mgdm;
	
	/**
	 * Dokeos185SurveyInvitation properties
	 */
	const PROPERTY_SURVEY_INVITATION_ID = 'survey_invitation_id';
	const PROPERTY_SURVEY_CODE = 'survey_code';
	const PROPERTY_USER = 'user';
	const PROPERTY_INVITATION_CODE = 'invitation_code';
	const PROPERTY_INVITATION_DATE = 'invitation_date';
	const PROPERTY_REMINDER_DATE = 'reminder_date';
	const PROPERTY_ANSWERED = 'answered';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Dokeos185SurveyInvitation object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185SurveyInvitation($defaultProperties = array ())
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
		return array (self :: PROPERTY_SURVEY_INVITATION_ID, self :: PROPERTY_SURVEY_CODE, self :: PROPERTY_USER, self :: PROPERTY_INVITATION_CODE, self :: PROPERTY_INVITATION_DATE, self :: PROPERTY_REMINDER_DATE, self :: PROPERTY_ANSWERED);
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
	 * Returns the survey_invitation_id of this Dokeos185SurveyInvitation.
	 * @return the survey_invitation_id.
	 */
	function get_survey_invitation_id()
	{
		return $this->get_default_property(self :: PROPERTY_SURVEY_INVITATION_ID);
	}

	/**
	 * Returns the survey_code of this Dokeos185SurveyInvitation.
	 * @return the survey_code.
	 */
	function get_survey_code()
	{
		return $this->get_default_property(self :: PROPERTY_SURVEY_CODE);
	}

	/**
	 * Returns the user of this Dokeos185SurveyInvitation.
	 * @return the user.
	 */
	function get_user()
	{
		return $this->get_default_property(self :: PROPERTY_USER);
	}

	/**
	 * Returns the invitation_code of this Dokeos185SurveyInvitation.
	 * @return the invitation_code.
	 */
	function get_invitation_code()
	{
		return $this->get_default_property(self :: PROPERTY_INVITATION_CODE);
	}

	/**
	 * Returns the invitation_date of this Dokeos185SurveyInvitation.
	 * @return the invitation_date.
	 */
	function get_invitation_date()
	{
		return $this->get_default_property(self :: PROPERTY_INVITATION_DATE);
	}

	/**
	 * Returns the reminder_date of this Dokeos185SurveyInvitation.
	 * @return the reminder_date.
	 */
	function get_reminder_date()
	{
		return $this->get_default_property(self :: PROPERTY_REMINDER_DATE);
	}

	/**
	 * Returns the answered of this Dokeos185SurveyInvitation.
	 * @return the answered.
	 */
	function get_answered()
	{
		return $this->get_default_property(self :: PROPERTY_ANSWERED);
	}
	
	/**
	 * Checks if a surveyinvitation is valid
	 * @param Array $array
	 * @return Boolean
	 */
	function is_valid($array)
	{
		$course = $array['course'];
	}
	
	/**
	 * migrate surveyinvitation, sets category
	 * @param Array $array
	 * @return 
	 */
	function convert_to_lcms($array)
	{	
		$course = $array['course'];
	}
	
	/**
	 * Gets all the survey questions of a course
	 * @param Array $array
	 * @return Array of dokeos185surveyinvitation
	 */
	static function get_all($parameters)
	{
		$old_mgdm = $parameters['old_mgdm'];
		
		$coursedb = $parameters['course']->get_db_name();
		$tablename = 'survey_invitation';
		$classname = 'Dokeos185SurveyInvitation';
			
		return $old_mgdm->get_all($coursedb, $tablename, $classname, $tool_name, $parameters['offset'], $parameters['limit']);	
	}
	
	static function get_database_table($parameters)
	{
		$array = array();
		$array['database'] = $parameters['course']->get_db_name();
		$array['table'] = 'survey_invitation';
		return $array;
	}
}

?>