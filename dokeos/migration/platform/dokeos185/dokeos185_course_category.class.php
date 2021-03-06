<?php

/**
 * @package migration.platform.dokeos185
 */


require_once dirname(__FILE__).'/../../lib/import/import_course_category.class.php';
require_once dirname(__FILE__) . '/../../../application/lib/weblcms/category_manager/course_category.class.php';


/**
 * This class represents an old Dokeos 1.8.5 course_category
 *
 * @author David Van Wayenbergh
 * @author Sven Vanpoucke
 */

class Dokeos185CourseCategory extends ImportCourseCategory
{
	/**
	 * Migration data manager
	 */

	
	/**
	 * course category properties
	 */
	const PROPERTY_ID = 'id';
	const PROPERTY_NAME = 'name';
	const PROPERTY_CODE = 'code';
	const PROPERTY_PARENT_ID = 'parent_id';
	const PROPERTY_TREE_POS = 'tree_pos';
	const PROPERTY_CHILDREN_COUNT = 'children_cont';
	const PROPERTY_AUTH_COURSE_CHILD = 'auth_course_child';
	const PROPERTY_AUTH_CAT_CHILD = 'auth_cat_child';

    /**
	 * Alfanumeric identifier of the course object.
	 */
	private $code;
	
	/**
	 * Default properties of the course_category object, stored in an associative
	 * array.
	 */
	private $defaultProperties;
	
	/**
	 * Creates a new course object.
	 * @param array $defaultProperties The default properties of the user
	 *                                 object. Associative array.
	 */
	function Dokeos185CourseCategory($defaultProperties = array ())
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
		return array (self :: PROPERTY_ID, self :: PROPERTY_NAME, self :: PROPERTY_CODE,
		self ::PROPERTY_PARENT_ID, self::PROPERTY_TREE_POS, self::PROPERTY_CHILDREN_COUNT,
		self::PROPERTY_AUTH_COURSE_CHILD, self::PROPERTY_AUTH_CAT_CHILD);
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
	 * Sets the default properties of this class
	 */
	function set_default_properties($defaultProperties)
	{
		$this->defaultProperties = $defaultProperties;
	}
	
	/**
	 * CATEGORY GETTERS AND SETTERS
	 */
	 
	 /**
	 * Returns the ID of this category.
	 * @return int The ID.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}
	
	/**
	 * Returns the name of this category.
	 * @return String The name.
	 */
	function get_name()
	{
		return $this->get_default_property(self :: PROPERTY_NAME);
	}
	
	/**
	 * Returns the code of this category.
	 * @return String The code.
	 */
	function get_code()
	{
		return $this->get_default_property(self :: PROPERTY_CODE);
	}
	
	/**
	 * Returns the parent_id of this category.
	 * @return String The parent_id.
	 */
	function get_parent_id()
	{
		return $this->get_default_property(self :: PROPERTY_PARENT_ID);
	}
	
	/**
	 * Returns the tree_pos of this category.
	 * @return int The tree_pos.
	 */
	function get_tree_pos()
	{
		return $this->get_default_property(self :: PROPERTY_TREE_POS);
	}
	
	/**
	 * Returns the children_count of this category.
	 * @return int The children_count.
	 */
	function get_children_count()
	{
		return $this->get_default_property(self :: PROPERTY_CHILDREN_COUNT);
	}
	
	/**
	 * Returns the auth_course_child of this category.
	 * @return Boolean The auth_course_child.
	 */
	function get_auth_course_child()
	{
		return $this->get_default_property(self :: PROPERTY_AUTH_COURSE_CHILD);
	}
	
	/**
	 * Returns the auth_cat_child of this category.
	 * @return Boolean The auth_cat_child.
	 */
	function get_auth_cat_child()
	{
		return $this->get_default_property(self :: PROPERTY_AUTH_CAT_CHILD);
	}
	
	/**
	 * Sets the code of this category.
	 * @param String $code The code.
	 */
	function set_code($code)
	{
		$this->set_default_property(self :: PROPERTY_CODE, $code);
	}
	
	/**
	 * Check if the course category is valid
	 * @param array $array the parameters for the validation
	 * @return true if the course category is valid 
	 */
	function is_valid($parameters)
	{
		$mgdm = MigrationDataManager :: get_instance();
		if(!$this->get_name() || !$this->get_code())
		{
			$mgdm->add_failed_element($this->get_id(),
				'dokeos_main.course_category');
			return false;
		}
		
		return true;
	}
	
	/**
	 * Convert to new course category
	 * @return the new course category
	 */
	function convert_to_lcms($parameters)
	{	
		$mgdm = MigrationDataManager :: get_instance();
		//Course category parameters
		$lcms_course_category = new CourseCategory();
		
		$lcms_course_category->set_name($this->get_name());
		
		$old_code = $this->get_code();
		$index = 0;
		while($mgdm->code_available('weblcms_course_category',$this->get_code()))
		{
			$this->set_code($this->get_code() . ($index ++));
		}
		unset($index);		
		
		//Add id references to temp table
		$mgdm->add_id_reference($old_code, $lcms_course_category->get_id(), 'weblcms_course_category');
		unset($old_code);

		if($this->get_parent_id())
		{
			$parent_id = $mgdm->get_id_reference($this->get_parent_id(), 'weblcms_course_category');
			if($parent_id)
				$lcms_course_category->set_parent($parent_id);
			unset($parent_id);
		}
		else
			$lcms_course_category->set_parent(0);
		
		//setters are not yet supported
                $lcms_course_category->set_default_property('tree_pos', $this->get_tree_pos());
		
		if($this->get_children_count())
			$lcms_course_category->set_default_property('children_count',$this->get_children_count());
		else
			$lcms_course_category->set_default_property('children_count',0);
			
		$lcms_course_category->set_default_property('auth_course_child',$this->get_auth_course_child()?1:0);
		$lcms_course_category->set_default_property('auth_cat_child',$this->get_auth_cat_child()?1:0);

		//create course_category in database
		$lcms_course_category->create();
		unset($mgdm);
		return $lcms_course_category;
	}
	
	/**
	 * Retrieve all course categories from the database
	 * @param array $parameters parameters for the retrieval
	 * @return array of course categories
	 */
	static function get_all($parameters)
	{
		$old_mgdm = $parameters['old_mgdm'];
		
		$db = 'main_database';
		$tablename = 'course_category';
		$classname = 'Dokeos185CourseCategory';
			
		return $old_mgdm->get_all($db, $tablename, $classname, $tool_name, $parameters['offset'], $parameters['limit']);	
	}
	
	static function get_database_table($parameters)
	{
		$array = array();
		$array['database'] = 'main_database';
		$array['table'] = 'course_category';
		return $array;
	}
}
?>