<?php
/**
 * @package application.lib.weblcms.course
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once dirname(__FILE__).'/../weblcms_data_manager.class.php';
require_once Path :: get_common_path() . 'data_class.class.php';

/**
 *	This class represents a course user category in the weblcms.
 *
 *	course user categories have a number of default properties:
 *	- id: the numeric course user category ID;
 *	- user: the course user category user;
 *	- sort: the course user category sort order;
 *	- title: the course user category title;
 *
 * To access the values of the properties, this class and its subclasses
 * should provide accessor methods. The names of the properties should be
 * defined as class constants, for standardization purposes. It is recommended
 * that the names of these constants start with the string "PROPERTY_".
 *
 */

class CourseUserCategory extends DataClass
{
    const CLASS_NAME = __CLASS__;

	const PROPERTY_USER = 'user_id';
	const PROPERTY_SORT = 'sort';
	const PROPERTY_TITLE = 'title';

	/**
	 * Get the default properties of all user course user categories.
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return parent :: get_default_property_names(array (self :: PROPERTY_USER, self :: PROPERTY_SORT, self :: PROPERTY_TITLE));
	}

	/**
	 * inherited
	 */
	function get_data_manager()
	{
		return WeblcmsDataManager :: get_instance();	
	}
	
	/**
	 * Returns the user of this course user category object
	 * @return int
	 */
    function get_user()
    {
    	return $this->get_default_property(self :: PROPERTY_USER);
    }

    /**
     * Sets the user of this course user category object
     * @param int $user
     */
	function set_user($user)
	{
		$this->set_default_property(self :: PROPERTY_USER, $user);
	}

	/**
	 * Returns the sort order of this course user category object
	 * @return int
	 */
    function get_sort()
    {
    	return $this->get_default_property(self :: PROPERTY_SORT);
    }

    /**
     * Sets the sort order of this course user category object
     * @param int $sort
     */
	function set_sort($sort)
	{
		$this->set_default_property(self :: PROPERTY_SORT, $sort);
	}

	/**
	 * Returns the title of this course user category object
	 * @return string
	 */
    function get_title()
    {
    	return $this->get_default_property(self :: PROPERTY_TITLE);
    }

    /**
     * Sets the title of this course user category object
     * @param string $title
     */
	function set_title($title)
	{
		$this->set_default_property(self :: PROPERTY_TITLE, $title);
	}

	/**
	 * Creates the course user category object in persistent storage
	 * @return boolean
	 */
	function create()
	{
		$wdm = WeblcmsDataManager :: get_instance();
		$this->set_id($wdm->get_next_course_user_category_id());
		
        $condition = new EqualityCondition(self :: PROPERTY_USER, $this->get_user());
        $sort = $wdm->retrieve_max_sort_value(self :: get_table_name(), self :: PROPERTY_SORT, $condition);
        $this->set_sort($sort + 1);
		
		$success = $wdm->create_course_user_category($this);
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