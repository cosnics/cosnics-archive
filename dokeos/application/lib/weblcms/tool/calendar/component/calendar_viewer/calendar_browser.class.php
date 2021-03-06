<?php
/**
 * $Id$
 * Calendar tool - browser
 * @package application.weblcms.tool
 * @subpackage calendar
 */
require_once dirname(__FILE__).'/../../../../weblcms_data_manager.class.php';
require_once dirname(__FILE__).'/../../../../content_object_publication_browser.class.php';
require_once dirname(__FILE__).'/calendar_list_renderer.class.php';
require_once dirname(__FILE__).'/calendar_details_renderer.class.php';
require_once dirname(__FILE__).'/../../../../browser/list_renderer/mini_month_calendar_content_object_publication_list_renderer.class.php';
require_once dirname(__FILE__).'/../../../../browser/list_renderer/month_calendar_content_object_publication_list_renderer.class.php';
require_once dirname(__FILE__).'/../../../../browser/list_renderer/week_calendar_content_object_publication_list_renderer.class.php';
require_once dirname(__FILE__).'/../../../../browser/list_renderer/day_calendar_content_object_publication_list_renderer.class.php';
require_once dirname(__FILE__).'/../../../../browser/list_renderer/content_object_publication_details_renderer.class.php';

class CalendarBrowser extends ContentObjectPublicationBrowser
{
	const CALENDAR_MONTH_VIEW = 'month';
	const CALENDAR_WEEK_VIEW = 'week';
	const CALENDAR_DAY_VIEW = 'day';
	const CALENDAR_LIST_VIEW = 'list';
	private $publications;
	private $time;
	
	function CalendarBrowser($parent)
	{
		parent :: __construct($parent, 'calendar');
		if(Request :: get('pid'))
		{
			$this->set_publication_id(Request :: get('pid'));
			//$renderer = new ContentObjectPublicationDetailsRenderer($this);
			$renderer = new CalendarDetailsRenderer($this);
		}
		else
		{
			$time = Request :: get('time') ? intval(Request :: get('time')) : time();
			$this->time = $time;
			//$this->set_parameter('time',$time);

			switch(Request :: get('view'))
			{
				case CalendarBrowser::CALENDAR_DAY_VIEW:
				{
					$renderer = new DayCalendarContentObjectPublicationListRenderer($this);
					$renderer->set_display_time($time);
					break;
				}
				case CalendarBrowser::CALENDAR_WEEK_VIEW:
				{
					$renderer = new WeekCalendarContentObjectPublicationListRenderer($this);
					$renderer->set_display_time($time);
					break;
				}
				case CalendarBrowser::CALENDAR_MONTH_VIEW:
				{
					$renderer = new MonthCalendarContentObjectPublicationListRenderer($this);
					$renderer->set_display_time($time);
					break;
				}
				case CalendarBrowser::CALENDAR_LIST_VIEW:
				{
					$renderer = new CalendarListRenderer($this);
					$actions = array(Tool :: ACTION_DELETE => Translation :: get('DeleteSelected'), 
						 Tool :: ACTION_HIDE => Translation :: get('Hide'), 
						 Tool :: ACTION_SHOW => Translation :: get('Show'));
					$renderer->set_actions($actions);
					break;
				}
				default:
				{
					$renderer = new MonthCalendarContentObjectPublicationListRenderer($this);
					$renderer->set_display_time($time);
					break;
				}
			}
		}
			
		$this->set_publication_list_renderer($renderer);
	}


	function get_publications($from, $count, $column, $direction)
	{
		if( isset($this->publications))
		{
			return $this->publications;
		}
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
		
		$datamanager = WeblcmsDataManager :: get_instance();
		$conditions = array();
		$conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_COURSE_ID, $this->get_course_id());
		$conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_TOOL, 'calendar');
		
		$access = array();
		$access[] = new InCondition('user_id', $user_id, $datamanager->get_database()->get_alias('content_object_publication_user'));
		$access[] = new InCondition('course_group_id', $course_groups, $datamanager->get_database()->get_alias('content_object_publication_course_group'));
		if (!empty($user_id) || !empty($course_groups))
		{
			$access[] = new AndCondition(array(new EqualityCondition('user_id', null, $datamanager->get_database()->get_alias('content_object_publication_user')), new EqualityCondition('course_group_id', null, $datamanager->get_database()->get_alias('content_object_publication_course_group'))));
		}
		$conditions[] = new OrCondition($access);
		
		$subselect_conditions = array();
		$subselect_conditions[] = new EqualityCondition('type', 'calendar_event');
		if($this->get_parent()->get_condition())
		{
			$subselect_conditions[] = $this->get_parent()->get_condition();
		}
		$subselect_condition = new AndCondition($subselect_conditions);
		$conditions[] = new SubselectCondition(ContentObjectPublication :: PROPERTY_CONTENT_OBJECT_ID, ContentObject :: PROPERTY_ID, RepositoryDataManager :: get_instance()->escape_table_name(ContentObject :: get_table_name()), $subselect_condition);
		$condition = new AndCondition($conditions);
		
		$this->publications = $datamanager->retrieve_content_object_publications_new($condition)->as_array();		
		return $this->publications;
	}

	function get_publication_count()
	{
		return count($this->get_publications());
	}

	/**
	 * Get calendar events in a certain time range
	 * @param int $from_time
	 * @param int $to_time
	 * @return array A set of publications of calendar_events
	 */
	function get_calendar_events($from_time, $to_time)
	{
		$publications = $this->get_publications();
		
		$events = array();
		foreach($publications as $index => $publication)
		{
			$object = $publication->get_content_object();
			
			if ($object->repeats())
			{
				$repeats = $object->get_repeats($from_time, $to_time);
				
				foreach($repeats as $repeat)
				{
					$the_publication = clone $publication;
					$the_publication->set_content_object($repeat);
					
					$events[] = $the_publication;
				}
			}
			elseif($from_time <= $object->get_start_date() && $object->get_start_date() <= $to_time || $from_time <= $object->get_end_date() && $object->get_end_date() <= $to_time || $object->get_start_date() <= $from_time && $to_time <= $object->get_end_date())
			{				
				$events[] = $publication;
			}
		}
		
		return $events;
	}
	public function as_html()
	{
		if(!Request :: get('pid'))
		{
			$minimonthcalendar = new MiniMonthCalendarContentObjectPublicationListRenderer($this);
			$minimonthcalendar->set_display_time($this->time);
			$html[] = '<div class="mini_calendar">';
			$html[] =  $minimonthcalendar->as_html();
			$html[] =  '</div>';
			$html[] =  '<div class="normal_calendar">';
			$html[] = parent::as_html();
			$html[] = '</div>';
		}
		else
		{
			$html[] = parent::as_html();
		}
		return implode("\n",$html);
	}
}
?>