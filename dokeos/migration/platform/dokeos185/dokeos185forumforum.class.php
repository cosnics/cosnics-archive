<?php 
/**
 * migration.lib.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../../lib/import/importforumforum.class.php';
require_once dirname(__FILE__) . '/../../../repository/lib/learning_object/forum/forum.class.php';
require_once dirname(__FILE__) . '/../../../application/lib/weblcms/learningobjectpublication.class.php';
require_once 'dokeos185itemproperty.class.php';
require_once dirname(__FILE__) . '/../../../repository/lib/learning_object/category/category.class.php';

/**
 * This class presents a Dokeos185 forum_forum
 *
 * @author Sven Vanpoucke
 */
class Dokeos185ForumForum
{
	/**
	 * Dokeos185ForumForum properties
	 */
	const PROPERTY_FORUM_ID = 'forum_id';
	const PROPERTY_FORUM_TITLE = 'forum_title';
	const PROPERTY_FORUM_COMMENT = 'forum_comment';
	const PROPERTY_FORUM_THREADS = 'forum_threads';
	const PROPERTY_FORUM_POSTS = 'forum_posts';
	const PROPERTY_FORUM_LAST_POST = 'forum_last_post';
	const PROPERTY_FORUM_CATEGORY = 'forum_category';
	const PROPERTY_ALLOW_ANONYMOUS = 'allow_anonymous';
	const PROPERTY_ALLOW_EDIT = 'allow_edit';
	const PROPERTY_APPROVAL_DIRECT_POST = 'approval_direct_post';
	const PROPERTY_ALLOW_ATTACHMENTS = 'allow_attachments';
	const PROPERTY_ALLOW_NEW_THREADS = 'allow_new_threads';
	const PROPERTY_DEFAULT_VIEW = 'default_view';
	const PROPERTY_FORUM_OF_GROUP = 'forum_of_group';
	const PROPERTY_FORUM_GROUP_PUBLIC_PRIVATE = 'forum_group_public_private';
	const PROPERTY_FORUM_ORDER = 'forum_order';
	const PROPERTY_LOCKED = 'locked';
	const PROPERTY_SESSION_ID = 'session_id';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Dokeos185ForumForum object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185ForumForum($defaultProperties = array ())
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
		return array (SELF :: PROPERTY_FORUM_ID, SELF :: PROPERTY_FORUM_TITLE, SELF :: PROPERTY_FORUM_COMMENT, SELF :: PROPERTY_FORUM_THREADS, SELF :: PROPERTY_FORUM_POSTS, SELF :: PROPERTY_FORUM_LAST_POST, SELF :: PROPERTY_FORUM_CATEGORY, SELF :: PROPERTY_ALLOW_ANONYMOUS, SELF :: PROPERTY_ALLOW_EDIT, SELF :: PROPERTY_APPROVAL_DIRECT_POST, SELF :: PROPERTY_ALLOW_ATTACHMENTS, SELF :: PROPERTY_ALLOW_NEW_THREADS, SELF :: PROPERTY_DEFAULT_VIEW, SELF :: PROPERTY_FORUM_OF_GROUP, SELF :: PROPERTY_FORUM_GROUP_PUBLIC_PRIVATE, SELF :: PROPERTY_FORUM_ORDER, SELF :: PROPERTY_LOCKED, SELF :: PROPERTY_SESSION_ID);
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
	 * Returns the forum_id of this Dokeos185ForumForum.
	 * @return the forum_id.
	 */
	function get_forum_id()
	{
		return $this->get_default_property(self :: PROPERTY_FORUM_ID);
	}

	/**
	 * Returns the forum_title of this Dokeos185ForumForum.
	 * @return the forum_title.
	 */
	function get_forum_title()
	{
		return $this->get_default_property(self :: PROPERTY_FORUM_TITLE);
	}

	/**
	 * Returns the forum_comment of this Dokeos185ForumForum.
	 * @return the forum_comment.
	 */
	function get_forum_comment()
	{
		return $this->get_default_property(self :: PROPERTY_FORUM_COMMENT);
	}

