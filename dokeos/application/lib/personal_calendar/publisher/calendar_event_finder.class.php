<?php
/**
 * @package application.lib.profiler.publisher
 */
 require_once Path :: get_application_library_path() . 'publisher/component/finder.class.php';
require_once dirname(__FILE__).'/calendar_event_browser.class.php';
require_once Path :: get_library_path().'condition/and_condition.class.php';
require_once Path :: get_library_path().'condition/or_condition.class.php';
require_once Path :: get_library_path().'condition/pattern_match_condition.class.php';
require_once Path :: get_library_path().'html/formvalidator/FormValidator.class.php';
/**
 * This class represents a profiler publisher component which can be used
 * to search for a certain learning object.
 */
 
class CalendarEventPublisherFinderComponent extends PublisherFinderComponent
{
	function CalendarEventPublisherFinderComponent($parent)
	{
		parent :: __construct($parent);
		$this->get_form()->addElement('hidden', PersonalCalendar :: PARAM_ACTION);
		$this->get_form()->addElement('hidden', Application :: PARAM_APPLICATION);
	}
}
?>