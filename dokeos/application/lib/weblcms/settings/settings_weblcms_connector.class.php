<?php
require_once Path :: get_application_path() . 'lib/weblcms/weblcms_data_manager.class.php';
require_once Path :: get_application_path() . 'lib/weblcms/course/course.class.php';
require_once Path :: get_library_path() . 'dokeos_utilities.class.php';
require_once Path :: get_library_path() . 'filesystem/path.class.php';
require_once Path :: get_library_path() . 'filesystem/filesystem.class.php';

/**
 * Simple connector class to facilitate rendering settings forms by
 * preprocessing data from the datamanagers to a simple array format.
 * @author Hans De Bisschop
 */

class SettingsWeblcmsConnector
{
	function get_course_layouts()
	{
		return Course :: get_layouts();
	}
	
	function get_tool_shortcut_options()
	{
		return Course :: get_tool_shortcut_options();
	}
	
	function get_course_menu_options()
	{
		return Course :: get_menu_options();
	}
	
	function get_breadcrumb_options()
	{
		return Course :: get_breadcrumb_options();
	}
}
?>
