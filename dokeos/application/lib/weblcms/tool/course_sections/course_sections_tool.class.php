<?php
/**
 * $Id: course_settings_tool.class.php 16605 2008-10-23 10:50:07Z vanpouckesven $
 * Announcement tool
 * @package application.weblcms.tool
 * @subpackage course_settings
 */

require_once dirname(__FILE__).'/course_sections_tool_component.class.php';
/**
 * This tool allows a user to publish course_sectionss in his or her course.
 */
class CourseSectionsTool extends Tool
{
	const ACTION_VIEW_COURSE_SECTIONS = 'view';
	const ACTION_CREATE_COURSE_SECTION = 'create';
	const ACTION_REMOVE_COURSE_SECTION = 'remove';
	const ACTION_UPDATE_COURSE_SECTION = 'update';
	const ACTION_MOVE_COURSE_SECTION = 'move_course_section';
	const ACTION_CHANGE_COURSE_SECTION_VISIBILITY = 'change_visibility';
	const ACTION_SELECT_TOOLS_COURSE_SECTION = 'tool_selector';
	const ACTION_CHANGE_SECTION = 'change_section';
	
	const PARAM_COURSE_SECTION_ID = 'course_section_id';
	const PARAM_DIRECTION = 'direction';
	const PARAM_REMOVE_SELECTED = 'remove_selected';
	
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
			case self :: ACTION_VIEW_COURSE_SECTIONS :
				$component = CourseSectionsToolComponent :: factory('Viewer', $this);
				break;
			case self :: ACTION_CREATE_COURSE_SECTION :
				$component = CourseSectionsToolComponent :: factory('Creator', $this);
				break;
			case self :: ACTION_REMOVE_COURSE_SECTION :
				$component = CourseSectionsToolComponent :: factory('Deleter', $this);
				break;
			case self :: ACTION_UPDATE_COURSE_SECTION :
				$component = CourseSectionsToolComponent :: factory('Updater', $this);
				break;
			case self :: ACTION_MOVE_COURSE_SECTION :
				$component = CourseSectionsToolComponent :: factory('Mover', $this);
				break;
			case self :: ACTION_CHANGE_COURSE_SECTION_VISIBILITY :
				$component = CourseSectionsToolComponent :: factory('VisibilityChanger', $this);
				break;
			case self :: ACTION_SELECT_TOOLS_COURSE_SECTION :
				$component = CourseSectionsToolComponent :: factory('ToolSelector', $this);
				break;
			case self :: ACTION_CHANGE_SECTION :
				$component = CourseSectionsToolComponent :: factory('ChangeSection', $this);
				break;
			default :
				$component = CourseSectionsToolComponent :: factory('Viewer', $this);
		}
		$component->run();
	}
}
?>