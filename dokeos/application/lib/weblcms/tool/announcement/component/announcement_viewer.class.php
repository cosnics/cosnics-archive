<?php

require_once dirname(__FILE__) . '/../announcement_tool.class.php';
require_once dirname(__FILE__) . '/../announcement_tool_component.class.php';
require_once dirname(__FILE__) . '/announcement_viewer/announcement_browser.class.php';
require_once Path :: get_library_path() . '/html/action_bar/action_bar_renderer.class.php';

class AnnouncementToolViewerComponent extends AnnouncementToolComponent
{
	private $action_bar;
	private $introduction_text;

	function run()
	{
		if(!$this->is_allowed(VIEW_RIGHT))
		{
			Display :: not_allowed();
			return;
		}
		
		$conditions = array();
		$conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_COURSE_ID, $this->get_course_id());
		$conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_TOOL, 'announcement');
		
		$subselect_condition = new EqualityCondition('type', 'introduction');
		$conditions[] = new SubselectCondition(ContentObjectPublication :: PROPERTY_CONTENT_OBJECT_ID, ContentObject :: PROPERTY_ID, RepositoryDataManager :: get_instance()->escape_table_name(ContentObject :: get_table_name()), $subselect_condition);
		$condition = new AndCondition($conditions);
		
		$publications = WeblcmsDataManager :: get_instance()->retrieve_content_object_publications_new($condition);
		$this->introduction_text = $publications->next_result();

		$this->action_bar = $this->get_action_bar();

		$browser = new AnnouncementBrowser($this);
        //$announcements = $browser->get_publications();
        //dump($announcements);
		$trail = new BreadcrumbTrail();
        if(Request :: get('tool_action')=='view')
        {
            $publication = WebLcmsDataManager :: get_instance()->retrieve_content_object_publication(Request :: get('pid'));
            $trail->add(new BreadCrumb($this->get_url(array(Tool :: PARAM_ACTION => 'view', Tool :: PARAM_PUBLICATION_ID => Request :: get('pid'))), $publication->get_content_object()->get_title()));
        }
        $trail->add_help('courses announcement tool');
		$this->display_header($trail, true);

		//echo $this->perform_requested_actions();
		if(!Request :: get('pid'))
		{
			if(PlatformSetting :: get('enable_introduction', 'weblcms'))
			{
				echo $this->display_introduction_text($this->introduction_text);
			}
		}

        $html = $browser->as_html();
		echo $this->action_bar->as_html();
		echo '<div id="action_bar_browser">';
		echo $html;
		echo '</div>';

		$this->display_footer();
	}

	function add_actionbar_item($item)
	{
		$this->action_bar->add_tool_action($item);
	}

	function get_action_bar()
	{
		$action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

		if(!Request :: get('pid'))
		{
			$action_bar->set_search_url($this->get_url());
			if($this->is_allowed(ADD_RIGHT))
			{
				$action_bar->add_common_action(new ToolbarItem(Translation :: get('Publish'), Theme :: get_common_image_path().'action_publish.png', $this->get_url(array(AnnouncementTool :: PARAM_ACTION => AnnouncementTool :: ACTION_PUBLISH)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
			}
		}

		$action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path().'action_browser.png', $this->get_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

		if(!$this->introduction_text && PlatformSetting :: get('enable_introduction', 'weblcms'))
		{
			if($this->is_allowed(EDIT_RIGHT))
			{
				$action_bar->add_common_action(new ToolbarItem(Translation :: get('PublishIntroductionText'), Theme :: get_common_image_path().'action_introduce.png', $this->get_url(array(AnnouncementTool :: PARAM_ACTION => Tool :: ACTION_PUBLISH_INTRODUCTION)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
			}
		}

		//$action_bar->add_tool_action(new ToolbarItem(Translation :: get('Edit'), Theme :: get_common_image_path().'action_edit.png', $this->get_url(array(AnnouncementTool :: PARAM_ACTION => AnnouncementTool :: ACTION_PUBLISH)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		//$action_bar->add_tool_action(new ToolbarItem(Translation :: get('Delete'), Theme :: get_common_image_path().'action_delete.png', $this->get_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
	
		if($this->is_allowed(EDIT_RIGHT))
		{
        	$action_bar->add_tool_action($this->get_access_details_toolbar_item($this));
		}

		return $action_bar;
	}

	function get_condition()
	{
		$query = $this->action_bar->get_query();
		if(isset($query) && $query != '')
		{
			$conditions[] = new LikeCondition(ContentObject :: PROPERTY_TITLE, $query);
			$conditions[] = new LikeCondition(ContentObject :: PROPERTY_DESCRIPTION, $query);
			return new OrCondition($conditions);
		}

		return null;
	}

}
?>