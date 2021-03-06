<?php
/**
 * $Id$
 * Announcement tool
 * @package application.weblcms.tool
 * @subpackage course_settings
 */

require_once dirname(__FILE__).'/course_settings_tool_component.class.php';
/**
 * This tool allows a user to publish course_settingss in his or her course.
 */
class CourseSettingsTool extends Tool
{
	const ACTION_UPDATE_COURSE_SETTINGS = 'update';
	
	/**
	 * Inherited.
	 */
	function run()
	{
		$action = $this->get_action();
		$component = parent :: run();
		
		if($component) return;
		
		switch ($action)
		{
			case self :: ACTION_UPDATE_COURSE_SETTINGS :
				$component = CourseSettingsToolComponent :: factory('Updater', $this);
				break;
			default :
				$component = CourseSettingsToolComponent :: factory('Updater', $this);
		}
		$component->run();
	}
}
?>