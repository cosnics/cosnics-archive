<?php
/**
 * $Id$
 * @package application.personal_calendar
 */
require_once (dirname(__FILE__).'/../personal_calendar_renderer.class.php');
require_once (Path :: get_application_library_path().'mini_month_calendar.class.php');
/**
 * This personal calendar renderer provides a tabular month view to navigate in
 * the calendar
 */
class PersonalCalendarMiniMonthRenderer extends PersonalCalendarRenderer
{
	/**
	 * @see PersonalCalendarRenderer::render()
	 */
	public function render()
	{
		$calendar = new MiniMonthCalendar($this->get_time());
		$from_date = strtotime(date('Y-m-1',$this->get_time()));
		$to_date = strtotime('-1 Second',strtotime('Next Month',$from_date));
		$events = $this->get_events($from_date,$to_date);
		$html = array();
		
		$start_time = $calendar->get_start_time();
		$end_time = $calendar->get_end_time();
		$table_date = $start_time;
		
		while($table_date <= $end_time)
		{
			$next_table_date = strtotime('+24 Hours',$table_date);
			
			foreach ($events as $index => $event)
			{
				if (!$calendar->contains_events_for_time($table_date))
				{
					$start_date = $event->get_start_date();
					$end_date = $event->get_end_date();
					
					if ($table_date < $start_date && $start_date < $next_table_date || $table_date <= $end_date && $end_date <= $next_table_date || $start_date <= $table_date && $next_table_date <= $end_date)
					{
						$content = $this->render_event($event);
						$calendar->add_event($table_date, $content);
					}
				}
			}
			$table_date = $next_table_date;
		}
		
		$parameters['time'] = '-TIME-';
		$calendar->add_calendar_navigation($this->get_parent()->get_url($parameters));
		switch($this->get_parent()->get_parameter('view'))
		{
			case 'month':
				$calendar->mark_period(MiniMonthCalendar::PERIOD_MONTH);
				break;
			case 'week':
				$calendar->mark_period(MiniMonthCalendar::PERIOD_WEEK);
				break;
			case 'day':
				$calendar->mark_period(MiniMonthCalendar::PERIOD_DAY);
				break;
		}
		$calendar->add_navigation_links($this->get_parent()->get_url($parameters));
		$html = $calendar->toHtml();
		return $html;
	}
	/**
	 * Gets a html representation of a published calendar event
	 * @param PersonalCalendarEvent $event
	 * @return string
	 */
	private function render_event($event)
	{
		$html[] = '<br /><img src="'.Theme :: get_common_image_path().'action_posticon.png"/>';
		return implode("\n",$html);
	}
}
?>