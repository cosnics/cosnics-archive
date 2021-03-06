<?php

require_once dirname(__FILE__) . '/../calendar_tool.class.php';
require_once dirname(__FILE__) . '/../calendar_tool_component.class.php';
require_once dirname(__FILE__).'/../../../content_object_repo_viewer.class.php';
require_once dirname(__FILE__) . '/../../../publisher/content_object_publisher.class.php';

class CalendarToolPublisherComponent extends CalendarToolComponent
{
	function run()
	{
		if(!$this->is_allowed(ADD_RIGHT))
		{
			Display :: not_allowed();
			return;
		}

		$trail = new BreadcrumbTrail();
        $trail->add(new BreadCrumb($this->get_url(array(Tool :: PARAM_ACTION => CalendarTool :: ACTION_PUBLISH)), Translation :: get('Publish')));
        $trail->add_help('courses calendar tool');
		//$pub = new ContentObjectPublisher($this, 'calendar_event', true);

		$event = new CalendarEvent();
		$event->set_owner_id($this->get_user_id());
		$event->set_start_date(intval(Request :: get('default_start_date')));
		$event->set_end_date(intval(Request :: get('default_end_date')));

		$object = Request :: get('object');
		$pub = new ContentObjectRepoViewer($this, 'calendar_event', true);
		$pub->set_default_content_object('calendar_event',$event);

		if(!isset($object))
		{
			$html[] =  $pub->as_html();
		}
		else
		{
			//$html[] = 'ContentObject: ';
			$publisher = new ContentObjectPublisher($pub);
			$html[] = $publisher->get_publications_form($object);
		}

		$this->display_header($trail, true);
		echo implode("\n",$html);
		$this->display_footer();
	}
}
?>