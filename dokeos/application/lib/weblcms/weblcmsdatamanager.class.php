<?php
/**
 * $Id$
 * @package application.weblcms
 */
require_once Path :: get_library_path().'configuration/configuration.class.php';
require_once dirname(__FILE__).'/../../../repository/lib/repositorydatamanager.class.php';
require_once dirname(__FILE__).'/../../../users/lib/usersdatamanager.class.php';
/**
==============================================================================
 *	This is a skeleton for a data manager for the Weblcms application. Data
 *	managers must extend this class.
 *
 *	@author Tim De Pauw
==============================================================================
 */

abstract class WeblcmsDataManager
{
	/**
	 * Instance of the class, for the singleton pattern.
	 */
	private static $instance;

	/**
	 * Constructor. Initializes the data manager.
	 */
	protected function WeblcmsDataManager()
	{
		$this->initialize();
	}

	/**
	 * Creates the shared instance of the configured data manager if
	 * necessary and returns it. Uses a factory pattern.
	 * @return WeblcmsDataManager The instance.
	 */
	static function get_instance()
	{
		if (!isset (self :: $instance))
		{
			$type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
			require_once dirname(__FILE__).'/data_manager/'.strtolower($type).'.class.php';
			$class = $type.'WeblcmsDataManager';
			self :: $instance = new $class ();
		}
		return self :: $instance;
	}

	abstract function retrieve_max_sort_value($table, $column, $condition = null);

	/**
	 * Determines whether the given learning object has been published in this
	 * application.
	 * @param int $object_id The ID of the learning object.
	 * @return boolean True if the object is currently published, false
	 *                 otherwise.
	 */
	abstract function learning_object_is_published($object_id);

	/**
	 * Determines whether any of the given learning objects has been published
	 * in this application.
	 * @param array $object_ids The Id's of the learning objects
	 * @return boolean True if at least one of the given objects is published in
	 * this application, false otherwise
	 */
	abstract function any_learning_object_is_published($object_ids);

	/**
	 * Determines where in this application the given learning object has been
	 * published.
	 * @param int $object_id The ID of the learning object.
	 * @return array An array of LearningObjectPublicationAttributes objects;
	 *               empty if the object has not been published anywhere.
	 */
	abstract function get_learning_object_publication_attributes($user, $object_id, $type = null, $offset = null, $count = null, $order_property = null, $order_direction = null);

	/**
	 * Retrieves the attributes for the given publication.
	 * @param int $publication_id
	 * @return array An array of LearningObjectPublicationAttributes objects;
	 *               empty if the object has not been published anywhere.
	 */
	abstract function get_learning_object_publication_attribute($publication_id);

	/**
	 * Counts the publication attributes
	 * @param string $type Type of retrieval
	 * @param Condition $conditions
	 * @return int
	 */
	abstract function count_publication_attributes($user, $type = null, $condition = null);

	/**
	 * Delete the publications
	 * @param Array $object_id An array of publication ids
	 * @return boolean
	 */
	abstract function delete_learning_object_publications($object_id);

	/**
	 * Initializes the data manager.
	 */
	abstract function initialize();

	/**
	 * Retrieves a single learning object publication from persistent
	 * storage.
	 * @param int $pid The numeric identifier of the publication.
	 * @return LearningObjectPublication The publication.
	 */
	abstract function retrieve_learning_object_publication($pid);

	/**
	 * Retrieves learning object publications from persistent storage.
	 * @param string $course The ID of the course to find publications in, or
	 *                       null if none.
	 * @param mixed $categories The IDs of the category that publications must
	 *                          located in, or null if none.
	 * @param mixed $users The IDs of the users who should have access to the
	 *                     publications, or null if any. An empty array means
	 *                     the publication should be accessible to all users.
	 * @param mixed $groups The IDs of the groups that should have access to
	 *                      the publications, or null if any. An empty array
	 *                      means the publication should be accessible to all
	 *                      groups.
	 * @param Condition $condition A Condition for publication selection. See
	 *                             the Conditions framework.
	 * @param boolean $allowDuplicates Whether or not to allow the same
	 *                                 publication to be returned twice, e.g.
	 *                                 if it was published for several groups
	 *                                 that the user is a member of. Defaults
	 *                                 to false.
	 * @param array $orderBy The properties to order publications by.
	 * @param array $orderDir An array representing the sorting direction
	 *                        for the corresponding property of $orderBy.
	 *                        Use SORT_ASC for ascending order, SORT_DESC
	 *                        for descending.
	 * @param int $offset The index of the first publication to retrieve.
	 * @param int $maxObjects The maximum number of objects to retrieve.
	 * @return ResultSet A set of LearningObjectPublications.
	 */
	abstract function retrieve_learning_object_publications($course = null, $categories = null, $users = null, $groups = null, $condition = null, $allowDuplicates = false, $orderBy = array ('display_order'), $orderDir = array (SORT_ASC), $offset = 0, $maxObjects = -1);

