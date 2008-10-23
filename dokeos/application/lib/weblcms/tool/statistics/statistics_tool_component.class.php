<?php
/**
 * $Id$
 * Repository tool
 * @package application.weblcms.tool.announcement
 */
require_once dirname(__FILE__) . '/../tool_component.class.php';
/**
==============================================================================
 *	This is the base class component for all announcement tool components.
 *
 *	@author Sven Vanpoucke
==============================================================================
 */

class StatisticsToolComponent extends ToolComponent
{
	static function factory($component_name, $announcement_tool)
	{
		return parent :: factory('Statistics', $component_name, $announcement_tool);
	}
}