<?php
/**
 * $Id: blog_browser.class.php 16944 2008-11-26 13:32:18Z vanpouckesven $
 * Blog tool - browser
 * @package application.weblcms.tool
 * @subpackage blog
 */
require_once dirname(__FILE__).'/../../../../weblcms_data_manager.class.php';
require_once dirname(__FILE__).'/../../../../content_object_publication_browser.class.php';
require_once dirname(__FILE__).'/../../../../browser/list_renderer/content_object_publication_details_renderer.class.php';
//require_once Path :: get_repository_path() . 'lib/content_object/blog/blog.class.php';
require_once Path :: get_repository_path() . 'lib/content_object/description/description.class.php';
/**
 * Browser to allow the user to view the published blogs
 */
class BlogBrowser extends ContentObjectPublicationBrowser
{
	/**
	 * @see ContentObjectPublicationBrowser::ContentObjectPublicationBrowser()
	 */
	private $publications;

	function BlogBrowser($parent)
	{
		parent :: __construct($parent, 'blog');
		if(Request :: get('pid') && $parent->get_action() == 'view')
		{
			$this->set_publication_id(Request :: get('pid'));
			$parent->set_parameter(Tool :: PARAM_ACTION, BlogTool :: ACTION_VIEW_BLOGS);
			$renderer = new ContentObjectPublicationDetailsRenderer($this);
		}
		else
		{
			$tree_id = 'pcattree';
			$value = Request :: get($tree_id)?Request :: get($tree_id):0;
			$parent->set_parameter($tree_id, $value);

			$tree = new ContentObjectPublicationCategoryTree($this, $tree_id);
			$this->set_publication_category_tree($tree);

			$renderer = new ListContentObjectPublicationListRenderer($this);
			$actions = array(Tool :: ACTION_DELETE => Translation :: get('DeleteSelected'),
						 Tool :: ACTION_HIDE => Translation :: get('Hide'),
						 Tool :: ACTION_SHOW => Translation :: get('Show'),
						 Tool :: ACTION_MOVE_SELECTED_TO_CATEGORY => Translation :: get('MoveSelected'));
			$renderer->set_actions($actions);


		}

		$this->set_publication_list_renderer($renderer);
	}
	
	function get_allowed_types()
	{
		return $this->get_parent()->get_allowed_types();
	}
	
	/**
	 * Retrieves the publications
	 * @return array An array of ContentObjectPublication objects
	 */
	function get_publications($from, $count, $column, $direction)
	{
		if(empty($this->publications))
		{
			$tree_id = 'pcattree';
			$category = Request :: get($tree_id)?Request :: get($tree_id):0;

			$datamanager = WeblcmsDataManager :: get_instance();
			if($this->is_allowed(EDIT_RIGHT))
			{
				$user_id = array();
				$course_groups = array();
			}
			else
			{
				$user_id = $this->get_user_id();
				$course_groups = $this->get_course_groups();
			}
			
			$conditions = array();
			$conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_COURSE_ID, $this->get_course_id());
			$conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_TOOL, 'blog');
			$conditions[] = new InCondition(ContentObjectPublication :: PROPERTY_CATEGORY_ID, $category);
			
			$access = array();
			$access[] = new InCondition('user_id', $user_id, $datamanager->get_database()->get_alias('content_object_publication_user'));
			$access[] = new InCondition('course_group_id', $course_groups, $datamanager->get_database()->get_alias('content_object_publication_course_group'));
			if (!empty($user_id) || !empty($course_groups))
			{
				$access[] = new AndCondition(array(new EqualityCondition('user_id', null, $datamanager->get_database()->get_alias('content_object_publication_user')), new EqualityCondition('course_group_id', null, $datamanager->get_database()->get_alias('content_object_publication_course_group'))));
			}
			$conditions[] = new OrCondition($access);
			
			$subselect_conditions = array();
			$subselect_conditions[] = new EqualityCondition('type', 'blog_item');
			if($this->get_parent()->get_condition())
			{
				$subselect_conditions[] = $this->get_parent()->get_condition();
			}
			$subselect_condition = new AndCondition($subselect_conditions);
			$conditions[] = new SubselectCondition(ContentObjectPublication :: PROPERTY_CONTENT_OBJECT_ID, ContentObject :: PROPERTY_ID, RepositoryDataManager :: get_instance()->escape_table_name(ContentObject :: get_table_name()), $subselect_condition);
			$condition = new AndCondition($conditions);
			
			$publications = $datamanager->retrieve_content_object_publications_new($condition, new ObjectTableOrder(ContentObjectPublication :: PROPERTY_DISPLAY_ORDER_INDEX, SORT_DESC));
			$visible_publications = array ();
			while ($publication = $publications->next_result())
			{
				// If the publication is hidden and the user is not allowed to DELETE or EDIT, don't show this publication
				if (!$publication->is_visible_for_target_users() && !($this->is_allowed(DELETE_RIGHT) || $this->is_allowed(EDIT_RIGHT)))
				{
					continue;
				}
				$visible_publications[] = $publication;
			}
			$this->publications = $visible_publications;
		}

		return $this->publications;

	}
	/**
	 * Retrieves the number of published annoucements
	 * @return int
	 */
	function get_publication_count()
	{
		return count($this->get_publications());
	}
}
?>