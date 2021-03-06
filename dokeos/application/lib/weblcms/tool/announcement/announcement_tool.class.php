<?php
/**
 * $Id$
 * Announcement tool
 * @package application.weblcms.tool
 * @subpackage announcement
 */

require_once dirname(__FILE__).'/announcement_tool_component.class.php';
/**
 * This tool allows a user to publish announcements in his or her course.
 */
class AnnouncementTool extends Tool
{
	const ACTION_VIEW_ANNOUNCEMENTS = 'view';
	
	/**
	 * Inherited.
	 */
	function run()
	{
		$action = $this->get_action();
		$component = parent :: run();
		
		if($component)
		{
			return;
		}
		
		switch ($action)
		{
			case self :: ACTION_VIEW_ANNOUNCEMENTS :
				$component = AnnouncementToolComponent :: factory('Viewer', $this);
				break;
			case self :: ACTION_PUBLISH :
				$component = AnnouncementToolComponent :: factory('Publisher', $this);
				break;
				
			default :
				$component = AnnouncementToolComponent :: factory('Viewer', $this);
		}
		$component->run();
	}
	
	static function get_allowed_types()
	{
		return array('announcement');
	}
}
?>