<?php
/**
 * $Id$
 * Link tool
 * @package application.weblcms.tool
 * @subpackage announcement
 */

require_once dirname(__FILE__).'/link_tool_component.class.php';
/**
 * This tool allows a user to publish links in his or her course.
 */
class LinkTool extends Tool
{
	const ACTION_VIEW_ANNOUNCEMENTS = 'view';
	
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
			case self :: ACTION_VIEW_ANNOUNCEMENTS :
				$component = LinkToolComponent :: factory('Viewer', $this);
				break;
			case self :: ACTION_PUBLISH :
				$component = LinkToolComponent :: factory('Publisher', $this);
				break;
			default :
				$component = LinkToolComponent :: factory('Viewer', $this);
		}
		$component->run();
	}
	
	static function get_allowed_types()
	{
		return array('link');
	}
}
?>