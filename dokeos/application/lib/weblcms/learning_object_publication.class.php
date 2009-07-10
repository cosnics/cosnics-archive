<?php
require_once Path :: get_repository_path() . 'lib/repository_data_manager.class.php';
require_once Path :: get_user_path() . 'lib/user_data_manager.class.php';
/**
 * $Id$
 * @package application.weblcms
 */
/**
 * This class represents a learning object publication.
 *
 * When publishing a learning object from the repository in the weblcms
 * application, a new object of this type is created.
 */
class LearningObjectPublication
{
    const CLASS_NAME = __CLASS__;

    /**#@+
     * Constant defining a property of the publication
     */
    const PROPERTY_ID = 'id';
    const PROPERTY_LEARNING_OBJECT_ID = 'learning_object';
    const PROPERTY_COURSE_ID = 'course';
    const PROPERTY_TOOL = 'tool';
    const PROPERTY_PARENT_ID = 'parent_id';
    const PROPERTY_CATEGORY_ID = 'category';
    const PROPERTY_FROM_DATE = 'from_date';
    const PROPERTY_TO_DATE = 'to_date';
    const PROPERTY_HIDDEN = 'hidden';
    const PROPERTY_PUBLISHER_ID = 'publisher';
    const PROPERTY_PUBLICATION_DATE = 'published';
    const PROPERTY_MODIFIED_DATE = 'modified';
    const PROPERTY_DISPLAY_ORDER_INDEX = 'display_order';
    const PROPERTY_EMAIL_SENT = 'email_sent';
    const PROPERTY_SHOW_ON_HOMEPAGE = 'show_on_homepage';

	private $defaultProperties;
	private $target_course_groups;
	private $target_users;

	private $learning_object;
	private $publisher;

    function LearningObjectPublication($defaultProperties = array ())
    {
        $this->defaultProperties = $defaultProperties;
    }

    /**
     * Gets a default property of this user object by name.
     * @param string $name The name of the property.
     */
    function get_default_property($name)
    {
        return $this->defaultProperties[$name];
    }

    /**
     * Gets the default properties of this user.
     * @return array An associative array containing the properties.
     */
    function get_default_properties()
    {
        return $this->defaultProperties;
    }

    function set_default_properties($defaultProperties)
    {
        $this->defaultProperties = $defaultProperties;
    }

    static function get_default_property_names()
    {
        return array(self :: PROPERTY_ID, self :: PROPERTY_LEARNING_OBJECT_ID, self :: PROPERTY_COURSE_ID, self :: PROPERTY_TOOL, self :: PROPERTY_PARENT_ID, self :: PROPERTY_CATEGORY_ID, self :: PROPERTY_FROM_DATE, self :: PROPERTY_TO_DATE, self :: PROPERTY_HIDDEN, self :: PROPERTY_PUBLISHER_ID, self :: PROPERTY_PUBLICATION_DATE, self :: PROPERTY_MODIFIED_DATE, self :: PROPERTY_DISPLAY_ORDER_INDEX, self :: PROPERTY_EMAIL_SENT, self :: PROPERTY_SHOW_ON_HOMEPAGE);
    }

    function set_default_property($name, $value)
    {
        $this->defaultProperties[$name] = $value;
    }

    /**
     * Checks if the given identifier is the name of a default user
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
     * Gets the publication id.
     * @return int
     */
    function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

    /**
     * Gets the learning object.
     * @return LearningObject
     */
    function get_learning_object_id()
    {
        return $this->get_default_property(self :: PROPERTY_LEARNING_OBJECT_ID);
    }

    /**
     * Gets the course code of the course in which this publication was made.
     * @return string The course code
     */
    function get_course_id()
    {
        return $this->get_default_property(self :: PROPERTY_COURSE_ID);
    }

    /**
     * Gets the tool in which this publication was made.
     * @return string
     */
    function get_tool()
    {
        return $this->get_default_property(self :: PROPERTY_TOOL);
    }

