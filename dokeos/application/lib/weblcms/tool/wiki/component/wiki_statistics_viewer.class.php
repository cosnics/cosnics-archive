<?php

require_once dirname(__FILE__) . '/../wiki_tool.class.php';
require_once dirname(__FILE__) . '/../wiki_tool_component.class.php';
require_once dirname(__FILE__).'/wiki_page_table/wiki_page_table.class.php';
require_once Path :: get_library_path() . '/html/action_bar/action_bar_renderer.class.php';

class WikiToolStatisticsViewerComponent extends WikiToolComponent
{
	private $action_bar;
    private $wiki_page_id;
    private $cid;


	function run()
	{
		if(!$this->is_allowed(VIEW_RIGHT))
		{
			Display :: not_allowed();
			return;
		}

        $params = array();
        $params[ReportingManager :: PARAM_COURSE_ID] = $this->get_course_id();
        $params['wiki_id'] = Request :: get('wiki_id');
        $url = ReportingManager :: get_reporting_template_registration_url('WikiReportingTemplate',$params);
        header('location: '.$url);
    }

    function get_toolbar()
	{
		$action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

		$action_bar->set_search_url($this->get_url());

        $action_bar->add_common_action(
			new ToolbarItem(
				Translation :: get('Create Wiki Page'), Theme :: get_common_image_path().'action_create.png', $this->get_url(array(Tool :: PARAM_ACTION => WikiTool :: ACTION_CREATE_PAGE, 'wiki_id' => $this->wiki_id)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);
		$action_bar->add_common_action(
			new ToolbarItem(
				Translation :: get('Edit'), Theme :: get_common_image_path().'action_edit.png', $this->get_url(array(Tool :: PARAM_ACTION => WikiTool :: ACTION_EDIT_PAGE, 'cid' => $this->cid)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);

        $action_bar->add_common_action(
			new ToolbarItem(
				Translation :: get('Delete'), Theme :: get_common_image_path().'action_delete.png', $this->get_url(array(WikiTool :: PARAM_ACTION => WikiTool :: ACTION_DELETE, 'cid' => $this->cid)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);

        $action_bar->add_tool_action(
			new ToolbarItem(
				Translation :: get('Discuss'), Theme :: get_common_image_path().'action_edit.png', $this->get_url(array(WikiTool :: PARAM_ACTION => WikiTool :: ACTION_DISCUSS, 'wiki_page_id' => $this->wiki_page_id, 'wiki_id' => $this->wiki_id)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);

        $action_bar->add_tool_action(
			new ToolbarItem(
				Translation :: get('History'), Theme :: get_common_image_path().'action_edit.png', $this->get_url(array(WikiTool :: PARAM_ACTION => WikiTool :: ACTION_HISTORY, 'wiki_page_id' => $this->wiki_page_id, 'wiki_id' => $this->wiki_id)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);

        $action_bar->add_tool_action(
			new ToolbarItem(
				Translation :: get('Notify Changes'), Theme :: get_common_image_path().'action_edit.png', $this->get_url(array(WikiTool :: PARAM_ACTION => WikiTool :: ACTION_HISTORY, 'wiki_page_id' => $this->wiki_page_id)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);

        $action_bar->add_tool_action(
			new ToolbarItem(
				Translation :: get('Statistics'), Theme :: get_common_image_path().'action_statistics.png', $this->get_url(array(WikiTool :: PARAM_ACTION => WikiTool :: ACTION_PAGE_STATISTICS, 'wiki_page_id' => $this->wiki_page_id)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);


		return $action_bar;
	}
}
?>