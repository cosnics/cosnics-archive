<?php

require_once dirname(__FILE__) . '/../forum_tool.class.php';
require_once dirname(__FILE__) . '/../forum_tool_component.class.php';
require_once Path :: get_library_path() . '/html/action_bar/action_bar_renderer.class.php';
require_once Path :: get_repository_path() . '/lib/learning_object/forum/forum.class.php';
require_once Path :: get_repository_path() . 'lib/complex_display/forum/forum_display.class.php';
require_once 'HTML/Table.php';

class ForumToolBrowserComponent extends ForumToolComponent
{
	private $action_bar;
	private $introduction_text;
	private $size; //Number of published forums

	function run()
	{
		if(!$this->is_allowed(VIEW_RIGHT))
		{
			Display :: not_allowed();
			return;
		}

        $publications = WeblcmsDataManager :: get_instance()->retrieve_learning_object_publications($this->get_course_id(), null, null, null, new EqualityCondition('tool','forum'));
		//$publications = WeblcmsDataManager :: get_instance()->retrieve_learning_object_publications($this->get_course_id(), null, null, null, new EqualityCondition('tool','forum'),false, null, null, 0, -1, null, new EqualityCondition('type','introduction'));
		//$this->introduction_text = $publications->next_result();
		$this->action_bar = $this->get_action_bar();

		$table = $this->get_table_html();
 
		$trail = new BreadcrumbTrail();
		$trail->add_help('courses forum tool');
		$this->display_header($trail, true);

//		if(PlatformSetting :: get('enable_introduction', 'weblcms'))
//		{
//			echo $this->display_introduction_text($this->introduction_text);
//		}

		echo $this->action_bar->as_html();
		echo $table->toHtml();

		if($this->size == 0)
			echo '<br><div style="text-align: center"><h3>' . Translation :: get('NoPublications') . '</h3></div>';

		$this->display_footer();
	}

	function get_table_html()
	{
		$table = new HTML_Table(array('class' => 'forum', 'cellspacing' => 1));

		$this->create_table_header($table);
		$row = 2;
		$this->create_table_forums($table, $row, 0);
		$this->create_table_categories($table, $row);

		return $table;
	}

	function create_table_header($table)
	{
		$table->setCellContents(0, 0, '');
		$table->setCellAttributes(0, 0, array('colspan' => 6, 'class' => 'category'));

		$table->setHeaderContents(1, 0, Translation :: get('Forum'));
		$table->setCellAttributes(1, 0, array('colspan' => 2));
		$table->setHeaderContents(1, 2, Translation :: get('Topics'));
		$table->setCellAttributes(1, 2, array('width' => 50));
		$table->setHeaderContents(1, 3, Translation :: get('Posts'));
		$table->setCellAttributes(1, 3, array('width' => 50));
		$table->setHeaderContents(1, 4, Translation :: get('LastPost'));
		$table->setCellAttributes(1, 4, array('width' => 130));
		$table->setHeaderContents(1, 5, '');
		$table->setCellAttributes(1, 5, array('width' => 125));
	}

	function create_table_categories($table, &$row)
	{
		$conditions[] = new EqualityCondition(LearningObjectPublicationCategory :: PROPERTY_COURSE, $this->get_parent()->get_course_id());
		$conditions[] = new EqualityCondition(LearningObjectPublicationCategory :: PROPERTY_TOOL, $this->get_parent()->get_tool_id());
		$condition = new AndCondition($conditions);

		$categories = WeblcmsDataManager :: get_instance()->retrieve_learning_object_publication_categories($condition, $offset, $count, $order_property, $order_direction);

		while($category = $categories->next_result())
		{
			$table->setCellContents($row, 0, '<a href="javascript:void();">' . $category->get_name() . '</a>');
			$table->setCellAttributes($row, 0, array('colspan' => 2, 'class' => 'category'));
			$table->setCellContents($row, 2, '');
			$table->setCellAttributes($row, 2, array('colspan' => 4, 'class' => 'category_right'));
			$row++;
			$this->create_table_forums($table, $row, $category->get_id());
		}

	}

	function create_table_forums($table, &$row, $parent)
	{
		$condition = new EqualityCondition(LearningObjectPublication :: PROPERTY_TOOL, 'forum');
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
		$cond = new EqualityCondition('type','forum');

		$publications = WeblcmsDataManager :: get_instance()->retrieve_learning_object_publications($this->get_course_id(), $parent, $user_id, $course_groups, $condition, false, array (Forum :: PROPERTY_DISPLAY_ORDER_INDEX), array (SORT_ASC), 0, -1, null, $cond);
        $rdm = RepositoryDataManager::get_instance();

		$size = $publications->size();
		$this->size = $size;

		$counter = 0;
		while($publication = $publications->next_result())
		{
			$first = $counter == 0? true : false;
			$last = $counter == ($size - 1) ? true : false;

            //$forum = $rdm->retrieve_learning_object($publication->get_id(), 'forum');
			$forum = $publication->get_learning_object();
			$title = '<a href="' . $this->get_url(array(Tool :: PARAM_ACTION => ForumTool :: ACTION_VIEW_FORUM,ComplexDisplay :: PARAM_DISPLAY_ACTION => ForumDisplay :: ACTION_VIEW_FORUM, Tool :: PARAM_PUBLICATION_ID => $publication->get_learning_object()->get_id())) . '">' . $forum->get_title() . '</a><br />' . strip_tags($forum->get_description());

			if($publication->is_hidden())
			{
				$title = '<span style="color: grey;">' . $title . '</span>';
			}

			$table->setCellContents($row, 0, '<img title="' . Translation :: get('NoNewPosts') . '" src="' . Theme :: get_image_path() . 'forum/forum_read.png" />');
			$table->setCellAttributes($row, 0, array('width' => 50, 'class' => 'row1', 'style' => 'height:50px;'));
			$table->setCellContents($row, 1, $title);
			$table->setCellAttributes($row, 1, array('width' => '0%', 'class' => 'row1'));
			$table->setCellContents($row, 2, $forum->get_total_topics());
			$table->setCellAttributes($row, 2, array('class' => 'row2', 'align' => 'center'));
			$table->setCellContents($row, 3, $forum->get_total_posts());
			$table->setCellAttributes($row, 3, array('class' => 'row2', 'align' => 'center'));
			$table->setCellContents($row, 4, '');
			$table->setCellAttributes($row, 4, array('class' => 'row2'));
			$table->setCellContents($row, 5, $this->get_forum_actions($publication, $first, $last));
			$table->setCellAttributes($row, 5, array('class' => 'row2'));
			$row++;
			$counter++;
		}
	}

