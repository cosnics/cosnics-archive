<?php
/**
 * @package application.lib.calendar.publisher
 */
require_once dirname(__FILE__).'/../personal_calendar_block.class.php';

require_once Path :: get_library_path() . 'dokeos_utilities.class.php';
require_once dirname(__FILE__).'/../../../../repository/lib/content_object_display.class.php';
require_once dirname(__FILE__).'/../renderer/personal_calendar_mini_month_renderer.class.php';
/**
 * This class represents a calendar publisher component which can be used
 * to browse through the possible learning objects to publish.
 */
class PersonalCalendarMonth extends PersonalCalendarBlock
{
	function run()
	{
		return $this->as_html();
	}
	
	/*
	 * Inherited
	 */
	function as_html()
	{
		$html = array();
		
		$html[] = $this->display_header();
		
		$time = Request :: get('time') ? intval(Request :: get('time')) : time();
		$minimonthcalendar = new PersonalCalendarMiniMonthRenderer($this->get_parent(), $time, 'link');
		$html[] =   $minimonthcalendar->render();
		
		$html[] = $this->display_footer();
		
		return implode("\n", $html);
	}
}
?>