	/**
	 * Counts learning object publications in persistent storage.
	 * @param string $course The ID of the course to find publications in, or
	 *                       null if none.
	 * @param mixed $categories The IDs of the category that publications must
	 *                          located in, or null if none.
	 * @param mixed $users The IDs of the user who should have access to the
	 *                     publications, or null if none.
	 * @param mixed $groups The IDs of the groups who should have access to
	 *                      the publications, or null if none.
	 * @param Condition $condition A Condition for publication selection. See
	 *                             the Conditions framework.
	 * @param boolean $allowDuplicates Whether or not to allow the same
	 *                                 publication to be returned twice, e.g.
	 *                                 if it was published for several groups
	 *                                 that the user is a member of. Defaults
	 *                                 to false.
	 * @return int The number of matching learning object publications.
	 */
	abstract function count_learning_object_publications($course = null, $categories = null, $users = null, $groups = null, $condition = null, $allowDuplicates = false);

	/**
	 * Count the number of courses
	 * @param Condition $condition
	 * @return int
	 */
	abstract function count_courses($conditions = null);

	/**
	 * Count the number of course categories
	 * @param Condition $condition
	 * @return int
	 */
	abstract function count_course_categories($condition = null);

	/**
	 * Count the number of courses th user is subscribed to
	 * @param Condition $condition
	 * @return int
	 */
	abstract function count_user_courses($conditions = null);

	/**
	 * Count the number of course user relations
	 * @param Condition $condition
	 * @return int
	 */
	abstract function count_course_user_relations($conditions = null);

	/**
	 * Count the number of course user categories
	 * @param Condition $condition
	 * @return int
	 */
	abstract function count_course_user_categories($conditions = null);

	/**
	 * Returns the next available learning object publication ID.
	 * @return int The ID.
	 */
	abstract function get_next_learning_object_publication_id();

	/**
	 * Creates a course object in persistent storage.
	 * @param Course $course The course to make persistent.
	 * @return boolean True if creation succceeded, false otherwise.
	 */
	abstract function create_course($course);

	/**
	 * Creates a course category object in persistent storage.
	 * @param CourseCategory $coursecategory The course category to make persistent.
	 * @return boolean True if creation succceeded, false otherwise.
	 */
	abstract function create_course_category($coursecategory);