	function get_forum_actions($publication, $first, $last)
	{
		if($this->is_allowed(DELETE_RIGHT))
		{
			$delete = array(
				'href' => $this->get_url(array('pid' => $publication->get_id(), Tool :: PARAM_ACTION => Tool :: ACTION_DELETE)),
				'label' => Translation :: get('Delete'),
				'img' => Theme :: get_common_image_path() . 'action_delete.png',
				'confirm' => true
			);
		}

		if($this->is_allowed(EDIT_RIGHT))
		{
			if($publication->is_hidden())
			{
				$actions[] = array(
					'href' => $this->get_url(array('pid' => $publication->get_id(), Tool :: PARAM_ACTION => Tool :: ACTION_TOGGLE_VISIBILITY)),
					'label' => Translation :: get('Show'),
					'img' => Theme :: get_common_image_path() . 'action_invisible.png'
				);
			}
			else
			{
				$actions[] = array(
					'href' => $this->get_url(array('pid' => $publication->get_id(), Tool :: PARAM_ACTION => Tool :: ACTION_TOGGLE_VISIBILITY)),
					'label' => Translation :: get('Hide'),
					'img' => Theme :: get_common_image_path() . 'action_visible.png'
				);
			}

			if($first)
			{
				$actions[] = array(
					'label' => Translation :: get('MoveUpNA'),
					'img' => Theme :: get_common_image_path() . 'action_up_na.png'
				);
			}
			else
			{
				$actions[] = array(
					'href' => $this->get_url(array('pid' => $publication->get_id(), Tool :: PARAM_ACTION => Tool :: ACTION_MOVE, Tool :: PARAM_MOVE => -1)),
					'label' => Translation :: get('MoveUp'),
					'img' => Theme :: get_common_image_path() . 'action_up.png'
				);
			}

			if($last)
			{
				$actions[] = array(
					'label' => Translation :: get('MoveDownNA'),
					'img' => Theme :: get_common_image_path() . 'action_down_na.png'
				);
			}
			else
			{
				$actions[] = array(
					'href' => $this->get_url(array('pid' => $publication->get_id(), Tool :: PARAM_ACTION => Tool :: ACTION_MOVE, Tool :: PARAM_MOVE => 1)),
					'label' => Translation :: get('MoveDown'),
					'img' => Theme :: get_common_image_path() . 'action_down.png'
				);
			}

			$actions[] = array(
				'href' => $this->get_url(array('pid' => $publication->get_id(), Tool :: PARAM_ACTION => Tool :: ACTION_MOVE_TO_CATEGORY)),
				'label' => Translation :: get('Move'),
				'img' => Theme :: get_common_image_path() . 'action_move.png'
			);

			$actions[] = array(
				'href' => $this->get_url(array('pid' => $publication->get_id(), Tool :: PARAM_ACTION => Tool :: ACTION_EDIT)),
				'label' => Translation :: get('Edit'),
				'img' => Theme :: get_common_image_path() . 'action_edit.png'
			);

			$actions[] = $delete;

		}

		return '<div style="float: right;">' . DokeosUtilities :: build_toolbar($actions) . '</div>';
	}

	function get_action_bar()
	{
		$action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

		$action_bar->add_common_action(new ToolbarItem(Translation :: get('Publish'), Theme :: get_common_image_path().'action_publish.png', $this->get_url(array(AnnouncementTool :: PARAM_ACTION => AnnouncementTool :: ACTION_PUBLISH)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		$action_bar->add_common_action(new ToolbarItem(Translation :: get('ManageCategories'), Theme :: get_common_image_path().'action_category.png', $this->get_url(array(DocumentTool :: PARAM_ACTION => DocumentTool :: ACTION_MANAGE_CATEGORIES)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

//		if(!$this->introduction_text && PlatformSetting :: get('enable_introduction', 'weblcms'))
//		{
//			$action_bar->add_common_action(new ToolbarItem(Translation :: get('PublishIntroductionText'), Theme :: get_common_image_path().'action_publish.png', $this->get_url(array(AnnouncementTool :: PARAM_ACTION => Tool :: ACTION_PUBLISH_INTRODUCTION)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
//		}

		return $action_bar;
	}
}
?>