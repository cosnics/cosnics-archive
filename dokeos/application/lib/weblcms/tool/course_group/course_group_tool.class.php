<?php
/**
 * $Id$
 * CourseGroup tool
 * @package application.weblcms.tool
 * @subpackage course_group
 */

require_once dirname(__FILE__).'/course_group_tool_component.class.php';
/**
 * This tool allows a course_group to publish course_groups in his or her course.
 */
class CourseGroupTool extends Tool
{
	const PARAM_COURSE_GROUP_ACTION = 'course_group_action';
	
	const ACTION_SUBSCRIBE = 'course_group_subscribe';
	const ACTION_UNSUBSCRIBE = 'course_group_unsubscribe';
	const ACTION_ADD_COURSE_GROUP = 'add_course_group';
	const ACTION_USER_SELF_SUBSCRIBE = 'user_subscribe';
	const ACTION_USER_SELF_UNSUBSCRIBE = 'user_unsubscribe';
	const ACTION_VIEW_GROUPS = 'view';
	
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
			case self :: ACTION_VIEW_GROUPS :
				$component = CourseGroupToolComponent :: factory('Browser', $this);
				break;
			case self :: ACTION_SUBSCRIBE :
				$component = CourseGroupToolComponent :: factory('SubscribeBrowser', $this);
				break;
			case self :: ACTION_UNSUBSCRIBE :
				$component = CourseGroupToolComponent :: factory('UnsubscribeBrowser', $this);
				break;
			case self :: ACTION_ADD_COURSE_GROUP :
				$component = CourseGroupToolComponent :: factory('Creator', $this);
				break;
			case self :: ACTION_USER_SELF_SUBSCRIBE :
				$component = CourseGroupToolComponent :: factory('SelfSubscriber', $this);
				break;
			case self :: ACTION_USER_SELF_UNSUBSCRIBE :
				$component = CourseGroupToolComponent :: factory('SelfUnsubscriber', $this);
				break;
			default :
				$component = CourseGroupToolComponent :: factory('Browser', $this);
		}
		$component->run();
	}
}
?>