	/**
	 * Returns the forum_threads of this Dokeos185ForumForum.
	 * @return the forum_threads.
	 */
	function get_forum_threads()
	{
		return $this->get_default_property(self :: PROPERTY_FORUM_THREADS);
	}

	/**
	 * Returns the forum_posts of this Dokeos185ForumForum.
	 * @return the forum_posts.
	 */
	function get_forum_posts()
	{
		return $this->get_default_property(self :: PROPERTY_FORUM_POSTS);
	}

	/**
	 * Returns the forum_last_post of this Dokeos185ForumForum.
	 * @return the forum_last_post.
	 */
	function get_forum_last_post()
	{
		return $this->get_default_property(self :: PROPERTY_FORUM_LAST_POST);
	}

	/**
	 * Returns the forum_category of this Dokeos185ForumForum.
	 * @return the forum_category.
	 */
	function get_forum_category()
	{
		return $this->get_default_property(self :: PROPERTY_FORUM_CATEGORY);
	}

	/**
	 * Returns the allow_anonymous of this Dokeos185ForumForum.
	 * @return the allow_anonymous.
	 */
	function get_allow_anonymous()
	{
		return $this->get_default_property(self :: PROPERTY_ALLOW_ANONYMOUS);
	}

	/**
	 * Returns the allow_edit of this Dokeos185ForumForum.
	 * @return the allow_edit.
	 */
	function get_allow_edit()
	{
		return $this->get_default_property(self :: PROPERTY_ALLOW_EDIT);
	}

	/**
	 * Returns the approval_direct_post of this Dokeos185ForumForum.
	 * @return the approval_direct_post.
	 */
	function get_approval_direct_post()
	{
		return $this->get_default_property(self :: PROPERTY_APPROVAL_DIRECT_POST);
	}

	/**
	 * Returns the allow_attachments of this Dokeos185ForumForum.
	 * @return the allow_attachments.
	 */
	function get_allow_attachments()
	{
		return $this->get_default_property(self :: PROPERTY_ALLOW_ATTACHMENTS);
	}

	/**
	 * Returns the allow_new_threads of this Dokeos185ForumForum.
	 * @return the allow_new_threads.
	 */
	function get_allow_new_threads()
	{
		return $this->get_default_property(self :: PROPERTY_ALLOW_NEW_THREADS);
	}

	/**
	 * Returns the default_view of this Dokeos185ForumForum.
	 * @return the default_view.
	 */
	function get_default_view()
	{
		return $this->get_default_property(self :: PROPERTY_DEFAULT_VIEW);
	}

	/**
	 * Returns the forum_of_group of this Dokeos185ForumForum.
	 * @return the forum_of_group.
	 */
	function get_forum_of_group()
	{
		return $this->get_default_property(self :: PROPERTY_FORUM_OF_GROUP);
	}

	/**
	 * Returns the forum_group_public_private of this Dokeos185ForumForum.
	 * @return the forum_group_public_private.
	 */
	function get_forum_group_public_private()
	{
		return $this->get_default_property(self :: PROPERTY_FORUM_GROUP_PUBLIC_PRIVATE);
	}

	/**
	 * Returns the forum_order of this Dokeos185ForumForum.
	 * @return the forum_order.
	 */
	function get_forum_order()
	{
		return $this->get_default_property(self :: PROPERTY_FORUM_ORDER);
	}

	/**
	 * Returns the locked of this Dokeos185ForumForum.
	 * @return the locked.
	 */
	function get_locked()
	{
		return $this->get_default_property(self :: PROPERTY_LOCKED);
	}

	/**
	 * Returns the session_id of this Dokeos185ForumForum.
	 * @return the session_id.
	 */
	function get_session_id()
	{
		return $this->get_default_property(self :: PROPERTY_SESSION_ID);
	}

	function is_valid($array)
	{
		$course = $array[0];
		$this->item_property = self :: $mgdm->get_item_property($course->get_db_name(),'forum',$this->get_id());	

		if(!$this->get_forum_id() || !($this->get_forum_title() || $this->get_comment())
			|| !$this->item_property->get_insert_date())
		{		 
			self :: $mgdm->add_failed_element($this->get_id(),
				$course->get_db_name() . '.forum_forum');
			return false;
		}
		return true;
	}
	
