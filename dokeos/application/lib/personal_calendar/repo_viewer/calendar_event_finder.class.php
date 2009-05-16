<?php
/**
 * @package application.lib.profiler.repo_viewer
 */
 require_once Path :: get_application_library_path() . 'repo_viewer/component/finder.class.php';
require_once dirname(__FILE__).'/calendar_event_browser.class.php';
require_once Path :: get_library_path().'condition/and_condition.class.php';
require_once Path :: get_library_path().'condition/or_condition.class.php';
require_once Path :: get_library_path().'condition/pattern_match_condition.class.php';
require_once Path :: get_library_path().'html/formvalidator/FormValidator.class.php';
/**
 * This class represents a profiler repo_viewer component which can be used
 * to search for a certain learning object.
 */
 
class CalendarEventRepoViewerFinderComponent extends RepoViewerFinderComponent
{
	function CalendarEventRepoViewerFinderComponent($parent)
	{
		parent :: __construct($parent);
		$this->get_form()->addElement('hidden', PersonalCalendarManager :: PARAM_ACTION);
		$this->get_form()->addElement('hidden', Application :: PARAM_APPLICATION);
	}
}
?>