	/**
	 * Checks whether subscription to a specific course is allowed.
	 * @param Course $course
	 * @param int $user_id
	 * @return boolean
	 */
	function course_subscription_allowed($course, $user_id)
	{
		$already_subscribed = $this->is_subscribed($course, $user_id);
		if ($course->get_visibility() == COURSE_VISIBILITY_CLOSED || $course->get_visibility() == COURSE_VISIBILITY_REGISTERED)
		{
			$visibility = false;
		}
		else
		{
			$visibility = true;
		}

		$subscription_allowed = ($course->get_subscribe_allowed() == 1 ? true : false);

		if ($visibility && !$already_subscribed && $subscription_allowed)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Checks whether unsubscription from a specific course is allowed.
	 * @param Course $course
	 * @param int $user_id
	 * @return boolean
	 */
	function course_unsubscription_allowed($course, $user_id)
	{
		if ($course->is_course_admin($user_id))
		{
			return false;
		}

		$already_subscribed = $this->is_subscribed($course, $user_id);
		$unsubscription_allowed = ($course->get_unsubscribe_allowed() == 1 ? true : false);
		if ($already_subscribed && $unsubscription_allowed)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Subscribe a user to a course.
	 * @param Course $course
	 * @param int $status
	 * @param int $tutor_id
	 * @param int $user_id
	 * @return boolean
	 */
	abstract function subscribe_user_to_course($course, $status, $tutor_id, $user_id);

	/**
	 * Unsubscribe a user from a course.
	 * @param Course $course
	 * @param int $user_id
	 * @return boolean
	 */
	abstract function unsubscribe_user_from_course($course, $user_id);

	/**
	 * Checks whether a user is subscribed to a course.
	 * @param Course $course
	 * @param int $user_id
	 * @return boolean
	 */
	abstract function is_subscribed($course, $user_id);

	/**
	 * Checks whether the course category exists.
	 * @param string $category_code
	 * @return boolean
	 */
	abstract function is_course_category($category_code);

	/**
	 * Checks whether the course exists.
	 * @param string $course_code
	 * @return boolean
	 */
	abstract function is_course($course_code);

	/**
	 * Checks whether the given user is an admin for the given course.
	 * @param Course $course
	 * @param int $user_id
	 * @return boolean
	 */
	abstract function is_course_admin($course, $user_id);

	/**
	 * Creates a course user category object in persistent storage.
	 * @param CourseUserCategory $courseusercategory The course user category to make persistent.
	 * @return boolean True if creation succceeded, false otherwise.
	 */
	abstract function create_course_user_category($courseusercategory);

	/**
	 * Deletes a course user category object from persistent storage.
	 * @param CourseUserCategory $courseusercategory The course user category to make persistent.
	 * @return boolean True if creation succceeded, false otherwise.
	 */
	abstract function delete_course_user_category($courseusercategory);

	/**
	 * Creates a learning object publication in persistent storage.
	 * @param LearningObjectPublication $publication The publication to make
	 *                                               persistent.
	 * @return boolean True if creation succceeded, false otherwise.
	 */
	abstract function create_learning_object_publication($publication);

	/**
	 * Updates a learning object publication in persistent storage.
	 * @param LearningObjectPublication $publication The publication to update
	 *                                               in storage.
	 * @return boolean True if the update succceeded, false otherwise.
	 */
	abstract function update_learning_object_publication($publication);

	/**
	 * Removes learning object publication from persistent storage.
	 * @param LearningObjectPublication $publication The publication to remove
	 *                                               from storage.
	 * @return boolean True if deletion succceeded, false otherwise.
	 */
	abstract function delete_learning_object_publication($publication);

	/**
	 * Updates a learning object publication object id in persistent storage.
	 * @param LearningObjectPublicationAttribute $publication_attr The publication to update
	 *                                               in storage.
	 * @return boolean True if the update succceeded, false otherwise.
	 */
	abstract function update_learning_object_publication_id($publication_attr);

	/**
	 * Retrieves a the list of courses a user is the admin for
	 * @param int $user_id
	 * @return array An array of course codes
	 */
	abstract function retrieve_course_list_of_user_as_course_admin($user_id);

	/**
	 * Moves a learning object publication among its siblings.
	 * @param LearningObjectPublication $publication The publication to move.
	 * @param int $places The number of places to move the publication down
	 *                    by. If negative, the publication will be moved up.
	 * @return int The number of places that the publication was moved down.
	 */
	abstract function move_learning_object_publication($publication, $places);

	/**
	 * Returns the next available index in the display order.
	 * @param string $course The course in which the publication will be
	 *                       added.
	 * @param string $tool The tool in which the publication will be added.
	 * @param string $category The category in which the publication will be
	 *                         added.
	 * @return int The requested display order index.
	 */
	abstract function get_next_learning_object_publication_display_order_index($course,$tool,$category);

	/**
	 * Returns the available learning object publication categories for the
	 * given course and tools.
	 * @param string $course The course ID.
	 * @param mixed $tools The tool names. May be a string if only one.
	 * @param integer $root_category_id If $tools is only one tool, then only
	 * return the categories under this given category_id (Default: 0 = root
	 * category of the tools)
	 * @return array The publication categories.
	 */
	abstract function retrieve_learning_object_publication_categories($course, $tools, $root_category_id = 0);

	/**
	 * Retrieves a single learning object publication category by ID and
	 * returns it.
	 * @param int $id The category ID.
	 * @return LearningObjectPublicationCategory The category, or null if it
	 *                                           could not be found.
	 */
	abstract function retrieve_learning_object_publication_category($id);

	/**
	 * Returns the next available learning object publication category ID.
	 * @return int The ID.
	 */
	abstract function get_next_learning_object_publication_category_id();

	/**
	 * Creates a new learning object publication category in persistent
	 * storage.
	 * @param LearningObjectPublicationCategory $category The category to make
	 *                                                    persistent.
	 * @return boolean True if creation succceeded, false otherwise.
	 */
	abstract function create_learning_object_publication_category($category);

	/**
	 * Updates a learning object publication category in persistent storage,
	 * making any changes permanent.
	 * @param LearningObjectPublicationCategory $category The category to
	 *                                                    update.
	 * @return boolean True if the update succceeded, false otherwise.
	 */
	abstract function update_learning_object_publication_category($category);

	/**
	 * Removes a learning object publication category from persistent storage,
	 * making it disappear forever. Also removes all child categories.
	 * @param LearningObjectPublicationCategory $category The category to
	 *                                                    delete.
	 * @return boolean True if deletion succceeded, false otherwise.
	 */
	abstract function delete_learning_object_publication_category($category);

	/**
	 * Gets the course modules in a given course
	 * @param string $course_code The course code
	 * @return array The list of available course modules
	 */
	abstract function get_course_modules($course_code);

	/**
	 * Retrieves a single course from persistent storage.
	 * @param string $course_code The alphanumerical identifier of the course.
	 * @return Course The course.
	 */
	abstract function retrieve_course($course_code);

	/**
	 * Retrieve a series of courses
	 * @param User $user
	 * @param string $category
	 * @param Condition $condition
	 * @param array $orderBy
	 * @param array $orderDir
	 * @param int $offset
	 * @param int $maxObjects
	 * @return CourseResultSet
	 */
	abstract function retrieve_courses($user = null, $category = null, $condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null);

	/**
	 * Retrieve a series of courses for a specific user + the relation
	 * @param Condition $condition
	 * @param array $orderBy
	 * @param array $orderDir
	 * @param int $offset
	 * @param int $maxObjects
	 * @return CourseResultSet
	 */
	abstract function retrieve_user_courses($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null);

	/**
	 * Updates the specified course in persistent storage,
	 * making any changes permanent.
	 * @param Course $course The course object
	 * @return boolean True if the update succceeded, false otherwise.
	 */
	abstract function update_course($course);

	/**
	 * Updates the specified course category in persistent storage,
	 * making any changes permanent.
	 * @param CourseCategory $coursecategory The coursecatgory object
	 * @return boolean True if the update succceeded, false otherwise.
	 */
	abstract function update_course_category($coursecategory);

	/**
	 * Updates the specified course user category in persistent storage,
	 * making any changes permanent.
	 * @param CourseUserCategory $course The course user category object
	 * @return boolean True if the update succceeded, false otherwise.
	 */
	abstract function update_course_user_category($courseusercategory);

	/**
	 * Updates the specified course user relation in persistent storage,
	 * making any changes permanent.
	 * @param CourseUserRelation $course The course user relation object
	 * @return boolean True if the update succceeded, false otherwise.
	 */
	abstract function update_course_user_relation($courseuserrelation);

	/**
	 * Deletes all records from the database related to this given course.
	 * @param string $course_code The course code
	 */
	abstract function delete_course($course_code);

	/**
	 * Deletes the given course category from the database.
	 * @param CourseCategory $course_category The course category
	 */
	abstract function delete_course_category($course_category);

	/**
	 * Sets the visibility of a course module.
	 * @param string $course_code
	 * @param string $module
	 * @param boolean $visible
	 */
	abstract function set_module_visible($course_code,$module,$visible);

	/**
	 * Retrieves a single course category from persistent storage.
	 * @param string $category_code The alphanumerical identifier of the course category.
	 * @return CourseCategory The course category.
	 */
	abstract function retrieve_course_category($category_code = null);

	/**
	 * Retrieves a single course user relation from persistent storage.
	 * @param string $course_code
	 * @param int $user_id
	 * @return CourseCategory The course category.
	 */
	abstract function retrieve_course_user_relation($course_code, $user_id);

	/**
	 * Retrieves the relations for the users subscribed to a certain course.
	 * @param Course $course
	 * @return CourseUserRelationResultSet
	 */
	abstract function retrieve_course_users($course);

	/**
	 * Retrieves the next course user relation according to.
	 * @param int $user_id
	 * @param int $category_id
	 * @param int $sort
	 * @param string $direction
	 * @return CourseUserRelationResultSet
	 */
	abstract function retrieve_course_user_relation_at_sort($user_id, $category_id, $sort, $direction);

	/**
	 * Retrieves a set of course user relations
	 * @param int $user_id
	 * @param string $course_user_category
	 */
	abstract function retrieve_course_user_relations($user_id, $course_user_category);

	/**
	 * Creates a storage unit
	 * @param string $name Name of the storage unit
	 * @param array $properties Properties of the storage unit
	 * @param array $indexes The indexes which should be defined in the created
	 * storage unit
	 */
	abstract function create_storage_unit($name,$properties,$indexes);

	/**
	 * Retrieves the course categories that match the criteria from persistent storage.
	 * @param string $parent The parent of the course category.
	 * @return DatabaseCourseCategoryResultSet The resultset of course category.
	 */
	abstract function retrieve_course_categories($parent = null);

	/**
	 * Retrieves the personal course categories for a given user.
	 * @return DatabaseUserCourseCategoryResultSet The resultset of course categories.
	 */
	abstract function retrieve_course_user_categories($conditions = null, $offset = null, $count = null, $order_property = null, $order_direction = null);

	/**
	 * Retrieves a personal course category for the user.
	 * @return CourseUserCategory The course user category.
	 */
	abstract function retrieve_course_user_category($course_user_category_id, $user_id = null);

	/**
	 * Retrieves a personal course category for the user according to
	 * @param int $user_id
	 * @param int $sort
	 * @param string $direction
	 * @return CourseUserCategory The course user category.
	 */
	abstract function retrieve_course_user_category_at_sort($user_id, $sort, $direction);

	/**
	 * Adds a course module to a course
	 * @param string $course_code
	 * @param string $module
	 * @param string $section
	 * @param boolean $visible
	 */
	abstract function add_course_module($course_code,$module,$section = 'basic',$visible = true);
	 /**
	  * Adds a record to the access log of a course module
	  * @param string $course_code
	  * @param int $user_id
	  * @param string $module_name
	  * @param int $category_id
	  */
	abstract function log_course_module_access($course_code, $user_id, $module_name = null, $category_id = 0);
	 /**
	  * Gets the last visit date
	  * @param string $course_code
	  * @param string $module_name
	  * @param int $category_id
	  * @param int $user_id
	  */
	abstract function get_last_visit_date($course_code,$user_id,$module_name = null,$category_id = 0);
	/**
	 * Deletes a group
	 * @param int $id The group id
	 */
	abstract function delete_group($id);
	/**
	 * Creates a group
	 * @param Group $group
	 */
	abstract function create_group($group);
	/**
	 * Updates a group
	 * @param Group $group
	 */
	abstract function update_group($group);
	/**
	 * Retrieves a group
	 * @param int id
	 */
	abstract function retrieve_group($id);
	/**
	 * Retrieves the groups defined in a given course
	 * @param string $course_code
	 */
	abstract function retrieve_groups($course_code,$category = null, $offset = null, $count = null, $order_property = null, $order_direction = null);
	/**
	 * Retrieves the groups from a given course in which the given user is
	 * subscribed
	 * @param User The user
	 * @param Course The course
	 * @return DatabaseGroupResultSet
	 */
	abstract function retrieve_groups_from_user($user,$course = null);
	/**
	 * Retrieves the users in a group
	 */
	abstract function retrieve_group_users($group,$condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null);
	/**
	 * Counts the users in a group
	 */
	abstract function count_group_users($group,$conditions = null);
	/**
	 * Retrieves the users that can be subscribed to a group
	 */
	abstract function retrieve_possible_group_users($group,$condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null);
	/**
	 * Counts the users that can be subscribed to a group
	 */
	abstract function count_possible_group_users($group,$conditions = null);
	/**
	 * Subscribes users to groups
	 * @param array|User $users A single user or an array of users
	 * @param array|Group $groups A single group or an array of groups
	 */
	abstract function subscribe_users_to_groups($users,$groups);
	/**
	 * Unsubscribes users from groups
	 * @param array|User $users A single user or an array of users
	 * @param array|Group $groups A single group or an array of groups
	 */
	abstract function unsubscribe_users_from_groups($users,$groups);
	/**
	 * Is user member of the group
	 */
	abstract function is_group_member($group,$user);
}
?>