	function convert_to_lcms($array)
	{
		$new_user_id = self :: $mgdm->get_id_reference($this->item_property->get_insert_user_id(),'user_user');	
		$course = $array[0];
		$new_course_code = self :: $mgdm->get_id_reference($course->get_code(),'weblcms_course');
		
		if(!$new_user_id)
		{
			$new_user_id = self :: $mgdm->get_owner($new_course_code);
		}
		
		//forum parameters
		$lcms_forum = new Forum();
		
		// Category for announcements already exists?
		$lcms_category_id = self :: $mgdm->get_parent_id($new_user_id, 'category',
			Translation :: get_lang('forums'));
		if(!$lcms_category_id)
		{
			//Create category for tool in lcms
			$lcms_repository_category = new Category();
			$lcms_repository_category->set_owner_id($new_user_id);
			$lcms_repository_category->set_title(Translation :: get_lang('forums'));
			$lcms_repository_category->set_description('...');
	
			//Retrieve repository id from course
			$repository_id = self :: $mgdm->get_parent_id($new_user_id, 
				'category', Translation :: get_lang('MyRepository'));
			$lcms_repository_category->set_parent_id($repository_id);
			
			//Create category in database
			$lcms_repository_category->create();
			
			$lcms_forum->set_parent_id($lcms_repository_category->get_id());
		}
		else
		{
			$lcms_forum->set_parent_id($lcms_category_id);	
		}
		
		if(!$this->get_forum_title())
			$lcms_forum->set_title(substr($this->get_forum_comment(),0,20));
		else
			$lcms_forum->set_title($this->get_forum_title());
		
		if(!$this->get_forum_comment())
			$lcms_forum->set_description($this->get_forum_title());
		else
			$lcms_forum->set_description($this->get_forum_comment());
		
		$lcms_forum->set_owner_id($new_user_id);
		$lcms_forum->set_creation_date(self :: $mgdm->make_unix_time($this->item_property->get_insert_date()));
		$lcms_forum->set_modification_date(self :: $mgdm->make_unix_time($this->item_property->get_lastedit_date()));
		
		if($this->item_property->get_visibility() == 2)
			$lcms_forum->set_state(1);
		
		//create announcement in database
		$lcms_forum->create_all();
		
		/*
		//publication
		if($this->item_property->get_visibility() <= 1) 
		{
			$publication = new LearningObjectPublication();
			
			$publication->set_learning_object($lcms_announcement);
			$publication->set_course_id($new_course_code);
			$publication->set_publisher_id($new_user_id);
			$publication->set_tool('announcement');
			$publication->set_category_id(0);
			//$publication->set_from_date(self :: $mgdm->make_unix_time($this->item_property->get_start_visible()));
			//$publication->set_to_date(self :: $mgdm->make_unix_time($this->item_property->get_end_visible()));
			$publication->set_from_date(0);
			$publication->set_to_date(0);
			$publication->set_publication_date(self :: $mgdm->make_unix_time($this->item_property->get_insert_date()));
			$publication->set_modified_date(self :: $mgdm->make_unix_time($this->item_property->get_lastedit_date()));
			//$publication->set_modified_date(0);
			//$publication->set_display_order_index($this->get_display_order());
			$publication->set_display_order_index(0);
			
			if($this->get_email_sent())
				$publication->set_email_sent($this->get_email_sent());
			else
				$publication->set_email_sent(0);
			
			$publication->set_hidden($this->item_property->get_visibility() == 1?0:1);
			
			//create publication in database
			$publication->create();
		}
		*/
		return $lcms_forum;
	}
	
	static function get_all($parameters = array())
	{
		self :: $mgdm = $parameters['mgdm'];

		if($array['del_files'] =! 1)
			$tool_name = 'forum_forum';
		
		$coursedb = $array['course'];
		$tablename = 'forum_forum';
		$classname = 'Dokeos185ForumForum';
			
		return self :: $mgdm->get_all($coursedb, $tablename, $classname, $tool_name);	
	}
}

?>