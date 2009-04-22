<?php

require_once dirname(__FILE__) . '/../wiki_tool.class.php';
require_once dirname(__FILE__) . '/../wiki_tool_component.class.php';
require_once dirname(__FILE__).'/wiki_page_table/wiki_page_table.class.php';
require_once Path :: get_library_path() . '/html/action_bar/action_bar_renderer.class.php';

class WikiToolStatisticsViewerComponent extends WikiToolComponent
{
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
        $params['pid'] = Request :: get('pid');
        $url = ReportingManager :: get_reporting_template_registration_url('WikiReportingTemplate',$params);
        header('location: '.$url);
    }
}
?>