    /**
     * Gets the parent_id of the learning object publication
     * @return int
     */
    function get_parent_id()
    {
        return $this->get_default_property(self :: PROPERTY_PARENT_ID);
    }

    /**
     * Gets the id of the learning object publication category in which this
     * publication was made
     * @return int
     */
    function get_category_id()
    {
        return $this->get_default_property(self :: PROPERTY_CATEGORY_ID);
    }

    /**
     * Gets the list of target users of this publication
     * @return array An array of user ids.
     * @see is_for_everybody()
     */
    function get_target_users()
    {
		if (!isset($this->target_users))
		{
			$wdm = WeblcmsDataManager :: get_instance();
			$this->target_groups = $wdm->retrieve_learning_object_publication_target_users($this);
		}

		return $this->target_users;
    }

    /**
     * Gets the list of target course_groups of this publication
     * @return array An array of course_group ids.
     * @see is_for_everybody()
     */
    function get_target_course_groups()
    {
		if (!isset($this->target_course_groups))
		{
			$wdm = WeblcmsDataManager :: get_instance();
			$this->target_groups = $wdm->retrieve_learning_object_publication_target_course_groups($this);
		}

		return $this->target_course_groups;
    }

    /**
     * Gets the date on which this publication becomes available
     * @return int
     * @see is_forever()
     */
    function get_from_date()
    {
        return $this->get_default_property(self :: PROPERTY_FROM_DATE);
    }

    /**
     * Gets the date on which this publication becomes unavailable
     * @return int
     * @see is_forever()
     */
    function get_to_date()
    {
        return $this->get_default_property(self :: PROPERTY_TO_DATE);
    }

    /**
     * Gets the user id of the user who made this publication
     * @return int
     */
    function get_publisher_id()
    {
        return $this->get_default_property(self :: PROPERTY_PUBLISHER_ID);
    }

	function get_learning_object()
	{
		if (!isset($this->learning_object))
		{
			$rdm = RepositoryDataManager :: get_instance();
			$this->learning_object = $rdm->retrieve_learning_object($this->get_learning_object_id());
		}

		return $this->learning_object;
	}

	function get_publication_publisher()
	{
	    if (!isset($this->publisher))
		{
		    $udm = UserDataManager :: get_instance();
		    $this->publisher = $udm->retrieve_user($this->get_publisher_id());
		}

		return $this->publisher;
	}

    /**
     * Gets the date on which this publication was made
     * @return int
     */
    function get_publication_date()
    {
        return $this->get_default_property(self :: PROPERTY_PUBLICATION_DATE);
    }

    /**
     * Gets the date on which this publication was made
     * @return int
     */
    function get_modified_date()
    {
        return $this->get_default_property(self :: PROPERTY_MODIFIED_DATE);
    }

    /**
     * Determines whether this publication was sent by email to the users and
     * course_groups for which this publication was made
     * @return boolean True if an email was sent
     */
    function is_email_sent()
    {
        return $this->get_default_property(self :: PROPERTY_EMAIL_SENT);
    }

    /**
     * Determines whether this publication is hidden or not
     * @return boolean True if the publication is hidden.
     */
    function is_hidden()
    {
        return $this->get_default_property(self :: PROPERTY_HIDDEN);
    }

    /**
     * Determines whether this publication is available forever
     * @return boolean True if the publication is available forever
     * @see get_from_date()
     * @see get_to_date()
     */
    function is_forever()
    {
        return $this->get_from_date() == 0 && $this->get_to_date() == 0;
    }

    function is_for_everybody()
    {
        return (! count($this->get_target_users()) && ! count($this->get_target_course_groups()));
    }

    function is_visible_for_target_users()
    {
        return (! $this->is_hidden()) && ($this->is_forever() || ($this->get_from_date() <= time() && time() <= $this->get_to_date()));
    }

    function get_display_order_index()
    {
        return $this->get_default_property(self :: PROPERTY_DISPLAY_ORDER_INDEX);
    }

    /**#@+
     * Sets a property of this learning object publication.
     * See constructor for detailed information about the property.
     * @see LearningObjectPublication()
     */
    function set_id($id)
    {
        $this->set_default_property(self :: PROPERTY_ID, $id);
    }

