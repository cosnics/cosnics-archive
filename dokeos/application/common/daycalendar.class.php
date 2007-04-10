<?php
/**
 * $Id: monthcalendarlearningobjectpublicationlistrenderer.class.php 10541 2006-12-21 10:08:16Z bmol $
 * @package application.common
 */
require_once ('HTML/Table.php');
/**
 * A tabular representation of a day calendar
 */
class DayCalendar extends HTML_Table
{
	/**
	 * A time in the day represented by this calendar
	 */
	private $display_time;
	/**
	 * The navigation links
	 */
	private $navigation_html;
	/**
	 * The number of hours for one table cell.
	 */
	private $hour_step;
	/**
	 * Creates a new day calendar
	 * @param int $display_time A time in the day to be displayed
	 * @param int $hour_step The number of hours for one table cell. Defaults to
	 * 1.
	 */
	function DayCalendar($display_time,$hour_step = 1)
	{
		$this->navigation_html = '';
		$this->hour_step = $hour_step;
		if (is_null($display_time))
		{
			$display_time = time();
		}
		$this->display_time = $display_time;
		parent::HTML_Table(array('class'=>'calendar'));
		$cell_mapping = array();
		$this->build_table();
	}
	/**
	 * Gets the number of hours for one table cell.
	 * @return int
	 */
	public function get_hour_step()
	{
		return $this->hour_step;
	}
	/**
	 * Gets the first date which will be displayed by this calendar.
	 * @return int
	 */
	public function get_start_time()
	{
		return strtotime(date('Y-m-d 00:00:00',$this->display_time));
	}
	/**
	 * Gets the end date which will be displayed by this calendar.
	 * @return int
	 */
	public function get_end_time()
	{
		return strtotime('+24 Hours',$this->get_start_time());
	}
	/**
	 * Builds the table
	 */
	private function build_table()
	{
		for($hour = 0; $hour < 24; $hour += $this->get_hour_step())
		{
			$table_start_date = mktime($hour,0,0,date('m',$this->display_time),date('d',$this->display_time),date('Y',$this->display_time));
			$table_end_date = strtotime('+'.$this->get_hour_step().' hours',$table_start_date);
			$cell_contents = $hour.'u - '.($hour+$this->get_hour_step()).'u';
			$this->setCellContents($hour/$this->get_hour_step(),0,$cell_contents);
			// Is current table date in this month or another one?
			if(date('H') >= $hour && date('H') < $hour + $this->get_hour_step())
			{
				$this->updateCellAttributes($hour/$this->get_hour_step(),0,'class="highlight"');
			}
			if($hour < 8 || $hour > 18)
			{
				$this->updateCellAttributes($hour/$this->get_hour_step(),0,'class="disabled_month"');
			}
		}
	}
	/**
	 * Add an event to the calendar
	 * @param int $time A time in the day on which the event should be displayed
	 * @param string $content The html content to insert in the month calendar
	 */
	public function add_event($time,$content)
	{
		if($time >= $this->get_end_time())
		{
			return;
		}
		$row = date('H',$time)/$this->hour_step;
		$cell_content = $this->getCellContents($row,0);
		$cell_content .= $content;
		$this->setCellContents($row,0, $cell_content );
	}
	/**
	 * Adds a navigation bar to the calendar
	 * @param string $url_format The -TIME- in this string will be replaced by a
	 * timestamp
	 */
	public function add_calendar_navigation($url_format)
	{
		$prev = strtotime('-1 Day',$this->display_time);
		$next = strtotime('+1 Day',$this->display_time);
		$navigation = new HTML_Table('class="calendar_navigation"');
		$navigation->updateCellAttributes(0,0,'style="text-align: left;"');
		$navigation->updateCellAttributes(0,1,'style="text-align: center;"');
		$navigation->updateCellAttributes(0,2,'style="text-align: right;"');
		$navigation->setCellContents(0,0,'<a href="'.str_replace('-TIME-',$prev,$url_format).'"><img src="'.api_get_path(WEB_CODE_PATH).'/img/prev.png" style="vertical-align: middle;" alt="&lt;&lt;"/></a> ');
		$navigation->setCellContents(0,1,date('l d F Y',$this->display_time));
		$navigation->setCellContents(0,2,' <a href="'.str_replace('-TIME-',$next,$url_format).'"><img src="'.api_get_path(WEB_CODE_PATH).'/img/next.png" style="vertical-align: middle;" alt="&gt;&gt;"/></a> ');
		$this->navigation_html = $navigation->toHtml();
	}
	/**
	 * Sets the daynames.
	 * If you don't use this function, the long daynames will be displayed
	 * @param array $daynames An array of 7 elements with keys 0 -> 6 containing
	 * the titles to display.
	 */
	public function set_daynames($daynames)
	{
		for($day = 0; $day < 7; $day++)
		{
			$this->setCellContents(0,$day+1,$daynames[$day]);
		}
	}
	/**
	 * Returns a html-representation of this monthcalendar
	 * @return string
	 */
	public function toHtml()
	{
		$html = parent::toHtml();
		return $this->navigation_html.$html;
	}
}
?>