<?php
require_once Path :: get_library_path().'/html/action_bar/action_bar_renderer.class.php';
require_once dirname(__FILE__) . '/learning_path_browser/learning_path_cell_renderer.class.php';
require_once dirname(__FILE__) . '/learning_path_browser/learning_path_column_model.class.php';
require_once dirname(__FILE__) . '/../../../browser/object_publication_table/object_publication_table.class.php';

class LearningPathToolBrowserComponent extends LearningPathToolComponent
{
	private $action_bar;

	function run()
	{
		if (!$this->is_allowed(VIEW_RIGHT))
		{
			Display :: not_allowed();
			return;
		}

		$conditions = array();
		$conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_COURSE_ID, $this->get_course_id());
		$conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_TOOL, 'learning_path');
		
		$subselect_condition = new EqualityCondition('type', 'introduction');
		$conditions[] = new SubselectCondition(ContentObjectPublication :: PROPERTY_CONTENT_OBJECT_ID, ContentObject :: PROPERTY_ID, RepositoryDataManager :: get_instance()->escape_table_name(ContentObject :: get_table_name()), $subselect_condition);
		$condition = new AndCondition($conditions);
		
		$publications = WeblcmsDataManager :: get_instance()->retrieve_content_object_publications_new($condition);
		$this->introduction_text = $publications->next_result();

		$this->action_bar = $this->get_toolbar();

		$trail = new BreadcrumbTrail();
		$trail->add_help('courses learnpath tool');
		$this->display_header($trail, true);

		if(PlatformSetting :: get('enable_introduction', 'weblcms'))
		{
			echo $this->display_introduction_text($this->introduction_text);
		}

		echo $this->action_bar->as_html();
		//$table = new LearningPathPublicationTable($this, $this->get_user(), array('learning_path'), null);
		$table = new ObjectPublicationTable($this, $this->get_user(), array('learning_path'), $this->get_condition(), new LearningPathCellRenderer($this), new LearningPathColumnModel());
		echo $table->as_html();

		$this->display_footer();
	}

	function get_toolbar()
	{
		$action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

		$action_bar->set_search_url($this->get_url());
		
		if($this->is_allowed(ADD_RIGHT))
		{
			$action_bar->add_common_action(
				new ToolbarItem(
					Translation :: get('Publish'), Theme :: get_common_image_path().'action_publish.png', $this->get_url(array(LearningPathTool :: PARAM_ACTION => LearningPathTool :: ACTION_PUBLISH)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
				)
			);
		}

		$action_bar->add_common_action(
			new ToolbarItem(
				Translation :: get('Browse'), Theme :: get_common_image_path().'action_browser.png', $this->get_url(array(LearningPathTool :: PARAM_ACTION => LearningPathTool :: ACTION_BROWSE_LEARNING_PATHS)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);

		if(!$this->introduction_text && PlatformSetting :: get('enable_introduction', 'weblcms') && $this->is_allowed(EDIT_RIGHT))
		{
			$action_bar->add_common_action(new ToolbarItem(Translation :: get('PublishIntroductionText'), Theme :: get_common_image_path().'action_introduce.png', $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_PUBLISH_INTRODUCTION)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		}
		
		if($this->is_allowed(ADD_RIGHT))
		{
			$action_bar->add_tool_action(new ToolbarItem(
					Translation :: get('ImportScorm'), Theme :: get_common_image_path().'action_import.png', $this->get_url(array(LearningPathTool :: PARAM_ACTION => LearningPathTool :: ACTION_IMPORT_SCORM)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
				));
		}

		return $action_bar;
	}

	function get_condition()
	{
		$query = $this->action_bar->get_query();
		if(isset($query) && $query != '')
		{
			$conditions[] = new LikeCondition(ContentObject :: PROPERTY_TITLE, $query, ContentObject :: get_table_name());
			$conditions[] = new LikeCondition(ContentObject :: PROPERTY_DESCRIPTION, $query, ContentObject :: get_table_name());
			return new OrCondition($conditions);
		}

		return null;
	}
}
?>