    function set_learning_object_id($learning_object_id)
    {
        $this->set_default_property(self :: PROPERTY_LEARNING_OBJECT_ID, $learning_object_id);
    }

    function set_course_id($course)
    {
        $this->set_default_property(self :: PROPERTY_COURSE_ID, $course);
    }

    function set_tool($tool)
    {
        $this->set_default_property(self :: PROPERTY_TOOL, $tool);
    }

    function set_parent_id($parent_id)
    {
        $this->set_default_property(self :: PROPERTY_PARENT_ID, $parent_id);
    }

    function set_category_id($category)
    {
        $this->set_default_property(self :: PROPERTY_CATEGORY_ID, $category);
    }

    function set_target_users($targetUsers)
    {
        $this->targetUsers = $targetUsers;
    }

    function set_target_course_groups($targetCourseGroups)
    {
        $this->targetCourseGroups = $targetCourseGroups;
    }

    function set_from_date($from_date)
    {
        $this->set_default_property(self :: PROPERTY_FROM_DATE, $from_date);
    }

    function set_to_date($to_date)
    {
        $this->set_default_property(self :: PROPERTY_TO_DATE, $to_date);
    }

    function set_publisher_id($publisher)
    {
        $this->set_default_property(self :: PROPERTY_PUBLISHER_ID, $publisher);
    }

    function set_publication_date($publication_date)
    {
        $this->set_default_property(self :: PROPERTY_PUBLICATION_DATE, $publication_date);
    }

    function set_modified_date($modified_date)
    {
        $this->set_default_property(self :: PROPERTY_MODIFIED_DATE, $modified_date);
    }

    function set_hidden($hidden)
    {
        $this->set_default_property(self :: PROPERTY_HIDDEN, $hidden);
    }

    function set_display_order_index($display_order)
    {
        $this->set_default_property(self :: PROPERTY_DISPLAY_ORDER_INDEX, $display_order);
    }

    function set_email_sent($email_sent)
    {
        $this->set_default_property(self :: PROPERTY_EMAIL_SENT, $email_sent);
    }

    /**#@-*/
    /**
     * Toggles the visibility of this publication.
     */
    function toggle_visibility()
    {
        $this->set_hidden(! $this->is_hidden());
    }

    function get_show_on_homepage()
    {
        return $this->get_default_property(self :: PROPERTY_SHOW_ON_HOMEPAGE);
    }

    function set_show_on_homepage($show_on_homepage)
    {
        $this->set_default_property(self :: PROPERTY_SHOW_ON_HOMEPAGE, $show_on_homepage);
    }

    /**
     * Creates this publication in persistent storage
     * @see WeblcmsDataManager::create_learning_object_publication()
     */
    function create()
    {
        $dm = WeblcmsDataManager :: get_instance();
        $id = $dm->get_next_learning_object_publication_id();
        $this->set_id($id);
        return $dm->create_learning_object_publication($this);
    }

    /**
     * Updates this publication in persistent storage
     * @see WeblcmsDataManager::update_learning_object_publication()
     */
    function update()
    {
        return WeblcmsDataManager :: get_instance()->update_learning_object_publication($this);
    }

    /**
     * Deletes this publication from persistent storage
     * @see WeblcmsDataManager::delete_learning_object_publication()
     */
    function delete()
    {
        return WeblcmsDataManager :: get_instance()->delete_learning_object_publication($this);
    }

    /**
     * Moves the publication up or down in the list.
     * @param $places The number of places to move the publication down. A
     *                negative number moves it up.
     * @return int The number of places that the publication was moved
     *             down.
     */
    function move($places)
    {
        return WeblcmsDataManager :: get_instance()->move_learning_object_publication($this, $places);
    }

    function retrieve_feedback()
    {
        return WeblcmsDataManager :: get_instance()->retrieve_learning_object_publication_feedback($this->get_id());
    }

    static function get_table_name()
    {
        return DokeosUtilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}
?>