<?php
/**
 * $Id: documentbrowser.class.php 12939 2007-09-05 16:36:36Z ceetee $
 * Document tool - slideshow
 * @package application.weblcms.tool
 * @subpackage document
 */
require_once dirname(__FILE__).'/../../../../weblcms_data_manager.class.php';
require_once dirname(__FILE__).'/../../../../learning_object_publication_browser.class.php';
require_once dirname(__FILE__).'/../../../../browser/learningobjectpublicationcategorytree.class.php';
require_once dirname(__FILE__).'/document_publication_slideshow_renderer.class.php';
require_once Path :: get_repository_path() . 'lib/learning_object/document/document.class.php';

class DocumentSlideshowBrowser extends LearningObjectPublicationBrowser
{
	function DocumentSlideshowBrowser($parent, $types)
	{
		parent :: __construct($parent, 'document');
		$tree_id = 'pcattree';
		//$tree = new LearningObjectPublicationCategoryTree($this, $tree_id);
		$parent->set_parameter($tree_id, Request :: get($tree_id));
		$renderer = new DocumentPublicationSlideshowRenderer($this);
		$this->set_publication_list_renderer($renderer);
		//$this->set_publication_category_tree($tree);
		$this->set_category(Request :: get($tree_id));
	}

	function get_publications($from, $count, $column, $direction)
	{
		$datamanager = WeblcmsDataManager :: get_instance();
		if($this->is_allowed(EDIT_RIGHT))
		{
			$user_id = null;
			$course_groups = null;
		}
		else
		{
			$user_id = $this->get_user_id();
			$course_groups = $this->get_course_groups();
		}

		$cond = new EqualityCondition('type','document');
		$publications = $datamanager->retrieve_learning_object_publications($this->get_course_id(), $this->get_category(), $user_id, $course_groups, $this->get_condition($this->get_category()), false, new ObjectTableOrder(Document :: PROPERTY_DISPLAY_ORDER_INDEX, SORT_DESC), array (), 0, -1, null, $cond);
		$visible_publications = array ();
		while ($publication = $publications->next_result())
		{
			// If the publication is hidden and the user is not allowed to DELETE or EDIT, don't show this publication
			if (!$publication->is_visible_for_target_users() && !($this->is_allowed(DELETE_RIGHT) || $this->is_allowed(EDIT_RIGHT)))
			{
				continue;
			}
			$document = $publication->get_learning_object();
			if($document->is_image())
			{
				$visible_publications[] = $publication;
			}

		}
		return $visible_publications;
	}

	function get_publication_count($category = null)
	{
		if(is_null($category))
		{
			$category = $this->get_category();
		}
		$dm = WeblcmsDataManager :: get_instance();
		return $dm->count_learning_object_publications($this->get_course_id(), $category, $this->get_user_id(), $this->get_course_groups(), $this->get_condition($category));
	}

	function get_condition($category = 0)
	{
		$tool_cond= new EqualityCondition(LearningObjectPublication :: PROPERTY_TOOL,'document');
		$category_cond = new EqualityCondition(LearningObjectPublication :: PROPERTY_CATEGORY_ID,$category );
		return new AndCondition($tool_cond, $category_cond);
	}

	function get_category()
	{
		$cat = Request :: get('pcattree');
		return $cat?$cat:0;
	}
}
?>