<?php

/**
 * @package migration.platform.dokeos185
 */

require_once dirname(__FILE__).'/../../lib/import/import_course.class.php';

/**
 * This class represents an old Dokeos 1.8.5 course
 *
 * @author David Van Wayenbergh
 */

class Dokeos185_Course extends Import
{
	const PROPERTY_CODE = 'code';
	const PROPERTY_DIRECTORY = 'directory';
	const PROPERTY_DB_NAME = 'db_name';
	const PROPERTY_COURSE_LANGUAGE = 'course_language';
	const PROPERTY_TITLE = 'title';
	const PROPERTY_DESCRIPTION = 'description';
	const PROPERTY_CATEGORY_CODE = 'category_code';
	const PROPERTY_VISIBILITY = 'visibility';
	const PROPERTY_SHOW_SCORE = 'show_score';
	const PROPERTY_TUTOR_NAME = 'tutor_name';
	const PROPERTY_VISUAL_CODE = 'visual_code';
	const PROPERTY_DEPARTMENT_URL = 'department_url';
	const PROPERTY_DISK_QUOTA = 'disk_quota';
	const PROPERTY_LAST_VISIT = 'last_visit';
	const PROPERTY_LAST_EDIT = 'last_edit';
	const PROPERTY_CREATION_DATE = 'creation_date';
	const PROPERTY_EXPIRATION_DATE = 'expiration_date';
	const PROPERTY_TARGET_COURSE_CODE = 'target_course_code';
	const PROPERTY_SUBSCRIBE = 'subscribe';
	const PROPERTY_UNSUBSCRIBE = 'unsubscribe';
	const PROPERTY_REGISTRATION_CODE = 'registration_code';
	
	/**
	 * Alfanumeric identifier of the course object.
	 */
	private $code;
	
	/**
	 * Default properties of the course object, stored in an associative
	 * array.
	 */
	private $defaultProperties;
	
	/**
	 * Creates a new course object.
	 * @param array $defaultProperties The default properties of the user
	 *                                 object. Associative array.
	 */
	function Course($defaultProperties = array ())
	{
		$this->defaultProperties = $defaultProperties;
	}
	
	/**
	 * Gets a default property of this course object by name.
	 * @param string $name The name of the property.
	 */
	function get_default_property($name)
	{
		return $this->defaultProperties[$name];
	}
	
	/**
	 * Gets the default properties of this course.
	 * @return array An associative array containing the properties.
	 */
	function get_default_properties()
	{
		return $this->defaultProperties;
	}
	
	/**
	 * Get the default properties of all courses.
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_USER_ID, self :: PROPERTY_LASTNAME, self :: PROPERTY_FIRSTNAME, self :: PROPERTY_USERNAME, self :: PROPERTY_PASSWORD, self :: PROPERTY_AUTH_SOURCE, self :: PROPERTY_EMAIL, self :: PROPERTY_STATUS, self :: PROPERTY_PLATFORMADMIN, self :: PROPERTY_PHONE, self :: PROPERTY_OFFICIAL_CODE, self ::PROPERTY_PICTURE_URI, self :: PROPERTY_CREATOR_ID, self :: PROPERTY_LANGUAGE, self :: PROPERTY_DISK_QUOTA, self :: PROPERTY_DATABASE_QUOTA, self :: PROPERTY_VERSION_QUOTA);
	}
	
	/**
	 * Sets a default property of this course by name.
	 * @param string $name The name of the property.
	 * @param mixed $value The new value for the property.
	 */
	function set_default_property($name, $value)
	{
		$this->defaultProperties[$name] = $value;
	}
	
	/**
	 * Checks if the given identifier is the name of a default course
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
	 * Returns the code of this course.
	 * @return String The code.
	 */
	function get_code()
	{
		return $this->code;
	}
	
	/**
	 * Returns the directory of this course.
	 * @return String The directory.
	 */
	function get_Directory()
	{
		return $this->directory;
	}
	
	/**
	 * Returns the db_name of this course.
	 * @return String The db_name.
	 */
	function get_db_name()
	{
		return $this->db_name;
	}
	
	/**
	 * Returns the course_language of this course.
	 * @return String The course_language.
	 */
	function get_course_language()
	{
		return $this->course_language;
	}
	
	/**
	 * Returns the title of this course.
	 * @return String The title.
	 */
	function get_title()
	{
		return $this->title;
	}
	
	/**
	 * Returns the description of this course.
	 * @return String The discription.
	 */
	function get_discription()
	{
		return $this->description;
	}
	
	/**
	 * Returns the category_code of this course.
	 * @return String The category_code.
	 */
	function get_category_code()
	{
		return $this->category_code;
	}
	
	/**
	 * Returns the visibility of this course.
	 * @return int The visibility.
	 */
	function get_visibility()
	{
		return $this->visibility;
	}
	
	/**
	 * Returns the show_score of this course.
	 * @return int The show_score.
	 */
	function get_show_score()
	{
		return $this->show_score;
	}
	
	/**
	 * Returns the tutor_name of this course.
	 * @return String The tutor_name.
	 */
	function get_tutor_name()
	{
		return $this->tutor_name;
	}
	
	/**
	 * Returns the visual_code of this course.
	 * @return String The visual_code.
	 */
	function get_visual_code()
	{
		return $this->visual_code;
	}
	
	/**
	 * Returns the department_url of this course.
	 * @return String The department_url.
	 */
	function get_department_url()
	{
		return $this->department_url;
	}
	
	/**
	 * Returns the disk_quota of this course.
	 * @return int The disk_quota.
	 */
	function get_disk_quota()
	{
		return $this->disk_quota;
	}
	
	/**
	 * Returns the last_visit of this course.
	 * @return String The last_visit.
	 */
	function last_visit()
	{
		return $this->last_visit;
	}
	
	/**
	 * Returns the last_edit of this course.
	 * @return String The last_edit.
	 */
	function get_last_edit()
	{
		return $this->last_edit;
	}
	
	/**
	 * Returns the creation_date of this course.
	 * @return String The creation_date.
	 */
	function get_creation_date()
	{
		return $this->creation_date;
	}
	
	/**
	 * Returns the expiration_date of this course.
	 * @return String The expiration_date.
	 */
	function get_expiration_date()
	{
		return $this->expiration_date;
	}
	
	/**
	 * Returns the target_course_code of this course.
	 * @return String The target_course_code.
	 */
	function get_target_course_code()
	{
		return $this->target_course_code;
	}
	
	/**
	 * Returns the subscribe of this course.
	 * @return int The subscribe.
	 */
	function get_subscribe()
	{
		return $this->subscribe;
	}
	
	/**
	 * Returns the unsubscribe of this course.
	 * @return int The unsubscribe.
	 */
	function get_unsubsribe()
	{
		return $this->unsubscribe;
	}
	
	/**
	 * Returns the registration_code of this course.
	 * @return String The registration_code.
	 */
	function get_registration_code()
	{
		return $this->registration_code;
	}
}
?>
